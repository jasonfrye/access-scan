<?php

namespace Tests\Unit\Jobs;

use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Services\NotificationService;
use App\Services\ScannerService;
use Mockery;
use Tests\TestCase;

class RunScanJobTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Create a Scan model with attributes set (bypasses mass assignment protection).
     */
    protected function createScan(array $attributes): Scan
    {
        $scan = new Scan;
        $scan->forceFill($attributes);

        return $scan;
    }

    /** @test */
    public function it_has_correct_queue_name()
    {
        $scan = $this->createScan(['id' => 1, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);

        $this->assertEquals('scans', $job->queue);
    }

    /** @test */
    public function it_generates_unique_id_based_on_scan_id()
    {
        $scan = $this->createScan(['id' => 123, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);

        $this->assertEquals('scan-123', $job->uniqueId());
    }

    /** @test */
    public function it_generates_correct_tags()
    {
        $scan = $this->createScan(['id' => 789, 'url' => 'https://example.com', 'user_id' => 456]);

        $job = new RunScanJob($scan);

        $tags = $job->tags();

        $this->assertContains('scan', $tags);
        $this->assertContains('scan-789', $tags);
        $this->assertContains('user-456', $tags);
    }

    /** @test */
    public function it_has_max_attempts_set()
    {
        $scan = $this->createScan(['id' => 100, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);

        $this->assertEquals(3, $job->maxAttempts);
    }

    /** @test */
    public function it_has_backoff_set()
    {
        $scan = $this->createScan(['id' => 101, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);

        $this->assertEquals(60, $job->backoff);
    }

    /** @test */
    public function it_returns_correct_retry_until()
    {
        $scan = $this->createScan(['id' => 102, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);

        $retryUntil = $job->retryUntil();

        $this->assertNotNull($retryUntil);
        $this->assertGreaterThan(now(), $retryUntil);
    }

    /** @test */
    public function it_gets_scan_instance()
    {
        $scan = $this->createScan(['id' => 200, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);
        $retrievedScan = $job->getScan();

        $this->assertEquals(200, $retrievedScan->id);
        $this->assertEquals('https://example.com', $retrievedScan->url);
    }

    /** @test */
    public function it_calls_scanner_and_notifications_on_success()
    {
        $scan = $this->createScan(['id' => 201, 'url' => 'https://example.com', 'status' => Scan::STATUS_PENDING]);

        $mockScanner = $this->mock(ScannerService::class);
        $mockScanner->shouldReceive('runScan')
            ->once()
            ->with($scan)
            ->andReturn($scan);

        $mockNotification = $this->mock(NotificationService::class);
        $mockNotification->shouldReceive('sendScanCompleteNotification')
            ->once()
            ->with($scan);

        $job = new RunScanJob($scan);
        $job->handle($mockScanner, $mockNotification);

        // Test passes if mocks were called as expected
        $this->assertTrue(true);
    }

    /** @test */
    public function it_rethrows_exception_on_first_attempt()
    {
        $scan = $this->createScan(['id' => 202, 'url' => 'https://example.com']);

        $mockScanner = $this->mock(ScannerService::class);
        $mockScanner->shouldReceive('runScan')
            ->once()
            ->andThrow(new \Exception('Connection timeout'));

        $mockNotification = $this->mock(NotificationService::class);

        $job = new RunScanJob($scan);

        // On first attempt (attempts() returns 1), with maxAttempts=3,
        // the exception should be rethrown for retry
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection timeout');

        $job->handle($mockScanner, $mockNotification);
    }

    /** @test */
    public function it_marks_failed_via_failed_method()
    {
        $scan = Mockery::mock(Scan::class)->makePartial();
        $scan->forceFill(['id' => 203, 'url' => 'https://example.com', 'status' => Scan::STATUS_PENDING]);
        $scan->shouldReceive('markAsFailed')
            ->once()
            ->with(Mockery::pattern('/Persistent failure/'));

        $job = new RunScanJob($scan);

        // The failed() method is called by Laravel after all retries are exhausted
        $job->failed(new \Exception('Persistent failure'));
    }

    /** @test */
    public function it_always_rethrows_exceptions_for_retry()
    {
        $scan = $this->createScan(['id' => 204, 'url' => 'https://example.com', 'status' => Scan::STATUS_PENDING]);

        $mockScanner = $this->mock(ScannerService::class);
        $mockScanner->shouldReceive('runScan')
            ->andThrow(new \Exception('Scanner failure'));

        $mockNotification = $this->mock(NotificationService::class);

        $job = new RunScanJob($scan);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Scanner failure');

        $job->handle($mockScanner, $mockNotification);
    }
}
