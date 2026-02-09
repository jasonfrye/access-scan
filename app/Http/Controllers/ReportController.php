<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Download PDF report.
     */
    public function pdf(Scan $scan)
    {
        $this->authorize('view', $scan);

        try {
            $filename = $this->reportService->generatePdf($scan);
            $path = Storage::disk('public')->path($filename);

            Log::info('PDF report generated', [
                'scan_id' => $scan->id,
                'filename' => $filename,
            ]);

            return response()->download($path, "accessibility-report-{$scan->domain}.pdf", [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF report', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to generate PDF report.');
        }
    }

    /**
     * Download CSV export.
     */
    public function csv(Scan $scan)
    {
        $this->authorize('view', $scan);

        try {
            $filename = $this->reportService->generateCsv($scan);
            $path = Storage::disk('public')->path($filename);

            Log::info('CSV report generated', [
                'scan_id' => $scan->id,
                'filename' => $filename,
            ]);

            return response()->download($path, "accessibility-report-{$scan->domain}.csv", [
                'Content-Type' => 'text/csv',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate CSV report', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to generate CSV report.');
        }
    }

    /**
     * Download JSON export.
     */
    public function json(Scan $scan)
    {
        $this->authorize('view', $scan);

        try {
            $filename = $this->reportService->generateJson($scan);
            $path = Storage::disk('public')->path($filename);

            Log::info('JSON report generated', [
                'scan_id' => $scan->id,
                'filename' => $filename,
            ]);

            return response()->download($path, "accessibility-report-{$scan->domain}.json", [
                'Content-Type' => 'application/json',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate JSON report', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to generate JSON report.');
        }
    }
}
