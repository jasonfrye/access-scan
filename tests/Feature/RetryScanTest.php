<?php

namespace Tests\Feature;

use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Models\ScanIssue;
use App\Models\ScanPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RetryScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_retry_a_failed_scan(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => Scan::STATUS_FAILED,
            'error_message' => 'Connection timed out',
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.scan.retry', $scan));

        $response->assertRedirect(route('dashboard.scan', $scan));
        $response->assertSessionHas('success');

        $scan->refresh();
        $this->assertEquals(Scan::STATUS_PENDING, $scan->status);
        $this->assertNull($scan->error_message);
        $this->assertNull($scan->score);

        Queue::assertPushed(RunScanJob::class);
    }

    public function test_user_can_retry_a_stuck_pending_scan(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => Scan::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.scan.retry', $scan));

        $response->assertRedirect(route('dashboard.scan', $scan));
        Queue::assertPushed(RunScanJob::class);
    }

    public function test_user_cannot_retry_a_completed_scan_via_retry_route(): void
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => Scan::STATUS_COMPLETED,
            'score' => 85,
            'grade' => 'B',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.scan.retry', $scan));

        $response->assertRedirect(route('dashboard.scan', $scan));
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_retry_another_users_scan(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $otherUser->id,
            'status' => Scan::STATUS_FAILED,
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.scan.retry', $scan));

        $response->assertForbidden();
    }

    public function test_retry_clears_old_pages_and_issues(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $scan = Scan::factory()->create([
            'user_id' => $user->id,
            'status' => Scan::STATUS_FAILED,
            'pages_scanned' => 3,
            'issues_found' => 10,
        ]);

        $page = ScanPage::create([
            'scan_id' => $scan->id,
            'url' => 'https://example.com/',
            'path' => '/',
            'score' => 50,
            'errors_count' => 5,
            'warnings_count' => 3,
            'notices_count' => 2,
        ]);

        ScanIssue::create([
            'scan_page_id' => $page->id,
            'type' => 'error',
            'message' => 'Missing alt text',
            'code' => 'WCAG2AA.Principle1.Guideline1_1.1_1_1.H37',
        ]);

        $this->actingAs($user)->post(route('dashboard.scan.retry', $scan));

        $this->assertDatabaseCount('scan_pages', 0);
        $this->assertDatabaseCount('scan_issues', 0);

        $scan->refresh();
        $this->assertEquals(0, $scan->pages_scanned);
    }
}
