<?php

namespace Database\Seeders;

use App\Models\PricingConfig;
use Illuminate\Database\Seeder;

class PricingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we already have configs
        if (PricingConfig::exists()) {
            return;
        }

        PricingConfig::create([
            'name' => 'Default Pricing',
            'description' => 'Standard pricing configuration',
            'config' => [
                'plans' => [
                    'free' => [
                        'name' => 'Free',
                        'price' => 0,
                        'interval' => null,
                        'features' => [
                            '5 scans per month',
                            'Up to 5 pages per scan',
                            'Basic report summary',
                            'Email notifications',
                        ],
                        'button_text' => 'Sign Up Free',
                        'highlight' => false,
                    ],
                    'monthly' => [
                        'name' => 'Pro',
                        'price' => 29,
                        'interval' => 'month',
                        'features' => [
                            '50 scans per month',
                            'Up to 100 pages per scan',
                            'Detailed PDF & CSV reports',
                            'Scheduled automatic scans',
                            'Priority support',
                            'Regression alerts',
                        ],
                        'button_text' => 'Start Free Trial',
                        'highlight' => true,
                        'badge' => 'Most Popular',
                    ],
                    'agency' => [
                        'name' => 'Agency',
                        'price' => 99,
                        'interval' => 'month',
                        'features' => [
                            '200 scans per month',
                            'Up to 200 pages per scan',
                            '25 scheduled scans',
                            'White-label PDF reports',
                            'API access',
                            'Priority support',
                        ],
                        'button_text' => 'Subscribe to Agency',
                        'highlight' => false,
                    ],
                ],
                'features_list' => [
                    'Unlimited team members',
                    'Automated scheduled scans',
                    'PDF & CSV exports',
                    'WCAG A/AA compliance',
                    'Detailed recommendations',
                    'Priority email support',
                ],
                'cta_copy' => 'Start Free Trial',
                'highlighted_plan' => 'monthly',
                'badge_text' => 'Most Popular',
            ],
            'is_active' => true,
            'traffic_split' => 100,
            'activated_at' => now(),
        ]);
    }
}
