@extends('layouts.guest')

@section('title', 'Profile Settings - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm inline-flex items-center gap-1 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-gray-600 mt-1">Manage your account information and preferences</p>
        </div>

        <div class="flex flex-wrap gap-2 mb-8">
            <a href="#profile-information" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">Profile Information</a>
            <a href="#password" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">Password</a>
            <a href="#api-key" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">API Key</a>
            @if (auth()->user()->plan === 'agency')
                <a href="#branding" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">Branding</a>
            @endif
            <a href="#email-preferences" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">Email Preferences</a>
            <a href="#delete-account" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-white border border-gray-200 text-red-600 hover:bg-red-50 transition-colors">Delete Account</a>
        </div>

        <div class="space-y-6">
            <div id="profile-information" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 scroll-mt-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div id="password" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 scroll-mt-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div id="api-key" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 scroll-mt-6">
                <div class="max-w-xl">
                    @include('profile.partials.manage-api-key-form')
                </div>
            </div>

            @if (auth()->user()->plan === 'agency')
                <div id="branding" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 scroll-mt-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-branding-form')
                    </div>
                </div>
            @endif

            <div id="email-preferences" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 scroll-mt-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-email-preferences-form')
                </div>
            </div>

            <div id="delete-account" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 scroll-mt-6">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
