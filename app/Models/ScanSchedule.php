<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'frequency',
        'next_run_at',
        'last_run_at',
        'is_active',
        'notify_on_regression',
        'email',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
        'notify_on_regression' => 'boolean',
    ];

    /**
     * Frequency constants.
     */
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';

    /**
     * Get the user that owns this schedule.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for due schedules.
     */
    public function scopeDue($query)
    {
        return $query->where('is_active', true)
            ->where('next_run_at', '<=', now());
    }

    /**
     * Check if schedule is daily.
     */
    public function isDaily(): bool
    {
        return $this->frequency === self::FREQUENCY_DAILY;
    }

    /**
     * Check if schedule is weekly.
     */
    public function isWeekly(): bool
    {
        return $this->frequency === self::FREQUENCY_WEEKLY;
    }

    /**
     * Check if schedule is monthly.
     */
    public function isMonthly(): bool
    {
        return $this->frequency === self::FREQUENCY_MONTHLY;
    }

    /**
     * Calculate the next run time based on frequency.
     */
    public function calculateNextRun(): void
    {
        $nextRun = match ($this->frequency) {
            self::FREQUENCY_DAILY => now()->addDay(),
            self::FREQUENCY_WEEKLY => now()->addWeek(),
            self::FREQUENCY_MONTHLY => now()->addMonth(),
            default => now()->addWeek(),
        };

        $this->next_run_at = $nextRun;
    }

    /**
     * Update the schedule after a scan runs.
     */
    public function updateAfterScan(): void
    {
        $this->last_run_at = now();
        $this->calculateNextRun();
        $this->save();
    }

    /**
     * Get the domain from the URL.
     */
    public function getDomainAttribute(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }
}
