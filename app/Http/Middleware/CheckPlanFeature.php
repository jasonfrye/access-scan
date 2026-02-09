<?php

namespace App\Http\Middleware;

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

        if (!$user) {
            return redirect()->route('login');
        }

        $hasAccess = match ($feature) {
            'scheduled_scans' => $user->isPaid(),
            'pdf_export' => $user->isPaid(),
            'csv_export' => $user->isPaid(),
            'json_export' => $user->isPaid(),
            'api_access' => $user->plan === 'lifetime',
            'white_label' => $user->plan === 'lifetime',
            'multi_page_scan' => $user->isPaid(),
            default => false,
        };

        if (!$hasAccess) {
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
     * Check if user can perform an action based on plan limits.
     */
    public static function checkLimit(User $user, string $limitType): array
    {
        return match ($limitType) {
            'scan_count' => [
                'allowed' => $user->hasScansRemaining(),
                'current' => $user->scan_count,
                'limit' => $user->scan_limit,
                'upgrade_message' => 'You\'ve reached your monthly scan limit. Upgrade for more scans.',
            ],
            'page_count' => [
                'allowed' => true, // Check at scan time
                'limit' => $user->getMaxPagesPerScan(),
                'upgrade_message' => 'Your plan limits scans to :limit pages. Upgrade for more.',
            ],
            'scheduled_scans' => [
                'allowed' => $user->isPaid(),
                'limit' => $user->isPaid() ? 10 : 0,
                'upgrade_message' => 'Scheduled scans are available on paid plans.',
            ],
            default => [
                'allowed' => true,
                'limit' => null,
                'upgrade_message' => null,
            ],
        };
    }

    /**
     * Get upgrade URL for a feature.
     */
    public static function getUpgradeUrl(string $feature): string
    {
        return route('billing.pricing');
    }
}
