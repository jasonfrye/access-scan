<?php

namespace Tests\Feature;

use App\Models\Scan;
use App\Models\ScanSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('groupedScans');
        $response->assertViewHas('schedulesByDomain');
    }

    public function test_dashboard_passes_schedules_by_domain(): void
    {
        $user = User::factory()->create(['plan' => 'monthly']);

        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com/page',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $schedule = ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'frequency' => 'weekly',
            'next_run_at' => now()->addWeek(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $schedulesByDomain = $response->viewData('schedulesByDomain');
        $this->assertNotNull($schedulesByDomain->get('example.com'));
    }

    public function test_dashboard_shows_schedule_icon_for_paid_user(): void
    {
        $user = User::factory()->create(['plan' => 'monthly']);

        Scan::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Schedule recurring scan');
    }

    public function test_dashboard_shows_lock_icon_for_free_user(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        Scan::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Upgrade to schedule scans');
    }
}
