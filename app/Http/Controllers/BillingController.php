<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

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
        
        return view('billing.pricing', [
            'plans' => $plans,
            'stripeKey' => config('cashier.key'),
        ]);
    }

    /**
     * Show billing dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('billing.index', [
            'user' => $user,
            'subscription' => $user->subscription(),
            'invoices' => $user->invoices(),
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
        if (!$user->stripe_id) {
            $this->stripeService->createCustomer($user);
        }

        $checkoutUrl = $this->stripeService->createCheckoutSession(
            $user,
            $plan->stripe_price_id,
            route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
            route('billing.cancel')
        );

        return redirect($checkoutUrl);
    }

    /**
     * Handle lifetime one-time purchase.
     */
    protected function handleLifetimePurchase($user, $plan)
    {
        $payment = $this->stripeService->createLifetimePayment(
            $user,
            $plan->price * 100, // Convert to cents
            $plan->stripe_product_id
        );

        // For lifetime, we'll handle via frontend or redirect to payment
        return view('billing.lifetime-checkout', [
            'clientSecret' => $payment['client_secret'],
            'amount' => $plan->price,
            'plan' => $plan,
        ]);
    }

    /**
     * Subscription checkout success.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        Log::info('Subscription checkout success', [
            'session_id' => $sessionId,
        ]);

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

        if (!$subscription) {
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

        if (!$subscription || $subscription->canceled()) {
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

        if (!$user->stripe_id) {
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
            'vendor' => 'AccessScan',
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
