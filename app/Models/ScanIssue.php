<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_page_id',
        'type',
        'code',
        'message',
        'context',
        'selector',
        'wcag_principle',
        'wcag_guideline',
        'wcag_criterion',
        'wcag_level',
        'impact',
        'recommendation',
        'help_url',
    ];

    protected $casts = [
        'type' => 'string',
        'wcag_level' => 'string',
        'impact' => 'string',
    ];

    /**
     * Issue type constants.
     */
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_NOTICE = 'notice';

    /**
     * WCAG level constants.
     */
    const LEVEL_A = 'A';
    const LEVEL_AA = 'AA';
    const LEVEL_AAA = 'AAA';

    /**
     * Impact constants.
     */
    const IMPACT_CRITICAL = 'critical';
    const IMPACT_SERIOUS = 'serious';
    const IMPACT_MODERATE = 'moderate';
    const IMPACT_MINOR = 'minor';

    /**
     * Get the page that owns this issue.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(ScanPage::class, 'scan_page_id');
    }

    /**
     * Convenience method for page relationship.
     */
    public function scanPage(): BelongsTo
    {
        return $this->page();
    }

    /**
     * Scope for errors.
     */
    public function scopeErrors($query)
    {
        return $query->where('type', self::TYPE_ERROR);
    }

    /**
     * Scope for warnings.
     */
    public function scopeWarnings($query)
    {
        return $query->where('type', self::TYPE_WARNING);
    }

    /**
     * Scope for notices.
     */
    public function scopeNotices($query)
    {
        return $query->where('type', self::TYPE_NOTICE);
    }

    /**
     * Scope for WCAG Level A.
     */
    public function scopeLevelA($query)
    {
        return $query->where('wcag_level', self::LEVEL_A);
    }

    /**
     * Scope for WCAG Level AA.
     */
    public function scopeLevelAA($query)
    {
        return $query->where('wcag_level', self::LEVEL_AA);
    }

    /**
     * Check if this is an error.
     */
    public function isError(): bool
    {
        return $this->type === self::TYPE_ERROR;
    }

    /**
     * Check if this is a warning.
     */
    public function isWarning(): bool
    {
        return $this->type === self::TYPE_WARNING;
    }

    /**
     * Check if this is a notice.
     */
    public function isNotice(): bool
    {
        return $this->type === self::TYPE_NOTICE;
    }

    /**
     * Get the WCAG reference (e.g., "WCAG 2.1 Level A - 1.1.1").
     */
    public function getWcagReferenceAttribute(): string
    {
        $level = $this->wcag_level ?? 'A';
        $principle = $this->wcag_principle ?? '';
        $guideline = $this->wcag_guideline ?? '';
        $criterion = $this->wcag_criterion ?? '';

        return "WCAG 2.1 Level {$level} - {$principle}.{$guideline}.{$criterion}";
    }

    /**
     * Get the full criterion code (e.g., "WCAG2AA.Principle1.Guideline1_1.1_1_1.H37").
     */
    public function getFullCriterionCodeAttribute(): string
    {
        return $this->code ?? 'Unknown';
    }
}
