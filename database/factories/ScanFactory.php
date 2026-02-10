<?php

namespace Database\Factories;

use App\Models\Scan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ScanFactory extends Factory
{
    protected $model = Scan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => Str::uuid()->toString(),
            'url' => fake()->url(),
            'status' => Scan::STATUS_PENDING,
            'scan_type' => Scan::TYPE_QUICK,
            'pages_scanned' => 0,
            'issues_found' => 0,
            'errors_count' => 0,
            'warnings_count' => 0,
            'notices_count' => 0,
            'score' => 100.00,
            'grade' => 'A',
        ];
    }
}
