<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct(
        protected StripeService $stripeService
    ) {}

    /**
     * Show pricing page.
     */
    public function pricing()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        $user = Auth::user();
        $upgradeCredit = 0;

        if ($user && $user->plan === 'monthly') {
            $upgradeCredit = 29; // Credit their most recent monthly payment
        }

        return view('billing.pricing', [
            'plans' => $plans,
            'stripeKey' => config('cashier.key'),
            'upgradeCredit' => $upgradeCredit,
        ]);
    }

    /**
     * Show billing dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        try {
            $subscription = $user->subscription();
        } catch (\Exception) {
            $subscription = null;
        }

        try {
            $invoices = $user->invoices();
        } catch (\Exception) {
            $invoices = collect();
        }

        $charges = collect();
        if ($user->stripe_id) {
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $stripeCharges = $stripe->charges->all([
                    'customer' => $user->stripe_id,
                    'limit' => 20,
                ]);
                $charges = collect($stripeCharges->data)->filter(fn ($c) => $c->status === 'succeeded');
            } catch (\Exception) {
                // Stripe unavailable
            }
        }

        return view('billing.index', [
            'user' => $user,
            'subscription' => $subscription,
            'invoices' => $invoices,
            'charges' => $charges,
        ]);
    }

    /**
     * Initiate subscription checkout.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,lifetime',
        ]);

        $user = Auth::user();
        $plan = Plan::where('slug', $request->plan)->firstOrFail();

        if ($request->plan === 'lifetime') {
            return $this->handleLifetimePurchase($user, $plan);
        }

        return $this->handleSubscription($user, $plan);
    }

    /**
     * Handle monthly subscription.
     */
    protected function handleSubscription($user, $plan)
    {
        if (! $user->stripe_id) {
            $this->stripeService->createCustomer($user);
        }

        $checkoutUrl = $this->stripeService->createCheckoutSession(
            $user,
            $plan->stripe_price_id,
            route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
            route('billing.cancel')
        );

        return redirect($checkoutUrl);
    }

    /**
     * Handle lifetime one-time purchase.
     */
    protected function handleLifetimePurchase($user, $plan)
    {
        if (! $user->stripe_id) {
            $this->stripeService->createCustomer($user);
        }

        $discountAmount = $user->plan === 'monthly' ? 2900 : 0; // $29.00 in cents

        $checkoutUrl = $this->stripeService->createLifetimeCheckoutSession(
            $user,
            $plan->stripe_lifetime_price_id ?? $plan->stripe_price_id,
            route('billing.success').'?session_id={CHECKOUT_SESSION_ID}&plan=lifetime',
            route('billing.cancel'),
            $discountAmount
        );

        return redirect($checkoutUrl);
    }

    /**
     * Subscription checkout success.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $user = Auth::user();

        if ($sessionId) {
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                $session = $stripe->checkout->sessions->retrieve($sessionId, [
                    'expand' => ['subscription'],
                ]);

                // Handle lifetime one-time payment
                if ($session->mode === 'payment' && $session->payment_status === 'paid') {
                    $plan = Plan::where('slug', 'lifetime')->first();

                    $user->update([
                        'plan' => 'lifetime',
                        'scan_limit' => $plan?->scan_limit ?? 1000,
                    ]);

                    Log::info('User upgraded to lifetime after checkout', [
                        'user_id' => $user->id,
                    ]);
                }

                // Handle subscription checkout
                if ($session->subscription && $session->subscription->status === 'active') {
                    $plan = Plan::where('stripe_price_id', $session->subscription->items->data[0]->price->id)->first();

                    $user->update([
                        'plan' => $plan?->slug ?? 'monthly',
                        'scan_limit' => $plan?->scan_limit ?? 50,
                    ]);

                    Log::info('User plan updated after checkout', [
                        'user_id' => $user->id,
                        'plan' => $plan?->slug ?? 'monthly',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to retrieve checkout session', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('billing.index')
            ->with('success', 'Your subscription is now active!');
    }

    /**
     * Subscription checkout cancelled.
     */
    public function cancel()
    {
        return redirect()->route('billing.pricing')
            ->with('error', 'Checkout was cancelled. Please try again.');
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscription();

        if (! $subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $this->stripeService->cancelSubscription($subscription->stripe_id);

        $user->update(['plan' => 'free']);

        return back()->with('success', 'Your subscription has been cancelled.');
    }

    /**
     * Resume subscription.
     */
    public function resumeSubscription(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscription();

        if (! $subscription || $subscription->canceled()) {
            return back()->with('error', 'Cannot resume subscription.');
        }

        $this->stripeService->resumeSubscription($subscription->stripe_id);

        $user->update(['plan' => $subscription->metadata->plan ?? 'monthly']);

        return back()->with('success', 'Your subscription has been resumed.');
    }

    /**
     * Open Stripe billing portal.
     */
    public function portal(Request $request)
    {
        $user = Auth::user();

        if (! $user->stripe_id) {
            return back()->with('error', 'No billing account found.');
        }

        $portalUrl = $this->stripeService->createPortalSession(
            $user,
            route('billing.index')
        );

        return redirect($portalUrl);
    }

    /**
     * Download invoice.
     */
    public function invoice(string $id)
    {
        $user = Auth::user();

        return $user->downloadInvoice($id, [
            'vendor' => 'Access Report Card',
            'product' => 'Accessibility Scanning Service',
        ]);
    }

    /**
     * Handle Stripe webhooks.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $eventType = $this->stripeService->handleWebhook($payload, $signature);

            Log::info('Stripe webhook received', [
                'event_type' => $eventType,
            ]);

            return response('Webhook handled', 200);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response('Webhook error', 400);
        }
    }
}
