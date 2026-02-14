@extends('layouts.guest')

@section('title', 'Billing - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Billing & Subscription</h1>

        <!-- Current Plan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Current Plan</h2>
            
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="text-2xl font-bold text-gray-900 capitalize">{{ $user->plan }} Plan</div>
                    @if($user->plan !== 'free')
                        @if($subscription && $subscription->ends_at)
                            <div class="text-gray-500">Renews {{ $subscription->ends_at->format('F j, Y') }}</div>
                        @endif
                    @endif
                </div>
                <a href="{{ route('billing.pricing') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    Change Plan →
                </a>
            </div>

            @if($user->plan === 'free')
                <div class="bg-indigo-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div>
                            <div class="font-medium text-indigo-900">You're on the free plan</div>
                            <div class="text-sm text-indigo-700">Upgrade to unlock more scans and features</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Usage Stats -->
            <div class="grid grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900">{{ $user->scan_count }}</div>
                    <div class="text-sm text-gray-500">Scans Used</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900">{{ $user->scan_limit }}</div>
                    <div class="text-sm text-gray-500">Monthly Limit</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900">{{ max(0, $user->scan_limit - $user->scan_count) }}</div>
                    <div class="text-sm text-gray-500">Remaining</div>
                </div>
            </div>
        </div>

        <!-- Billing Actions -->
        @if($user->plan !== 'free')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Billing Actions</h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <a href="{{ route('billing.portal') }}" class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Manage Payment Methods</div>
                        <div class="text-sm text-gray-500">Update card or billing info</div>
                    </div>
                </a>

                @if($subscription && !$subscription->canceled())
                    <form action="{{ route('billing.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel?')">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-4 p-4 border border-red-200 rounded-xl hover:bg-red-50 transition-colors text-left">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </div>
                            <div>
                                <div class="font-medium text-red-900">Cancel Subscription</div>
                                <div class="text-sm text-red-700">{{ $subscription->ends_at ? 'Continue until ' . $subscription->ends_at->format('F j, Y') : 'Cancels immediately' }}</div>
                            </div>
                        </button>
                    </form>
                @elseif($subscription && $subscription->canceled())
                    <form action="{{ route('billing.resume') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-left">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Resume Subscription</div>
                                <div class="text-sm text-gray-500">Reactivate your plan</div>
                            </div>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endif

        <!-- Billing History -->
        @if((isset($charges) && $charges->count() > 0) || (isset($invoices) && $invoices->count() > 0))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Billing History</h2>

            <div class="space-y-4">
                @foreach($charges as $charge)
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-0">
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $charge->description ?? 'Access Report Card Payment' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::createFromTimestamp($charge->created)->format('F j, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-medium text-gray-900">
                                ${{ number_format($charge->amount / 100, 2) }}
                            </span>
                            @if($charge->receipt_url)
                                <a href="{{ $charge->receipt_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
