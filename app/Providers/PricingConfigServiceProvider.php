<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PricingConfigService;
use App\Facades\PricingConfig;

class PricingConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('pricing-config', function ($app) {
            return new PricingConfigService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register facade
        if (!class_exists('PricingConfig')) {
            class_alias(PricingConfig::class, 'PricingConfig');
        }
    }
}
