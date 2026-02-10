# AccessScan Design System

## Design Concept: "Clear Compliance"

A professional, trustworthy design system that makes accessibility compliance approachable for small business owners. The design emphasizes clarity, actionability, and confidence-building through familiar patterns (report card grades) and professional typography.

---

## Visual Identity

### Typography
- **Primary Font**: IBM Plex Sans (400, 500, 600, 700)
  - Clear, technical, yet friendly
  - Excellent readability at all sizes
  - Professional without being intimidating
- **Monospace Font**: IBM Plex Mono (400, 500)
  - Used for URLs, code snippets, and technical data
  - Maintains the IBM Plex family cohesion

### Color Palette

#### Primary Colors
- **Blue 600** (#1e40af) - Primary brand, trust, compliance
- **Blue 700-800** - Gradients and depth
- **White** (#ffffff) - Clean backgrounds

#### Semantic Colors
- **Success Green** (#059669) - A grades, passing tests
- **Warning Amber** (#d97706) - B/C grades, warnings
- **Error Red** (#dc2626) - D/F grades, critical issues
- **Info Cyan** (#0891b2) - Notices, informational

#### Grading System Colors
- **Grade A**: Green gradient (500-600) - Excellent accessibility
- **Grade B**: Light green gradient (400-500) - Good accessibility
- **Grade C**: Yellow gradient (400-500) - Needs improvement
- **Grade D**: Orange gradient (400-500) - Poor accessibility
- **Grade F**: Red gradient (500-600) - Critical issues

### Design Principles

1. **Accessibility First** (Practice What We Preach)
   - High contrast ratios (WCAG AA compliant)
   - Clear focus states on all interactive elements
   - Skip-to-content link for keyboard navigation
   - Semantic HTML throughout
   - ARIA labels where needed

2. **Report Card Metaphor**
   - Large, prominent letter grades (A-F)
   - Familiar grading system everyone understands
   - Color-coded for quick recognition
   - Reduces intimidation factor

3. **Confidence Building**
   - Smooth animations and transitions
   - Clear call-to-actions
   - Progress indicators
   - Trust badges and social proof

4. **Professional but Approachable**
   - Clean layouts with generous whitespace
   - Friendly micro-copy
   - Clear visual hierarchy
   - No jargon in primary UI

---

## Components

### Navigation
- Clean white background with subtle border
- Blue brand logo with checkmark icon
- Responsive with mobile menu
- Clear hierarchy: Dashboard > Pricing > Sign In > Get Started (CTA)

### Hero Section (Homepage)
- Full-width gradient background (blue 600-800)
- Subtle geometric pattern overlay
- Clear value proposition
- Trust indicators below scan form
- Prominent white card for scan input

### Stats Cards (Dashboard)
- Rounded corners (2xl = 1rem)
- 2px borders with hover color transitions
- Icon + Label + Large Number layout
- Monospace "AVG", "TOTAL", "LEFT" labels
- Color-coded icons matching semantic meaning

### Grade Badges
- Report card style: 16x20 grid
- Gradient backgrounds
- Large letter grade (3xl)
- "GRADE" label below
- Animated fade-in on load
- Shadow for depth

### Scan History Cards
- Clean white cards with 2px borders
- Grade badge + URL + Score layout
- Colored badge pills for issue counts
- Hover state with background tint
- Font-mono for URLs

### Sidebar Widgets

#### Quick Scan
- Gradient background (blue 600-800)
- White input with rounded corners
- Lightning bolt icon
- Full-width button

#### Account Info
- Gradient plan badge (blue 50-100)
- Icon + metric rows
- Split action buttons (Upgrade / Settings)

### Buttons

#### Primary (CTA)
- `bg-blue-600` with `hover:bg-blue-700`
- `rounded-xl` (12px)
- Font-semibold
- Scale on hover (`hover:scale-105`)
- White text

#### Secondary
- `bg-gray-100` with `hover:bg-gray-200`
- Same rounded corners
- Gray text

### Forms
- Rounded inputs (`rounded-xl`)
- Clear placeholder text
- Visible focus states (blue ring)
- Inline validation
- Font-mono for URL inputs

---

## Animations

### Subtle Transitions
```css
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}
```

### Grade Badge Animation
```css
@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.grade-badge {
    animation: fadeInScale 0.4s ease-out;
}
```

### Scan Progress
```css
@keyframes scan-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.scanning {
    animation: scan-pulse 2s ease-in-out infinite;
}
```

### Hover States
- All buttons scale slightly on hover
- Cards lift with shadow
- Links underline
- Smooth 300ms transitions

---

## Responsive Behavior

### Breakpoints
- **Mobile**: < 768px
  - Single column layouts
  - Stacked cards
  - Simplified navigation

- **Tablet**: 768px - 1024px
  - 2-column grids
  - Sidebar below content

- **Desktop**: > 1024px
  - Full multi-column layouts
  - Sidebar beside content
  - Maximum width containers (7xl = 80rem)

### Mobile Optimizations
- Larger touch targets (min 44x44px)
- Simplified navigation with hamburger menu
- Stacked stat cards
- Full-width buttons

---

## Accessibility Features

### Keyboard Navigation
- Visible focus states (3px blue outline, 2px offset)
- Skip-to-content link
- Logical tab order
- Enter/Space for buttons

### Screen Readers
- Semantic HTML5 elements
- ARIA labels on icons (`aria-hidden="true"` for decorative)
- `role` attributes on nav, main, contentinfo
- Descriptive link text

### Color Contrast
- All text meets WCAG AA (4.5:1 minimum)
- Icons have sufficient contrast
- Grade badges use both color AND text
- Never rely on color alone

### Focus Management
- Clear focus indicators
- Focus trapped in modals
- Keyboard shortcuts documented

---

## Implemented Pages

### 1. Landing Page (`scan/index.blade.php`)
- Hero with gradient background
- Geometric pattern overlay
- Prominent scan form in white card
- Trust indicators (free scan, no CC, 60 seconds)
- "How It Works" - 3 steps with numbered icons
- Features grid (6 cards)
- CTA section with dual buttons

### 2. Dashboard (`dashboard.blade.php`)
- 4 stat cards (Total, Average, Remaining, Issues)
- Scan history with report card grades
- Quick scan sidebar widget
- Account info with plan badge
- Empty state with illustration
- Pagination for history

### 3. Guest Layout (`layouts/guest.blade.php`)
- Clean navigation with logo
- Skip-to-content link
- Footer with links
- IBM Plex Sans fonts loaded
- CSS animations defined
- Accessible markup throughout

### 4. Scan Results (Ready for Implementation)
- Large grade badge header
- Score breakdown (errors, warnings, notices)
- Issue list with priorities
- Fix recommendations
- Export buttons (PDF, CSV)
- Share functionality

### 5. Pricing Page (Ready for Implementation)
- 3-column pricing cards
- Free / Pro ($29/mo) / Lifetime ($197)
- Feature comparison
- Clear CTAs
- FAQ section
- Highlighted "Most Popular"

---

## Implementation Notes

### CSS Variables
```css
:root {
    --color-primary: #1e40af;
    --color-primary-dark: #1e3a8a;
    --color-success: #059669;
    --color-warning: #d97706;
    --color-error: #dc2626;
    --gradient-hero: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
}
```

### Tailwind Config
- Extended with IBM Plex fonts
- Custom animations configured
- Accessibility plugin enabled
- Responsive breakpoints standard

### Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Grid and Flexbox
- CSS Custom Properties
- Transform and transitions

---

## Future Enhancements

### Phase 2 Features
- Dark mode toggle
- Animated charts for trend data
- Advanced filtering on scan history
- Bulk actions on scans
- Team collaboration features

### Micro-interactions
- Confetti on passing grade
- Progress bar for scanning
- Toast notifications for actions
- Skeleton loaders during data fetch

### Performance
- Lazy load images below fold
- Prefetch critical routes
- Code splitting for JS
- Optimize font loading

---

## Brand Voice

### Tone
- **Professional**: We know what we're doing
- **Friendly**: We're here to help, not intimidate
- **Confident**: Accessibility is achievable
- **Clear**: No jargon, plain language

### Example Copy
- ❌ "WCAG 2.1 Level AA Conformance Evaluation"
- ✅ "Check if your website is ADA compliant"

- ❌ "Initiate accessibility audit"
- ✅ "Scan your website"

- ❌ "Remediation recommendations"
- ✅ "How to fix these issues"

---

## Design Checklist

For every new component, ensure:
- [ ] Uses IBM Plex Sans/Mono fonts
- [ ] Meets color contrast requirements
- [ ] Has visible focus states
- [ ] Works on mobile (responsive)
- [ ] Has smooth transitions (300ms)
- [ ] Uses semantic HTML
- [ ] Includes ARIA labels where needed
- [ ] Follows 8px spacing grid
- [ ] Uses consistent border radius (xl = 12px, 2xl = 16px)
- [ ] Matches existing component patterns

---

## Resources

- **Fonts**: [IBM Plex on Google Fonts](https://fonts.google.com/specimen/IBM+Plex+Sans)
- **Colors**: [Tailwind Blue Palette](https://tailwindcss.com/docs/customizing-colors)
- **Icons**: Heroicons (outline style)
- **WCAG Guidelines**: [Web Content Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

**Design System Version**: 1.0
**Last Updated**: 2026-02-09
**Maintained by**: AccessScan Design Team
