<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'user_id',
        'format',
        'file_path',
        'file_size',
        'generated_at',
        'expires_at',
    ];

    protected $casts = [
        'format' => 'string',
        'file_size' => 'integer',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Format constants.
     */
    const FORMAT_PDF = 'pdf';
    const FORMAT_HTML = 'html';
    const FORMAT_CSV = 'csv';
    const FORMAT_JSON = 'json';

    /**
     * Get the scan that owns this report.
     */
    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Get the user that owns this report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for PDF reports.
     */
    public function scopePdf($query)
    {
        return $query->where('format', self::FORMAT_PDF);
    }

    /**
     * Scope for HTML reports.
     */
    public function scopeHtml($query)
    {
        return $query->where('format', self::FORMAT_HTML);
    }

    /**
     * Scope for CSV reports.
     */
    public function scopeCsv($query)
    {
        return $query->where('format', self::FORMAT_CSV);
    }

    /**
     * Scope for JSON reports.
     */
    public function scopeJson($query)
    {
        return $query->where('format', self::FORMAT_JSON);
    }

    /**
     * Check if this is a PDF report.
     */
    public function isPdf(): bool
    {
        return $this->format === self::FORMAT_PDF;
    }

    /**
     * Check if this report has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file extension.
     */
    public function getExtensionAttribute(): string
    {
        return match ($this->format) {
            self::FORMAT_PDF => 'pdf',
            self::FORMAT_HTML => 'html',
            self::FORMAT_CSV => 'csv',
            self::FORMAT_JSON => 'json',
            default => 'txt',
        };
    }
}
