<?php

namespace Database\Factories;

use App\Models\ScanIssue;
use App\Models\ScanPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScanIssueFactory extends Factory
{
    protected $model = ScanIssue::class;

    public function definition(): array
    {
        return [
            'scan_page_id' => ScanPage::factory(),
            'type' => fake()->randomElement(['error', 'warning', 'notice']),
            'code' => fake()->word(),
            'message' => fake()->sentence(),
            'context' => fake()->randomHtml(),
            'selector' => fake()->word(),
            'wcag_principle' => fake()->numberBetween(1, 4),
            'wcag_guideline' => fake()->word(),
            'wcag_criterion' => fake()->word(),
            'wcag_level' => fake()->randomElement(['A', 'AA', 'AAA']),
            'impact' => fake()->randomElement(['critical', 'serious', 'moderate', 'minor']),
            'recommendation' => fake()->sentence(),
            'help_url' => fake()->url(),
        ];
    }
}
