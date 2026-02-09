<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Jobs\RunScanJob;
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

        $trendData = $this->getTrendData($user);

        return view('dashboard', compact('scans', 'stats', 'recentScans', 'trendData'));
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
        if (!$user->hasScansRemaining()) {
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
                'scan_type' => Scan::TYPE_QUICK,
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
     * Display a specific scan result.
     */
    public function showScan(Scan $scan)
    {
        $this->authorize('view', $scan);

        $scan->load(['pages.issues']);

        return view('dashboard.scan', compact('scan'));
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

        $labels = $scans->map(fn($s) => $s->completed_at->format('M d'))->toArray();
        $scores = $scans->pluck('score')->toArray();

        return [
            'labels' => $labels,
            'scores' => $scores,
        ];
    }
}
