<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use App\Services\ScannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScanApiController extends Controller
{
    /**
     * List all scans for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->isPaid()) {
            return response()->json([
                'success' => false,
                'error' => 'API access requires a paid plan',
                'upgrade_url' => route('billing.pricing'),
            ], 403);
        }

        $scans = $user->scans()
            ->latest('completed_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $scans->items(),
            'meta' => [
                'current_page' => $scans->currentPage(),
                'last_page' => $scans->lastPage(),
                'per_page' => $scans->perPage(),
                'total' => $scans->total(),
            ],
            'links' => [
                'first' => $scans->url(1),
                'last' => $scans->url($scans->lastPage()),
                'prev' => $scans->previousPageUrl(),
                'next' => $scans->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Create a new scan via API.
     */
    public function store(Request $request, ScannerService $scanner): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
            'pages' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if ($user && ! $user->isPaid()) {
            return response()->json([
                'success' => false,
                'error' => 'API access requires a paid plan',
                'upgrade_url' => route('billing.pricing'),
            ], 403);
        }

        $url = $request->input('url');
        $maxPages = $request->input('pages', 5);

        // Validate URL
        $validation = $this->validateUrl($url);
        if ($validation !== true) {
            return response()->json([
                'success' => false,
                'error' => $validation,
            ], 422);
        }

        // Check scan limits
        $currentMonthScans = $user->scans()
            ->where('status', Scan::STATUS_COMPLETED)
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();

        $limit = $user->scan_limit ?? 5;
        if ($currentMonthScans >= $limit) {
            return response()->json([
                'success' => false,
                'error' => 'Monthly scan limit reached',
                'limit' => $limit,
                'current' => $currentMonthScans,
                'reset_date' => now()->endOfMonth()->format('Y-m-d'),
                'upgrade_url' => route('billing.pricing'),
            ], 403);
        }

        try {
            $scan = Scan::create([
                'user_id' => $user->id,
                'url' => $url,
                'status' => Scan::STATUS_PENDING,
                'scan_type' => Scan::TYPE_API,
            ]);

            dispatch(new \App\Jobs\RunScanJob($scan));

            Log::info('API scan initiated', [
                'scan_id' => $scan->id,
                'user_id' => $user->id,
                'url' => $url,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $scan->id,
                    'url' => $scan->url,
                    'status' => $scan->status,
                    'created_at' => $scan->created_at->toIso8601String(),
                    'status_url' => route('api.v1.scans.status', $scan),
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('API scan failed', [
                'user_id' => $user->id,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to initiate scan',
            ], 500);
        }
    }

    /**
     * Get a specific scan's results.
     */
    public function show(Request $request, Scan $scan): JsonResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($scan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Scan not found',
            ], 404);
        }

        if (! $scan->isCompleted()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $scan->id,
                    'url' => $scan->url,
                    'status' => $scan->status,
                    'status_url' => url("/api/v1/scans/{$scan->id}/status"),
                ],
            ], 200);
        }

        // Return full results for completed scans
        $scan->load('pages.issues');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $scan->id,
                'url' => $scan->url,
                'status' => $scan->status,
                'score' => $scan->score,
                'grade' => $scan->grade,
                'issues_found' => $scan->issues_found,
                'errors_count' => $scan->errors_count,
                'warnings_count' => $scan->warnings_count,
                'notices_count' => $scan->notices_count,
                'pages_scanned' => $scan->pages_scanned,
                'completed_at' => $scan->completed_at?->toIso8601String(),
                'pages' => $scan->pages->map(fn ($page) => [
                    'id' => $page->id,
                    'url' => $page->url,
                    'path' => $page->path,
                    'score' => $page->score,
                    'errors_count' => $page->errors_count,
                    'warnings_count' => $page->warnings_count,
                    'notices_count' => $page->notices_count,
                    'issues' => $page->issues->map(fn ($issue) => [
                        'id' => $issue->id,
                        'type' => $issue->type,
                        'code' => $issue->code,
                        'message' => $issue->message,
                        'context' => $issue->context,
                        'selector' => $issue->selector,
                        'wcag_reference' => $issue->wcag_reference,
                        'wcag_level' => $issue->wcag_level,
                        'impact' => $issue->impact,
                        'recommendation' => $issue->recommendation,
                        'help_url' => $issue->help_url,
                    ]),
                ]),
            ],
        ]);
    }

    /**
     * Get scan status (for polling).
     */
    public function status(Request $request, Scan $scan): JsonResponse
    {
        $user = $request->user();

        // Verify ownership
        if ($scan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => 'Scan not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $scan->id,
                'status' => $scan->status,
                'progress' => $scan->status === Scan::STATUS_RUNNING ? 50 : 100,
            ],
        ]);
    }

    /**
     * Validate URL for scanning.
     */
    protected function validateUrl(string $url): string|true
    {
        if (preg_match('/(localhost|127\.0\.0\.1|\.local|\.test)/i', $url)) {
            return 'Cannot scan localhost or local URLs';
        }

        if (preg_match('/^https?:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url)) {
            return 'Cannot scan IP addresses';
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (! in_array($scheme, ['http', 'https'])) {
            return 'URL must use HTTP or HTTPS protocol';
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (! $host || ! filter_var('http://'.$host, FILTER_VALIDATE_URL)) {
            return 'Invalid domain name';
        }

        return true;
    }
}
