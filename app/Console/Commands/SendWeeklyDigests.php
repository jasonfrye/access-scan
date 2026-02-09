<?php

namespace App\Console\Commands;

use App\Services\WeeklyDigestService;
use Illuminate\Console\Command;

class SendWeeklyDigests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access-scan:send-weekly-digests {--dry-run : Preview without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly accessibility digest emails';

    /**
     * Execute the console command.
     */
    public function handle(WeeklyDigestService $service): int
    {
        if ($this->option('dry-run')) {
            $this->info('Dry run mode — would send digests to eligible users.');
            return Command::SUCCESS;
        }

        $this->info('Sending weekly digests...');

        $sent = $service->sendWeeklyDigests();
        $this->info("Sent {$sent} weekly digests.");

        return Command::SUCCESS;
    }
}
