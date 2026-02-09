<?php

namespace App\Traits;

use App\Models\User;
use App\Http\Middleware\CheckPlanFeature;

trait ChecksPlanFeatures
{
    /**
     * Get the user's scan usage for the current month.
     */
    protected function getScanUsage(User $user): array
    {
        return CheckPlanFeature::canStartScan($user);
    }

    /**
     * Check if user can start a new scan.
     */
    protected function canStartScan(User $user): array
    {
        return CheckPlanFeature::canStartScan($user);
    }

    /**
     * Check if user can scan a URL with the given page count.
     */
    protected function canScanPages(User $user, int $pageCount): array
    {
        return CheckPlanFeature::canScanPages($user, $pageCount);
    }

    /**
     * Check if user can create a scheduled scan.
     */
    protected function canCreateScheduledScan(User $user): array
    {
        return CheckPlanFeature::canCreateScheduledScan($user);
    }

    /**
     * Check if user can export to a specific format.
     */
    protected function canExport(User $user, string $format): array
    {
        return CheckPlanFeature::canExport($user, $format);
    }

    /**
     * Get upgrade redirect response for a failed feature check.
     */
    protected function featureDeniedResponse(array $checkResult, $request = null): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request = $request ?: request();
        return CheckPlanFeature::deniedResponse('feature', $checkResult, $request);
    }

    /**
     * Throw exception if feature is not available.
     */
    protected function assertFeatureAvailable(array $checkResult, string $feature = 'feature'): void
    {
        if (!$checkResult['allowed']) {
            throw new \Exception($checkResult['message'] ?? 'Feature not available on your plan.');
        }
    }
}
