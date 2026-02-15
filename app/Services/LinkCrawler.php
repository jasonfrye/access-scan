<?php

namespace App\Services;

use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkCrawler
{
    /**
     * Maximum number of pages to crawl.
     */
    protected int $maxPages = 100;

    /**
     * Maximum depth for crawling.
     */
    protected int $maxDepth = 3;

    /**
     * Timeout for each request in seconds.
     */
    protected int $timeout = 30;

    /**
     * User agent to use for requests.
     */
    protected string $userAgent = 'AccessReportCard/1.0 (+https://accessreportcard.com)';

    /**
     * The base URL being crawled.
     */
    protected string $baseUrl;

    /**
     * Host being crawled.
     */
    protected string $host;

    /**
     * Scheme (http/https).
     */
    protected string $scheme;

    /**
     * Discovered URLs.
     */
    protected array $discoveredUrls = [];

    /**
     * URLs to process.
     */
    protected array $queue = [];

    /**
     * Processed URLs.
     */
    protected array $processed = [];

    /**
     * Internal URLs discovered.
     */
    protected array $internalUrls = [];

    /**
     * External URLs discovered.
     */
    protected array $externalUrls = [];

    /**
     * URLs that failed to load.
     */
    protected array $failedUrls = [];

    /**
     * URLs disallowed by robots.txt.
     */
    protected array $disallowedUrls = [];

    /**
     * URLs that have been queued (for deduplication).
     */
    protected array $queuedUrls = [];

    /**
     * Crawl a website and discover all internal pages.
     */
    public function crawl(string $url, ?int $maxPages = null, ?int $maxDepth = null): array
    {
        $this->validateUrl($url);

        $this->baseUrl = $url;
        $this->maxPages = $maxPages ?? $this->maxPages;
        $this->maxDepth = $maxDepth ?? $this->maxDepth;

        $parsedUrl = parse_url($url);
        $this->host = $parsedUrl['host'] ?? '';
        $this->scheme = $parsedUrl['scheme'] ?? 'https';

        Log::info('Starting crawl', [
            'url' => $url,
            'max_pages' => $this->maxPages,
            'max_depth' => $this->maxDepth,
        ]);

        // Check robots.txt first
        $this->checkRobotsTxt();

        // Add the starting URL to the queue
        $normalizedUrl = $this->normalizeUrl($url);
        $this->queue = [
            ['url' => $normalizedUrl, 'depth' => 0],
        ];
        $this->queuedUrls = [$normalizedUrl => true];

        // Process the queue
        $this->processQueue();

        Log::info('Crawl completed', [
            'total_discovered' => count($this->internalUrls),
            'failed' => count($this->failedUrls),
            'disallowed' => count($this->disallowedUrls),
        ]);

        return [
            'pages' => array_values($this->internalUrls),
            'total_pages' => count($this->internalUrls),
            'failed' => $this->failedUrls,
            'disallowed' => $this->disallowedUrls,
        ];
    }

    /**
     * Process the crawl queue.
     */
    protected function processQueue(): void
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => $this->timeout,
            'headers' => [
                'User-Agent' => $this->userAgent,
            ],
            'allow_redirects' => [
                'max' => 10,
                'strict' => false,
                'track_redirects' => true,
            ],
        ]);

        while (! empty($this->queue) && count($this->internalUrls) < $this->maxPages) {
            $item = array_shift($this->queue);
            $url = $this->normalizeUrl($item['url']);
            $depth = $item['depth'];

            // Skip if already processed
            if (isset($this->processed[$url])) {
                continue;
            }

            // Skip if we've reached max pages
            if (count($this->internalUrls) >= $this->maxPages) {
                break;
            }

            // Check if URL is disallowed
            if ($this->isDisallowed($url)) {
                $this->disallowedUrls[] = $url;
                $this->processed[$url] = true;

                continue;
            }

            Log::debug('Processing URL', ['url' => $url, 'depth' => $depth]);

            try {
                $response = $client->get($url, [
                    'on_stats' => function (TransferStats $stats) use ($url) {
                        $response = $stats->getResponse();
                        if ($response !== null && $response->getStatusCode() >= 400) {
                            $this->failedUrls[$url] = [
                                'url' => $url,
                                'status' => $response->getStatusCode(),
                            ];
                        }
                    },
                ]);

                $status = $response->getStatusCode();

                if ($status >= 400) {
                    $this->failedUrls[$url] = [
                        'url' => $url,
                        'status' => $status,
                    ];
                    $this->processed[$url] = true;

                    continue;
                }

                // Check content type - only process HTML
                $contentType = $response->getHeader('Content-Type')[0] ?? '';
                if (! $this->isHtmlContent($contentType)) {
                    $this->processed[$url] = true;

                    continue;
                }

                $html = (string) $response->getBody();

                // Add to internal URLs if we haven't seen it and haven't exceeded limit
                $normalizedUrl = $this->normalizeUrl($url);
                if (! isset($this->internalUrls[$normalizedUrl]) && count($this->internalUrls) < $this->maxPages) {
                    $this->internalUrls[$normalizedUrl] = [
                        'url' => $normalizedUrl,
                        'status' => $status,
                        'title' => $this->extractTitle($html),
                    ];
                }

                // Extract links if we haven't reached max depth or page limit
                if ($depth < $this->maxDepth && count($this->internalUrls) < $this->maxPages) {
                    $links = $this->extractLinks($html, $url);

                    foreach ($links as $link) {
                        if (count($this->queuedUrls) >= $this->maxPages) {
                            break;
                        }

                        $normalizedLink = $this->normalizeUrl($link);
                        if (! isset($this->processed[$normalizedLink]) && ! isset($this->queuedUrls[$normalizedLink])) {
                            if ($this->isInternalLink($normalizedLink)) {
                                $this->queue[] = [
                                    'url' => $normalizedLink,
                                    'depth' => $depth + 1,
                                ];
                                $this->queuedUrls[$normalizedLink] = true;
                            } elseif (! isset($this->externalUrls[$normalizedLink])) {
                                $this->externalUrls[$normalizedLink] = $normalizedLink;
                            }
                        }
                    }
                }

                $this->processed[$url] = true;

            } catch (\Exception $e) {
                Log::warning('Failed to crawl URL', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
                $this->failedUrls[$url] = [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ];
                $this->processed[$url] = true;
            }
        }
    }

    /**
     * Check robots.txt and store disallowed paths.
     */
    protected function checkRobotsTxt(): void
    {
        $robotsUrl = $this->scheme.'://'.$this->host.'/robots.txt';

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['User-Agent' => $this->userAgent])
                ->get($robotsUrl);

            if ($response->successful()) {
                $content = $response->body();
                $this->parseRobotsTxt($content);
                Log::info('robots.txt parsed', [
                    'host' => $this->host,
                    'disallowed_count' => count($this->disallowedUrls),
                ]);
            }
        } catch (\Exception $e) {
            Log::info('Could not fetch robots.txt', [
                'host' => $this->host,
                'error' => $e->getMessage(),
            ]);
            // Continue without robots.txt - crawl anyway
        }
    }

    /**
     * Parse robots.txt content.
     */
    protected function parseRobotsTxt(string $content): void
    {
        $lines = explode("\n", $content);
        $userAgent = '*';
        $isRelevantBlock = false;
        $currentDisallowed = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            $parts = preg_split('/\s+/', $line, 2);
            $directive = strtolower($parts[0] ?? '');
            $value = $parts[1] ?? '';

            switch ($directive) {
                case 'user-agent':
                    if ($value === '*' || stripos($value, 'accessreportcard') !== false) {
                        $isRelevantBlock = true;
                        $currentDisallowed = [];
                    } else {
                        $isRelevantBlock = false;
                    }
                    break;

                case 'disallow':
                    if ($isRelevantBlock && ! empty($value)) {
                        $path = $value;
                        if (! str_starts_with($path, '/')) {
                            $path = '/'.$path;
                        }
                        $currentDisallowed[] = $path;
                    }
                    break;

                case 'allow':
                    if ($isRelevantBlock && ! empty($value)) {
                        $path = $value;
                        if (! str_starts_with($path, '/')) {
                            $path = '/'.$path;
                        }
                        // Remove from disallowed if explicitly allowed
                        $currentDisallowed = array_filter($currentDisallowed, fn ($d) => $d !== $path);
                    }
                    break;

                case 'crawl-delay':
                    // Could implement rate limiting based on this
                    break;
            }
        }

        $this->disallowedUrls = array_unique(array_merge($this->disallowedUrls, $currentDisallowed));
    }

    /**
     * Check if a URL is disallowed by robots.txt.
     */
    protected function isDisallowed(string $url): bool
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        $path = $path ?: '/';

        foreach ($this->disallowedUrls as $disallowed) {
            // Check if the URL starts with a disallowed path
            if (str_starts_with($path, $disallowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract all links from HTML content.
     */
    protected function extractLinks(string $html, string $baseUrl): array
    {
        if (empty($html)) {
            return [];
        }

        $links = [];

        // Use DOMDocument for parsing
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument;
        @$doc->loadHTML($html);
        libxml_clear_errors();

        // Find all anchor tags
        $anchors = $doc->getElementsByTagName('a');

        foreach ($anchors as $anchor) {
            $href = $anchor->getAttribute('href');

            if (empty($href) || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) {
                continue;
            }

            // Resolve relative URLs
            try {
                $resolvedUrl = $this->resolveUrl($href, $baseUrl);
                if ($resolvedUrl !== null) {
                    $links[] = $resolvedUrl;
                }
            } catch (\Exception $e) {
                // Skip invalid URLs
                continue;
            }
        }

        // Also check for canonical URLs and other link relations
        $canonical = $doc->getElementsByTagName('link');
        foreach ($canonical as $link) {
            $rel = strtolower($link->getAttribute('rel'));
            if ($rel === 'canonical') {
                $href = $link->getAttribute('href');
                if (! empty($href)) {
                    $resolvedUrl = $this->resolveUrl($href, $baseUrl);
                    if ($resolvedUrl !== null) {
                        $links[] = $resolvedUrl;
                    }
                }
            }
        }

        return array_unique($links);
    }

    /**
     * Extract page title from HTML.
     */
    protected function extractTitle(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument;
        @$doc->loadHTML($html);
        libxml_clear_errors();

        $titles = $doc->getElementsByTagName('title');

        if ($titles->length > 0) {
            return $titles->item(0)->textContent;
        }

        return null;
    }

    /**
     * Check if a URL is internal (same host).
     */
    protected function isInternalLink(string $url): bool
    {
        $parsedUrl = parse_url($url);

        // No host means it's relative - treat as internal
        if (! isset($parsedUrl['host'])) {
            return true;
        }

        // Check if same host
        return strtolower($parsedUrl['host']) === strtolower($this->host);
    }

    /**
     * Resolve a potentially relative URL against a base URL.
     */
    protected function resolveUrl(string $href, string $baseUrl): ?string
    {
        // Already absolute
        if (preg_match('#^https?://#i', $href)) {
            return strtok($href, '#');
        }

        $parsed = parse_url($baseUrl);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? $this->host;
        $basePath = $parsed['path'] ?? '/';

        if (str_starts_with($href, '//')) {
            return strtok($scheme.':'.$href, '#');
        }

        if (str_starts_with($href, '/')) {
            return strtok($scheme.'://'.$host.$href, '#');
        }

        // Relative path
        $baseDir = rtrim(substr($basePath, 0, (int) strrpos($basePath, '/')), '/');

        return strtok($scheme.'://'.$host.$baseDir.'/'.$href, '#');
    }

    /**
     * Normalize a URL to prevent duplicates (trailing slashes, fragments, etc.).
     */
    protected function normalizeUrl(string $url): string
    {
        // Strip fragments
        $url = strtok($url, '#');

        // Strip trailing slash unless it's just the root path
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        $normalized = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? '');
        $normalized .= $path;

        if (! empty($parsed['query'])) {
            $normalized .= '?'.$parsed['query'];
        }

        return $normalized;
    }

    /**
     * Check if content type is HTML.
     */
    protected function isHtmlContent(string $contentType): bool
    {
        return stripos($contentType, 'text/html') !== false;
    }

    /**
     * Validate URL before crawling.
     */
    protected function validateUrl(string $url): void
    {
        // Check for localhost
        if (preg_match('/(localhost|127\.0\.0\.1|\.local)/i', $url)) {
            throw new \InvalidArgumentException('Cannot crawl localhost URLs');
        }

        // Check for IP addresses
        if (preg_match('/^https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url)) {
            throw new \InvalidArgumentException('Cannot crawl IP addresses');
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
     * Set maximum pages to crawl.
     */
    public function setMaxPages(int $max): self
    {
        $this->maxPages = $max;

        return $this;
    }

    /**
     * Set maximum crawl depth.
     */
    public function setMaxDepth(int $depth): self
    {
        $this->maxDepth = $depth;

        return $this;
    }

    /**
     * Set timeout for requests.
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Set user agent.
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get discovered internal URLs.
     */
    public function getInternalUrls(): array
    {
        return array_keys($this->internalUrls);
    }

    /**
     * Get discovered external URLs.
     */
    public function getExternalUrls(): array
    {
        return array_keys($this->externalUrls);
    }

    /**
     * Get failed URLs.
     */
    public function getFailedUrls(): array
    {
        return $this->failedUrls;
    }

    /**
     * Get disallowed URLs.
     */
    public function getDisallowedUrls(): array
    {
        return $this->disallowedUrls;
    }
}
