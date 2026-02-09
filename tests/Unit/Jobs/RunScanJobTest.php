<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Models\User;
use App\Services\ScannerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class RunScanJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_queue_name()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();

        $job = new RunScanJob($scan);

        $this->assertEquals('scans', $job->queue);
    }

    /** @test */
    public function it_generates_unique_id_based_on_scan_id()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create(['id' => 123]);

        $job = new RunScanJob($scan);

        $this->assertEquals('scan-123', $job->uniqueId());
    }

    /** @test */
    public function it_generates_correct_tags()
    {
        $user = User::factory()->create(['id' => 456]);
        $scan = Scan::factory()->for($user)->create(['id' => 789]);

        $job = new RunScanJob($scan);

        $tags = $job->tags();

        $this->assertContains('scan', $tags);
        $this->assertContains('scan-789', $tags);
        $this->assertContains('user-456', $tags);
    }

    /** @test */
    public function it_has_max_attempts_set()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();

        $job = new RunScanJob($scan);

        $this->assertEquals(3, $job->maxAttempts);
    }

    /** @test */
    public function it_has_backoff_set()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();

        $job = new RunScanJob($scan);

        $this->assertEquals(60, $job->backoff);
    }

    /** @test */
    public function it_returns_correct_retry_until()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();

        $job = new RunScanJob($scan);

        $retryUntil = $job->retryUntil();

        $this->assertNotNull($retryUntil);
        $this->assertGreaterThan(now(), $retryUntil);
    }

    /** @test */
    public function it_gets_scan_instance()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create();

        $job = new RunScanJob($scan);
        $retrievedScan = $job->getScan();

        $this->assertEquals($scan->id, $retrievedScan->id);
        $this->assertEquals($scan->url, $retrievedScan->url);
    }

    /** @test */
    public function it_handles_successful_scan_execution()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create([
            'status' => Scan::STATUS_PENDING,
        ]);

        // Mock the ScannerService
        $this->mock(ScannerService::class)
            ->shouldReceive('runScan')
            ->once()
            ->with($scan)
            ->andReturn($scan);

        $job = new RunScanJob($scan);
        $job->handle(new ScannerService());

        $this->assertEquals(Scan::STATUS_RUNNING, $scan->fresh()->status);
    }

    /** @test */
    public function it_marks_scan_as_failed_after_max_retries()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create([
            'status' => Scan::STATUS_PENDING,
        ]);

        $this->mock(ScannerService::class)
            ->shouldReceive('runScan')
            ->andThrow(new \Exception('Connection timeout'));

        $job = new RunScanJob($scan);

        // Manually set attempts to max to simulate exhausted retries
        $reflection = new \ReflectionClass($job);
        $property = $reflection->getProperty('attempts');
        $property->setAccessible(true);
        $property->setValue($job, 3);

        $job->handle(new ScannerService());

        $this->assertEquals(Scan::STATUS_FAILED, $scan->fresh()->status);
        $this->assertStringContainsString('Maximum retry attempts exceeded', $scan->fresh()->error_message);
    }

    /** @test */
    public function it_logs_scan_start()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create([
            'id' => 100,
        ]);

        Log::shouldReceive('info')
            ->with('Starting scan job', \Mockery::on(function ($data) {
                return isset($data['scan_id']) && $data['scan_id'] === 100;
            }))
            ->once();

        $this->mock(ScannerService::class)
            ->shouldReceive('runScan')
            ->once()
            ->andReturn($scan);

        $job = new RunScanJob($scan);
        $job->handle(new ScannerService());
    }

    /** @test */
    public function it_logs_scan_completion()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create([
            'id' => 101,
        ]);

        Log::shouldReceive('info')
            ->with('Scan job completed successfully', \Mockery::on(function ($data) {
                return isset($data['scan_id']) && $data['scan_id'] === 101;
            }))
            ->once();

        $this->mock(ScannerService::class)
            ->shouldReceive('runScan')
            ->once()
            ->andReturn($scan);

        $job = new RunScanJob($scan);
        $job->handle(new ScannerService());
    }

    /** @test */
    public function it_logs_scan_failure()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create([
            'id' => 102,
        ]);

        Log::shouldReceive('error')
            ->with('Scan job failed', \Mockery::on(function ($data) {
                return isset($data['scan_id']) && $data['scan_id'] === 102;
            }))
            ->once();

        // For the first attempt, let it throw
        $this->mock(ScannerService::class)
            ->shouldReceive('runScan')
            ->andThrow(new \Exception('Something went wrong'));

        $job = new RunScanJob($scan);

        try {
            $job->handle(new ScannerService());
        } catch (\Exception $e) {
            // Expected - will be retried
        }
    }

    /** @test */
    public function it_releases_job_for_retry_on_first_attempt_failure()
    {
        $user = User::factory()->create();
        $scan = Scan::factory()->for($user)->create([
            'id' => 103,
        ]);

        $this->mock(ScannerService::class)
            ->shouldReceive('runScan')
            ->andThrow(new \Exception('Temporary failure'));

        $job = new RunScanJob($scan);

        $this->expectException(\Exception::class);

        try {
            $job->handle(new ScannerService());
        } catch (\Exception $e) {
            $this->assertLessThan($job->maxAttempts, $job->attempts());
            throw $e;
        }
    }
}
