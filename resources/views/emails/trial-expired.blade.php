@component('mail::message')
# Your AccessScan Trial Has Expired 👋

Hi {{ $user->name }},

Your free trial has ended. Don't worry — your scan history is still saved!

## What you missed during your trial

You had access to all Pro features:
- 50 scans per month (instead of 5)
- 100 pages per scan
- Scheduled automatic scans
- PDF & CSV exports
- Priority support

## Come back anytime!

You can still use AccessScan for free — you'll just have:
- 5 scans per month
- Up to 5 pages per scan
- Basic reports

@component('mail::panel')
### Ready to unlock full access?
Upgrade to Pro for just **$29/month** — or grab **lifetime access for $197**.
@endcomponent

@component('mail::button', ['url' => route('billing.pricing'), 'color' => 'primary'])
View Plans & Pricing
@endcomponent

If you have any questions, just hit reply — we're here to help!

@slot('footer')
&copy; {{ date('Y') }} AccessScan. [View plans]({{ route('billing.pricing') }}) | [Contact support](mailto:support@accessscan.app)
@endslot
@endcomponent
