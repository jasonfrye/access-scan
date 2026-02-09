@component('mail::message')
# Your Accessibility Scan is Complete! 🎉

Hi {{ $scan->user->name ?? 'there' }},

Good news! We've finished scanning **{{ $scan->domain }}**. Here's what we found:

@component('mail::panel')
## Score: {{ $grade }} ({{ $score }}/100)

- **{{ $errorsCount }} Errors** - Must fix for compliance
- **{{ $warningsCount }} Warnings** - Should fix
- **{{ $issuesCount - $errorsCount - $warningsCount }} Notices** - Nice to have
@endcomponent

@if(count($topIssues) > 0)
## Top Issues to Fix

@foreach($topIssues as $issue)
@component('mail::table')
| WCAG {{ $issue['wcag'] }} ({{ $issue['level'] }}) |
|---|
| {{ Str::limit($issue['message'], 100) }} |
@endcomponent
@endforeach
@endif

@component('mail::button', ['url' => route('dashboard.scan', $scan), 'color' => 'primary'])
View Full Report
@endcomponent

@component('mail::panel')
### Quick Tips
- Errors prevent screen readers from working properly
- Fix images without alt text first
- Ensure form inputs have labels
- Check color contrast ratios
@endcomponent

Thanks for using AccessScan!

@component('mail::subcopy')
Questions? Just reply to this email — we're here to help.
@endcomponent

@slot('footer')
&copy; {{ date('Y') }} AccessScan. All rights reserved.
[Unsubscribe]({{ url('/unsubscribe/'.$scan->user_id) }})
@endslot
@endcomponent
