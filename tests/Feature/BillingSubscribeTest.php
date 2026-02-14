<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingSubscribeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PlanSeeder::class);
    }

    public function test_pricing_page_loads(): void
    {
        $response = $this->get(route('billing.pricing'));

        $response->assertStatus(200);
        $response->assertSee('Simple, Transparent Pricing');
        $response->assertSee('Monthly');
        $response->assertSee('Yearly');
    }

    public function test_pricing_page_shows_yearly_savings_badge(): void
    {
        $response = $this->get(route('billing.pricing'));

        $response->assertStatus(200);
        $response->assertSee('Save 17%');
    }

    public function test_subscribe_requires_authentication(): void
    {
        $response = $this->post(route('billing.subscribe'), [
            'plan' => 'monthly',
            'interval' => 'monthly',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_subscribe_validates_plan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('billing.subscribe'), [
            'plan' => 'invalid',
            'interval' => 'monthly',
        ]);

        $response->assertSessionHasErrors('plan');
    }

    public function test_subscribe_validates_interval(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('billing.subscribe'), [
            'plan' => 'monthly',
            'interval' => 'weekly',
        ]);

        $response->assertSessionHasErrors('interval');
    }

    public function test_subscribe_requires_interval(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('billing.subscribe'), [
            'plan' => 'monthly',
        ]);

        $response->assertSessionHasErrors('interval');
    }

    public function test_subscribe_monthly_uses_monthly_price_id(): void
    {
        $user = User::factory()->create();
        $plan = Plan::where('slug', 'monthly')->first();

        $this->mock(StripeService::class, function ($mock) use ($user, $plan) {
            $mock->shouldReceive('createCustomer')->once();
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->withArgs(function ($passedUser, $priceId) use ($user, $plan) {
                    return $passedUser->id === $user->id && $priceId === $plan->stripe_price_id;
                })
                ->andReturn('https://checkout.stripe.com/test');
        });

        $response = $this->actingAs($user)->post(route('billing.subscribe'), [
            'plan' => 'monthly',
            'interval' => 'monthly',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test');
    }

    public function test_subscribe_yearly_uses_yearly_price_id(): void
    {
        $user = User::factory()->create();
        $plan = Plan::where('slug', 'monthly')->first();

        $this->mock(StripeService::class, function ($mock) use ($user, $plan) {
            $mock->shouldReceive('createCustomer')->once();
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->withArgs(function ($passedUser, $priceId) use ($user, $plan) {
                    return $passedUser->id === $user->id && $priceId === $plan->stripe_yearly_price_id;
                })
                ->andReturn('https://checkout.stripe.com/test');
        });

        $response = $this->actingAs($user)->post(route('billing.subscribe'), [
            'plan' => 'monthly',
            'interval' => 'yearly',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test');
    }

    public function test_subscribe_agency_yearly_uses_yearly_price_id(): void
    {
        $user = User::factory()->create();
        $plan = Plan::where('slug', 'agency')->first();

        $this->mock(StripeService::class, function ($mock) use ($user, $plan) {
            $mock->shouldReceive('createCustomer')->once();
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->withArgs(function ($passedUser, $priceId) use ($user, $plan) {
                    return $passedUser->id === $user->id && $priceId === $plan->stripe_yearly_price_id;
                })
                ->andReturn('https://checkout.stripe.com/test');
        });

        $response = $this->actingAs($user)->post(route('billing.subscribe'), [
            'plan' => 'agency',
            'interval' => 'yearly',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test');
    }
}
