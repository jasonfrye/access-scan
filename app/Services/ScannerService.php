<?php

namespace App\Services;

use App\Models\Scan;
use App\Models\ScanIssue;
use App\Models\ScanPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ScannerService
{
    /**
     * The timeout for scans in seconds.
     */
    protected int $timeout = 300; // 5 minutes

    /**
     * The maximum pages to crawl in a full scan.
     */
    protected int $maxPages = 100;

    /**
     * Link crawler instance.
     */
    protected ?LinkCrawler $crawler = null;

    /**
     * Run a single-page accessibility scan.
     */
    public function scanPage(string $url): array
    {
        $this->validateUrl($url);

        $command = $this->buildPa11yCommand($url);

        Log::debug('Pa11y command', ['command' => $command]);

        $nodeBinDir = config('services.pa11y.node_bin_dir', '/usr/local/bin');
        $result = Process::env(['PATH' => $nodeBinDir.':/usr/local/bin:/usr/bin:/bin'])->run($command);

        // Pa11y exit codes: 0 = no issues, 1 = tool error, 2 = issues found
        $exitCode = $result->exitCode();

        Log::debug('Pa11y result', [
            'url' => $url,
            'exit_code' => $exitCode,
            'stdout_length' => strlen($result->output()),
            'stdout_preview' => substr($result->output(), 0, 500),
            'stderr_preview' => substr($result->errorOutput(), 0, 500),
        ]);

        if ($exitCode !== 0 && $exitCode !== 2) {
            Log::error('Pa11y scan failed', [
                'url' => $url,
                'exit_code' => $exitCode,
                'output' => $result->output(),
                'error' => $result->errorOutput(),
            ]);

            throw new \RuntimeException('Scan failed (exit code '.$exitCode.'): '.$result->errorOutput());
        }

        return $this->parsePa11yOutput($result->output());
    }

    /**
     * Run a scan and store results in the database.
     */
    public function runScan(Scan $scan): Scan
    {
        $scan->markAsRunning();

        try {
            $pages = $this->crawlAndScan($scan);

            // Calculate overall statistics
            $totalErrors = 0;
            $totalWarnings = 0;
            $totalNotices = 0;
            $totalIssues = 0;

            foreach ($pages as $page) {
                $totalErrors += $page['errors_count'];
                $totalWarnings += $page['warnings_count'];
                $totalNotices += $page['notices_count'];
                $totalIssues += $page['issues_count'];
            }

            // Calculate score (simple formula: 100 - weighted errors)
            $score = $this->calculateScore($totalErrors, $totalWarnings, $totalNotices);

            // Update scan with results
            $scan->markAsCompleted([
                'pages_scanned' => count($pages),
                'issues_found' => $totalIssues,
                'errors_count' => $totalErrors,
                'warnings_count' => $totalWarnings,
                'notices_count' => $totalNotices,
                'score' => $score,
            ]);

            $scan->calculateGrade();
            $scan->save();

            Log::info('Scan completed', [
                'scan_id' => $scan->id,
                'pages_scanned' => count($pages),
                'score' => $score,
                'grade' => $scan->grade,
            ]);

            return $scan;
        } catch (\Exception $e) {
            $scan->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Crawl a site and scan each page.
     */
    protected function crawlAndScan(Scan $scan): array
    {
        $url = $scan->url;
        $pages = [];

        // Determine how many pages to scan based on scan type and plan
        $maxPages = $this->determineMaxPages($scan);

        // For single-page scans, just scan the main URL
        if ($maxPages === 1) {
            $result = $this->scanPage($url);
            $page = $this->storePageResult($scan, $url, $result);
            $pages[] = $page;

            foreach ($result['issues'] ?? [] as $issue) {
                $this->storeIssue($page, $issue);
            }

            return $pages;
        }

        // For multi-page scans, crawl the site first
        $crawler = $this->getCrawler();
        $crawler->setMaxPages($maxPages);
        $crawler->setMaxDepth(3); // Crawl up to 3 levels deep

        $crawlResult = $crawler->crawl($url);

        Log::info('Crawl completed', [
            'scan_id' => $scan->id,
            'pages_found' => $crawlResult['total_pages'],
        ]);

        // Scan each discovered page (enforce max pages as safety net)
        $pagesToScan = array_slice($crawlResult['pages'], 0, $maxPages);
        foreach ($pagesToScan as $pageInfo) {
            try {
                $result = $this->scanPage($pageInfo['url']);

                $page = $this->storePageResult($scan, $pageInfo['url'], $result, [
                    'title' => $pageInfo['title'] ?? null,
                ]);

                foreach ($result['issues'] ?? [] as $issue) {
                    $this->storeIssue($page, $issue);
                }

                $pages[] = $page;

            } catch (\Exception $e) {
                Log::warning('Failed to scan page', [
                    'url' => $pageInfo['url'],
                    'error' => $e->getMessage(),
                ]);

                // Store failed page
                $page = ScanPage::create([
                    'scan_id' => $scan->id,
                    'url' => $pageInfo['url'],
                    'status' => 'failed',
                    'issues_count' => 0,
                    'errors_count' => 0,
                    'warnings_count' => 0,
                    'notices_count' => 0,
                    'http_status' => 500,
                ]);
                $pages[] = $page;
            }
        }

        return $pages;
    }

    /**
     * Determine maximum pages to scan based on scan type and user plan.
     */
    protected function determineMaxPages(Scan $scan): int
    {
        // Quick scans always scan 1 page
        if ($scan->scan_type === Scan::TYPE_QUICK) {
            return 1;
        }

        // Full, scheduled, and API scans use the user's max pages per scan
        $user = $scan->user;
        if ($user) {
            return $user->getMaxPagesPerScan();
        }

        // Guest scans get a limited number of pages
        return 5;
    }

    /**
     * Get or create the link crawler.
     */
    protected function getCrawler(): LinkCrawler
    {
        if ($this->crawler === null) {
            $this->crawler = new LinkCrawler;
        }

        return $this->crawler;
    }

    /**
     * Store a page result in the database.
     */
    protected function storePageResult(Scan $scan, string $url, array $result, array $metadata = []): ScanPage
    {
        $errors = $result['counts']['error'] ?? 0;
        $warnings = $result['counts']['warning'] ?? 0;
        $notices = $result['counts']['notice'] ?? 0;

        return ScanPage::create([
            'scan_id' => $scan->id,
            'url' => $url,
            'status' => 'completed',
            'issues_count' => count($result['issues'] ?? []),
            'errors_count' => $errors,
            'warnings_count' => $warnings,
            'notices_count' => $notices,
            'score' => $this->calculateScore($errors, $warnings, $notices),
            'page_title' => $result['document']['title'] ?? $metadata['title'] ?? null,
            'http_status' => $metadata['http_status'] ?? 200,
        ]);
    }

    /**
     * Store an issue in the database.
     */
    protected function storeIssue(ScanPage $page, array $issue): ScanIssue
    {
        return ScanIssue::create([
            'scan_page_id' => $page->id,
            'type' => $issue['type'] ?? 'notice',
            'code' => $issue['code'] ?? null,
            'message' => $issue['message'] ?? null,
            'context' => $issue['context'] ?? null,
            'selector' => $issue['selector'] ?? null,
            'wcag_principle' => $this->extractWcagPrinciple($issue['code'] ?? ''),
            'wcag_guideline' => $this->extractWcagGuideline($issue['code'] ?? ''),
            'wcag_criterion' => $this->extractWcagCriterion($issue['code'] ?? ''),
            'wcag_level' => $this->determineWcagLevel($issue['code'] ?? ''),
            'impact' => $this->determineImpact($issue['type'] ?? 'notice'),
            'recommendation' => $this->generateRecommendation($issue),
            'help_url' => $this->generateHelpUrl($issue['code'] ?? ''),
        ]);
    }

    /**
     * Build the Pa11y CLI command.
     */
    protected function buildPa11yCommand(string $url): string
    {
        $npxPath = config('services.pa11y.npx_path', '/usr/local/bin/npx');

        $configPath = storage_path('app/pa11y-config.json');

        if (! file_exists($configPath)) {
            file_put_contents($configPath, json_encode([
                'chromeLaunchConfig' => [
                    'args' => ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage', '--disable-gpu', '--single-process'],
                ],
            ]));
        }

        return sprintf(
            '%s pa11y %s --standard WCAG2AA --reporter json --include-warnings --include-notices --timeout %d --config %s',
            escapeshellarg($npxPath),
            escapeshellarg($url),
            $this->timeout * 1000, // Convert to milliseconds
            escapeshellarg($configPath)
        );
    }

    /**
     * Parse Pa11y JSON output.
     */
    protected function parsePa11yOutput(string $output): array
    {
        $issues = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON output from Pa11y: '.json_last_error_msg());
        }

        // Handle both single issue and array of issues
        if (! is_array($issues)) {
            $issues = [];
        }

        // If output is just an array of issues, wrap it
        if (isset($issues[0]) && is_array($issues[0]) && isset($issues[0]['type'])) {
            // Already in the right format
        } elseif (isset($issues['issues']) && is_array($issues['issues'])) {
            $issues = $issues['issues'];
        }

        // Count by type
        $counts = [
            'error' => 0,
            'warning' => 0,
            'notice' => 0,
        ];

        foreach ($issues as $issue) {
            $type = strtolower($issue['type'] ?? 'notice');
            if (isset($counts[$type])) {
                $counts[$type]++;
            }
        }

        return [
            'issues' => $issues,
            'counts' => $counts,
            'document' => [
                'title' => $this->extractTitle($issues),
            ],
        ];
    }

    /**
     * Extract title from issues (often contains page info).
     */
    protected function extractTitle(array $issues): ?string
    {
        foreach ($issues as $issue) {
            if (isset($issue['page'])) {
                return $issue['page']['title'] ?? null;
            }
        }

        return null;
    }

    /**
     * Calculate accessibility score (0-100).
     *
     * Uses logarithmic decay so the first few issues have the biggest impact
     * on score, while additional issues have diminishing returns.
     *
     * Pa11y reports most WCAG2AA violations as "error", so weights are
     * calibrated for that reality:
     *   5 errors  → ~88    (good site, minor issues)
     *   15 errors → ~68    (decent site, grade D)
     *   25 errors → ~52    (mediocre, grade F but close to D)
     *   40 errors → ~35    (poor)
     *   60+ errors → ~21   (very poor)
     */
    protected function calculateScore(int $errors, int $warnings, int $notices): float
    {
        $weightedIssues = ($errors * 2) + ($warnings * 1) + ($notices * 0.25);

        if ($weightedIssues <= 0) {
            return 100.0;
        }

        // k = 0.013 produces a gentler curve suited for Pa11y's error-heavy output
        $score = 100 * exp(-0.013 * $weightedIssues);

        return max(0, min(100, round($score, 2)));
    }

    /**
     * Validate a URL before scanning.
     */
    protected function validateUrl(string $url): void
    {
        if (! app()->environment('local')) {
            // Check for localhost
            if (preg_match('/(localhost|127\.0\.0\.1|\.local)/i', $url)) {
                throw new \InvalidArgumentException('Cannot scan localhost URLs');
            }

            // Check for IP addresses
            if (preg_match('/^https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url)) {
                throw new \InvalidArgumentException('Cannot scan IP addresses');
            }
        }

        // Check for valid URL format
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL format');
        }

        // Check protocol
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (! in_array($scheme, ['http', 'https'])) {
            throw new \InvalidArgumentException('URL must use HTTP or HTTPS protocol');
        }
    }

    /**
     * Extract WCAG principle from code (e.g., "WCAG2AA.Principle1" -> "1").
     */
    protected function extractWcagPrinciple(string $code): string
    {
        if (preg_match('/Principle(\d+)/i', $code, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Extract WCAG guideline from code (e.g., "Guideline1_1" -> "1_1").
     */
    protected function extractWcagGuideline(string $code): string
    {
        if (preg_match('/Guideline([\d_]+)/i', $code, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Extract WCAG criterion from code (e.g., "1_1_1" -> "1_1_1").
     */
    protected function extractWcagCriterion(string $code): string
    {
        if (preg_match('/(\d+_\d+_\d+)/i', $code, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Determine WCAG level from code (A, AA, or AAA).
     */
    protected function determineWcagLevel(string $code): string
    {
        if (stripos($code, 'WCAG2AAA') !== false) {
            return 'AAA';
        }
        if (stripos($code, 'WCAG2AA') !== false) {
            return 'AA';
        }
        if (stripos($code, 'WCAG2A') !== false) {
            return 'A';
        }

        return 'A'; // Default to A
    }

    /**
     * Determine impact level from issue type.
     */
    protected function determineImpact(string $type): string
    {
        return match (strtolower($type)) {
            'error' => ScanIssue::IMPACT_CRITICAL,
            'warning' => ScanIssue::IMPACT_MODERATE,
            default => ScanIssue::IMPACT_MINOR,
        };
    }

    /**
     * Generate a human-readable recommendation.
     */
    protected function generateRecommendation(array $issue): string
    {
        $type = ucfirst($issue['type'] ?? 'issue');
        $message = $issue['message'] ?? 'An accessibility issue was found.';

        // Extract actionable advice from message
        $recommendation = $message;

        // Add specific guidance based on code patterns
        if (isset($issue['code'])) {
            $code = $issue['code'];

            if (stripos($code, 'ImgAltIsTooLong') !== false) {
                $recommendation = 'Shorten the alt text to be concise and descriptive. Keep it under 125 characters.';
            } elseif (stripos($code, 'ImgAltIsTooLong') !== false) {
                $recommendation = 'Add descriptive alt text that conveys the purpose or content of the image.';
            } elseif (stripos($code, 'ImgAltMissing') !== false) {
                $recommendation = 'Add an alt attribute to the image. Use an empty alt="" for decorative images.';
            } elseif (stripos($code, 'LinkHasText') !== false) {
                $recommendation = 'Add descriptive text to the link that explains where it goes.';
            } elseif (stripos($code, 'HtmlHasLang') !== false) {
                $recommendation = 'Add a lang attribute to the HTML element indicating the page language.';
            } elseif (stripos($code, 'DocumentHasTitle') !== false) {
                $recommendation = 'Add a descriptive title to the page.';
            } elseif (stripos($code, 'LabelContentName') !== false) {
                $recommendation = 'Ensure form labels contain the text that is visible on the screen.';
            } elseif (stripos($code, 'LabelUnique') !== false) {
                $recommendation = 'Ensure each form control has a unique label.';
            }
        }

        return $recommendation;
    }

    /**
     * Generate help URL for an issue code.
     */
    protected function generateHelpUrl(string $code): string
    {
        // Deque/Axe help URLs
        $baseUrl = 'https://dequeuniversity.com/rules/axe/';

        // Extract the rule name from code
        $ruleName = str_replace(['WCAG2AA.', 'WCAG2A.', 'WCAG2AAA.', 'Principle', 'Guideline', '_'], ['', '', '', '', '', '/'], $code);

        // Clean up and format
        $ruleName = strtolower(preg_replace('/[^a-z0-9]/i', '-', $ruleName));
        $ruleName = trim($ruleName, '-');

        return $baseUrl.$ruleName;
    }

    /**
     * Set the timeout for scans.
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Set the maximum pages to crawl.
     */
    public function setMaxPages(int $max): self
    {
        $this->maxPages = $max;

        return $this;
    }

    /**
     * Get the timeout value.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get the maximum pages value.
     */
    public function getMaxPages(): int
    {
        return $this->maxPages;
    }
}
