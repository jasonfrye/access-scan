@component('mail::message')
# @if($daysLeft === 1) ⚠️ Your trial ends tomorrow! @else Your trial ends in {{ $daysLeft }} days @endif

Hi {{ $user->name }},

Your Access Report Card trial is winding down — you have **{{ $daysLeft }} {{ Str::plural('day', $daysLeft) }}** left before your free trial expires.

@if($daysLeft > 1)
## What you get with Pro ($29/month)
@else
## Don't lose your progress — upgrade now!
@endif

During your trial, you've had the chance to:
- ✅ Scan websites for accessibility issues
- ✅ Generate PDF & CSV reports
- ✅ See detailed WCAG compliance breakdowns
- ✅ Get recommendations to fix issues

@component('mail::panel')
### Pro vs Free
| Feature | Free | Pro |
|---------|------|-----|
| Scans/month | 5 | 50 |
| Pages/scan | 5 | 100 |
| Scheduled scans | ❌ | ✅ |
| PDF/CSV exports | ❌ | ✅ |
| Priority support | ❌ | ✅ |
@endcomponent

@if($daysLeft === 1)
@component('mail::button', ['url' => route('billing.pricing'), 'color' => 'red'])
Upgrade Now — Don't Lose Access
@endcomponent
@else
@component('mail::button', ['url' => route('billing.pricing'), 'color' => 'primary'])
Upgrade to Pro
@endcomponent
@endif

@if($daysLeft > 1)
### Still deciding?
No problem! Here are a few things our Pro users love:
- **Scheduled scans** — Automatically check your site weekly/monthly
- **More pages per scan** — Scan up to 100 pages at once
- **Export to CSV** — Hand off issues to your developers
- **Priority support** — Get answers faster

@component('mail::panel')
### What happens when my trial ends?
- You'll be downgraded to Free plan (5 scans/month)
- All your scan history is saved
- You can upgrade anytime to unlock Pro features
@endcomponent
@endif

Thanks for trying Access Report Card!

@slot('footer')
&copy; {{ date('Y') }} Access Report Card. [Upgrade now]({{ route('billing.pricing') }}) | [Unsubscribe]({{ URL::signedRoute('email.unsubscribe', $user) }})
@endslot
@endcomponent
