<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\PricingConfig;

class PricingConfigTest extends TestCase
{
    protected PricingConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new PricingConfig();
        $this->config->forceFill([
            'name' => 'Test Config',
            'config' => [
                'plans' => [
                    'free' => [
                        'name' => 'Free',
                        'price' => 0,
                        'interval' => null,
                        'features' => ['5 scans/month'],
                    ],
                    'monthly' => [
                        'name' => 'Pro',
                        'price' => 29,
                        'interval' => 'month',
                        'features' => ['50 scans/month', 'PDF exports'],
                    ],
                ],
                'highlighted_plan' => 'monthly',
                'badge_text' => 'Most Popular',
            ],
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $fillable = $this->config->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('config', $fillable);
        $this->assertContains('is_active', $fillable);
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $casts = $this->config->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('array', $casts['config']);
    }

    /** @test */
    public function it_can_get_a_specific_plan()
    {
        $plan = $this->config->getPlan('monthly');

        $this->assertIsArray($plan);
        $this->assertEquals('Pro', $plan['name']);
        $this->assertEquals(29, $plan['price']);
    }

    /** @test */
    public function it_returns_null_for_missing_plan()
    {
        $plan = $this->config->getPlan('nonexistent');

        $this->assertNull($plan);
    }

    /** @test */
    public function it_can_get_all_plans()
    {
        $plans = $this->config->getPlans();

        $this->assertIsArray($plans);
        $this->assertArrayHasKey('free', $plans);
        $this->assertArrayHasKey('monthly', $plans);
    }

    /** @test */
    public function it_can_get_highlighted_plan_key()
    {
        $highlighted = $this->config->getHighlightedPlan();

        $this->assertEquals('monthly', $highlighted);
    }

    /** @test */
    public function it_returns_null_when_no_highlighted_plan()
    {
        $config = new PricingConfig();
        $config->forceFill([
            'name' => 'Test',
            'config' => ['plans' => []],
        ]);

        $highlighted = $config->getHighlightedPlan();

        $this->assertNull($highlighted);
    }

    /** @test */
    public function it_has_scopes_method()
    {
        // Verify the model has the expected methods
        $this->assertTrue(method_exists(PricingConfig::class, 'scopeActive'));
        $this->assertTrue(method_exists(PricingConfig::class, 'scopeOrdered'));
    }

    /** @test */
    public function it_handles_null_config_gracefully()
    {
        $config = new PricingConfig();
        $config->forceFill(['name' => 'Test']);

        $plans = $config->getPlans();
        $highlighted = $config->getHighlightedPlan();

        $this->assertEquals([], $plans);
        $this->assertNull($highlighted);
    }
}
