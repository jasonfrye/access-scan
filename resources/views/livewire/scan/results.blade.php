<div class="max-w-2xl mx-auto">
    @if($scan)
        <!-- Loading State -->
        @if(!$isComplete)
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                <div class="mb-6">
                    <div class="w-20 h-20 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="animate-spin w-10 h-10 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Scanning Your Website</h2>
                    <p class="text-gray-600">{{ parse_url($scan->url, PHP_URL_HOST) }}</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-indigo-600 transition-all duration-500"
                            style="width: {{ $progress }}%"
                        ></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        @switch($scan->status)
                            @case('pending')
                                Preparing scan...
                                @break
                            @case('running')
                                Analyzing pages...
                                @break
                            @case('failed')
                                Scan encountered an error
                                @break
                        @endswitch
                    </p>
                </div>

                <p class="text-sm text-gray-500">
                    This may take a few minutes. You can close this page and come back later.
                </p>
            </div>
        @else
            <!-- Results -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Score Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white text-center">
                    <div class="text-sm font-medium opacity-80 mb-2">ACCESSIBILITY SCORE</div>
                    <div class="flex items-center justify-center gap-6">
                        <div class="text-7xl font-bold">{{ $scan->grade ?? 'N/A' }}</div>
                        <div class="text-left border-l border-white/30 pl-6">
                            <div class="text-4xl font-semibold">{{ number_format($scan->score, 0) }}<span class="text-lg opacity-70">/100</span></div>
                            <div class="text-sm opacity-70 mt-1">
                                {{ $scan->issues_found }} issues found
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issue Breakdown -->
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Issue Breakdown</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-red-50 rounded-xl">
                            <div class="text-3xl font-bold text-red-600">{{ $scan->errors_count }}</div>
                            <div class="text-sm text-red-700">Errors</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-xl">
                            <div class="text-3xl font-bold text-yellow-600">{{ $scan->warnings_count }}</div>
                            <div class="text-sm text-yellow-700">Warnings</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-xl">
                            <div class="text-3xl font-bold text-blue-600">{{ $scan->notices_count }}</div>
                            <div class="text-sm text-blue-700">Notices</div>
                        </div>
                    </div>
                </div>

                <!-- Pages Scanned -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">Website</h3>
                            <p class="text-sm text-gray-600">{{ $scan->url }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">{{ $scan->pages_scanned }}</div>
                            <div class="text-sm text-gray-600">Pages Scanned</div>
                        </div>
                    </div>
                </div>

                <!-- Email Capture (Guest Users) -->
                @if($showEmailForm)
                    <div class="p-6 bg-gray-50">
                        <div class="text-center mb-4">
                            <h3 class="font-semibold text-gray-900 mb-1">Get Your Full Report</h3>
                            <p class="text-sm text-gray-600">Enter your email to see detailed fix recommendations</p>
                        </div>
                        <form class="flex gap-2">
                            <input
                                type="email"
                                placeholder="you@example.com"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            />
                            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                Send Report
                            </button>
                        </form>
                    </div>
                @endif

                <!-- CTA -->
                <div class="p-6">
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Unlock unlimited scans and detailed reports</p>
                        <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                            Create Free Account
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- No Scan -->
        <div class="text-center py-12">
            <p class="text-gray-500">No scan results available</p>
        </div>
    @endif
</div>
