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

        $scans = $user->scans()
            ->with('pages')
            ->orderBy('created_at', 'desc')
            ->paginate(10, page: $request->get('page', 1));

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

        $trendData = $this->getTrendData($user);

        return view('dashboard', compact('scans', 'stats', 'recentScans', 'scheduledScans', 'trendData'));
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
        ]);

        $user = Auth::user();

        // Check if user has scans remaining
        if (! $user->hasScansRemaining()) {
            return redirect()->route('dashboard')
                ->with('error', 'You have reached your scan limit. Please upgrade your plan.');
        }

        $url = $request->input('url');

        try {
            // Create scan record
            $scan = Scan::create([
                'user_id' => $user->id,
                'url' => $url,
                'status' => Scan::STATUS_PENDING,
                'scan_type' => Scan::TYPE_FULL,
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

            return redirect()->route('scan.pending', $scan)
                ->with('success', 'Scan started! You\'ll be notified when it\'s complete.');
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

        $scan->load(['pages']);

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
