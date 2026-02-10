<?php

namespace Tests\Feature;

use App\Models\Scan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('AccessScan');
    }

    public function test_scan_index_page_loads_successfully(): void
    {
        $response = $this->get('/scan');

        $response->assertStatus(200);
    }

    public function test_pending_page_loads_for_pending_scan(): void
    {
        $scan = Scan::factory()->create([
            'user_id' => null,
            'status' => Scan::STATUS_PENDING,
        ]);

        $response = $this->get("/scan/{$scan->slug}/pending");

        $response->assertStatus(200);
        $response->assertSee('Scanning Your Website');
    }

    public function test_pending_page_loads_for_running_scan(): void
    {
        $scan = Scan::factory()->create([
            'user_id' => null,
            'status' => Scan::STATUS_RUNNING,
        ]);

        $response = $this->get("/scan/{$scan->slug}/pending");

        $response->assertStatus(200);
    }

    public function test_pending_page_redirects_when_scan_is_completed(): void
    {
        $scan = Scan::factory()->create([
            'user_id' => null,
            'status' => Scan::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $response = $this->get("/scan/{$scan->slug}/pending");

        $response->assertRedirect(route('scan.results', $scan));
    }

    public function test_results_page_loads_for_guest_completed_scan(): void
    {
        $scan = Scan::factory()->create([
            'user_id' => null,
            'status' => Scan::STATUS_COMPLETED,
            'score' => 85,
            'grade' => 'B',
            'completed_at' => now(),
        ]);

        $response = $this->get("/scan/{$scan->slug}");

        $response->assertStatus(200);
        $response->assertSee($scan->domain);
    }

    public function test_results_page_loads_for_authenticated_user_scan(): void
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => Scan::STATUS_COMPLETED,
            'score' => 92,
            'grade' => 'A',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get("/scan/{$scan->slug}");

        $response->assertStatus(200);
        $response->assertSee($scan->domain);
    }

    public function test_results_page_shows_pending_for_incomplete_scan(): void
    {
        $scan = Scan::factory()->create([
            'user_id' => null,
            'status' => Scan::STATUS_RUNNING,
        ]);

        $response = $this->get("/scan/{$scan->slug}");

        $response->assertStatus(200);
        $response->assertSee('Scanning Your Website');
    }

    public function test_scan_status_endpoint_returns_json(): void
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => Scan::STATUS_COMPLETED,
            'score' => 100,
            'grade' => 'A',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson("/scan/{$scan->slug}/status");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('scan.status', 'completed');
    }

    public function test_scan_pages_return_404_for_invalid_slug(): void
    {
        $this->get('/scan/nonexistent-slug/pending')->assertStatus(404);
        $this->get('/scan/nonexistent-slug')->assertStatus(404);
        $this->getJson('/scan/nonexistent-slug/status')->assertStatus(404);
    }

    public function test_pricing_page_loads_for_guest(): void
    {
        config(['cashier.secret' => 'sk_test_fake']);

        $response = $this->get('/pricing');

        $response->assertStatus(200);
        $response->assertSee('Pricing');
    }

    public function test_pricing_page_loads_for_authenticated_user(): void
    {
        config(['cashier.secret' => 'sk_test_fake']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/pricing');

        $response->assertStatus(200);
        $response->assertSee('Pricing');
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_api_docs_page_loads(): void
    {
        $response = $this->get('/api/docs');

        $response->assertStatus(200);
    }
}
