<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Services\ScannerService;
use App\Services\NotificationService;
use Mockery;

class RunScanJobTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_has_correct_queue_name()
    {
        $scan = new Scan(['id' => 1, 'url' => 'https://example.com']);
        
        $job = new RunScanJob($scan);

        $this->assertEquals('scans', $job->queue);
    }

    /** @test */
    public function it_generates_unique_id_based_on_scan_id()
    {
        $scan = new Scan(['id' => 123, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);

        $this->assertEquals('scan-123', $job->uniqueId());
    }

    /** @test */
    public function it_generates_correct_tags()
    {
        $scan = new Scan(['id' => 789, 'url' => 'https://example.com', 'user_id' => 456]);

        $job = new RunScanJob($scan);

        $tags = $job->tags();

        $this->assertContains('scan', $tags);
        $this->assertContains('scan-789', $tags);
        $this->assertContains('user-456', $tags);
    }

    /** @test */
    public function it_has_max_attempts_set()
    {
        $scan = new Scan(['id' => 100, 'url' => 'https://example.com']);
        
        $job = new RunScanJob($scan);

        $this->assertEquals(3, $job->maxAttempts);
    }

    /** @test */
    public function it_has_backoff_set()
    {
        $scan = new Scan(['id' => 101, 'url' => 'https://example.com']);
        
        $job = new RunScanJob($scan);

        $this->assertEquals(60, $job->backoff);
    }

    /** @test */
    public function it_returns_correct_retry_until()
    {
        $scan = new Scan(['id' => 102, 'url' => 'https://example.com']);
        
        $job = new RunScanJob($scan);

        $retryUntil = $job->retryUntil();

        $this->assertNotNull($retryUntil);
        $this->assertGreaterThan(now(), $retryUntil);
    }

    /** @test */
    public function it_gets_scan_instance()
    {
        $scan = new Scan(['id' => 200, 'url' => 'https://example.com']);

        $job = new RunScanJob($scan);
        $retrievedScan = $job->getScan();

        $this->assertEquals(200, $retrievedScan->id);
        $this->assertEquals('https://example.com', $retrievedScan->url);
    }

    /** @test */
    public function it_calls_scanner_and_notifications_on_success()
    {
        $scan = new Scan(['id' => 201, 'url' => 'https://example.com']);

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
        $scan = new Scan(['id' => 202, 'url' => 'https://example.com']);

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
    public function it_marks_failed_after_exhausted_retries()
    {
        $scan = new Scan(['id' => 203, 'url' => 'https://example.com', 'status' => Scan::STATUS_PENDING]);

        $mockScanner = $this->mock(ScannerService::class);
        $mockScanner->shouldReceive('runScan')
            ->andThrow(new \Exception('Persistent failure'));

        $mockNotification = $this->mock(NotificationService::class);

        $job = new RunScanJob($scan);
        
        // Override maxAttempts to 1 so attempts() < maxAttempts is false
        // (attempts() returns 1 by default, maxAttempts=1 means 1 < 1 = false)
        $job->maxAttempts = 1;

        // Should NOT throw when attempts >= maxAttempts
        $job->handle($mockScanner, $mockNotification);

        // Scan status should be 'failed' after handle completes without throwing
        $this->assertEquals(Scan::STATUS_FAILED, $scan->status);
    }
}
