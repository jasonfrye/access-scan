@extends('layouts.app')

@section('title', 'Pricing - AccessScan')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Start free and upgrade when you need more scans, features, or priority support.
            </p>
        </div>

        <!-- Pricing Cards -->
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Free Plan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Free</h3>
                <p class="text-gray-500 mb-6">Perfect for trying out AccessScan</p>
                
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
                        <span class="text-gray-600">Basic PDF reports</span>
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

            <!-- Monthly Plan -->
            <div class="bg-white rounded-2xl shadow-xl border-2 border-indigo-600 p-8 relative">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-600 text-white px-4 py-1 rounded-full text-sm font-medium">
                    Most Popular
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pro Monthly</h3>
                <p class="text-gray-500 mb-6">For ongoing accessibility monitoring</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">$29</span>
                    <span class="text-gray-500">/month</span>
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

            <!-- Lifetime Plan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Lifetime</h3>
                <p class="text-gray-500 mb-6">Pay once, scan forever</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">$197</span>
                    <span class="text-gray-500">/one-time</span>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600 font-medium">Unlimited scans</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600 font-medium">Up to 500 pages per scan</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span class="text-gray-600">Scheduled scans</span>
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
                        <input type="hidden" name="plan" value="lifetime">
                        <button type="submit" class="block w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors">
                            Get Lifetime Access
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}?plan=lifetime" class="block w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors text-center">
                        Get Lifetime Access
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
                    <p class="text-gray-600">Yes! You can upgrade or downgrade your plan at any time. Monthly subscribers can switch to lifetime at any point.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">What payment methods do you accept?</h3>
                    <p class="text-gray-600">We accept all major credit cards through Stripe. For lifetime plans, we also accept wire transfers for orders over $500.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Is there a refund policy?</h3>
                    <p class="text-gray-600">Monthly subscriptions can be cancelled anytime with no refunds for partial months. Lifetime purchases have a 30-day money-back guarantee.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
