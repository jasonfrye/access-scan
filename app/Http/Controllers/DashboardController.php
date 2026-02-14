<?php

namespace App\Http\Controllers;

use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Models\ScanPage;
use App\Models\ScanSchedule;
use App\Services\IssueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $groupedScans = $user->scans()
            ->with('pages')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn ($scan) => $scan->domain)
            ->sortByDesc(fn ($scans) => $scans->first()->created_at);

        $stats = [
            'total_scans' => $user->scans()->count(),
            'completed_scans' => $user->scans()->completed()->count(),
            'average_score' => $user->scans()->completed()->avg('score'),
            'scan_limit' => $user->scan_limit,
            'scans_remaining' => max(0, $user->scan_limit - $user->scan_count),
        ];

        $recentScans = $user->scans()
            ->with('pages')
            ->completed()
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        $scheduledScans = $user->scheduledScans()
            ->active()
            ->orderBy('next_run_at', 'asc')
            ->take(5)
            ->get();

        $allSchedules = $user->scheduledScans()->get();
        $schedulesByDomain = $allSchedules->keyBy(fn ($s) => $s->domain);

        $trendData = $this->getTrendData($user);

        return view('dashboard', compact('groupedScans', 'stats', 'recentScans', 'scheduledScans', 'schedulesByDomain', 'trendData'));
    }

    /**
     * Store a new scheduled scan.
     */
    public function storeScheduledScan(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048',
            'frequency' => 'required|in:daily,weekly,monthly',
        ]);

        $user = Auth::user();

        $schedule = ScanSchedule::create([
            'user_id' => $user->id,
            'url' => $request->input('url'),
            'frequency' => $request->input('frequency'),
            'next_run_at' => now()->addHour(), // First run in 1 hour
            'is_active' => true,
            'notify_on_regression' => true,
        ]);

        Log::info('Scheduled scan created', [
            'schedule_id' => $schedule->id,
            'user_id' => $user->id,
            'url' => $schedule->url,
            'frequency' => $schedule->frequency,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Scheduled scan created! First run in 1 hour.');
    }

    /**
     * Toggle a scheduled scan active status.
     */
    public function toggleScheduledScan(Request $request, ScanSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $schedule->update(['is_active' => ! $schedule->is_active]);

        $status = $schedule->is_active ? 'enabled' : 'paused';

        return redirect()->route('dashboard')
            ->with('success', "Scheduled scan {$status}.");
    }

    /**
     * Delete a scheduled scan.
     */
    public function destroyScheduledScan(Request $request, ScanSchedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Scheduled scan deleted.');
    }

    /**
     * Store a new scan from the dashboard.
     */
    public function storeScan(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048',
            'scan_type' => 'sometimes|in:full,single',
        ]);

        $user = Auth::user();

        // Check if user has scans remaining
        if (! $user->hasScansRemaining()) {
            return redirect()->route('dashboard')
                ->with('error', 'You have reached your scan limit. Please upgrade your plan.');
        }

        $url = $request->input('url');
        $scanType = $request->input('scan_type', 'full') === 'single'
            ? Scan::TYPE_QUICK
            : Scan::TYPE_FULL;

        try {
            // Create scan record
            $scan = Scan::create([
                'user_id' => $user->id,
                'url' => $url,
                'status' => Scan::STATUS_PENDING,
                'scan_type' => $scanType,
            ]);

            // Increment user's scan count
            $user->incrementScanCount();

            // Dispatch scan job
            dispatch(new RunScanJob($scan));

            Log::info('Dashboard scan initiated', [
                'scan_id' => $scan->id,
                'user_id' => $user->id,
                'url' => $url,
            ]);

            return redirect()->route('dashboard.scan', $scan)
                ->with('success', 'Scan started! Results will appear shortly.');
        } catch (\Exception $e) {
            Log::error('Failed to initiate dashboard scan', [
                'user_id' => $user->id,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to start scan. Please try again.');
        }
    }

    /**
     * Display a specific scan result (pages overview).
     */
    public function showScan(Scan $scan)
    {
        $this->authorize('view', $scan);

        $scan->load(['pages.issues']);

        $pages = $scan->pages->sortBy('score');

        return view('dashboard.scan', compact('scan', 'pages'));
    }

    /**
     * Display a specific page's categorized issues.
     */
    public function showScanPage(Scan $scan, ScanPage $scanPage)
    {
        $this->authorize('view', $scan);

        abort_unless($scanPage->scan_id === $scan->id, 404);

        $scanPage->load('issues');

        $categories = IssueCategory::groupByCategory($scanPage->issues);

        return view('dashboard.scan-page', compact('scan', 'scanPage', 'categories'));
    }

    /**
     * Retry a failed or stuck scan.
     */
    public function retryScan(Request $request, Scan $scan)
    {
        $this->authorize('view', $scan);

        if (! in_array($scan->status, [Scan::STATUS_FAILED, Scan::STATUS_PENDING])) {
            return redirect()->route('dashboard.scan', $scan)
                ->with('error', 'This scan cannot be retried.');
        }

        // Reset scan state
        $scan->update([
            'status' => Scan::STATUS_PENDING,
            'error_message' => null,
            'score' => null,
            'grade' => null,
            'pages_scanned' => 0,
            'issues_found' => 0,
            'errors_count' => 0,
            'warnings_count' => 0,
            'notices_count' => 0,
            'started_at' => null,
            'completed_at' => null,
        ]);

        // Delete old pages/issues
        $scan->pages()->each(function ($page) {
            $page->issues()->delete();
            $page->delete();
        });

        dispatch(new RunScanJob($scan));

        Log::info('Scan retry initiated', [
            'scan_id' => $scan->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard.scan', $scan)
            ->with('success', 'Scan restarted! Results will appear shortly.');
    }

    /**
     * Get scan trend data for the chart.
     */
    protected function getTrendData($user): array
    {
        $scans = $user->scans()
            ->completed()
            ->orderBy('completed_at', 'asc')
            ->take(30)
            ->get();

        $labels = $scans->map(fn ($s) => $s->completed_at->format('M d'))->toArray();
        $scores = $scans->pluck('score')->toArray();

        return [
            'labels' => $labels,
            'scores' => $scores,
        ];
    }
}
