@component('mail::message')
# Get More Out of Your Pro Plan, {{ $user->name }}! 🚀

You're a Pro member, but you're not using all the features you're paying for!

@if(!empty($unusedFeatures))
## Features You Haven't Used Yet

@component('mail::table')
| Feature | Description |
|---------|-------------|
@foreach($unusedFeatures as $feature)
| {{ $feature['name'] }} | {{ $feature['description'] }} |
@endforeach
@endcomponent

@else
## Pro Features You're Not Using

Here are some powerful Pro features you might be missing:

### 📅 Scheduled Scans
Set up automatic weekly or monthly scans — never forget to check your site again!

**Used:** 0 times

### 📊 PDF & CSV Exports
Download detailed reports to share with your team or developers.

**Used:** 0 times

### 📈 Weekly Digests
Get a summary of your accessibility progress every Monday.

**Enabled:** No

### 🔔 Regression Alerts
We'll notify you if your accessibility score drops.

**Enabled:** No
@endif

## 💡 Quick Tutorial

@component('mail::panel')
**Scheduled Scans:** Go to Dashboard → Create Scan → Select "Schedule" → Choose frequency

**PDF Export:** Open any scan → Click "Export" → Select PDF format

**Weekly Digests:** Settings → Notifications → Enable weekly digest
@endcomponent

## 🎯 Your Next Step

@component('mail::button', ['url' => route('dashboard'), 'color' => 'primary'])
Explore Your Pro Features
@endcomponent

Not finding value in Pro? [Downgrade to Free]({{ route('billing.pricing') }}) — no hard feelings!

Thanks for being a Pro member!

@slot('footer')
&copy; {{ date('Y') }} Access Report Card. [View dashboard]({{ route('dashboard') }}) | [Unsubscribe]({{ URL::signedRoute('email.unsubscribe', $user) }})
@endslot
@endcomponent
