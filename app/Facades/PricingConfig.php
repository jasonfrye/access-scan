<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\PricingConfig getActive()
 * @method static \App\Models\PricingConfig getForVisitor()
 * @method static \App\Models\PricingConfig getById(int $id)
 * @method static \Illuminate\Database\Eloquent\Collection getAll()
 * @method static void assignToVisitor(\App\Models\PricingConfig $config)
 * @method static \App\Models\PricingConfig create(array $data)
 * @method static bool update(\App\Models\PricingConfig $config, array $data)
 * @method static bool delete(\App\Models\PricingConfig $config)
 * @method static bool activate(\App\Models\PricingConfig $config)
 */
class PricingConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'pricing-config';
    }
}
