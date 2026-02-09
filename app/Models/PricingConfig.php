<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'config',
        'is_active',
        'traffic_split',
        'activated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'traffic_split' => 'integer',
        'activated_at' => 'datetime',
    ];

    /**
     * Get the active pricing configuration.
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Set this config as the active one.
     */
    public function setAsActive(): void
    {
        // Deactivate all configs
        static::query()->update(['is_active' => false]);

        // Activate this one
        $this->update(['is_active' => true, 'activated_at' => now()]);
    }

    /**
     * Get a specific plan from the config.
     */
    public function getPlan(string $planKey): ?array
    {
        $config = $this->config ?? [];
        return $config['plans'][$planKey] ?? null;
    }

    /**
     * Get all plans from the config.
     */
    public function getPlans(): array
    {
        return ($this->config ?? [])['plans'] ?? [];
    }

    /**
     * Get the highlighted plan key.
     */
    public function getHighlightedPlan(): ?string
    {
        return ($this->config ?? [])['highlighted_plan'] ?? null;
    }

    /**
     * Scope for active configs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered configs.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}
