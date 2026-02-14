@extends('layouts.guest')

@section('title', 'Pricing - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 py-12" x-data="{ interval: 'monthly' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Start free and upgrade when you need more scans, features, or priority support.
            </p>

            <!-- Billing Toggle -->
            <div class="mt-8 flex items-center justify-center gap-3">
                <span class="text-sm font-medium" :class="interval === 'monthly' ? 'text-gray-900' : 'text-gray-500'">Monthly</span>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="interval === 'yearly'"
                    @click="interval = interval === 'monthly' ? 'yearly' : 'monthly'"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                    :class="interval === 'yearly' ? 'bg-indigo-600' : 'bg-gray-200'"
                >
                    <span
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="interval === 'yearly' ? 'translate-x-5' : 'translate-x-0'"
                    ></span>
                </button>
                <span class="text-sm font-medium" :class="interval === 'yearly' ? 'text-gray-900' : 'text-gray-500'">
                    Yearly
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Save 17%</span>
                </span>
            </div>
        </div>

        <!-- Pricing Cards -->
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Free Plan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Free</h3>
                <p class="text-gray-500 mb-6">Perfect for trying out Access Report Card</p>

                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">$0</span>
                    <span class="text-gray-500">/forever</span>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">5 scans per month</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Up to 5 pages per scan</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Summary reports</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Email support</span>
                    </li>
                </ul>

                <a href="{{ route('register') }}" class="block w-full py-3 px-4 text-center border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Get Started Free
                </a>
            </div>

            <!-- Pro Plan -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-600 p-8 relative">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-600 text-white px-4 py-1 rounded-full text-sm font-medium">
                    Most Popular
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Pro</h3>
                <p class="text-gray-500 mb-6">For ongoing accessibility monitoring</p>

                <div class="mb-6">
                    <template x-if="interval === 'monthly'">
                        <div>
                            <span class="text-4xl font-bold text-gray-900">$29</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                    </template>
                    <template x-if="interval === 'yearly'">
                        <div>
                            <span class="text-4xl font-bold text-gray-900">$290</span>
                            <span class="text-gray-500">/year</span>
                            <div class="text-sm text-green-600 font-medium mt-1">$24.17/mo &mdash; save $58/yr</div>
                        </div>
                    </template>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600 font-medium">50 scans per month</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600 font-medium">Up to 100 pages per scan</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Scheduled scans</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Detailed PDF & CSV exports</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Priority email support</span>
                    </li>
                </ul>

                @auth
                    <form action="{{ route('billing.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="monthly">
                        <input type="hidden" name="interval" :value="interval">
                        <button type="submit" class="block w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                            Subscribe Now
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=monthly" class="block w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors text-center">
                        Subscribe Now
                    </a>
                @endauth
            </div>

            <!-- Agency Plan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Agency</h3>
                <p class="text-gray-500 mb-6">For agencies managing multiple client sites</p>

                <div class="mb-6">
                    <template x-if="interval === 'monthly'">
                        <div>
                            <span class="text-4xl font-bold text-gray-900">$99</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                    </template>
                    <template x-if="interval === 'yearly'">
                        <div>
                            <span class="text-4xl font-bold text-gray-900">$890</span>
                            <span class="text-gray-500">/year</span>
                            <div class="text-sm text-green-600 font-medium mt-1">$74.17/mo &mdash; save $298/yr</div>
                        </div>
                    </template>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600 font-medium">200 scans per month</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600 font-medium">Up to 200 pages per scan</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">25 scheduled scans</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">White-label PDF reports</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">API access</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Priority support</span>
                    </li>
                </ul>

                @auth
                    <form action="{{ route('billing.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan" value="agency">
                        <input type="hidden" name="interval" :value="interval">
                        <button type="submit" class="block w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors">
                            Subscribe to Agency
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=agency" class="block w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors text-center">
                        Subscribe to Agency
                    </a>
                @endauth
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16 max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Frequently Asked Questions</h2>

            <div class="space-y-6">
                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Can I change plans later?</h3>
                    <p class="text-gray-600">Yes! You can upgrade or downgrade your plan at any time from your billing dashboard.</p>
                </div>

                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">What payment methods do you accept?</h3>
                    <p class="text-gray-600">We accept all major credit cards through Stripe.</p>
                </div>

                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Is there a refund policy?</h3>
                    <p class="text-gray-600">All subscriptions can be cancelled anytime with no refunds for partial months.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
