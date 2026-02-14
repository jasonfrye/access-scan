<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class SetupStripeProducts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stripe:setup-products
                            {--force : Force recreation of products even if they exist}';

    /**
     * The console command description.
     */
    protected $description = 'Set up Stripe products and prices for Access Report Card plans';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! config('services.stripe.secret')) {
            $this->error('Stripe secret key not found. Please set STRIPE_SECRET in your .env file.');

            return self::FAILURE;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $this->info('Setting up Stripe products and prices...');
        $this->newLine();

        // Create Monthly Plan
        $this->info('Creating Pro Monthly plan...');
        $monthlyProduct = $this->createProduct(
            'Access Report Card Pro (Monthly)',
            'Monthly subscription to Access Report Card Pro with 50 scans per month'
        );

        $monthlyPrice = $this->createPrice(
            $monthlyProduct->id,
            2900, // $29.00
            'month'
        );

        $this->line("  Product ID: {$monthlyProduct->id}");
        $this->line("  Price ID: {$monthlyPrice->id}");
        $this->updateEnvFile('STRIPE_PRICE_MONTHLY', $monthlyPrice->id);
        $this->newLine();

        // Create Lifetime Plan
        $this->info('Creating Lifetime plan...');
        $lifetimeProduct = $this->createProduct(
            'Access Report Card Lifetime',
            'One-time payment for lifetime access to Access Report Card with 1,000 scans per month'
        );

        $lifetimePrice = $this->createPrice(
            $lifetimeProduct->id,
            19700, // $197.00
            null // one-time payment
        );

        $this->line("  Product ID: {$lifetimeProduct->id}");
        $this->line("  Price ID: {$lifetimePrice->id}");
        $this->updateEnvFile('STRIPE_PRICE_LIFETIME', $lifetimePrice->id);
        $this->newLine();

        // Update plans table
        $this->info('Updating plans table...');
        Plan::where('slug', 'monthly')->update(['stripe_price_id' => $monthlyPrice->id]);
        Plan::where('slug', 'lifetime')->update(['stripe_lifetime_price_id' => $lifetimePrice->id]);

        $this->newLine();
        $this->info('✅ Stripe products and prices created successfully!');
        $this->newLine();
        $this->line('Add these to your .env file:');
        $this->line("STRIPE_PRICE_MONTHLY={$monthlyPrice->id}");
        $this->line("STRIPE_PRICE_LIFETIME={$lifetimePrice->id}");
        $this->newLine();
        $this->line('Your .env file has been updated automatically.');

        return self::SUCCESS;
    }

    /**
     * Create a Stripe product.
     */
    protected function createProduct(string $name, string $description): Product
    {
        try {
            return Product::create([
                'name' => $name,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            $this->error("Failed to create product: {$e->getMessage()}");
            exit(1);
        }
    }

    /**
     * Create a Stripe price.
     */
    protected function createPrice(string $productId, int $amountCents, ?string $recurring = null): Price
    {
        try {
            $priceData = [
                'product' => $productId,
                'unit_amount' => $amountCents,
                'currency' => 'usd',
            ];

            if ($recurring) {
                $priceData['recurring'] = ['interval' => $recurring];
            }

            return Price::create($priceData);
        } catch (\Exception $e) {
            $this->error("Failed to create price: {$e->getMessage()}");
            exit(1);
        }
    }

    /**
     * Update .env file with new value.
     */
    protected function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        // Check if key exists
        if (preg_match("/^{$key}=/m", $envContent)) {
            // Update existing key
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            // Add new key
            $envContent .= "\n{$key}={$value}\n";
        }

        file_put_contents($envPath, $envContent);
    }
}
