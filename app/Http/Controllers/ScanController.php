<?php

namespace App\Http\Controllers;

use App\Models\EmailLead;
use App\Models\GuestScan;
use App\Models\Scan;
use App\Services\UrlValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class ScanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected UrlValidator $urlValidator) {}

    /**
     * Display the scan form or redirect to results.
     */
    public function index()
    {
        return view('scan.index');
    }

    /**
     * Initiate a new scan (guest or authenticated).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $url = $request->input('url');

        // Validate URL
        $validation = $this->urlValidator->validateForScanning($url);
        if ($validation !== true) {
            return response()->json([
                'success' => false,
                'error' => $validation,
            ], 422);
        }

        // Check rate limit for guest scans (1 scan per 24 hours)
        $rateLimitKey = 'guest-scan:'.$request->ip();
        $decaySeconds = 60 * 60 * 24; // 24 hours

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return response()->json([
                'success' => false,
                'error' => 'Rate limit exceeded. You can run 1 free scan per 24 hours.',
            ], 429);
        }

        // Hit the rate limiter
        RateLimiter::hit($rateLimitKey, $decaySeconds);

        try {
            // Create scan record
            $scan = Scan::create([
                'user_id' => null,
                'url' => $url,
                'status' => Scan::STATUS_PENDING,
                'scan_type' => Scan::TYPE_QUICK,
            ]);

            // Record guest scan
            GuestScan::create([
                'ip_address' => $request->ip(),
                'email' => $request->input('email'),
                'scan_id' => $scan->id,
            ]);

            // Dispatch scan job
            dispatch(new \App\Jobs\RunScanJob($scan));

            // Store scan ID in session so it can be attached after registration
            $request->session()->put('guest_scan_id', $scan->id);

            Log::info('Guest scan initiated', [
                'scan_id' => $scan->id,
                'ip' => $request->ip(),
                'url' => $url,
            ]);

            return response()->json([
                'success' => true,
                'scan_id' => $scan->id,
                'redirect_url' => route('scan.pending', $scan),
                'message' => 'Scan started. This may take a few minutes.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to initiate scan', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to start scan. Please try again.',
            ], 500);
        }
    }

    /**
     * Check scan status and get results.
     */
    public function show(Request $request, Scan $scan): JsonResponse
    {
        // Check if user can view this scan
        if (! $scan->user_id) {
            // Guest scan - check IP or email
            $guestScan = $scan->guestScans()->first();
            if (! $guestScan) {
                return response()->json([
                    'success' => false,
                    'error' => 'Scan not found',
                ], 404);
            }
        }

        return response()->json([
            'success' => true,
            'scan' => [
                'id' => $scan->id,
                'status' => $scan->status,
                'url' => $scan->url,
                'score' => $scan->score,
                'grade' => $scan->grade,
                'issues_found' => $scan->issues_found,
                'errors_count' => $scan->errors_count,
                'warnings_count' => $scan->warnings_count,
                'notices_count' => $scan->notices_count,
                'pages_scanned' => $scan->pages_scanned,
                'completed_at' => $scan->completed_at?->toIso8601String(),
                'is_complete' => $scan->isCompleted(),
            ],
        ]);
    }

    /**
     * Display pending scan page.
     */
    public function pending(Scan $scan)
    {
        if ($scan->isCompleted()) {
            return redirect()->route('scan.results', $scan);
        }

        return view('scan.pending', [
            'scan' => $scan,
        ]);
    }

    /**
     * Display guest scan results (teaser view).
     */
    public function results(Scan $scan)
    {
        if (! $scan->isCompleted()) {
            return view('scan.pending', [
                'scan' => $scan,
            ]);
        }

        // Eager load issues for the view
        $scan->load('issues');

        return view('scan.results', [
            'scan' => $scan,
        ]);
    }

    /**
     * Capture email for guest scan results.
     */
    public function captureEmail(Request $request, Scan $scan): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update guest scan with email
        $guestScan = $scan->guestScans()->first();
        if ($guestScan) {
            $guestScan->update(['email' => $request->input('email')]);
        }

        // Create email lead
        EmailLead::create([
            'email' => $request->input('email'),
            'source' => EmailLead::SOURCE_FREE_SCAN,
            'scan_id' => $scan->id,
            'subscribed_at' => now(),
        ]);

        Log::info('Email captured for guest scan', [
            'scan_id' => $scan->id,
            'email' => $request->input('email'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email captured. Full results sent to your inbox.',
        ]);
    }

    /**
     * API endpoint for scan status (polling).
     */
    public function status(Request $request, Scan $scan): JsonResponse
    {
        return $this->show($request, $scan);
    }

    /**
     * Cancel a pending scan.
     */
    public function cancel(Request $request, Scan $scan): JsonResponse
    {
        if (! $scan->isPending()) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot cancel a scan that is not pending',
            ], 400);
        }

        $scan->markAsFailed('Cancelled by user');

        return response()->json([
            'success' => true,
            'message' => 'Scan cancelled',
        ]);
    }
}
