@extends('layouts.guest')

@section('title', 'Scanning... - AccessScan')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-white flex items-center justify-center">
    <div class="max-w-lg w-full mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Loading Spinner -->
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="animate-spin w-12 h-12 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Scanning Your Website</h1>
                <p class="text-gray-600">{{ $scan->url }}</p>
            </div>

            <!-- Progress Stages -->
            <div class="space-y-4 mb-8">
                <div class="flex items-center gap-3 text-sm" :class="$scan->isPending() ? 'text-gray-900 font-medium' : 'text-gray-500'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center @if($scan->isPending()) bg-indigo-600 text-white @elseif($scan->isRunning() || $scan->isCompleted()) bg-green-500 text-white @else bg-gray-200 text-gray-500 @endif">
                        @if($scan->isPending())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        @endif
                    </div>
                    <span>Preparing scan...</span>
                </div>

                <div class="flex items-center gap-3 text-sm" :class="$scan->isRunning() ? 'text-gray-900 font-medium' : 'text-gray-500'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center @if($scan->isRunning()) bg-indigo-600 text-white animate-pulse @elseif($scan->isCompleted()) bg-green-500 text-white @else bg-gray-200 text-gray-500 @endif">
                        @if($scan->isRunning())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        @elseif($scan->isCompleted())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        @else
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        @endif
                    </div>
                    <span>Analyzing pages...</span>
                </div>

                <div class="flex items-center gap-3 text-sm" :class="$scan->isCompleted() ? 'text-gray-900 font-medium' : 'text-gray-500'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center @if($scan->isCompleted()) bg-green-500 text-white @else bg-gray-200 text-gray-500 @endif">
                        @if($scan->isCompleted())
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        @else
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        @endif
                    </div>
                    <span>Generating report...</span>
                </div>
            </div>

            <!-- Status Message -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-600">
                    @switch($scan->status)
                        @case('pending')
                            Your scan is queued and will begin shortly.
                            @break
                        @case('running')
                            We're analyzing {{ $scan->url }} for accessibility issues. This typically takes 1-3 minutes.
                            @break
                        @case('failed')
                            <span class="text-red-600">Scan failed: {{ $scan->error_message ?? 'An error occurred during scanning.' }}</span>
                            @break
                    @endswitch
                </p>
            </div>

            <!-- Auto-refresh -->
            @if($scan->isPending() || $scan->isRunning())
                <div class="flex items-center justify-center gap-4">
                    <div class="text-sm text-gray-500">
                        Checking status<span class="animate-pulse">...</span>
                    </div>
                </div>
                <meta http-equiv="refresh" content="5">
            @elseif($scan->isFailed())
                <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Try Again
                </a>
            @else
                <a href="{{ route('scan.results', $scan) }}" class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    View Results →
                </a>
            @endif
        </div>

        <!-- Tip -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>💡 Tip: This page will automatically update. You can also bookmark it and come back later.</p>
        </div>
    </div>
</div>
@endsection
