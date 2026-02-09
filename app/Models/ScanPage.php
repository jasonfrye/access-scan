<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScanPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'url',
        'status',
        'issues_count',
        'errors_count',
        'warnings_count',
        'notices_count',
        'page_title',
        'http_status',
        'dom_depth',
        'load_time_ms',
    ];

    protected $casts = [
        'issues_count' => 'integer',
        'errors_count' => 'integer',
        'warnings_count' => 'integer',
        'notices_count' => 'integer',
        'dom_depth' => 'integer',
        'load_time_ms' => 'integer',
    ];

    /**
     * Get the scan that owns this page.
     */
    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Get the issues for this page.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(ScanIssue::class);
    }

    /**
     * Get the domain from the URL.
     */
    public function getDomainAttribute(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Get the path from the URL.
     */
    public function getPathAttribute(): string
    {
        return parse_url($this->url, PHP_URL_PATH) ?? '/';
    }
}
