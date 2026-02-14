@php
    $schedule = $schedulesByDomain[$domain] ?? null;
    $isPaid = Auth::user()->isPaid();
    $domainUrl = $latestScan->url;
@endphp

<div class="flex-shrink-0" x-data="{ popoverOpen: false }" @click.stop>
    @if($isPaid && $schedule)
        {{-- Paid user with existing schedule: filled clock with popover --}}
        <div class="relative">
            <button @click="popoverOpen = !popoverOpen" class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors" title="Scheduled: {{ $schedule->frequency }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </button>

            <div x-show="popoverOpen" @click.outside="popoverOpen = false" x-transition
                 class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 p-4 z-30">
                <div class="text-sm font-semibold text-gray-900 mb-2">Scheduled Scan</div>
                <div class="space-y-2 text-sm text-gray-600 mb-3">
                    <div class="flex justify-between">
                        <span>Frequency</span>
                        <span class="font-medium capitalize">{{ $schedule->frequency }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status</span>
                        <span class="font-medium {{ $schedule->is_active ? 'text-green-600' : 'text-gray-400' }}">{{ $schedule->is_active ? 'Active' : 'Paused' }}</span>
                    </div>
                    @if($schedule->next_run_at)
                        <div class="flex justify-between">
                            <span>Next run</span>
                            <span class="font-medium">{{ $schedule->next_run_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex gap-2 pt-2 border-t border-gray-100">
                    <form action="{{ route('dashboard.scheduled.toggle', $schedule) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-3 text-xs font-medium rounded-lg transition-colors {{ $schedule->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                            {{ $schedule->is_active ? 'Pause' : 'Resume' }}
                        </button>
                    </form>
                    <form action="{{ route('dashboard.scheduled.destroy', $schedule) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-1.5 px-3 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($isPaid)
        {{-- Paid user without schedule: outline clock to open modal --}}
        <button @click="$dispatch('open-modal', { modal: 'add-scheduled', url: '{{ $domainUrl }}' })" class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Schedule recurring scan">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    @else
        {{-- Free user: clock with lock badge linking to pricing --}}
        <a href="{{ route('billing.pricing') }}" class="relative p-2 rounded-lg text-gray-300 hover:text-gray-400 transition-colors" title="Upgrade to schedule scans">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg class="w-3 h-3 absolute -top-0.5 -right-0.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
        </a>
    @endif
</div>
