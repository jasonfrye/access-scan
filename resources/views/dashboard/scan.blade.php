@extends('layouts.guest')

@section('title', 'Scan Results - ' . $scan->domain . ' - Access Report Card')

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
                    <a href="{{ $scan->url }}" target="_blank" rel="noopener noreferrer" class="text-white/80 hover:text-white text-sm mt-1 inline-flex items-center gap-1">
                        {{ $scan->url }}
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
                <div class="text-right">
                    <div class="text-sm text-white/70">{{ $scan->completed_at ? 'Scanned ' . $scan->completed_at->diffForHumans() : ucfirst($scan->status) }}</div>
                    <div class="flex items-center gap-2 mt-2">
                        @if($scan->isFailed() || $scan->isPending())
                            <form action="{{ route('dashboard.scan.retry', $scan) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                                    Retry Scan
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                            New Scan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl p-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Failed State --}}
        @if($scan->isFailed())
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border-2 border-red-100">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 flex-shrink-0 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-bold text-gray-900">Scan Incomplete</h2>
                        <p class="text-sm text-gray-600 mt-0.5">
                            @if($pages->count() > 0)
                                The scan for <strong>{{ $scan->domain }}</strong> didn't finish, but {{ $pages->count() }} {{ str('page')->plural($pages->count()) }} were scanned before it stopped.
                            @else
                                We weren't able to complete the scan for <strong>{{ $scan->domain }}</strong>.
                            @endif
                        </p>
                        @if($scan->error_message)
                            <p class="text-xs text-red-600 mt-1">{{ $scan->error_message }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <form action="{{ route('dashboard.scan.retry', $scan) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                                Retry Scan
                            </button>
                        </form>
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>

            @if($pages->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm mb-8">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Pages Scanned Before Timeout</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ $pages->count() }} {{ str('page')->plural($pages->count()) }} were analyzed &mdash; results below are still valid</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                Partial
                            </span>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($pages as $page)
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
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        {{-- Pending / Running State --}}
        @elseif($scan->isPending() || $scan->isRunning())
            {{-- Progress Banner --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border-2 border-indigo-100">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center scanning">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ $scan->isRunning() ? 'Scan In Progress' : 'Scan Queued' }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-0.5">
                            @if($pages->count() > 0)
                                {{ $pages->count() }} {{ str('page')->plural($pages->count()) }} scanned so far. Still working on the rest &mdash; this page refreshes automatically.
                            @else
                                {{ $scan->isRunning() ? 'Scanning your site now. Results will appear here as pages complete.' : 'Your scan is in the queue and will start shortly.' }}
                            @endif
                        </p>
                        <div x-data="{
                            index: 0,
                            messages: [
                                'Warming up the accessibility engines...',
                                'Teaching robots to read alt text...',
                                'Asking every button if it has a label...',
                                'Interrogating your heading hierarchy...',
                                'Counting contrast ratios on our fingers...',
                                'Politely requesting your CSS cooperate...',
                                'Checking if screen readers would swipe right...',
                                'Bribing the DOM for insider information...',
                                'Making sure links actually go somewhere...',
                                'Judging your color choices (respectfully)...',
                                'Consulting the WCAG sacred texts...',
                                'Arguing with ARIA about proper roles...',
                                'Verifying forms aren\'t playing hide and seek...',
                                'Auditing tab order for cutting in line...',
                            ],
                            start() { setInterval(() => { this.index = (this.index + 1) % this.messages.length }, 3000) }
                        }" x-init="start()" class="inline-flex items-center gap-2 text-xs text-indigo-600 font-medium mt-2">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="messages[index]" x-transition></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Show pages already scanned --}}
            @if($pages->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm mb-8">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Pages Scanned So Far</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ $pages->count() }} {{ str('page')->plural($pages->count()) }} completed &mdash; more on the way</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                                Live
                            </span>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($pages as $page)
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
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <script>
                setTimeout(function() { window.location.reload(); }, 5000);
            </script>

        {{-- Completed State --}}
        @else
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
                            @if(Auth::user()->isPaid())
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
                                <div x-data="{ copied: false, loading: false }">
                                    <button
                                        @click="
                                            if (loading) return;
                                            loading = true;
                                            fetch('{{ route('report.json', $scan) }}')
                                                .then(r => r.json())
                                                .then(data => {
                                                    let md = `# Accessibility Report: ${data.scan.domain}\n`;
                                                    md += `**URL:** ${data.scan.url}\n`;
                                                    md += `**Score:** ${data.scan.score}/100 (${data.scan.grade})\n\n`;
                                                    data.pages.forEach(page => {
                                                        md += `## ${page.url} (Score: ${page.score})\n\n`;
                                                        if (page.issues.length > 0) {
                                                            page.issues.forEach(issue => {
                                                                md += `### ${issue.type}: ${issue.wcag_reference}\n`;
                                                                md += `${issue.message}\n\n`;
                                                            });
                                                        } else {
                                                            md += `No issues found.\n\n`;
                                                        }
                                                    });
                                                    navigator.clipboard.writeText(md);
                                                    copied = true;
                                                    loading = false;
                                                    setTimeout(() => copied = false, 2000);
                                                })
                                                .catch(() => { loading = false; });
                                        "
                                        class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2"
                                        :class="copied && 'border-green-300 text-green-700 bg-green-50'"
                                    >
                                        <template x-if="!copied && !loading">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                                Copy as Markdown
                                            </span>
                                        </template>
                                        <template x-if="loading">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                Loading...
                                            </span>
                                        </template>
                                        <template x-if="copied">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Copied!
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            @else
                                <a href="{{ route('billing.pricing') }}" class="w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    Upgrade to Export Reports
                                </a>
                            @endif
                            <form action="{{ route('dashboard.scan.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="url" value="{{ $scan->url }}">
                                <button type="submit" class="w-full py-3 px-4 border border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Re-scan This Site
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
