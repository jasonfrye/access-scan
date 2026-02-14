<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrantUserPlanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PlanSeeder::class);
    }

    public function test_grants_monthly_plan_to_user(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'scan_limit' => 5]);

        $this->artisan('user:grant-plan', ['email' => $user->email, 'plan' => 'monthly'])
            ->expectsOutputToContain('Granted')
            ->assertExitCode(0);

        $user->refresh();
        $this->assertEquals('monthly', $user->getRawOriginal('plan'));
        $this->assertEquals(50, $user->scan_limit);
    }

    public function test_grants_agency_plan_to_user(): void
    {
        $user = User::factory()->create(['plan' => 'free', 'scan_limit' => 5]);

        $this->artisan('user:grant-plan', ['email' => $user->email, 'plan' => 'agency'])
            ->expectsOutputToContain('Granted')
            ->assertExitCode(0);

        $user->refresh();
        $this->assertEquals('agency', $user->getRawOriginal('plan'));
        $this->assertEquals(200, $user->scan_limit);
    }

    public function test_fails_for_unknown_email(): void
    {
        $this->artisan('user:grant-plan', ['email' => 'nobody@example.com', 'plan' => 'monthly'])
            ->expectsOutputToContain('No user found')
            ->assertExitCode(1);
    }

    public function test_fails_for_invalid_plan(): void
    {
        $user = User::factory()->create();

        $this->artisan('user:grant-plan', ['email' => $user->email, 'plan' => 'enterprise'])
            ->expectsOutputToContain('Invalid plan')
            ->assertExitCode(1);
    }
}
