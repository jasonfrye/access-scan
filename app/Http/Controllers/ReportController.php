<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * Preview a sample PDF with the user's branding.
     */
    public function preview(Request $request)
    {
        $user = $request->user();

        if ($user->plan !== 'agency') {
            abort(403);
        }

        $companyLogoBase64 = null;
        if ($user->company_logo_path) {
            $logoPath = Storage::disk('public')->path($user->company_logo_path);
            if (file_exists($logoPath)) {
                $mime = mime_content_type($logoPath);
                $companyLogoBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($logoPath));
            }
        }

        $scan = new Scan([
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'score' => 82,
            'grade' => 'B',
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $scan->setRelation('pages', collect());
        $scan->setRelation('user', $user);

        $stats = [
            'total_pages' => 5,
            'total_issues' => 23,
            'errors' => 8,
            'warnings' => 10,
            'notices' => 5,
            'critical_issues' => 3,
        ];

        $pdf = Pdf::loadView('reports.pdf', [
            'scan' => $scan,
            'stats' => $stats,
            'whiteLabel' => true,
            'companyName' => $user->company_name,
            'companyLogoBase64' => $companyLogoBase64,
        ]);

        return $pdf->stream('branding-preview.pdf');
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
