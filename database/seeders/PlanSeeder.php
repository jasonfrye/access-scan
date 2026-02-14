<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'stripe_price_id' => null,
                'stripe_yearly_price_id' => null,

                'price_monthly' => 0,
                'price_yearly' => 0,

                'scan_limit' => 5,
                'page_limit_per_scan' => 5,
                'scheduled_scan_limit' => 0,
                'has_pdf_export' => false,
                'has_api_access' => false,
                'features' => json_encode([
                    '5 scans per month',
                    'Up to 5 pages per scan',
                    'Summary reports',
                    'Email support',
                ]),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro Monthly',
                'slug' => 'monthly',
                'stripe_price_id' => 'price_monthly_stripe_id_placeholder',
                'stripe_yearly_price_id' => 'price_yearly_stripe_id_placeholder',

                'price_monthly' => 29,
                'price_yearly' => 290,

                'scan_limit' => 50,
                'page_limit_per_scan' => 100,
                'scheduled_scan_limit' => 5,
                'has_pdf_export' => true,
                'has_api_access' => false,
                'features' => json_encode([
                    '50 scans per month',
                    'Up to 100 pages per scan',
                    'Scheduled scans',
                    'Detailed PDF & CSV exports',
                    'Priority email support',
                ]),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Agency',
                'slug' => 'agency',
                'stripe_price_id' => 'price_agency_stripe_id_placeholder',
                'stripe_yearly_price_id' => 'price_agency_yearly_stripe_id_placeholder',
                'price_monthly' => 99,
                'price_yearly' => 890,
                'scan_limit' => 200,
                'page_limit_per_scan' => 200,
                'scheduled_scan_limit' => 25,
                'has_pdf_export' => true,
                'has_api_access' => true,
                'features' => json_encode([
                    '200 scans per month',
                    'Up to 200 pages per scan',
                    '25 scheduled scans',
                    'White-label PDF reports',
                    'API access',
                    'Priority support',
                ]),
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        DB::table('plans')->insert($plans);
    }
}
