<x-guest-layout>
    <div class="min-h-[60vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Sign in to your account</h1>
                <p class="mt-2 text-sm text-gray-600">Or <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">create a free account</a></p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-700 font-medium" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <x-primary-button class="w-full justify-center py-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
