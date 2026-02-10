<?php

namespace App\Console\Commands;

use App\Jobs\RunScanJob;
use App\Models\Scan;
use App\Models\ScanSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunScheduledScans extends Command
{
    protected $signature = 'app:run-scheduled-scans';

    protected $description = 'Run all due scheduled scans';

    public function handle(): int
    {
        $dueSchedules = ScanSchedule::due()->with('user')->get();

        if ($dueSchedules->isEmpty()) {
            $this->info('No scheduled scans are due.');

            return self::SUCCESS;
        }

        $dispatched = 0;
        $skipped = 0;

        foreach ($dueSchedules as $schedule) {
            $user = $schedule->user;

            if (! $user) {
                $schedule->update(['is_active' => false]);
                $skipped++;

                continue;
            }

            $activeScheduleCount = $user->scheduledScans()->active()->count();
            $scheduledScanLimit = $user->getScheduledScanLimit();

            if ($scheduledScanLimit <= 0) {
                $skipped++;

                continue;
            }

            $scan = Scan::create([
                'user_id' => $user->id,
                'url' => $schedule->url,
                'status' => Scan::STATUS_PENDING,
                'scan_type' => Scan::TYPE_SCHEDULED,
            ]);

            dispatch(new RunScanJob($scan));

            $schedule->updateAfterScan();
            $dispatched++;

            Log::info('Scheduled scan dispatched', [
                'schedule_id' => $schedule->id,
                'scan_id' => $scan->id,
                'user_id' => $user->id,
                'url' => $schedule->url,
            ]);
        }

        $this->info("Dispatched {$dispatched} scheduled scan(s), skipped {$skipped}.");

        return self::SUCCESS;
    }
}
