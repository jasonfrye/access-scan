@extends('layouts.guest')

@section('title', ($scanPage->path === '/' ? 'Homepage' : $scanPage->path) . ' - Scan Results - AccessScan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard.scan', $scan) }}" class="text-white/80 hover:text-white text-sm mb-2 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Pages Overview
            </a>
            <div class="flex items-center justify-between mt-2">
                <div>
                    <h1 class="text-2xl font-bold">{{ $scanPage->path === '/' ? 'Homepage' : $scanPage->path }}</h1>
                    <p class="text-white/80 text-sm mt-1">{{ $scanPage->url }}</p>
                </div>
                <div class="text-right">
                    <div class="text-5xl font-bold">{{ number_format($scanPage->score ?? 0, 0) }}</div>
                    <div class="text-sm text-white/70">Page Score</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $scanPage->errors_count }}</div>
                <div class="text-sm text-gray-500">Errors</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $scanPage->warnings_count }}</div>
                <div class="text-sm text-gray-500">Warnings</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $scanPage->notices_count }}</div>
                <div class="text-sm text-gray-500">Notices</div>
            </div>
        </div>

        <!-- Categorized Issues -->
        <div class="space-y-4">
            @forelse($categories as $category)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" x-data="{ open: {{ $category['errors'] > 0 ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors text-left">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $category['name'] }}</h3>
                            <span class="text-xs text-gray-400">WCAG 2.1 AA</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $category['score_class'] }}">
                                {{ $category['score_label'] }}
                            </span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-gray-100">
                        <div class="divide-y divide-gray-100">
                            @foreach($category['issues'] as $issue)
                                <div class="p-5" x-data="{ expanded: false }">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-0.5">
                                            @if($issue->type === 'error')
                                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                            @elseif($issue->type === 'warning')
                                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full @if($issue->type === 'error') bg-red-100 text-red-700 @elseif($issue->type === 'warning') bg-yellow-100 text-yellow-700 @else bg-blue-100 text-blue-700 @endif">
                                                    {{ ucfirst($issue->type) }}
                                                </span>
                                                <span class="text-xs text-gray-400">{{ $issue->wcag_reference }}</span>
                                            </div>
                                            <p class="text-sm text-gray-900">{{ $issue->message }}</p>

                                            <!-- Expanded details -->
                                            <div x-show="expanded" x-collapse class="mt-3 space-y-3">
                                                @if($issue->context)
                                                    <div>
                                                        <div class="text-xs font-medium text-gray-500 mb-1">AFFECTED ELEMENT</div>
                                                        <code class="block bg-gray-900 text-gray-100 p-3 rounded-lg text-xs overflow-x-auto">{{ $issue->context }}</code>
                                                    </div>
                                                @endif
                                                @if($issue->recommendation)
                                                    <div>
                                                        <div class="text-xs font-medium text-gray-500 mb-1">HOW TO FIX</div>
                                                        <p class="text-sm text-gray-700">{{ $issue->recommendation }}</p>
                                                    </div>
                                                @endif
                                                @if($issue->help_url)
                                                    <a href="{{ $issue->help_url }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                                                        Learn more
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <button @click="expanded = !expanded" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Issues Found</h3>
                    <p class="text-gray-500">This page passed all accessibility checks.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
