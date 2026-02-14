<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeService
{
    protected ?StripeClient $stripe = null;

    protected function stripe(): StripeClient
    {
        if ($this->stripe === null) {
            $this->stripe = new StripeClient(config('cashier.secret'));
        }

        return $this->stripe;
    }

    /**
     * Create a Stripe customer for a user.
     */
    public function createCustomer(User $user): string
    {
        $customer = $this->stripe()->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_id' => $customer->id]);

        Log::info('Stripe customer created', [
            'user_id' => $user->id,
            'customer_id' => $customer->id,
        ]);

        return $customer->id;
    }

    /**
     * Create a subscription for a user.
     */
    public function createSubscription(User $user, string $priceId, string $plan): Subscription
    {
        if (! $user->stripe_id) {
            $this->createCustomer($user);
        }

        $subscription = $this->stripe()->subscriptions->create([
            'customer' => $user->stripe_id,
            'items' => [
                ['price' => $priceId],
            ],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
            'metadata' => [
                'user_id' => $user->id,
                'plan' => $plan,
            ],
        ]);

        Log::info('Stripe subscription created', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan' => $plan,
        ]);

        return $subscription;
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(string $subscriptionId): Subscription
    {
        $subscription = $this->stripe()->subscriptions->cancel($subscriptionId);

        Log::info('Stripe subscription cancelled', [
            'subscription_id' => $subscriptionId,
        ]);

        return $subscription;
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(string $subscriptionId): Subscription
    {
        $subscription = $this->stripe()->subscriptions->retrieve($subscriptionId);

        $updatedSubscription = $this->stripe()->subscriptions->update($subscriptionId, [
            'cancel_at_period_end' => false,
        ]);

        Log::info('Stripe subscription resumed', [
            'subscription_id' => $subscriptionId,
        ]);

        return $updatedSubscription;
    }

    /**
     * Get subscription status.
     */
    public function getSubscriptionStatus(string $subscriptionId): ?object
    {
        try {
            return $this->stripe()->subscriptions->retrieve($subscriptionId);
        } catch (ApiErrorException $e) {
            return null;
        }
    }

    /**
     * Create checkout session for subscription.
     */
    public function createCheckoutSession(User $user, string $priceId, string $successUrl, string $cancelUrl): string
    {
        if (! $user->stripe_id) {
            $this->createCustomer($user);
        }

        $session = $this->stripe()->checkout->sessions->create([
            'customer' => $user->stripe_id,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        return $session->url;
    }

    /**
     * Create portal session for billing management.
     */
    public function createPortalSession(User $user, string $returnUrl): string
    {
        $session = $this->stripe()->billingPortal->sessions->create([
            'customer' => $user->stripe_id,
            'return_url' => $returnUrl,
        ]);

        return $session->url;
    }

    /**
     * Handle webhook event.
     */
    public function handleWebhook(array $payload, string $signature): ?string
    {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            config('cashier.webhook.secret')
        );

        return match ($event->type) {
            'customer.subscription.created' => $this->handleSubscriptionCreated($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            'invoice.payment_succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'invoice.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            default => null,
        };
    }

    protected function handleSubscriptionCreated(object $subscription): string
    {
        $userId = $subscription->metadata->user_id ?? null;

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'plan' => $subscription->metadata->plan ?? 'monthly',
                ]);
            }
        }

        return 'subscription_created';
    }

    protected function handleSubscriptionUpdated(object $subscription): string
    {
        $userId = $subscription->metadata->user_id ?? null;

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'plan' => $subscription->status === 'active'
                        ? ($subscription->metadata->plan ?? 'monthly')
                        : 'free',
                ]);
            }
        }

        return 'subscription_updated';
    }

    protected function handleSubscriptionDeleted(object $subscription): string
    {
        $userId = $subscription->metadata->user_id ?? null;

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->update([
                    'plan' => 'free',
                ]);
            }
        }

        return 'subscription_deleted';
    }

    protected function handlePaymentSucceeded(object $invoice): string
    {
        return 'payment_succeeded';
    }

    protected function handlePaymentFailed(object $invoice): string
    {
        return 'payment_failed';
    }

    protected function handleCheckoutCompleted(object $session): string
    {
        return 'checkout_completed';
    }
}
