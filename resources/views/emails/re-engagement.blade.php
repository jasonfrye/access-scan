@component('mail::message')
# We Miss You, {{ $user->name }}! 👋

It's been **{{ $daysInactive }} {{ Str::plural('day', $daysInactive) }}** since you last used Access Report Card.

@if($daysInactive >= 60)
## It's Not Too Late to Finish What You Started

You started making your website more accessible — don't let all that progress slip away!

@elseif($daysInactive >= 30)
## Come Back to Accessibility

A lot can change on a website in {{ $daysInactive }} days. Maybe you added new pages, images, or forms?

@endif

@component('mail::panel')
### Here's What You've Been Missing
- 📊 Weekly accessibility digests
- 🚨 Regression alerts (score drops)
- 🎉 Celebration emails (score improvements!)
- 📈 Track your progress over time
@endcomponent

## 🔥 Special Come-Back Offer

@if($user->isOnTrial() && $user->trial_ends_at)
**Your trial is still active!** You have until {{ $user->trial_ends_at->format('F j, Y') }} to upgrade.

@elseif(!$user->isPro())
**Unlock Pro features** — get 50 scans/month, scheduled scans, and exports.

@endif

@component('mail::button', ['url' => route('dashboard'), 'color' => 'green'])
Run a Scan Now
@endcomponent

@if(!$user->isPro())
@component('mail::button', ['url' => route('billing.pricing'), 'color' => 'primary'])
Upgrade to Pro
@endcomponent
@endif

## 💡 Quick Win

Just ran a new scan? Here are the top 3 issues to fix first:

1. **Alt Text** — Add descriptions to images
2. **Color Contrast** — Make text readable
3. **Form Labels** — Help users navigate forms

Not ready to scan? No problem — just [update your notification preferences]({{ URL::signedRoute('email.unsubscribe', $user) }}).

Thanks for being part of the Access Report Card community!

@slot('footer')
&copy; {{ date('Y') }} Access Report Card. [View dashboard]({{ route('dashboard') }}) | [Unsubscribe]({{ URL::signedRoute('email.unsubscribe', $user) }})
@endslot
@endcomponent
