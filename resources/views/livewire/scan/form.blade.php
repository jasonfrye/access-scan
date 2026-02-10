<div class="max-w-2xl mx-auto">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Enter Your Website URL</h2>
        <p class="text-gray-600">Get your accessibility score in 60 seconds</p>
    </div>

    <form wire:submit.prevent="initiateScan" class="space-y-4">
        <!-- URL Input -->
        <div>
            <label for="url" class="sr-only">Website URL</label>
            <div class="relative">
                <input
                    type="url"
                    id="url"
                    wire:model="url"
                    placeholder="https://example.com"
                    class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-gray-900 text-lg font-mono placeholder-gray-400 transition-all"
                />
                    <button
                        type="submit"
                        class="absolute right-2.5 top-2.5 bottom-2.5 px-8 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2 shadow-lg"
                    >
                        <span wire:loading.remove wire:target="initiateScan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="initiateScan">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span class="text-base" wire:loading.remove wire:target="initiateScan">Scan Now</span>
                        <span class="text-base" wire:loading wire:target="initiateScan">Scanning...</span>
                    </button>
                </div>
                @error('url')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Error Message -->
            @if($errorMessage)
                <div class="p-4 bg-red-50 border-2 border-red-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-red-700 font-medium">{{ $errorMessage }}</p>
                    </div>
                </div>
            @endif

            <!-- Free Scan Badge -->
            <div class="text-center pt-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-full text-sm font-semibold border border-green-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Free Scan • No Credit Card Required
                </span>
            </div>
        </form>
    </div>

    <!-- Trust Indicators Removed - already in main page -->
</div>
