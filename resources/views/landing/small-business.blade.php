@extends('layouts.guest')

@section('title', 'ADA Website Compliance for Small Business - Access Report Card')

@section('content')
<div class="min-h-screen">
    {{-- Hero --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 text-white">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative max-w-5xl mx-auto px-4 py-16 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                {{-- Urgency badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 border border-red-400/30 rounded-full text-sm font-medium text-red-200 mb-8">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    ADA lawsuits against small businesses rose 300% since 2018
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    Is Your Website Putting<br>
                    Your Business at Risk?
                </h1>

                <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
                    Most small business websites fail basic ADA accessibility standards. Find out where yours stands in 60 seconds &mdash; before a demand letter does it for you.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-900 font-bold rounded-xl hover:bg-blue-50 transition-all hover:scale-105 text-lg shadow-lg shadow-blue-900/30">
                        Scan My Website Free
                    </a>
                    <a href="#how-it-works" class="px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-semibold rounded-xl hover:bg-white/20 transition-all text-lg">
                        See How It Works
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-6 text-sm text-blue-200">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        No credit card required
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Results in 60 seconds
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Plain-English fixes
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Problem / Stakes --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">The Cost of Doing Nothing</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">ADA website lawsuits don't just target big corporations. Small businesses are the #1 target because they're easier to settle.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-red-50 border border-red-100 rounded-2xl p-8 text-center">
                    <div class="text-4xl font-bold text-red-600 mb-2">$50K+</div>
                    <div class="text-sm text-red-800 font-medium mb-3">Average ADA Settlement</div>
                    <p class="text-sm text-gray-600">The average demand letter starts at $10,000. Litigation pushes costs to $50K or more, even if you win.</p>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-8 text-center">
                    <div class="text-4xl font-bold text-amber-600 mb-2">4,600+</div>
                    <div class="text-sm text-amber-800 font-medium mb-3">Lawsuits Filed in 2025</div>
                    <p class="text-sm text-gray-600">Web accessibility lawsuits have hit record numbers &mdash; and show no sign of slowing down.</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-8 text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">96%</div>
                    <div class="text-sm text-blue-800 font-medium mb-3">Of Sites Have Issues</div>
                    <p class="text-sm text-gray-600">The vast majority of websites fail WCAG accessibility standards. Yours might too &mdash; and you won't know until it's too late.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- How It Works --}}
    <div id="how-it-works" class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Fix It Before They Find It</h2>
                <p class="text-lg text-gray-600">Three steps. No technical knowledge needed.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-5 text-2xl font-bold shadow-lg shadow-blue-600/30">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Paste Your URL</h3>
                    <p class="text-gray-600">Enter your website address. We scan every page automatically &mdash; no software to install.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-5 text-2xl font-bold shadow-lg shadow-blue-600/30">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Get Your Report Card</h3>
                    <p class="text-gray-600">Receive a clear A&ndash;F grade with every issue explained in plain English. No jargon, no guessing.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-5 text-2xl font-bold shadow-lg shadow-blue-600/30">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Follow the Fix List</h3>
                    <p class="text-gray-600">Each issue comes with step-by-step instructions. Hand them to your web developer or follow along yourself.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Benefits --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Built for Business Owners, Not Developers</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">You don't need to understand code to protect your business.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="flex gap-5 p-6 bg-gray-50 rounded-2xl">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Reduce Lawsuit Risk</h3>
                        <p class="text-gray-600">Know exactly where your site falls short of ADA requirements. Fix issues proactively instead of reactively.</p>
                    </div>
                </div>
                <div class="flex gap-5 p-6 bg-gray-50 rounded-2xl">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">60-Second Scans</h3>
                        <p class="text-gray-600">No waiting days for an audit. Get your full accessibility report in about a minute.</p>
                    </div>
                </div>
                <div class="flex gap-5 p-6 bg-gray-50 rounded-2xl">
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Plain-English Reports</h3>
                        <p class="text-gray-600">Every issue is explained in language anyone can understand. No WCAG jargon, no confusion.</p>
                    </div>
                </div>
                <div class="flex gap-5 p-6 bg-gray-50 rounded-2xl">
                    <div class="flex-shrink-0 w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Ongoing Monitoring</h3>
                        <p class="text-gray-600">Schedule automatic weekly or monthly scans. Get alerted if your score drops after a site update.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Social Proof --}}
    <div class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-3">Trusted by Small Businesses</p>
                <h2 class="text-3xl font-bold text-gray-900">What Business Owners Are Saying</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4">"We had no idea our site had 47 accessibility issues. Fixed them all in a weekend using the report. Peace of mind for $29/month is a no-brainer."</p>
                    <div class="text-sm"><span class="font-semibold text-gray-900">Sarah M.</span> <span class="text-gray-500">&mdash; E-commerce Owner</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4">"Got a demand letter last year. Wish I'd found this tool sooner. Now I scan monthly and hand the PDF to my web guy. Simple."</p>
                    <div class="text-sm"><span class="font-semibold text-gray-900">Mike R.</span> <span class="text-gray-500">&mdash; Restaurant Owner</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4">"The A&ndash;F grade makes it so easy to understand. I showed our board the report card and got the budget approved same day."</p>
                    <div class="text-sm"><span class="font-semibold text-gray-900">Linda K.</span> <span class="text-gray-500">&mdash; Nonprofit Director</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Less Than the Cost of One Legal Consultation</h2>
                <p class="text-lg text-gray-600">Protect your business for the price of a coffee a day.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
                {{-- Free --}}
                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Free</h3>
                    <p class="text-sm text-gray-500 mb-4">See where you stand</p>
                    <div class="text-4xl font-bold text-gray-900 mb-6">$0</div>
                    <ul class="space-y-3 mb-8 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            5 scans per month
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            A&ndash;F grade &amp; summary
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Up to 5 pages per scan
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 text-center border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-100 transition-colors">
                        Start Free
                    </a>
                </div>

                {{-- Pro --}}
                <div class="bg-white rounded-2xl border-2 border-blue-600 p-8 relative shadow-lg shadow-blue-600/10">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-blue-600 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Best Value</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Pro Monthly</h3>
                    <p class="text-sm text-gray-500 mb-4">Full protection &amp; monitoring</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">$29</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="space-y-3 mb-8 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-medium">50 scans per month</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Up to 100 pages per scan
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Scheduled weekly/monthly scans
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            PDF reports for your web developer
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Score drop alerts
                        </li>
                    </ul>
                    <a href="{{ route('register') }}?plan=monthly" class="block w-full py-3 text-center bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        Start Pro Trial
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-10">Common Questions</h2>

            <div class="space-y-4" x-data="{ open: null }">
                @php
                    $faqs = [
                        ['q' => 'Do I really need to worry about ADA compliance?', 'a' => 'Yes. The ADA applies to businesses of all sizes. Courts have consistently ruled that websites are "places of public accommodation." Even a single-page site for a local shop can be targeted.'],
                        ['q' => 'What does the scan actually check?', 'a' => 'We test against WCAG 2.1 Level AA &mdash; the standard used in most ADA web accessibility lawsuits. This covers things like image alt text, color contrast, keyboard navigation, form labels, and more.'],
                        ['q' => 'I\'m not technical. Can I still use this?', 'a' => 'Absolutely. Every issue in your report includes a plain-English explanation and step-by-step fix. You can hand the PDF directly to whoever maintains your website.'],
                        ['q' => 'How is this different from a free Chrome extension?', 'a' => 'Browser extensions only check one page at a time and miss many issues. Access Report Card scans your entire site, prioritizes issues by severity, and tracks your progress over time.'],
                        ['q' => 'Can I cancel anytime?', 'a' => 'Yes. No contracts, no commitments. Cancel your Pro plan anytime from your dashboard.'],
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                    <div class="bg-white rounded-xl border border-gray-200">
                        <button @click="open = open === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-5 text-left">
                            <span class="font-semibold text-gray-900">{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform" :class="open === {{ $index }} && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === {{ $index }}" x-collapse>
                            <div class="px-5 pb-5 text-gray-600">{!! $faq['a'] !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Final CTA --}}
    <div class="py-16 sm:py-20 bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Don't Wait for a Demand Letter</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-xl mx-auto">
                Find out if your website is compliant in 60 seconds. Your first scan is free.
            </p>
            <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition-all hover:scale-105 text-lg shadow-lg shadow-blue-900/30">
                Scan My Website Free
            </a>
            <p class="text-sm text-blue-200 mt-4">No credit card required. Results in 60 seconds.</p>
        </div>
    </div>
</div>
@endsection
