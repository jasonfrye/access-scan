<?php

namespace App\Console\Commands;

use App\Services\TrialReminderService;
use Illuminate\Console\Command;

class SendTrialReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access-scan:send-trial-reminders 
        {--process-expired : Also process expired trials}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send trial expiration reminder emails';

    /**
     * Execute the console command.
     */
    public function handle(TrialReminderService $service): int
    {
        $this->info('Checking for trial reminders...');

        $remindersSent = $service->checkAndSendReminders();
        $this->info("Sent {$remindersSent} trial reminders.");

        if ($this->option('process-expired')) {
            $expired = $service->processExpiredTrials();
            $this->info("Processed {$expired} expired trials.");
        }

        return Command::SUCCESS;
    }
}
