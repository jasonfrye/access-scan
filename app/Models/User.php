<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_id',
        'plan',
        'scan_count',
        'scan_limit',
        'trial_ends_at',
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
        ];
    }

    /**
     * Get the user's plan.
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
        return in_array($this->plan, ['monthly', 'lifetime']);
    }

    /**
     * Check if user has remaining scans.
     */
    public function hasScansRemaining(): bool
    {
        return $this->scan_count < $this->scan_limit;
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
     * Get the scan limit based on plan.
     */
    public function getScanLimit(): int
    {
        return match ($this->plan) {
            'monthly' => 50,
            'lifetime' => 100,
            default => $this->scan_limit ?? 5, // Free plan default
        };
    }

    /**
     * Get the max pages per scan based on plan.
     */
    public function getMaxPagesPerScan(): int
    {
        return match ($this->plan) {
            'monthly' => 100,
            'lifetime' => 500,
            default => 5, // Free plan limited to 5 pages
        };
    }
}
