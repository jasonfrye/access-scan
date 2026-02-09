@extends('layouts.app')

@section('title', 'Scan Results - ' . $scan->domain . ' - AccessScan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('home') }}" class="text-white/80 hover:text-white text-sm mb-2 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Back to Scanner
                    </a>
                    <h1 class="text-2xl font-bold">{{ $scan->domain }}</h1>
                    <p class="text-white/80 text-sm mt-1">{{ $scan->url }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-white/70">Scanned {{ $scan->completed_at?->diffForHumans() ?? 'recently' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Score Overview (Limited for guests) -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <!-- Overall Score -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="text-sm text-gray-500 mb-2">ACCESSIBILITY SCORE</div>
                <div class="text-6xl font-bold @if($scan->grade === 'A' || $scan->score >= 90) text-green-600 @elseif($scan->grade === 'B' || $scan->score >= 70) text-yellow-600 @elseif($scan->grade === 'C' || $scan->score >= 50) text-orange-500 @else text-red-600 @endif">
                    {{ $scan->grade ?? 'N/A' }}
                </div>
                <div class="text-2xl text-gray-700 mt-1">
                    {{ number_format($scan->score ?? 0, 0) }}<span class="text-lg text-gray-400">/100</span>
                </div>
            </div>

            <!-- Issues Summary -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="text-sm text-gray-500 mb-2">TOTAL ISSUES</div>
                <div class="text-5xl font-bold text-gray-800">{{ number_format($scan->issues_found ?? 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">across {{ $scan->pages_scanned ?? 1 }} page(s)</div>
            </div>

            <!-- Errors -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-red-600">{{ number_format($scan->errors_count ?? 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">Errors</div>
            </div>

            <!-- Warnings -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-yellow-600">{{ number_format($scan->warnings_count ?? 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">Warnings</div>
            </div>
        </div>

        <!-- Guest Teaser Banner -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 mb-8 text-white">
            <div class="flex items-start gap-6">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold mb-2">Unlock Full Results</h2>
                    <p class="text-white/90 mb-4">
                        Sign up for free to access your detailed scan report with:
                    </p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Detailed WCAG compliance breakdown
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Specific code snippets and fixes
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Download PDF & CSV reports
                        </li>
                    </ul>
                    <div class="flex gap-4">
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-indigo-600 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                            Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-white/20 text-white rounded-lg font-medium hover:bg-white/30 transition-colors">
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Issues (Limited) -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Top Issues Found</h2>

            @if($scan->issues && $scan->issues->count() > 0)
                <div class="space-y-4">
                    @foreach($scan->issues->take(5) as $issue)
                        <div class="border border-gray-200 rounded-xl p-4 @if($issue->type === 'error') border-l-4 border-l-red-500 @elseif($issue->type === 'warning') border-l-4 border-l-yellow-500 @else border-l-4 border-l-blue-500 @endif">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    @if($issue->type === 'error')
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @elseif($issue->type === 'warning')
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $issue->wcag_reference ?? 'WCAG Issue' }}</span>
                                        <span class="px-2 py-0.5 text-xs rounded-full @if($issue->type === 'error') bg-red-100 text-red-700 @elseif($issue->type === 'warning') bg-yellow-100 text-yellow-700 @else bg-blue-100 text-blue-700 @endif">
                                            {{ ucfirst($issue->type) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($issue->message, 150) }}</p>
                                    @if($loop->index < 4)
                                        <div class="text-sm text-gray-500">
                                            <strong>Fix:</strong> {{ Str::limit($issue->recommendation ?? 'Review the issue and add necessary accessibility attributes.', 100) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($scan->issues->count() > 5)
                    <div class="text-center mt-6">
                        <p class="text-gray-500">...and {{ $scan->issues->count() - 5 }} more issues</p>
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>No issues found! Your site appears to be accessible.</p>
                </div>
            @endif
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <p class="text-gray-600 mb-4">Want to fix these issues?</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                Create Free Account
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
