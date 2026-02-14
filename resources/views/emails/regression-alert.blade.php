@component('mail::message')
# ⚠️ Accessibility Score Dropped

Hi {{ $currentScan->user->name ?? 'there' }},

We noticed a regression in your accessibility score for **{{ $domain }}**:

@component('mail::table')
| Metric | Previous | Current | Change |
|--------|----------|---------|--------|
| Score | {{ $previousScore }} | {{ $currentScore }} | -{{ $scoreDrop }} |
| Grade | {{ $previousGrade }} | {{ $currentGrade }} | ↓ |
@endcomponent

@component('mail::panel')
## What changed?
Your score dropped by **{{ $scoreDrop }} points**. This typically means:
- New accessibility issues were introduced
- A page was modified without considering accessibility
- Third-party content was added that doesn't meet WCAG standards
@endcomponent

@component('mail::button', ['url' => route('dashboard.scan', $currentScan), 'color' => 'red'])
View What Changed
@endcomponent

@component('mail::panel')
### Common Causes
- New images without alt text
- Forms without labels
- Links that don't make sense out of context
- Missing heading structure
- Color contrast issues
@endcomponent

Don't worry — we'll help you get back on track!

Thanks for using Access Report Card!

@slot('footer')
&copy; {{ date('Y') }} Access Report Card. [Unsubscribe]({{ URL::signedRoute('email.unsubscribe', $currentScan->user) }})
@endslot
@endcomponent
