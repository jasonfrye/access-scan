<?php

namespace Tests\Feature;

use App\Models\Scan;
use App\Models\ScanIssue;
use App\Models\ScanPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanPageDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_scan_page_detail(): void
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $page = ScanPage::factory()->create([
            'scan_id' => $scan->id,
            'errors_count' => 2,
        ]);
        ScanIssue::factory()->create([
            'scan_page_id' => $page->id,
            'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
            'message' => 'Img element missing an alt attribute.',
            'type' => 'error',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scan.page', [$scan, $page]));

        $response->assertOk();
        $response->assertSee('Graphics');
        $response->assertSee('Img element missing an alt attribute.');
    }

    public function test_guest_cannot_view_scan_page_detail(): void
    {
        $scan = Scan::factory()->create([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $page = ScanPage::factory()->create(['scan_id' => $scan->id]);

        $response = $this->get(route('dashboard.scan.page', [$scan, $page]));

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_view_other_users_scan_page(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $page = ScanPage::factory()->create(['scan_id' => $scan->id]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scan.page', [$scan, $page]));

        $response->assertForbidden();
    }

    public function test_returns_404_for_page_not_belonging_to_scan(): void
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $otherScan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $page = ScanPage::factory()->create(['scan_id' => $otherScan->id]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scan.page', [$scan, $page]));

        $response->assertNotFound();
    }

    public function test_scan_overview_shows_pages_list(): void
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $page = ScanPage::factory()->create([
            'scan_id' => $scan->id,
            'url' => 'https://example.com/about',
            'page_title' => 'About Us',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.scan', $scan));

        $response->assertOk();
        $response->assertSee('Pages Scanned');
        $response->assertSee('About Us');
    }
}
