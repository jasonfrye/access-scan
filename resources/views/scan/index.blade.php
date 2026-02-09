@extends('layouts.guest')

@section('title', 'Free ADA/WCAG Accessibility Scanner')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section -->
    <div class="max-w-6xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                Is Your Website <span class="text-indigo-600">ADA Compliant?</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Scan your website for WCAG 2.1 accessibility issues. Get a detailed report with fix recommendations.
            </p>
        </div>

        <!-- Scan Form -->
        <div class="mb-16">
            @livewire('scan.index')
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-indigo-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">WCAG 2.1 AA Standard</h3>
                <p class="text-gray-600">Comprehensive checks against international accessibility guidelines</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Instant Results</h3>
                <p class="text-gray-600">Get your accessibility score in minutes with actionable recommendations</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-purple-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Detailed Reports</h3>
                <p class="text-gray-600">PDF reports with screenshots and code-level fix instructions</p>
            </div>
        </div>

        <!-- Pricing Preview -->
        <div class="bg-gray-900 rounded-2xl p-8 text-white">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold mb-2">Simple, Transparent Pricing</h2>
                <p class="text-gray-400">Start free, upgrade when you need more</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="text-sm text-gray-400 mb-2">Free</div>
                    <div class="text-3xl font-bold mb-4">$0<span class="text-sm font-normal text-gray-400">/mo</span></div>
                    <ul class="space-y-2 text-sm text-gray-300 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            1 free scan
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Summary results
                        </li>
                    </ul>
                    <button class="w-full py-2 border border-gray-600 rounded-lg hover:bg-gray-700 transition-colors">Current</button>
                </div>
                <div class="bg-indigo-600 rounded-xl p-6 ring-4 ring-indigo-500/30">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm text-indigo-200">Pro</div>
                        <span class="px-2 py-1 bg-white text-indigo-600 text-xs font-bold rounded">POPULAR</span>
                    </div>
                    <div class="text-3xl font-bold mb-4">$29<span class="text-sm font-normal text-indigo-200">/mo</span></div>
                    <ul class="space-y-2 text-sm text-indigo-100 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Unlimited scans
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Detailed reports
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            PDF export
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-2 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors text-center">Start Free Trial</a>
                </div>
                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="text-sm text-gray-400 mb-2">Lifetime</div>
                    <div class="text-3xl font-bold mb-4">$197<span class="text-sm font-normal text-gray-400">one-time</span></div>
                    <ul class="space-y-2 text-sm text-gray-300 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Everything in Pro
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Forever access
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Priority support
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-2 border border-gray-600 rounded-lg hover:bg-gray-700 transition-colors text-center">Get Lifetime</a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-sm text-gray-500">
            <p>Trusted by {{ number_format(2000) }}+ websites • WCAG 2.1 AA Standard • No credit card required</p>
        </div>
    </div>
</div>
@endsection
