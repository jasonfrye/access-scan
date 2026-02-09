<?php

namespace App\Jobs;

use App\Models\Scan;
use App\Services\ScannerService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunScanJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $maxAttempts = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The scan to process.
     */
    protected Scan $scan;

    /**
     * Create a new job instance.
     */
    public function __construct(Scan $scan)
    {
        $this->scan = $scan;
        $this->onQueue('scans');
    }

    /**
     * Get the unique ID for this job.
     */
    public function uniqueId(): string
    {
        return 'scan-' . $this->scan->id;
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['scan', 'scan-' . $this->scan->id, 'user-' . $this->scan->user_id];
    }

    /**
     * Execute the job.
     */
    public function handle(ScannerService $scanner): void
    {
        Log::info('Starting scan job', ['scan_id' => $this->scan->id]);

        try {
            // Run the scan
            $scanner->runScan($this->scan);

            Log::info('Scan job completed successfully', ['scan_id' => $this->scan->id]);

            // TODO: Dispatch notification event
            // event(new ScanCompleted($this->scan));
        } catch (Throwable $e) {
            Log::error('Scan job failed', [
                'scan_id' => $this->scan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // If we haven't exceeded max attempts, rethrow to trigger retry
            if ($this->attempts() < $this->maxAttempts) {
                throw $e;
            }

            // Mark as failed after all retries exhausted
            $this->scan->markAsFailed('Maximum retry attempts exceeded: ' . $e->getMessage());
        }
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(30);
    }

    /**
     * Get the scan instance.
     */
    public function getScan(): Scan
    {
        return $this->scan;
    }
}
