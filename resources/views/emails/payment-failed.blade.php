@component('mail::message')
# Payment Failed ⚠️

Hi {{ $user->name ?? 'there' }},

We tried to process your payment for your **{{ $planName }}** subscription ({{ $amount }}) but it didn't go through.

@component('mail::panel')
## What happened?
{{ $errorMessage }}

@if($lastFour)
Card ending in: ••••{{ $lastFour }}
@endif
@endcomponent

This means your subscription is at risk of being canceled. To avoid any interruption to your accessibility scanning service, please update your payment method.

@component('mail::button', ['url' => $updatePaymentUrl, 'color' => 'error'])
Update Payment Method
@endcomponent

@component('mail::panel')
### What happens if payment fails again?
- You'll receive reminders over the next few days
- After 7 days of failed payments, your subscription will be canceled
- You'll lose access to paid features (scheduled scans, detailed reports, unlimited scans)
@endcomponent

If you have any questions or need help with payment, just reply to this email — we're here to help.

@slot('footer')
&copy; {{ date('Y') }} AccessScan. All rights reserved.
[Update Payment Method]({{ $updatePaymentUrl }})
@endslot
@endcomponent
