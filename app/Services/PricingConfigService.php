<?php

namespace App\Services;

use App\Models\PricingConfig;
use Illuminate\Support\Facades\Cookie;

class PricingConfigService
{
    /**
     * Get the active pricing configuration.
     */
    public function getActive(): ?PricingConfig
    {
        return PricingConfig::getActive();
    }

    /**
     * Get a config by ID.
     */
    public function getById(int $id): ?PricingConfig
    {
        return PricingConfig::find($id);
    }

    /**
     * Get all configs ordered.
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return PricingConfig::ordered()->get();
    }

    /**
     * Get the config for a visitor (from cookie or default).
     */
    public function getForVisitor(): PricingConfig
    {
        $cookieName = 'pricing_config_id';

        // Check for existing cookie
        $configId = Cookie::get($cookieName);

        if ($configId) {
            $config = PricingConfig::find($configId);
            if ($config) {
                return $config;
            }
        }

        // Fall back to active config
        $active = $this->getActive();
        if ($active) {
            return $active;
        }

        // Last resort: create default
        $config = $this->createDefault();
        $this->setCookie($config);

        return $config;
    }

    /**
     * Assign a config to a visitor (sets cookie).
     */
    public function assignToVisitor(PricingConfig $config): void
    {
        Cookie::queue($config->id, 'pricing_config_id', 60 * 24 * 30); // 30 days
    }

    /**
     * Create a new pricing config.
     */
    public function create(array $data): PricingConfig
    {
        return PricingConfig::create($data);
    }

    /**
     * Update a pricing config.
     */
    public function update(PricingConfig $config, array $data): bool
    {
        return $config->update($data);
    }

    /**
     * Delete a pricing config.
     */
    public function delete(PricingConfig $config): bool
    {
        // Don't allow deletion if it's the only config
        if (PricingConfig::count() <= 1) {
            return false;
        }

        return $config->delete();
    }

    /**
     * Activate a config (deactivates others).
     */
    public function activate(PricingConfig $config): bool
    {
        $config->setAsActive();

        return true;
    }

    /**
     * Create default pricing config.
     */
    protected function createDefault(): PricingConfig
    {
        return PricingConfig::create([
            'name' => 'Default Pricing',
            'description' => 'Standard pricing configuration',
            'config' => [
                'plans' => [
                    'free' => [
                        'name' => 'Free',
                        'price' => 0,
                        'interval' => null,
                        'features' => ['5 scans/month', '5 pages/scan', 'Basic report'],
                    ],
                    'monthly' => [
                        'name' => 'Pro',
                        'price' => 29,
                        'interval' => 'month',
                        'features' => ['50 scans/month', '100 pages/scan', 'PDF exports', 'Scheduled scans'],
                    ],
                    'agency' => [
                        'name' => 'Agency',
                        'price' => 99,
                        'interval' => 'month',
                        'features' => ['200 scans/month', '200 pages/scan', 'White-label PDF', 'API access', '25 scheduled scans'],
                    ],
                ],
                'features_list' => ['Automated scans', 'PDF reports', 'WCAG compliance'],
                'cta_copy' => 'Start Free Trial',
                'highlighted_plan' => 'monthly',
                'badge_text' => 'Most Popular',
            ],
            'is_active' => true,
            'traffic_split' => 100,
            'activated_at' => now(),
        ]);
    }

    /**
     * Set cookie for visitor config assignment.
     */
    protected function setCookie(PricingConfig $config): void
    {
        Cookie::queue('pricing_config_id', $config->id, 60 * 24 * 30);
    }
}
