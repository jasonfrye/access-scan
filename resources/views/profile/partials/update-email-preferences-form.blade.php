<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Email Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Choose which emails you want to receive from us.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.email-preferences.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-4">
            <label class="flex items-start gap-3">
                <input type="hidden" name="marketing_emails_enabled" value="0">
                <input type="checkbox" name="marketing_emails_enabled" value="1"
                    class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    {{ $user->marketing_emails_enabled ? 'checked' : '' }}>
                <div>
                    <span class="font-medium text-gray-900">{{ __('Marketing emails') }}</span>
                    <p class="text-sm text-gray-500">{{ __('Tips, re-engagement reminders, score celebrations, weekly digests, and trial notifications.') }}</p>
                </div>
            </label>

            <label class="flex items-start gap-3">
                <input type="hidden" name="system_emails_enabled" value="0">
                <input type="checkbox" name="system_emails_enabled" value="1"
                    class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    {{ $user->system_emails_enabled ? 'checked' : '' }}>
                <div>
                    <span class="font-medium text-gray-900">{{ __('System emails') }}</span>
                    <p class="text-sm text-gray-500">{{ __('Scan complete notifications, regression alerts, and payment failure notices.') }}</p>
                </div>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'email-preferences-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
