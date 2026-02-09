<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'config',
        'traffic_split',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'traffic_split' => 'integer',
    ];

    /**
     * Get the default pricing config.
     */
    public static function getActiveConfig(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Check if this config is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the plan names from config.
     */
    public function getPlanNamesAttribute(): array
    {
        return $this->config['plans'] ?? [];
    }

    /**
     * Get the CTA text.
     */
    public function getCtaTextAttribute(): string
    {
        return $this->config['cta']['text'] ?? 'Get Started';
    }

    /**
     * Get the highlighted plan slug.
     */
    public function getHighlightedPlanAttribute(): ?string
    {
        return $this->config['highlighted_plan'] ?? null;
    }

    /**
     * Get feature list for a specific plan.
     */
    public function getPlanFeatures(string $planSlug): array
    {
        return $this->config['plans'][$planSlug]['features'] ?? [];
    }

    /**
     * Get price for a specific plan and cycle.
     */
    public function getPlanPrice(string $planSlug, string $cycle = 'monthly'): ?float
    {
        $prices = $this->config['plans'][$planSlug]['price'] ?? null;
        if (!$prices) {
            return null;
        }

        return $prices[$cycle] ?? $prices['monthly'] ?? null;
    }

    /**
     * Scope for active configs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
