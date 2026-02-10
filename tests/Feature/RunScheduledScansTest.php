<?php

namespace Tests\Feature;

use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Models\ScanSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RunScheduledScansTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_scans_for_due_schedules(): void
    {
        Queue::fake();

        $user = User::factory()->create(['plan' => 'monthly']);
        $schedule = ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'frequency' => ScanSchedule::FREQUENCY_DAILY,
            'next_run_at' => now()->subMinute(),
            'is_active' => true,
            'notify_on_regression' => false,
        ]);

        $this->artisan('app:run-scheduled-scans')
            ->expectsOutputToContain('Dispatched 1 scheduled scan(s)')
            ->assertSuccessful();

        Queue::assertPushed(RunScanJob::class);

        $scan = Scan::where('user_id', $user->id)->first();
        $this->assertNotNull($scan);
        $this->assertEquals(Scan::TYPE_SCHEDULED, $scan->scan_type);
        $this->assertEquals('https://example.com', $scan->url);

        $schedule->refresh();
        $this->assertNotNull($schedule->last_run_at);
        $this->assertTrue($schedule->next_run_at->isFuture());
    }

    /** @test */
    public function it_skips_inactive_schedules(): void
    {
        Queue::fake();

        $user = User::factory()->create(['plan' => 'monthly']);
        ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'frequency' => ScanSchedule::FREQUENCY_DAILY,
            'next_run_at' => now()->subMinute(),
            'is_active' => false,
            'notify_on_regression' => false,
        ]);

        $this->artisan('app:run-scheduled-scans')
            ->expectsOutputToContain('No scheduled scans are due')
            ->assertSuccessful();

        Queue::assertNotPushed(RunScanJob::class);
    }

    /** @test */
    public function it_skips_schedules_not_yet_due(): void
    {
        Queue::fake();

        $user = User::factory()->create(['plan' => 'monthly']);
        ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'frequency' => ScanSchedule::FREQUENCY_DAILY,
            'next_run_at' => now()->addHour(),
            'is_active' => true,
            'notify_on_regression' => false,
        ]);

        $this->artisan('app:run-scheduled-scans')
            ->expectsOutputToContain('No scheduled scans are due')
            ->assertSuccessful();

        Queue::assertNotPushed(RunScanJob::class);
    }

    /** @test */
    public function it_skips_users_with_no_scheduled_scan_allowance(): void
    {
        Queue::fake();

        $user = User::factory()->create(['plan' => 'free']);
        ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'frequency' => ScanSchedule::FREQUENCY_DAILY,
            'next_run_at' => now()->subMinute(),
            'is_active' => true,
            'notify_on_regression' => false,
        ]);

        $this->artisan('app:run-scheduled-scans')
            ->expectsOutputToContain('skipped 1')
            ->assertSuccessful();

        Queue::assertNotPushed(RunScanJob::class);
    }

    /** @test */
    public function it_handles_multiple_due_schedules(): void
    {
        Queue::fake();

        $user = User::factory()->create(['plan' => 'monthly']);

        ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'frequency' => ScanSchedule::FREQUENCY_DAILY,
            'next_run_at' => now()->subMinute(),
            'is_active' => true,
            'notify_on_regression' => false,
        ]);

        ScanSchedule::create([
            'user_id' => $user->id,
            'url' => 'https://example.org',
            'frequency' => ScanSchedule::FREQUENCY_WEEKLY,
            'next_run_at' => now()->subHour(),
            'is_active' => true,
            'notify_on_regression' => false,
        ]);

        $this->artisan('app:run-scheduled-scans')
            ->expectsOutputToContain('Dispatched 2 scheduled scan(s)')
            ->assertSuccessful();

        Queue::assertPushed(RunScanJob::class, 2);
        $this->assertEquals(2, Scan::where('user_id', $user->id)->count());
    }
}
