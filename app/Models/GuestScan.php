<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'email',
        'scan_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the associated scan.
     */
    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Check if email was provided.
     */
    public function hasEmail(): bool
    {
        return !empty($this->email);
    }

    /**
     * Get the domain from IP (for display).
     */
    public function getIpDisplayAttribute(): string
    {
        return $this->ip_address ?? 'Unknown';
    }

    /**
     * Scope for recent scans (within given hours).
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for scans from a specific IP.
     */
    public function scopeFromIp($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Check if this IP has scanned recently (within rate limit).
     */
    public static function hasRecentScanFromIp(string $ipAddress, int $hours = 24): bool
    {
        return static::fromIp($ipAddress)->recent($hours)->exists();
    }
}
