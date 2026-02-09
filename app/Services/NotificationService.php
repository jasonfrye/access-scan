<?php

namespace App\Services;

use App\Models\Scan;
use App\Models\User;
use App\Models\EmailLead;
use App\Mail\ScanCompleteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send scan complete notification.
     */
    public function sendScanCompleteNotification(Scan $scan): void
    {
        $user = $scan->user;

        if (!$user || !$user->email) {
            Log::warning('Cannot send scan notification: no user or email', [
                'scan_id' => $scan->id,
            ]);
            return;
        }

        try {
            Mail::to($user->email)->send(new ScanCompleteMail($scan));

            Log::info('Scan complete notification sent', [
                'scan_id' => $scan->id,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send scan notification', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send regression alert for scheduled scans.
     */
    public function sendRegressionAlert(User $user, Scan $currentScan, Scan $previousScan, int $scoreDrop): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\RegressionAlertMail($currentScan, $previousScan, $scoreDrop));

            Log::info('Regression alert sent', [
                'user_id' => $user->id,
                'scan_id' => $currentScan->id,
                'score_drop' => $scoreDrop,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send regression alert', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send score improvement celebration.
     */
    public function sendScoreImproveNotification(User $user, Scan $currentScan, Scan $previousScan, int $improvement): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\ScoreImproveMail($user, $currentScan, $previousScan, $improvement));

            Log::info('Score improvement notification sent', [
                'user_id' => $user->id,
                'scan_id' => $currentScan->id,
                'improvement' => $improvement,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send score improvement notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send first issue fix guide.
     */
    public function sendFirstIssueFixEmail(Scan $scan, array $topIssues = []): void
    {
        $user = $scan->user;

        if (!$user || !$user->email) {
            return;
        }

        // If no specific issues provided, get top errors from the scan
        if (empty($topIssues) && $scan->issues()->exists()) {
            $topIssues = $scan->issues()
                ->where('type', 'error')
                ->limit(3)
                ->get()
                ->map(fn($issue) => [
                    'type' => $issue->wcag_reference ?? 'Issue',
                    'message' => $issue->message,
                    'code' => $issue->code,
                ])
                ->toArray();
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\FirstIssueFixMail($user, $scan, $topIssues));

            Log::info('First issue fix email sent', [
                'scan_id' => $scan->id,
                'user_id' => $user->id,
                'issues_count' => count($topIssues),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send first issue fix email', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send re-engagement email to inactive users.
     */
    public function sendReEngagementEmail(User $user, int $daysInactive): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\ReEngagementMail($user, $daysInactive));

            Log::info('Re-engagement email sent', [
                'user_id' => $user->id,
                'days_inactive' => $daysInactive,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send re-engagement email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send welcome email to new user.
     */
    public function sendWelcomeEmail(User $user): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));

            Log::info('Welcome email sent', [
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send trial expiring reminder.
     */
    public function sendTrialExpiringReminder(User $user, int $daysLeft): void
    {
        if (!$user->email || !$user->trial_ends_at) {
            return;
        }

        try {
            Mail::to($user->email)->send(new \App\Mail\TrialExpiringMail($user, $daysLeft));

            Log::info('Trial expiring reminder sent', [
                'user_id' => $user->id,
                'days_left' => $daysLeft,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send trial reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send lead nurture email (captured from free scan).
     */
    public function sendLeadEmail(EmailLead $lead, string $template, int $step): void
    {
        if (!$lead->email) {
            return;
        }

        // Implement based on template type
        $templates = [
            'lead_scan_results' => \App\Mail\LeadScanResultsMail::class,
            'lead_compliance_matters' => \App\Mail\LeadComplianceMattersMail::class,
            'lead_case_study' => \App\Mail\LeadCaseStudyMail::class,
            'lead_offer' => \App\Mail\LeadOfferMail::class,
        ];

        $mailClass = $templates[$template] ?? null;

        if (!$mailClass) {
            Log::warning('Unknown lead email template', [
                'template' => $template,
                'lead_id' => $lead->id,
            ]);
            return;
        }

        try {
            Mail::to($lead->email)->send(new $mailClass($lead));

            $lead->update([
                'last_email_sent_at' => now(),
                'email_sequence_step' => $step,
            ]);

            Log::info('Lead email sent', [
                'lead_id' => $lead->id,
                'template' => $template,
                'step' => $step,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send lead email', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Queue a lead nurture sequence.
     */
    public function queueLeadSequence(EmailLead $lead): void
    {
        // Queue emails at intervals:
        // - Email 1: immediate
        // - Email 2: +2 days
        // - Email 3: +5 days
        // - Email 4: +7 days

        // This would use Laravel's queue system with delays
        Log::info('Lead sequence queued', [
            'lead_id' => $lead->id,
            'email' => $lead->email,
        ]);
    }
}
