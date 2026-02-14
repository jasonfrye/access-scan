@component('mail::message')
# Welcome to Access Report Card! 🎉

Hi {{ $name }},

Thanks for signing up! You're now ready to start scanning websites for accessibility compliance. We're here to help you make the web more inclusive.

@component('mail::panel')
## Your Free Plan Includes

- **{{ $scanLimit }} scans** per month
- Basic scan results
- Email notifications when scans complete
@endcomponent

## Getting Started

@component('mail::button', ['url' => route('dashboard'), 'color' => 'primary'])
Run Your First Scan
@endcomponent

### What We'll Check

Our scanner tests for WCAG A/AA compliance across:

- **Images** - Missing alt text, decorative images
- **Links** - Broken links, vague link text
- **Forms** - Missing labels, unclear error messages
- **Navigation** - Keyboard accessibility, skip links
- **Color** - Contrast ratios, color-dependent information
- **ARIA** - Proper landmark roles and attributes

@component('mail::panel')
### Why Accessibility Matters

- **Legal protection** - ADA lawsuits increased 400%+ since 2020
- **SEO benefits** - Accessible sites rank better
- **More customers** - 1 in 4 adults have a disability
- **Better UX** - Improvements help everyone
@endcomponent

Need help? Just reply to this email — we're happy to assist!

@component('mail::subcopy')
P.S. Want unlimited scans and detailed reports? Check out our paid plans anytime.
@endcomponent

@slot('footer')
&copy; {{ date('Y') }} Access Report Card. All rights reserved.
[Unsubscribe]({{ URL::signedRoute('email.unsubscribe', $user) }})
@endslot
@endcomponent
