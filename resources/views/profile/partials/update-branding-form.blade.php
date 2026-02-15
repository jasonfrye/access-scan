<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('White-Label Branding') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Customize PDF reports with your company branding.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.branding.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="company_name" :value="__('Company Name')" />
            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $user->company_name)" maxlength="255" placeholder="Your Agency Name" />
            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
        </div>

        <div>
            <x-input-label for="company_logo" :value="__('Company Logo')" />
            @if ($user->company_logo_path)
                <div class="mt-2 mb-3 flex items-center gap-4">
                    <img src="{{ Storage::disk('public')->url($user->company_logo_path) }}" alt="Current logo" class="h-12 max-w-[200px] object-contain rounded border border-gray-200 p-1">
                    <button type="submit" form="remove-logo-form" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                </div>
            @endif
            <input type="file" id="company_logo" name="company_logo" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <p class="mt-1 text-xs text-gray-500">JPG or PNG, max 1MB. Recommended height: 60px.</p>
            <x-input-error class="mt-2" :messages="$errors->get('company_logo')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <a href="{{ route('profile.branding.preview') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-colors">
                {{ __('Preview PDF') }}
            </a>

            @if (session('status') === 'branding-updated')
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

    @if ($user->company_logo_path)
        <form id="remove-logo-form" method="post" action="{{ route('profile.branding.update') }}" class="hidden">
            @csrf
            @method('put')
            <input type="hidden" name="remove_logo" value="1">
        </form>
    @endif
</section>
