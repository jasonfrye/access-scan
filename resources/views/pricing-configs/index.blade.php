@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pricing Configurations</h1>
            <p class="text-gray-600 mb-4">Manage pricing A/B tests and configuration variants.</p>

            <div class="flex gap-4">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-700">
                    ← Back to Dashboard
                </a>
                @if(PricingConfig::count() > 1)
                    <button onclick="window.location.href='{{ route('pricing-configs.create') }}'"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create New Config
                    </button>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($configs->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h-2.95a1.1.1-1.1 2.9l2.07 1.1-2.7 8-8a1-1-1-1-1.2 5.1-1-1.4 8-9-1.1 1-2 9-1-8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold">No Pricing Configurations</h3>
                    <p class="text-gray-600 mb-4">Create your first pricing configuration to get started.</p>
                    <a href="{{ route('pricing-configs.create') }}"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
                        Create First Config
                    </a>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plans</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($configs as $config)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm">
                                    {{ $config->name }}
                                    @if($config->description)
                                        <p class="text-gray-500 text-xs mt-1">{{ $config->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($config->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="font-mono text-gray-700">
                                        {{ count($config['plans'] ?? []) }} plans
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($config->is_active)
                                        <span class="text-green-600 font-medium">Yes</span>
                                    @else
                                        <span class="text-gray-600">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $config->created_at?->format('M j, Y') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $config->updated_at?->format('M j, Y g:i A') : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        @if(!$config->is_active)
                                            <button onclick="window.location.href='{{ route('pricing-configs.activate', $config->id) }}'"
                                                class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                Activate
                                            </button>
                                        @endif
                                        @if($config->is_active)
                                            <button onclick="window.location.href='{{ route('pricing-configs.edit', $config->id) }}'"
                                                class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                                Edit
                                            </button>
                                        @endif
                                        <a href="{{ route('pricing-configs.preview', $config->id) }}"
                                            class="text-blue-600 hover:text-blue-700 text-sm">
                                            Preview
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </div>
@endsection
