@extends('layouts.guest')

@section('title', 'Scan Results - ' . $scan->domain . ' - Access Report Card')

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
                    <div class="text-sm text-white/70">Scanned {{ $scan->completed_at->diffForHumans() }}</div>
                    <div class="flex items-center gap-3 mt-2">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <button class="px-4 py-2 bg-white text-indigo-600 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Score Overview -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <!-- Overall Score -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="text-sm text-gray-500 mb-2">ACCESSIBILITY SCORE</div>
                <div class="text-6xl font-bold" :class="$scan->score >= 90 ? 'text-green-600' : ($scan->score >= 70 ? 'text-yellow-600' : ($scan->score >= 50 ? 'text-orange-500' : 'text-red-600'))">
                    {{ $scan->grade ?? 'N/A' }}
                </div>
                <div class="text-2xl text-gray-700 mt-1">
                    {{ number_format($scan->score, 0) }}<span class="text-lg text-gray-400">/100</span>
                </div>
            </div>

            <!-- Errors -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $scan->errors_count }}</div>
                <div class="text-sm text-gray-500">Errors</div>
            </div>

            <!-- Warnings -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $scan->warnings_count }}</div>
                <div class="text-sm text-gray-500">Warnings</div>
            </div>

            <!-- Notices -->
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $scan->notices_count }}</div>
                <div class="text-sm text-gray-500">Notices</div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Issues List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Issues Found</h2>
                            <div class="flex items-center gap-2">
                                <select class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="all">All Issues</option>
                                    <option value="errors">Errors Only</option>
                                    <option value="warnings">Warnings Only</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($scan->pages as $page)
                            @foreach($page->issues as $issue)
                                <div class="p-6 hover:bg-gray-50 cursor-pointer transition-colors" x-data="{ expanded: false }">
                                    <div class="flex items-start gap-4">
                                        <!-- Issue Type Icon -->
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center @switch($issue->type) @case('error') bg-red-100 text-red-600 @break @case('warning') bg-yellow-100 text-yellow-600 @break @default bg-blue-100 text-blue-600 @endswitch">
                                            @switch($issue->type)
                                                @case('error')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                    @break
                                                @case('warning')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    @break
                                                @default
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endswitch
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full @switch($issue->type) @case('error') bg-red-100 text-red-700 @break @case('warning') bg-yellow-100 text-yellow-700 @break @default bg-blue-100 text-blue-700 @endswitch">
                                                    {{ ucfirst($issue->type) }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $issue->wcag_level }} Level</span>
                                                <span class="text-xs text-gray-500">{{ $issue->wcag_reference }}</span>
                                            </div>
                                            <h3 class="font-medium text-gray-900 mb-1">{{ $issue->message }}</h3>
                                            <p class="text-sm text-gray-500 mb-2">{{ parse_url($page->url, PHP_URL_PATH) }}</p>
                                            
                                            <!-- Expandable Content -->
                                            <div x-show="expanded" x-collapse class="mt-4 space-y-3">
                                                <!-- Code Context -->
                                                @if($issue->context)
                                                    <div>
                                                        <div class="text-xs font-medium text-gray-500 mb-1">AFFECTED ELEMENT</div>
                                                        <code class="block bg-gray-900 text-gray-100 p-3 rounded-lg text-sm overflow-x-auto">{{ $issue->context }}</code>
                                                    </div>
                                                @endif

                                                <!-- Recommendation -->
                                                <div>
                                                    <div class="text-xs font-medium text-gray-500 mb-1">HOW TO FIX</div>
                                                    <p class="text-sm text-gray-700">{{ $issue->recommendation }}</p>
                                                </div>

                                                <!-- WCAG Reference -->
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ $issue->help_url }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                                        Learn more
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <button @click="expanded = !expanded" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Issues Found!</h3>
                                <p class="text-gray-500">This page passed all accessibility checks.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pages Scanned -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Pages Scanned</h3>
                    <div class="space-y-3">
                        @foreach($scan->pages->take(5) as $page)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
                                    <span class="text-sm text-gray-600 truncate">{{ parse_url($page->url, PHP_URL_PATH) }}</span>
                                </div>
                                <span class="text-xs text-gray-400 flex-shrink-0 ml-2">{{ $page->issues_count }} issues</span>
                            </div>
                        @endforeach
                        @if($scan->pages->count() > 5)
                            <div class="text-center text-sm text-gray-500 pt-2">
                                +{{ $scan->pages->count() - 5 }} more pages
                            </div>
                        @endif
                    </div>
                </div>

                <!-- WCAG Summary -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">WCAG Compliance</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Level A</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500" style="width: {{ $scan->issues->where('wcag_level', 'A')->count() > 0 ? '70%' : '100%' }}"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $scan->issues->where('wcag_level', 'A')->count() }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Level AA</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-500" style="width: {{ $scan->issues->where('wcag_level', 'AA')->count() > 0 ? '60%' : '100%' }}"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $scan->issues->where('wcag_level', 'AA')->count() }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Level AAA</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-orange-500" style="width: {{ $scan->issues->where('wcag_level', 'AAA')->count() > 0 ? '40%' : '100%' }}"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $scan->issues->where('wcag_level', 'AAA')->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button class="w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Download PDF Report
                        </button>
                        <button class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                            Share Results
                        </button>
                        <button class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Re-scan Website
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
