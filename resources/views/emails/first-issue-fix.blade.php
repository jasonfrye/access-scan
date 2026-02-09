@component('mail::message')
# How to Fix Your Top Accessibility Issues 🚀

Hi {{ $user->name }},

We found some common accessibility issues on **{{ $scan->domain }}**. Here's how to fix them!

@component('mail::panel')
## Quick Wins
Focus on these fixes first for maximum impact:
@endcomponent

@if(!empty($topIssues))
## Your Top Issues

@foreach($topIssues as $index => $issue)
### {{ $index + 1 }}. {{ $issue['type'] ?? 'Accessibility Issue' }}

**Problem:** {{ $issue['message'] ?? 'Issue detected' }}

**How to Fix:**
@if(isset($issue['code']) && str_contains($issue['code'], 'AltText'))
> Add descriptive alt text to your images. Skip phrases like "image of" or "picture of" — just describe what the image shows.

**Example:**
```html
<!-- Instead of this: -->
<img src="photo.jpg" alt="image">

<!-- Use this: -->
<img src="photo.jpg" alt="Golden retriever playing fetch in a park">
```

@elseif(isset($issue['code']) && str_contains($issue['code'], 'Contrast'))
> Check color contrast between text and background. Use tools like WebAIM's Contrast Checker.

**Fix:** Darken text or lighten backgrounds. WCAG AA requires:
- 4.5:1 for normal text
- 3:1 for large text (18pt+ or 14pt+ bold)

@elseif(isset($issue['code']) && str_contains($issue['code'], 'Label'))
> Add labels to all form fields so screen readers can announce them.

**Example:**
```html
<!-- Instead of this: -->
<input type="text">

<!-- Use this: -->
<label for="email">Email Address</label>
<input type="text" id="email" name="email">
```

@else
> Review the issue details in your scan report and add the necessary accessibility attributes.

@endif

@if(!$loop->last)
---
@endif
@endforeach

@else
## Common Issues & Fixes

### 🔤 Missing Alt Text
Images without alt text are invisible to screen reader users.

**Fix:** Add descriptive `alt` attributes to all images.

### 🎨 Color Contrast
Text that's hard to read fails users with vision impairments.

**Fix:** Ensure 4.5:1 contrast ratio for normal text.

### 📝 Form Labels
Unlabeled forms confuse everyone — especially screen reader users.

**Fix:** Add `<label>` elements connected to form inputs via `for` attribute.

### 🔗 Link Text
Links like "click here" or "read more" are meaningless out of context.

**Fix:** Describe where the link goes: "Read our accessibility guide"

### 🏗️ Heading Structure
Skipping heading levels (h1 → h4) confuses navigation.

**Fix:** Use sequential headings that describe section content.
@endif

## 🎯 Your Next Step

@component('mail::button', ['url' => route('dashboard.scan', $scan), 'color' => 'green'])
View Full Report & Fixes
@endcomponent

## 📚 Learn More

- [WebAIM Checklist](https://webaim.org/standards/wcag/checklist)
- [W3C WCAG 2.1 Quick Reference](https://www.w3.org/WAI/WCAG21/quickref/)
- [Google Lighthouse Accessibility Guide](https://developers.google.com/web/fundamentals/accessibility)

Making your site accessible helps **everyone** — and it's great for SEO too!

Thanks for using AccessScan!

@slot('footer')
&copy; {{ date('Y') }} AccessScan. [View dashboard]({{ route('dashboard') }}) | [Unsubscribe]({{ url('/settings') }})
@endslot
@endcomponent
