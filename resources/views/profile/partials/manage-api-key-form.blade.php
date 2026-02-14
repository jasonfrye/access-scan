<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('API Key') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Use an API key to access your scan data programmatically.') }}
            <a href="{{ route('api.docs') }}" class="text-blue-600 hover:text-blue-800">View API docs</a>
        </p>
    </header>

    <div class="mt-6 space-y-4">
        @unless($user->isPaid())
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                <p class="text-sm text-gray-600">API access is available on paid plans. <a href="{{ route('billing.pricing') }}" class="text-blue-600 hover:text-blue-800 font-medium">Upgrade</a> to get started.</p>
            </div>
        @else
        @if (session('status') === 'api-key-created' && session('api_token'))
            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-sm font-medium text-green-800 mb-2">API key created. Copy it now — you won't see it again.</p>
                <div class="flex items-center gap-2" x-data="{ copied: false }">
                    <code class="flex-1 bg-white border border-green-300 rounded px-3 py-2 text-sm font-mono text-gray-900 select-all break-all">{{ session('api_token') }}</code>
                    <button
                        @click="navigator.clipboard.writeText('{{ session('api_token') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="flex-shrink-0 px-3 py-2 text-sm font-medium rounded-lg border transition-colors"
                        :class="copied ? 'border-green-300 text-green-700 bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                    >
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied">Copied!</span>
                    </button>
                </div>
            </div>
        @endif

        @if (session('status') === 'api-key-revoked')
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                <p class="text-sm text-yellow-800">Your API key has been revoked.</p>
            </div>
        @endif

        @if ($user->tokens->isNotEmpty())
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Active API key</p>
                        <p class="text-xs text-gray-500 mt-1">Created {{ $user->tokens->first()->created_at->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('profile.api-key.revoke') }}" onsubmit="return confirm('Are you sure? Any integrations using this key will stop working.')">
                        @csrf
                        @method('delete')
                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            Revoke
                        </button>
                    </form>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('profile.api-key.create') }}">
                @csrf
                <x-primary-button>{{ __('Generate API Key') }}</x-primary-button>
            </form>
        @endif
        @endunless
    </div>
</section>
