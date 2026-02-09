@component('mail::message')
# 🎉 Amazing Progress, {{ $user->name }}!

Your accessibility score went from **{{ $previousScan->score }}** to **{{ $currentScan->score }}** — that's a **+{{ $improvement }} point improvement**!

@component('mail::panel')
### Your Improvement
| Before | After |
|--------|-------|
| {{ $previousScan->score }} pts ({{ $previousScan->grade }}) | {{ $currentScan->score }} pts ({{ $currentScan->grade }}) |
@endcomponent

## What You Fixed

@if($currentScan->issues_count < $previousScan->issues_count)
You resolved **{{ $previousScan->issues_count - $currentScan->issues_count }} accessibility issues**! Great work on making your site more inclusive.

@else
Your score improved even though issue count stayed similar — likely thanks to fixing higher-priority errors. Keep it up!

@endif

## 🚀 Keep the Momentum Going

You're on a roll! Here are your options:

@component('mail::button', ['url' => route('dashboard'), 'color' => 'green'])
Run Another Scan
@endcomponent

@if(!$user->isPro())
@component('mail::button', ['url' => route('billing.pricing'), 'color' => 'primary'])
Upgrade to Pro — Unlock Scheduled Scans
@endcomponent
@endif

## 💡 Pro Tip

Consistent accessibility improvements help:
- **SEO** — Search engines favor accessible sites
- **UX** — Everyone benefits from clear navigation
- **Legal** — Reduce ADA/WCAG compliance risk
- **Brand** — Show you care about all users

Thanks for making the web more accessible!

@slot('footer')
&copy; {{ date('Y') }} AccessScan. [View dashboard]({{ route('dashboard') }}) | [Unsubscribe]({{ url('/settings') }})
@endslot
@endcomponent
