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

                <!-- Scheduled Scans -->
                @if(isset($scheduledScans) && $scheduledScans->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900">Scheduled Scans</h3>
                            <button x-data @click="$dispatch('open-modal', 'add-scheduled')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                + Add
                            </button>
                        </div>
                        <div class="space-y-3">
                            @foreach($scheduledScans as $schedule)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ parse_url($schedule->url, PHP_URL_HOST) }}</p>
                                        <p class="text-xs text-gray-500 capitalize">{{ $schedule->frequency }} • {{ $schedule->next_run_at->diffForHumans() }}</p>
                                    </div>
                                    <form action="{{ route('dashboard.scheduled.toggle', $schedule) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="p-1 {{ $schedule->is_active ? 'text-green-600' : 'text-gray-400' }}" title="{{ $schedule->is_active ? 'Active' : 'Paused' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($schedule->is_active)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                @endif
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        @if($scheduledScans->count() >= 5)
                            <div class="text-center text-sm text-gray-500 pt-2">
                                <a href="#" class="text-indigo-600 hover:text-indigo-800">View all →</a>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Add Scheduled Scan CTA -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto bg-indigo-100 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 mb-1">Schedule Scans</h3>
                            <p class="text-sm text-gray-500 mb-3">Automatically scan your site on a schedule</p>
                            <button x-data @click="$dispatch('open-modal', 'add-scheduled')" class="w-full py-2 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                Add Schedule
                            </button>
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

<!-- Add Scheduled Scan Modal -->
<div x-data="{ open: false }" 
     @open-modal.window="open = ($event.detail === 'add-scheduled'); if(open) $nextTick(() => $refs.url.focus())"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-transition.opacity>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="open" 
             @click="open = false"
             class="fixed inset-0 bg-gray-900/50 transition-opacity"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6"
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Schedule Automatic Scans</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('dashboard.scheduled.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input 
                            type="url" 
                            name="url" 
                            id="url"
                            x-ref="url"
                            placeholder="https://example.com"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">Scan Frequency</label>
                        <select name="frequency" id="frequency" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="notify_on_regression" id="notify_on_regression" value="1" checked class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                        <label for="notify_on_regression" class="text-sm text-gray-700">
                            Notify me if score drops
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" @click="open = false" class="flex-1 py-3 px-4 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 px-4 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                        Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
