@extends('layouts.guest')

@section('title', 'Scan Results - ' . $scan->domain . ' - AccessScan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-white text-sm mb-2 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Back to Dashboard
                    </a>
                    <h1 class="text-2xl font-bold">{{ $scan->domain }}</h1>
                    <p class="text-white/80 text-sm mt-1">{{ $scan->url }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-white/70">Scanned {{ $scan->completed_at->diffForHumans() }}</div>
                    <div class="flex items-center gap-2 mt-2">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                            New Scan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Score Overview -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="text-sm text-gray-500 mb-2">ACCESSIBILITY SCORE</div>
                <div class="text-6xl font-bold @if($scan->score >= 90) text-green-600 @elseif($scan->score >= 70) text-yellow-600 @elseif($scan->score >= 50) text-orange-500 @else text-red-600 @endif">
                    {{ $scan->grade ?? 'N/A' }}
                </div>
                <div class="text-2xl text-gray-700 mt-1">
                    {{ number_format($scan->score, 0) }}<span class="text-lg text-gray-400">/100</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $scan->errors_count }}</div>
                <div class="text-sm text-gray-500">Errors</div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $scan->warnings_count }}</div>
                <div class="text-sm text-gray-500">Warnings</div>
            </div>

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

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Pages Table -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900">Pages Scanned</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $pages->count() }} {{ str('page')->plural($pages->count()) }} analyzed</p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($pages as $page)
                            <a href="{{ route('dashboard.scan.page', [$scan, $page]) }}" class="flex items-center justify-between p-5 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center font-bold text-sm
                                        @if(($page->score ?? 0) >= 90) bg-green-100 text-green-700
                                        @elseif(($page->score ?? 0) >= 70) bg-yellow-100 text-yellow-700
                                        @elseif(($page->score ?? 0) >= 50) bg-orange-100 text-orange-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ number_format($page->score ?? 0, 0) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-900 truncate">{{ $page->path }}</div>
                                        @if($page->page_title)
                                            <div class="text-sm text-gray-500 truncate">{{ $page->page_title }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                                    @if($page->errors_count > 0)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ $page->errors_count }} {{ str('error')->plural($page->errors_count) }}</span>
                                    @endif
                                    @if($page->warnings_count > 0)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">{{ $page->warnings_count }} {{ str('warning')->plural($page->warnings_count) }}</span>
                                    @endif
                                    @if($page->notices_count > 0)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ $page->notices_count }}</span>
                                    @endif
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </a>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Pages Found</h3>
                                <p class="text-gray-500">No pages were scanned.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('report.pdf', $scan) }}" class="w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Download PDF Report
                        </a>
                        <a href="{{ route('report.csv', $scan) }}" class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export CSV
                        </a>
                        <a href="{{ route('report.json', $scan) }}" class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                            Export JSON
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
