@component('mail::message')
# Your Weekly Accessibility Digest 📊

Hi {{ $user->name }},

Here's a summary of your accessibility scans from the past week.

@if(!empty($stats['scans']) && $stats['scans'] > 0)
## 📈 Your Weekly Stats

@component('mail::table')
| Metric | Value |
|--------|-------|
| Scans Completed | {{ $stats['scans'] ?? 0 }} |
| Pages Scanned | {{ $stats['pages'] ?? 0 }} |
| Issues Found | {{ $stats['issues'] ?? 0 }} |
| Avg. Score | {{ $stats['avg_score'] ?? 'N/A' }} |
@endcomponent

@if(!empty($stats['improved']))
### ✅ Improvements
Great job! Your average score improved by **{{ $stats['improved'] }} points** this week.

@elseif(!empty($stats['declined']))
### ⚠️ Score Decline
Your average score dropped by **{{ $stats['declined'] }} points** this week. Consider running a new scan to check for regressions.
@endif

@else
## No Scans This Week

You haven't run any scans in the past 7 days. Here's a quick reminder of what AccessScan can do for you:

@component('mail::panel')
**What you'll find:**
- ♿ WCAG A/AA violations
- 🖼️ Alt text issues
- 🔗 Broken links
- 🎨 Color contrast problems
- ⌨️ Keyboard navigation issues
@endcomponent
@endif

## 💡 Pro Tip of the Week

Did you know? **Alt text** should be descriptive but concise — aim for 125 characters or less. Avoid phrases like "image of" or "picture of" since screen readers already announce it's an image.

@component('mail::button', ['url' => route('dashboard'), 'color' => 'primary'])
Run a Scan Now
@endcomponent

Thanks for using AccessScan!

@slot('footer')
&copy; {{ date('Y') }} AccessScan. [View dashboard]({{ route('dashboard') }}) | [Manage settings]({{ url('/settings') }})
@endslot
@endcomponent
