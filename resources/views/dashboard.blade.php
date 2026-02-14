@extends('layouts.guest')

@section('title', 'Dashboard - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-2 text-lg">Track your accessibility progress and manage scans</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Scans -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border-2 border-gray-100 hover:border-blue-200 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="text-sm text-gray-500 font-mono">TOTAL</div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_scans']) }}</p>
                    <p class="text-sm text-gray-500">Scans Completed</p>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border-2 border-gray-100 hover:border-green-200 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-sm text-gray-500 font-mono">AVG</div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['average_score'] ? number_format($stats['average_score'], 0) : 'N/A' }}</p>
                    <p class="text-sm text-gray-500">Average Score</p>
                </div>
            </div>

            <!-- Scans Remaining -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border-2 border-gray-100 hover:border-amber-200 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-sm text-gray-500 font-mono">LEFT</div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['scans_remaining'] }}</p>
                    <p class="text-sm text-gray-500">Scans Remaining</p>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 transition-all" style="width: {{ ($stats['scans_remaining'] / max($stats['scan_limit'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Issues Found -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border-2 border-gray-100 hover:border-red-200 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="text-sm text-gray-500 font-mono">ISSUES</div>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['completed_scans'] ?? 0) }}</p>
                    <p class="text-sm text-gray-500">This Month</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content: Scan History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100">
                    <div class="p-6 border-b-2 border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Scan History</h2>
                            <p class="text-sm text-gray-500 mt-1">Your recent accessibility scans</p>
                        </div>
                        <button x-data @click="$dispatch('open-modal', 'new-scan')" class="px-5 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all hover:scale-105">
                            + New Scan
                        </button>
                    </div>

                    @if($groupedScans->count() > 0)
                        <div class="divide-y-2 divide-gray-100">
                            @foreach($groupedScans as $domain => $domainScans)
                                @php $latestScan = $domainScans->first(); @endphp

                                @if($domainScans->count() === 1)
                                    {{-- Single scan — link directly to report --}}
                                    <div class="flex items-center p-6 hover:bg-blue-50/30 transition-all group gap-3">
                                        <a href="{{ route('dashboard.scan', $latestScan) }}" class="flex items-center justify-between gap-6 flex-1 min-w-0">
                                            <div class="flex items-center gap-5">
                                                <div class="relative flex-shrink-0">
                                                    <div class="w-16 h-20 rounded-lg flex flex-col items-center justify-center font-bold shadow-md {{ $latestScan->grade === 'A' ? 'bg-gradient-to-br from-green-500 to-green-600 text-white' : ($latestScan->grade === 'B' ? 'bg-gradient-to-br from-green-400 to-green-500 text-white' : ($latestScan->grade === 'C' ? 'bg-gradient-to-br from-yellow-400 to-yellow-500 text-white' : ($latestScan->grade === 'D' ? 'bg-gradient-to-br from-orange-400 to-orange-500 text-white' : 'bg-gradient-to-br from-red-500 to-red-600 text-white'))) }}">
                                                        <div class="text-3xl">{{ $latestScan->grade ?? '?' }}</div>
                                                        <div class="text-xs font-normal opacity-90">GRADE</div>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <span class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors text-lg group-hover:underline">{{ $domain }}</span>
                                                    <div class="flex items-center gap-3 mt-2">
                                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                            {{ $latestScan->completed_at?->diffForHumans() ?? $latestScan->created_at->diffForHumans() }}
                                                        </span>
                                                        <span class="text-xs text-gray-400">•</span>
                                                        <span class="text-xs text-gray-400">{{ $latestScan->pages_scanned }} pages</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-3xl font-bold text-gray-900">{{ number_format($latestScan->score, 0) }}</div>
                                                <div class="text-sm text-gray-500 font-medium">/100</div>
                                                <div class="text-xs text-gray-400 mt-1">{{ $latestScan->issues_found }} {{ Str::plural('issue', $latestScan->issues_found) }}</div>
                                            </div>
                                        </a>
                                        @include('dashboard._schedule-icon', ['domain' => $domain, 'latestScan' => $latestScan])
                                    </div>
                                @else
                                    {{-- Multiple scans — accordion --}}
                                    <div x-data="{ open: false }" class="transition-all">
                                        <div class="flex items-center p-6 hover:bg-blue-50/30 transition-all gap-3">
                                            <button @click="open = !open" class="flex items-center justify-between gap-6 flex-1 min-w-0 text-left">
                                                <div class="flex items-center gap-5">
                                                    <div class="relative flex-shrink-0">
                                                        <div class="w-16 h-20 rounded-lg flex flex-col items-center justify-center font-bold shadow-md {{ $latestScan->grade === 'A' ? 'bg-gradient-to-br from-green-500 to-green-600 text-white' : ($latestScan->grade === 'B' ? 'bg-gradient-to-br from-green-400 to-green-500 text-white' : ($latestScan->grade === 'C' ? 'bg-gradient-to-br from-yellow-400 to-yellow-500 text-white' : ($latestScan->grade === 'D' ? 'bg-gradient-to-br from-orange-400 to-orange-500 text-white' : 'bg-gradient-to-br from-red-500 to-red-600 text-white'))) }}">
                                                            <div class="text-3xl">{{ $latestScan->grade ?? '?' }}</div>
                                                            <div class="text-xs font-normal opacity-90">GRADE</div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-3">
                                                            <span class="font-semibold text-gray-900 text-lg">{{ $domain }}</span>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $domainScans->count() }} scans</span>
                                                        </div>
                                                        <div class="flex items-center gap-3 mt-2">
                                                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                                Latest: {{ $latestScan->completed_at?->diffForHumans() ?? $latestScan->created_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    <div class="text-right flex-shrink-0">
                                                        <div class="text-3xl font-bold text-gray-900">{{ number_format($latestScan->score, 0) }}</div>
                                                        <div class="text-sm text-gray-500 font-medium">/100</div>
                                                    </div>
                                                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </button>
                                            @include('dashboard._schedule-icon', ['domain' => $domain, 'latestScan' => $latestScan])
                                        </div>

                                        <div x-show="open" x-collapse>
                                            <div class="px-6 pb-4 space-y-2 ml-20">
                                                @foreach($domainScans as $scan)
                                                    <a href="{{ route('dashboard.scan', $scan) }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors group/scan">
                                                        <div class="flex items-center gap-3">
                                                            <span class="w-8 h-8 rounded-md flex items-center justify-center text-sm font-bold {{ $scan->grade === 'A' ? 'bg-green-100 text-green-700' : ($scan->grade === 'B' ? 'bg-green-50 text-green-600' : ($scan->grade === 'C' ? 'bg-yellow-100 text-yellow-700' : ($scan->grade === 'D' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'))) }}">{{ $scan->grade ?? '?' }}</span>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-700 group-hover/scan:text-blue-600"
                                                                     x-data="{ formatted: '' }"
                                                                     x-init="formatted = new Date('{{ ($scan->completed_at ?? $scan->created_at)->toIso8601String() }}').toLocaleString(undefined, { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })"
                                                                     x-text="formatted">{{ ($scan->completed_at ?? $scan->created_at)->format('M d, Y g:ia') }}</div>
                                                                <div class="text-xs text-gray-400">{{ $scan->pages_scanned }} pages • {{ $scan->issues_found }} {{ Str::plural('issue', $scan->issues_found) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="text-lg font-bold text-gray-900">{{ number_format($scan->score, 0) }}</span>
                                                            <span class="text-xs text-gray-400">/100</span>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="p-16 text-center">
                            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Scans Yet</h3>
                            <p class="text-gray-600 mb-6 max-w-sm mx-auto">Ready to check your website's accessibility? Run your first scan now.</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Run Your First Scan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 min-w-0">
                <!-- Quick Scan Widget -->
                <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 rounded-2xl p-6 text-white shadow-lg">
                    @if($stats['scans_remaining'] <= 0)
                        <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm rounded-2xl z-10 flex flex-col items-center justify-center p-6 text-center">
                            <svg class="w-10 h-10 shrink-0 text-white/80 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <p class="text-white font-semibold mb-1">No Scans Remaining</p>
                            <p class="text-white/70 text-sm mb-4">Upgrade your plan to run more scans</p>
                            <a href="{{ route('billing.pricing') }}" class="px-5 py-2.5 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-all text-sm">
                                Upgrade Now
                            </a>
                        </div>
                    @endif
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                        <h3 class="font-bold text-lg">Quick Scan</h3>
                    </div>
                    <p class="text-blue-100 text-sm mb-4">Scan another website instantly</p>
                    <button x-data @click="$dispatch('open-modal', 'new-scan')" class="w-full px-4 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-all hover:scale-105 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Start New Scan
                    </button>
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
                <div class="bg-white rounded-2xl shadow-sm p-6 border-2 border-gray-100">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <h3 class="font-bold text-gray-900">Your Plan</h3>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                            <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Current Plan</div>
                            <div class="text-2xl font-bold text-blue-900 capitalize">{{ Auth::user()->plan }}</div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Scans Used
                                </span>
                                <span class="font-bold text-gray-900">{{ Auth::user()->scan_count }}<span class="text-gray-400 font-normal"> / {{ Auth::user()->scan_limit }}</span></span>
                            </div>

                            @if(Auth::user()->trial_ends_at)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Trial Ends</span>
                                    <span class="font-semibold text-amber-600">{{ Auth::user()->trial_ends_at->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t-2 border-gray-100 flex gap-2">
                            <a href="{{ route('billing.pricing') }}" class="flex-1 text-center px-3 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors text-xs">
                                Upgrade
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-xs">
                                Settings
                            </a>
                        </div>
                    </div>
                </div>

                {{-- API Access Card --}}
                @if(Auth::user()->isPaid())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">API Access</h3>
                            <p class="text-xs text-gray-500 mt-1">Run scans programmatically and integrate accessibility checks into your workflow.</p>
                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('profile.edit') }}#api-key" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Get API Key</a>
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('api.docs') }}" class="text-xs font-medium text-gray-600 hover:text-gray-800">View Docs</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Scan Modal -->
<div x-data="{ open: false, submitting: false }"
     @open-modal.window="if ($event.detail === 'new-scan') { open = true; $nextTick(() => $refs.scanUrl.focus()); }"
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
             @click.stop
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">New Scan</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('dashboard.scan.store') }}" method="POST" @submit="submitting = true">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="scan_url" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input
                            type="url"
                            name="url"
                            id="scan_url"
                            x-ref="scanUrl"
                            placeholder="https://example.com"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                        />
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Scan Type</p>
                        <div class="space-y-2">
                            <label class="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-colors">
                                <input type="radio" name="scan_type" value="full" checked class="mt-0.5 w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Full Site Scan</div>
                                    <div class="text-xs text-gray-500">Crawl and scan up to {{ Auth::user()->getMaxPagesPerScan() }} pages</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-blue-300 transition-colors">
                                <input type="radio" name="scan_type" value="single" class="mt-0.5 w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Single Page</div>
                                    <div class="text-xs text-gray-500">Scan only the URL entered above</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $stats['scans_remaining'] }} scans remaining this month</span>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" @click="open = false" class="flex-1 py-3 px-4 border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 px-4 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors flex items-center justify-center gap-2" :disabled="submitting">
                        <template x-if="!submitting">
                            <span>Start Scan</span>
                        </template>
                        <template x-if="submitting">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Starting...
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Scheduled Scan Modal -->
<div x-data="{ open: false }"
     @open-modal.window="if ($event.detail === 'add-scheduled' || ($event.detail && $event.detail.modal === 'add-scheduled')) { open = true; $nextTick(() => { if ($event.detail && $event.detail.url) { $refs.url.value = $event.detail.url; } $refs.url.focus(); }); }"
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
