<?php

namespace App\Services;

use App\Models\Scan;
use App\Models\User;
use App\Mail\WeeklyDigestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class WeeklyDigestService
{
    /**
     * Generate weekly stats for a user.
     */
    public function generateStats(User $user): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // Get scans from this week
        $scans = Scan::where('user_id', $user->id)
            ->where('status', Scan::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->get();

        if ($scans->isEmpty()) {
            return [
                'scans' => 0,
                'pages' => 0,
                'issues' => 0,
                'avg_score' => null,
                'improved' => null,
                'declined' => null,
            ];
        }

        $totalPages = $scans->sum('pages_scanned');
        $totalIssues = $scans->sum('issues_found');
        $avgScore = round($scans->avg('score'), 1);

        // Compare to previous week
        $prevStart = $startOfWeek->copy()->subWeek();
        $prevEnd = $endOfWeek->copy()->subWeek();

        $prevScans = Scan::where('user_id', $user->id)
            ->where('status', Scan::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$prevStart, $prevEnd])
            ->get();

        $improved = null;
        $declined = null;

        if ($prevScans->isNotEmpty()) {
            $prevAvgScore = round($prevScans->avg('score'), 1);
            $diff = $avgScore - $prevAvgScore;

            if ($diff > 0) {
                $improved = $diff;
            } elseif ($diff < 0) {
                $declined = abs($diff);
            }
        }

        return [
            'scans' => $scans->count(),
            'pages' => $totalPages,
            'issues' => $totalIssues,
            'avg_score' => $avgScore,
            'improved' => $improved,
            'declined' => $declined,
        ];
    }

    /**
     * Send weekly digest to all users with completed scans.
     */
    public function sendWeeklyDigests(): int
    {
        $users = User::whereNotNull('email_verified_at')
            ->where('weekly_digest', true)
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            try {
                $stats = $this->generateStats($user);

                Mail::to($user->email)->send(new WeeklyDigestMail($user, $stats));

                Log::info('Weekly digest sent', [
                    'user_id' => $user->id,
                    'stats' => $stats,
                ]);

                $sent++;
            } catch (\Exception $e) {
                Log::error('Failed to send weekly digest', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
