<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'stripe_yearly_price_id',
        'stripe_lifetime_price_id',
        'price_monthly',
        'price_yearly',
        'price_lifetime',
        'scan_limit',
        'page_limit_per_scan',
        'scheduled_scan_limit',
        'has_pdf_export',
        'has_api_access',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'price_lifetime' => 'decimal:2',
        'scan_limit' => 'integer',
        'page_limit_per_scan' => 'integer',
        'scheduled_scan_limit' => 'integer',
        'has_pdf_export' => 'boolean',
        'has_api_access' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the plan by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Check if this is a paid plan.
     */
    public function isPaid(): bool
    {
        return $this->price_monthly > 0 || $this->price_yearly > 0 || $this->price_lifetime > 0;
    }

    /**
     * Get the price for a given billing cycle.
     */
    public function getPrice(string $cycle = 'monthly'): float
    {
        return match ($cycle) {
            'yearly' => $this->price_yearly,
            'lifetime' => $this->price_lifetime,
            default => $this->price_monthly,
        };
    }
}
