<?php

namespace App\Services;

use App\Models\Scan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    /**
     * Generate a PDF report for a scan.
     */
    public function generatePdf(Scan $scan): string
    {
        $pdf = Pdf::loadView('reports.pdf', [
            'scan' => $scan->load(['pages.issues', 'user']),
            'stats' => $this->calculateStats($scan),
        ]);

        $filename = "reports/scan-{$scan->id}-" . time() . '.pdf';
        
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate CSV export of scan issues.
     */
    public function generateCsv(Scan $scan): string
    {
        $issues = $scan->pages->flatMap->issues;

        $headers = ['Type', 'WCAG Reference', 'Message', 'Page', 'Context', 'Recommendation'];

        $rows = $issues->map(function ($issue) {
            return [
                $issue->type,
                $issue->wcag_reference,
                $issue->message,
                $issue->page->url,
                str_replace(["\r", "\n"], ' ', $issue->context ?? ''),
                $issue->recommendation ?? '',
            ];
        });

        $csv = implode("\n", [implode(',', $headers), ...$rows->map(fn($r) => '"' . implode('","', $r) . '"')]);

        $filename = "reports/scan-{$scan->id}-" . time() . '.csv';
        
        Storage::disk('public')->put($filename, $csv);

        return $filename;
    }

    /**
     * Generate JSON export of scan results.
     */
    public function generateJson(Scan $scan): string
    {
        $data = [
            'scan' => [
                'id' => $scan->id,
                'url' => $scan->url,
                'domain' => $scan->domain,
                'score' => $scan->score,
                'grade' => $scan->grade,
                'status' => $scan->status,
                'started_at' => $scan->started_at?->toIso8601String(),
                'completed_at' => $scan->completed_at?->toIso8601String(),
            ],
            'summary' => $this->calculateStats($scan),
            'pages' => $scan->pages->map(function ($page) {
                return [
                    'url' => $page->url,
                    'score' => $page->score,
                    'issues_count' => $page->issues_count,
                    'issues' => $page->issues->map(function ($issue) {
                        return [
                            'type' => $issue->type,
                            'wcag_reference' => $issue->wcag_reference,
                            'wcag_level' => $issue->wcag_level,
                            'message' => $issue->message,
                            'context' => $issue->context,
                            'recommendation' => $issue->recommendation,
                            'help_url' => $issue->help_url,
                        ];
                    }),
                ];
            }),
        ];

        $filename = "reports/scan-{$scan->id}-" . time() . '.json';
        
        Storage::disk('public')->put($filename, json_encode($data, JSON_PRETTY_PRINT));

        return $filename;
    }

    /**
     * Calculate summary statistics for a scan.
     */
    protected function calculateStats(Scan $scan): array
    {
        $totalPages = $scan->pages->count();
        $totalIssues = $scan->pages->sum('issues_count');
        
        $errors = $scan->pages->flatMap->issues->where('type', 'error')->count();
        $warnings = $scan->pages->flatMap->issues->where('type', 'warning')->count();
        $notices = $scan->pages->flatMap->issues->where('type', 'notice')->count();

        $criticalIssues = $scan->pages->flatMap->issues
            ->whereIn('wcag_level', ['A', 'AA'])
            ->where('type', 'error')
            ->count();

        return [
            'total_pages' => $totalPages,
            'total_issues' => $totalIssues,
            'errors' => $errors,
            'warnings' => $warnings,
            'notices' => $notices,
            'critical_issues' => $criticalIssues,
        ];
    }

    /**
     * Get the file path for a report download.
     */
    public function getReportPath(Scan $scan, string $format): ?string
    {
        $method = match ($format) {
            'pdf' => 'generatePdf',
            'csv' => 'generateCsv',
            'json' => 'generateJson',
            default => null,
        };

        if (!$method) {
            return null;
        }

        return $this->$method($scan);
    }
}
