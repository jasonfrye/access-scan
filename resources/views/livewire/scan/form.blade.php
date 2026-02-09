<div class="max-w-2xl mx-auto">
    <!-- URL Input Form -->
    <div class="bg-white rounded-2xl shadow-xl p-8" x-data="{ url: @entangle('url') }">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Scan Your Website</h2>
            <p class="text-gray-600">Enter your URL to check for ADA/WCAG accessibility issues</p>
        </div>

        <form wire:submit.prevent="initiateScan" class="space-y-6">
            <!-- URL Input -->
            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                    Website URL
                </label>
                <div class="relative">
                    <input
                        type="url"
                        id="url"
                        wire:model="url"
                        x-model="url"
                        placeholder="https://example.com"
                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 text-lg transition-all"
                        :class="{'border-indigo-500 ring-4 ring-indigo-100': url.length > 0}"
                        :disabled="isLoading"
                    />
                    <button
                        type="submit"
                        wire:click="initiateScan"
                        :disabled="!url || isLoading"
                        class="absolute right-2 top-2 bottom-2 px-6 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2"
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
                        <span x-text="isLoading ? 'Scanning...' : 'Scan Now'"></span>
                    </button>
                </div>
                @error('url')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Error Message -->
            @if($errorMessage)
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-700">{{ $errorMessage }}</p>
                    </div>
                </div>
            @endif

            <!-- Free Scan Badge -->
            <div class="text-center">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Free Scan • No Registration Required
                </span>
            </div>
        </form>
    </div>

    <!-- Trust Indicators -->
    <div class="mt-8 grid grid-cols-3 gap-4 text-center">
        <div class="p-4 bg-gray-50 rounded-xl">
            <div class="text-2xl font-bold text-gray-900">2,000+</div>
            <div class="text-sm text-gray-600">Websites Scanned</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <div class="text-2xl font-bold text-gray-900">98%</div>
            <div class="text-sm text-gray-600">Have Issues</div>
        </div>
        <div class="p-4 bg-gray-50 rounded-xl">
            <div class="text-2xl font-bold text-gray-900">WCAG 2.1</div>
            <div class="text-sm text-gray-600">AA Standard</div>
        </div>
    </div>
</div>
