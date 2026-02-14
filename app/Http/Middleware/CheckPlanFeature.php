<?php

namespace App\Http\Middleware;

use App\Models\Scan;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $hasAccess = match ($feature) {
            'scheduled_scans' => $user->isPaid(),
            'pdf_export' => $user->isPaid(),
            'csv_export' => $user->isPaid(),
            'json_export' => $user->isPaid(),
            'api_access' => $user->plan === 'agency',
            'white_label' => $user->plan === 'agency',
            'multi_page_scan' => $user->isPaid(),
            default => false,
        };

        if (! $hasAccess) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Upgrade required',
                    'message' => 'This feature requires a paid plan.',
                    'upgrade_url' => route('billing.pricing'),
                ], 403);
            }

            return redirect()->route('billing.pricing')
                ->with('error', 'This feature requires a paid plan. Upgrade to unlock!');
        }

        return $next($request);
    }

    /**
     * Check if user can start a new scan (respects scan limits).
     */
    public static function canStartScan(User $user): array
    {
        $currentMonthScans = Scan::where('user_id', $user->id)
            ->where('status', Scan::STATUS_COMPLETED)
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();

        $limit = $user->getScanLimit();
        $remaining = max(0, $limit - $currentMonthScans);

        if ($currentMonthScans >= $limit) {
            return [
                'allowed' => false,
                'reason' => 'scan_limit_exceeded',
                'current' => $currentMonthScans,
                'limit' => $limit,
                'remaining' => 0,
                'reset_date' => now()->endOfMonth()->format('F j, Y'),
                'message' => "You've reached your monthly limit of {$limit} scans. Your limit resets on ".now()->endOfMonth()->format('F j, Y').'.',
                'upgrade_url' => route('billing.pricing'),
                'upgrade_message' => 'Upgrade to Pro for more scans!',
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
            'current' => $currentMonthScans,
            'limit' => $limit,
            'remaining' => $remaining,
            'reset_date' => now()->endOfMonth()->format('F j, Y'),
            'message' => "You have {$remaining} scan(s) remaining this month.",
            'upgrade_url' => null,
            'upgrade_message' => null,
        ];
    }

    /**
     * Check if user can scan a URL with the given page count.
     */
    public static function canScanPages(User $user, int $pageCount): array
    {
        $maxPages = $user->getMaxPagesPerScan() ?? 5;

        if ($pageCount > $maxPages) {
            return [
                'allowed' => false,
                'reason' => 'page_limit_exceeded',
                'requested' => $pageCount,
                'limit' => $maxPages,
                'message' => "Your plan limits scans to {$maxPages} pages. You requested {$pageCount} pages.",
                'upgrade_url' => route('billing.pricing'),
                'upgrade_message' => 'Upgrade to Pro for 100 pages per scan!',
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
            'requested' => $pageCount,
            'limit' => $maxPages,
            'message' => null,
            'upgrade_url' => null,
            'upgrade_message' => null,
        ];
    }

    /**
     * Check if user can create a scheduled scan.
     */
    public static function canCreateScheduledScan(User $user): array
    {
        if (! $user->isPaid()) {
            return [
                'allowed' => false,
                'reason' => 'paid_plan_required',
                'message' => 'Scheduled scans are available on paid plans.',
                'upgrade_url' => route('billing.pricing'),
                'upgrade_message' => 'Upgrade to Pro for scheduled scans!',
            ];
        }

        $activeSchedules = $user->scheduledScans()->active()->count();
        $maxSchedules = $user->getScheduledScanLimit();

        if ($activeSchedules >= $maxSchedules) {
            return [
                'allowed' => false,
                'reason' => 'schedule_limit_exceeded',
                'current' => $activeSchedules,
                'limit' => $maxSchedules,
                'message' => "You've reached your limit of {$maxSchedules} scheduled scans.",
                'upgrade_url' => route('billing.pricing'),
                'upgrade_message' => 'Need more scheduled scans? Contact support.',
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
            'current' => $activeSchedules,
            'limit' => $maxSchedules,
            'message' => null,
            'upgrade_url' => null,
            'upgrade_message' => null,
        ];
    }

    /**
     * Check if user can export to a specific format.
     */
    public static function canExport(User $user, string $format): array
    {
        if (! $user->isPaid()) {
            $formats = ['pdf', 'csv', 'json'];
            $formatName = strtoupper($format);

            return [
                'allowed' => false,
                'reason' => 'paid_plan_required',
                'message' => "{$formatName} exports are available on paid plans.",
                'upgrade_url' => route('billing.pricing'),
                'upgrade_message' => 'Upgrade to Pro for export functionality!',
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
            'message' => null,
            'upgrade_url' => null,
            'upgrade_message' => null,
        ];
    }

    /**
     * Get the appropriate response for a failed feature check.
     */
    public static function deniedResponse(string $feature, array $checkResult, Request $request): Response
    {
        $upgradeUrl = $checkResult['upgrade_url'] ?? route('billing.pricing');
        $upgradeMessage = $checkResult['upgrade_message'] ?? 'Upgrade to unlock this feature!';
        $message = $checkResult['message'] ?? 'This feature requires a paid plan.';

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Feature not available',
                'reason' => $checkResult['reason'] ?? 'paid_plan_required',
                'message' => $message,
                'upgrade' => [
                    'url' => $upgradeUrl,
                    'message' => $upgradeMessage,
                ],
            ], 403);
        }

        return redirect($upgradeUrl)
            ->with('error', $message)
            ->with('upgrade_message', $upgradeMessage);
    }

    /**
     * Get upgrade URL for a feature.
     */
    public static function getUpgradeUrl(string $feature): string
    {
        return route('billing.pricing');
    }
}
