<?php

namespace App\Services;

use App\Mail\TrialExpiringMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TrialReminderService
{
    /**
     * Check for trials expiring and send reminders.
     */
    public function checkAndSendReminders(): int
    {
        $remindersSent = 0;

        // Send 3-day reminders
        $remindersSent += $this->sendRemindersForDays(3);

        // Send 1-day reminders
        $remindersSent += $this->sendRemindersForDays(1);

        // Send expired notifications
        $remindersSent += $this->sendExpiredNotifications();

        return $remindersSent;
    }

    /**
     * Send reminders for trials expiring in X days.
     */
    protected function sendRemindersForDays(int $days): int
    {
        $users = User::where('trial_ends_at', '=', now()->addDays($days))
            ->where('plan', 'free')
            ->whereNotNull('trial_ends_at')
            ->where('marketing_emails_enabled', true)
            ->get();

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new TrialExpiringMail($user, $days));

                Log::info('Trial expiring reminder sent', [
                    'user_id' => $user->id,
                    'days_left' => $days,
                ]);

                $user->update(['trial_reminder_sent_at' => now()]);
            } catch (\Exception $e) {
                Log::error('Failed to send trial reminder', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $users->count();
    }

    /**
     * Notify users whose trials have expired today.
     */
    protected function sendExpiredNotifications(): int
    {
        $users = User::where('trial_ends_at', '<', now())
            ->where('trial_ends_at', '>=', now()->subDay())
            ->where('plan', 'free')
            ->whereNotNull('trial_reminder_sent_at')
            ->where('marketing_emails_enabled', true)
            ->get();

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new \App\Mail\TrialExpiredMail($user));

                Log::info('Trial expired notification sent', [
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send trial expired notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $users->count();
    }

    /**
     * Check if trial has expired and downgrade user.
     */
    public function processExpiredTrials(): int
    {
        $expiredCount = 0;

        $users = User::where('trial_ends_at', '<', now())
            ->where('plan', 'free')
            ->whereNotNull('trial_ends_at')
            ->get();

        foreach ($users as $user) {
            // Downgrade to free plan if not already
            if ($user->scan_limit !== 5) {
                $user->update([
                    'scan_limit' => 5,
                    'trial_ends_at' => null,
                    'trial_reminder_sent_at' => null,
                ]);

                Log::info('User trial expired, downgraded to free', [
                    'user_id' => $user->id,
                ]);

                $expiredCount++;
            }
        }

        return $expiredCount;
    }
}
