<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'source',
        'scan_id',
        'subscribed_at',
        'converted_at',
        'nurture_email_sent_at',
        'nurture_sequence_step',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'converted_at' => 'datetime',
        'nurture_email_sent_at' => 'datetime',
        'nurture_sequence_step' => 'integer',
    ];

    /**
     * Source constants.
     */
    const SOURCE_FREE_SCAN = 'free_scan';
    const SOURCE_SIGNUP = 'signup';
    const SOURCE_PRICING = 'pricing';

    /**
     * Get the associated scan.
     */
    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Scope for unconverted leads.
     */
    public function scopeUnconverted($query)
    {
        return $query->whereNull('converted_at');
    }

    /**
     * Scope for converted leads.
     */
    public function scopeConverted($query)
    {
        return $query->whereNotNull('converted_at');
    }

    /**
     * Scope for leads from free scans.
     */
    public function scopeFromFreeScans($query)
    {
        return $query->where('source', self::SOURCE_FREE_SCAN);
    }

    /**
     * Check if lead has been converted.
     */
    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }

    /**
     * Mark lead as converted.
     */
    public function markAsConverted(): void
    {
        $this->update(['converted_at' => now()]);
    }

    /**
     * Increment nurture sequence step.
     */
    public function advanceNurtureSequence(): void
    {
        $this->increment('nurture_sequence_step');
        $this->update(['nurture_email_sent_at' => now()]);
    }

    /**
     * Get the current nurture step.
     */
    public function getCurrentNurtureStepAttribute(): int
    {
        return $this->nurture_sequence_step ?? 0;
    }

    /**
     * Check if lead is ready for nurture email.
     */
    public function isReadyForNurture(): bool
    {
        // Ready if not converted and hasn't received email in 24+ hours
        if ($this->isConverted()) {
            return false;
        }

        if (!$this->nurture_email_sent_at) {
            return true;
        }

        return $this->nurture_email_sent_at->addDay()->isPast();
    }
}
