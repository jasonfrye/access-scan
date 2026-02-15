<?php

namespace App\Jobs;

use App\Models\Scan;
use App\Services\NotificationService;
use App\Services\ScannerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunScanJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $maxAttempts = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 900;

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
        return 'scan-'.$this->scan->id;
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['scan', 'scan-'.$this->scan->id, 'user-'.$this->scan->user_id];
    }

    /**
     * Execute the job.
     */
    public function handle(ScannerService $scanner, NotificationService $notifications): void
    {
        Log::info('Starting scan job', ['scan_id' => $this->scan->id, 'attempt' => $this->attempts()]);

        try {
            // Run the scan
            $scanner->runScan($this->scan);

            Log::info('Scan job completed successfully', ['scan_id' => $this->scan->id]);

            // Send scan complete notification only if pages were actually scanned
            if ($this->scan->pages_scanned > 0) {
                $notifications->sendScanCompleteNotification($this->scan);
            }

            // Check for regression on scheduled scans
            if ($this->scan->user && $this->scan->scan_type === Scan::TYPE_SCHEDULED) {
                $this->checkForRegression($notifications);
            }
        } catch (Throwable $e) {
            Log::error('Scan job failed', [
                'scan_id' => $this->scan->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rethrow to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception = null): void
    {
        Log::error('Scan job failed after all retry attempts', [
            'scan_id' => $this->scan->id,
            'error' => $exception?->getMessage(),
        ]);

        // Mark scan as failed
        $this->scan->markAsFailed(
            $exception
                ? 'Scan failed: '.$exception->getMessage()
                : 'Scan failed after maximum retry attempts'
        );
    }

    /**
     * Check for score regression or significant improvement.
     */
    protected function checkForRegression(NotificationService $notifications): void
    {
        $user = $this->scan->user;

        if (! $user) {
            return;
        }

        // Get the previous scan for the same URL
        $previousScan = $user->scans()
            ->where('id', '!=', $this->scan->id)
            ->where('url', 'like', '%'.parse_url($this->scan->url, PHP_URL_HOST).'%')
            ->completed()
            ->latest('completed_at')
            ->first();

        if (! $previousScan) {
            return;
        }

        $currentScore = $this->scan->score ?? 0;
        $previousScore = $previousScan->score ?? 0;
        $scoreDrop = $previousScore - $currentScore;
        $scoreImprove = $currentScore - $previousScore;

        // Alert if score dropped by 10+ points (regression)
        if ($scoreDrop >= 10) {
            $notifications->sendRegressionAlert($user, $this->scan, $previousScan, $scoreDrop);
        }

        // Celebrate if score improved by 20+ points
        if ($scoreImprove >= 20) {
            $notifications->sendScoreImproveNotification($user, $this->scan, $previousScan, $scoreImprove);
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
