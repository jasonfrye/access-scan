@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Create Pricing Configuration</h1>
            <p class="text-gray-600 mb-4">Configure pricing plans and features for your A/B testing.</p>

            <div class="flex gap-4">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">
                    ← Cancel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('pricing-configs.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Configuration Name</label>
                    <input type="text"
                            id="name"
                            name="name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Default Pricing, A/B Test A"
                            required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="description"
                            name="description"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Internal notes about this configuration..."></textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                </div>

                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Plans Configuration</h3>
                    <p class="text-blue-100 text-sm mb-4">Define up to 3 plans: free, monthly, lifetime.</p>
                </div>

                <div class="mb-6">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Free Plan</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                            <input type="text"
                                name="plans[free][name]"
                                value="Free"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.free.name')
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                            <input type="number"
                                name="plans[free][price]"
                                value="0"
                                min="0"
                                step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.free.price')
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interval</label>
                            <select name="plans[free][interval]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">None</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                                <option value="lifetime">Lifetime</option>
                            </select>
                            @error('plans.free.interval')
                        </div>
                    </div>

                    <div class="col-span-3 mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Features (comma-separated)</label>
                        <input type="text"
                                name="plans[free][features]"
                                placeholder="5 scans/month, basic report"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.free.features')
                        </div>
                    <div class="col-span-2 mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                        <input type="text"
                                name="plans[free][button_text]"
                                value="Sign Up Free"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.free.button_text')
                        </div>
                    <div class="col-span-1 flex items-end mt-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox"
                                    id="plans[free][highlight]"
                                    name="plans[free][highlight]"
                                    class="h-4 w-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Highlight</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6 mt-8">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Monthly Plan</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                            <input type="text"
                                name="plans[monthly][name]"
                                value="Pro"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.monthly.name')
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                            <input type="number"
                                name="plans[monthly][price]"
                                value="29"
                                min="0"
                                step="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.monthly.price')
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interval</label>
                            <select name="plans[monthly][interval]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="month" selected>Month</option>
                                <option value="year">Year</option>
                                <option value="lifetime">Lifetime</option>
                            </select>
                            @error('plans.monthly.interval')
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-2">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                            <input type="text"
                                name="plans[monthly][features]"
                                placeholder="50 scans/month, detailed reports"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.monthly.features')
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                            <input type="text"
                                name="plans[monthly][button_text]"
                                value="Start Free Trial"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.monthly.button_text')
                        </div>
                        <div class="col-span-1 flex items-end mt-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox"
                                    id="plans[monthly][highlight]"
                                    name="plans[monthly][highlight]"
                                    class="h-4 w-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Highlight</span>
                            </label>
                        </div>
                    </div>

                <div class="mb-6 mt-8">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">Lifetime Plan</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                            <input type="text"
                                name="plans[lifetime][name]"
                                value="Lifetime"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.lifetime.name')
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                            <input type="number"
                                name="plans[lifetime][price]"
                                value="197"
                                min="0"
                                step="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.lifetime.price')
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interval</label>
                            <select name="plans[lifetime][interval]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="lifetime" selected>Lifetime</option>
                            </select>
                            @error('plans.lifetime.interval')
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-2">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                            <input type="text"
                                name="plans[lifetime][features]"
                                placeholder="Unlimited, detailed reports, never pay again"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.lifetime.features')
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                            <input type="text"
                                name="plans[lifetime][button_text]"
                                value="Get Lifetime Access"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            @error('plans.lifetime.button_text')
                        </div>
                        <div class="col-span-1 flex items-end mt-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox"
                                    id="plans[lifetime][highlight]"
                                    name="plans[lifetime][highlight]"
                                    class="h-4 w-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Highlight</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Display Settings</h3>
                    <p class="text-blue-100 text-sm mb-4">Customize how plans are displayed to users.</p>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Badge Text</label>
                                <input type="text"
                                    name="badge_text"
                                    placeholder="Most Popular"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 bg-white">
                                    @error('badge_text')
                            </div>
                            <p class="text-blue-200 text-xs">Shown on highlighted plan</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">CTA Copy</label>
                                <input type="text"
                                    name="cta_copy"
                                    placeholder="Get Started"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 bg-white">
                                    @error('cta_copy')
                            </div>
                            <p class="text-blue-200 text-xs">Call-to-action button text</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-medium text-white mb-2">Sort Order</label>
                                <input type="number"
                                    name="sort_order"
                                    value="0"
                                    min="0"
                                    class="w-24 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 bg-white">
                                    @error('sort_order')
                            </div>
                            <p class="text-blue-200 text-xs">Lower numbers appear first</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <label class="block text-sm font-medium text-white mb-2">Activate After Creating</label>
                                <div class="flex items-center">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" id="activate_no" name="activate" value="0"
                                            class="h-4 w-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                    </label>
                                    <span class="text-sm font-medium text-white ml-2">No</span>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" id="activate_yes" name="activate" value="1" checked
                                            class="h-4 w-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                    </label>
                                    <span class="text-sm font-medium text-white ml-2">Yes</span>
                                </label>
                                </div>
                            </div>
                            <p class="text-blue-200 text-xs">Make this config immediately active</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                        Create Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
