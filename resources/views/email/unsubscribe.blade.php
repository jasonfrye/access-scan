@extends('layouts.guest')

@section('title', 'Email Preferences - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Email Preferences</h1>
            <p class="text-gray-600 mb-6">Choose which emails you'd like to receive, {{ $user->name }}.</p>

            @if (session('status') === 'email-preferences-updated')
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                    <p class="text-sm text-green-800 font-medium">Your email preferences have been updated.</p>
                </div>
            @endif

            <form method="POST" action="{{ URL::signedRoute('email.unsubscribe.update', $user) }}" class="space-y-5">
                @csrf

                <label class="flex items-start gap-3">
                    <input type="hidden" name="marketing_emails_enabled" value="0">
                    <input type="checkbox" name="marketing_emails_enabled" value="1"
                        class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                        {{ $user->marketing_emails_enabled ? 'checked' : '' }}>
                    <div>
                        <span class="font-medium text-gray-900">Marketing emails</span>
                        <p class="text-sm text-gray-500">Tips, re-engagement reminders, score celebrations, weekly digests, and trial notifications.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3">
                    <input type="hidden" name="system_emails_enabled" value="0">
                    <input type="checkbox" name="system_emails_enabled" value="1"
                        class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                        {{ $user->system_emails_enabled ? 'checked' : '' }}>
                    <div>
                        <span class="font-medium text-gray-900">System emails</span>
                        <p class="text-sm text-gray-500">Scan complete notifications, regression alerts, and payment failure notices.</p>
                    </div>
                </label>

                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Save Preferences
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
