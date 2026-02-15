<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'stripe_id',
        'plan',
        'scan_count',
        'scan_limit',
        'trial_ends_at',
        'marketing_emails_enabled',
        'system_emails_enabled',
        'company_name',
        'company_logo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'scan_count' => 'integer',
            'scan_limit' => 'integer',
            'trial_ends_at' => 'datetime',
            'marketing_emails_enabled' => 'boolean',
            'system_emails_enabled' => 'boolean',
        ];
    }

    /**
     * Check if the user wants to receive emails of the given category.
     */
    public function wantsEmail(string $category): bool
    {
        return match ($category) {
            'marketing' => $this->marketing_emails_enabled,
            'system' => $this->system_emails_enabled,
            default => true,
        };
    }

    /**
     * Get the user's scans.
     */
    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    /**
     * Get the user's scheduled scans.
     */
    public function scheduledScans(): HasMany
    {
        return $this->hasMany(ScanSchedule::class);
    }

    /**
     * Get active scheduled scans.
     */
    public function activeScheduledScans(): HasMany
    {
        return $this->hasMany(ScanSchedule::class)->where('is_active', true);
    }

    /**
     * Get the user's reports.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the Plan model for this user.
     */
    public function planModel(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan', 'slug');
    }

    /**
     * Get the user's plan slug.
     */
    public function getPlanAttribute($value): string
    {
        return $value ?? 'free';
    }

    /**
     * Check if user is on a paid plan.
     */
    public function isPaid(): bool
    {
        return in_array($this->plan, ['monthly', 'agency']);
    }

    /**
     * Check if user has remaining scans.
     */
    public function hasScansRemaining(): bool
    {
        return $this->scan_count < $this->getScanLimit();
    }

    /**
     * Increment scan count.
     */
    public function incrementScanCount(): void
    {
        $this->increment('scan_count');
    }

    /**
     * Reset monthly scan count.
     */
    public function resetMonthlyScans(): void
    {
        $this->update(['scan_count' => 0]);
    }

    /**
     * Get the scan limit from the Plan model.
     */
    public function getScanLimit(): int
    {
        try {
            $plan = Plan::findBySlug($this->plan);

            if ($plan) {
                return $plan->scan_limit;
            }
        } catch (\Throwable) {
            // Plan table may not exist (e.g., during testing)
        }

        return match ($this->plan) {
            'monthly' => 50,
            'agency' => 200,
            default => 5,
        };
    }

    /**
     * Get the max pages per scan from the Plan model.
     */
    public function getMaxPagesPerScan(): int
    {
        try {
            $plan = Plan::findBySlug($this->plan);

            if ($plan) {
                return $plan->page_limit_per_scan;
            }
        } catch (\Throwable) {
            // Plan table may not exist (e.g., during testing)
        }

        return match ($this->plan) {
            'monthly' => 100,
            'agency' => 200,
            default => 5,
        };
    }

    /**
     * Get the scheduled scan limit from the Plan model.
     */
    public function getScheduledScanLimit(): int
    {
        try {
            $plan = Plan::findBySlug($this->plan);

            if ($plan) {
                return $plan->scheduled_scan_limit;
            }
        } catch (\Throwable) {
            // Plan table may not exist (e.g., during testing)
        }

        return match ($this->plan) {
            'monthly' => 5,
            'agency' => 25,
            default => 0,
        };
    }
}
