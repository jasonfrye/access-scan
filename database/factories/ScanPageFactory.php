<?php

namespace Database\Factories;

use App\Models\ScanPage;
use App\Models\Scan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScanPageFactory extends Factory
{
    protected $model = ScanPage::class;

    public function definition(): array
    {
        return [
            'scan_id' => Scan::factory(),
            'url' => fake()->url(),
            'status' => 'completed',
            'issues_count' => 0,
            'errors_count' => 0,
            'warnings_count' => 0,
            'notices_count' => 0,
            'page_title' => fake()->sentence(),
            'http_status' => 200,
        ];
    }
}
