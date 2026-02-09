<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'status',
        'scan_type',
        'pages_scanned',
        'issues_found',
        'errors_count',
        'warnings_count',
        'notices_count',
        'score',
        'grade',
        'error_message',
        'started_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'pages_scanned' => 'integer',
        'issues_found' => 'integer',
        'errors_count' => 'integer',
        'warnings_count' => 'integer',
        'notices_count' => 'integer',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Scan type constants.
     */
    const TYPE_QUICK = 'quick';
    const TYPE_FULL = 'full';
    const TYPE_SCHEDULED = 'scheduled';

    /**
     * Get the user that owns this scan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pages scanned.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(ScanPage::class);
    }

    /**
     * Get all issues found in this scan.
     */
    public function issues(): HasManyThrough
    {
        return $this->hasManyThrough(ScanIssue::class, ScanPage::class);
    }

    /**
     * Get the report for this scan.
     */
    public function report(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Scope for pending scans.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for running scans.
     */
    public function scopeRunning($query)
    {
        return $query->where('status', self::STATUS_RUNNING);
    }

    /**
     * Scope for completed scans.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for failed scans.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for user's scans.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if scan is still pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if scan is currently running.
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Check if scan has completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if scan has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark scan as running.
     */
    public function markAsRunning(): void
    {
        $this->update([
            'status' => self::STATUS_RUNNING,
            'started_at' => now(),
        ]);
    }

    /**
     * Mark scan as completed with results.
     */
    public function markAsCompleted(array $results): void
    {
        $this->update(array_merge([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ], $results));
    }

    /**
     * Mark scan as failed with error message.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Calculate and set the grade based on score.
     */
    public function calculateGrade(): void
    {
        $score = $this->score;

        if ($score >= 90) {
            $this->grade = 'A';
        } elseif ($score >= 80) {
            $this->grade = 'B';
        } elseif ($score >= 70) {
            $this->grade = 'C';
        } elseif ($score >= 60) {
            $this->grade = 'D';
        } else {
            $this->grade = 'F';
        }
    }

    /**
     * Get formatted score display.
     */
    public function getScoreDisplayAttribute(): string
    {
        return $this->score !== null ? number_format($this->score, 0) . '/100' : 'N/A';
    }

    /**
     * Get the domain from the URL.
     */
    public function getDomainAttribute(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }
}
