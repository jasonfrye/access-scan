@extends('layouts.app')

@section('title', 'Dashboard - AccessScan')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Manage your accessibility scans and track your progress</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Scans -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total Scans</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_scans']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Average Score</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['average_score'] ? number_format($stats['average_score'], 0) : 'N/A' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Scans Remaining -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Scans Remaining</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['scans_remaining'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500 transition-all" style="width: {{ ($stats['scans_remaining'] / max($stats['scan_limit'], 1)) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Completed Scans -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Completed</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['completed_scans']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content: Scan History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900">Scan History</h2>
                        <a href="{{ route('home') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            New Scan
                        </a>
                    </div>

                    @if($scans->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($scans as $scan)
                                <div class="p-6 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <!-- Score Badge -->
                                            <div class="w-14 h-14 rounded-xl flex items-center justify-center font-bold text-lg {{ $scan->grade === 'A' ? 'bg-green-100 text-green-700' : ($scan->grade === 'B' ? 'bg-green-50 text-green-600' : ($scan->grade === 'C' ? 'bg-yellow-100 text-yellow-700' : ($scan->grade === 'D' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700')) }}">
                                                {{ $scan->grade ?? 'N/A' }}
                                            </div>

                                            <div>
                                                <a href="{{ route('dashboard.scan', $scan) }}" class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                                    {{ parse_url($scan->url, PHP_URL_HOST) }}
                                                </a>
                                                <p class="text-sm text-gray-500">{{ parse_url($scan->url, PHP_URL_PATH) ?: '/' }}</p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $scan->completed_at?->diffForHumans() ?? $scan->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-gray-900">{{ number_format($scan->score, 0) }}</div>
                                            <div class="text-sm text-gray-500">/100 score</div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ $scan->pages_scanned }} pages • {{ $scan->issues_found }} issues
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Issue Summary -->
                                    <div class="mt-4 flex items-center gap-4 text-sm">
                                        @if($scan->errors_count > 0)
                                            <span class="px-2 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-medium">
                                                {{ $scan->errors_count }} errors
                                            </span>
                                        @endif
                                        @if($scan->warnings_count > 0)
                                            <span class="px-2 py-1 bg-yellow-50 text-yellow-700 rounded-lg text-xs font-medium">
                                                {{ $scan->warnings_count }} warnings
                                            </span>
                                        @endif
                                        @if($scan->notices_count > 0)
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                                                {{ $scan->notices_count }} notices
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="p-6 border-t border-gray-100">
                            {{ $scans->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No scans yet</h3>
                            <p class="text-gray-500 mb-4">Run your first accessibility scan to get started</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                Run First Scan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Scan Widget -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl p-6 text-white">
                    <h3 class="font-bold text-lg mb-2">Quick Scan</h3>
                    <p class="text-white/80 text-sm mb-4">Check another website for accessibility issues</p>
                    <form action="{{ route('dashboard.scan.store') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input
                            type="url"
                            name="url"
                            placeholder="https://example.com"
                            class="flex-1 px-3 py-2 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50"
                            required
                        />
                        <button type="submit" class="px-4 py-2 bg-white text-indigo-600 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                            Scan
                        </button>
                    </form>
                </div>

                <!-- Trend Chart -->
                @if(count($trendData['scores']) > 1)
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">Score Trend</h3>
                        <div class="h-32 flex items-end gap-1">
                            @foreach($trendData['scores'] as $score)
                                <div class="flex-1 bg-indigo-100 rounded-t" style="height: {{ ($score / 100) * 100 }}%"></div>
                            @endforeach
                        </div>
                        <div class="flex justify-between mt-2 text-xs text-gray-400">
                            <span>{{ $trendData['labels'][0] ?? '' }}</span>
                            <span>{{ $trendData['labels'][count($trendData['labels']) - 1] ?? '' }}</span>
                        </div>
                    </div>
                @endif

                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Account</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Plan</span>
                            <span class="font-medium text-gray-900 capitalize">{{ Auth::user()->plan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Scans Used</span>
                            <span class="font-medium text-gray-900">{{ Auth::user()->scan_count }} / {{ Auth::user()->scan_limit }}</span>
                        </div>
                        @if(Auth::user()->trial_ends_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Trial Ends</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->trial_ends_at->diffForHumans() }}</span>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-100">
                            <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                Manage Account →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
