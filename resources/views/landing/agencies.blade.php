@extends('layouts.guest')

@section('title', 'Accessibility Scanning for Agencies - Access Report Card')

@section('content')
<div class="min-h-screen">
    {{-- Hero --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-900 text-white">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative max-w-5xl mx-auto px-4 py-16 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                {{-- Audience badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-sm font-medium text-indigo-200 mb-8">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Built for Web Agencies &amp; Consultancies
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    Turn Accessibility Into<br>
                    <span class="text-indigo-300">Recurring Revenue</span>
                </h1>

                <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">
                    Scan client sites in bulk, deliver branded PDF reports, and offer ongoing monitoring &mdash; all from one dashboard. No manual audits.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('register') }}?plan=lifetime" class="px-8 py-4 bg-white text-indigo-900 font-bold rounded-xl hover:bg-indigo-50 transition-all hover:scale-105 text-lg shadow-lg shadow-indigo-900/30">
                        Start Your Free Trial
                    </a>
                    <a href="#roi" class="px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-semibold rounded-xl hover:bg-white/20 transition-all text-lg">
                        See the ROI
                    </a>
                </div>

                <div class="flex flex-wrap justify-center gap-6 text-sm text-indigo-200">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        1,000 scans/month on Lifetime
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        White-label PDF reports
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        REST API included
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Pain Points --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Manual Accessibility Audits Don't Scale</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Your clients need compliance. You need a way to deliver it without burning hours on every site.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-14 h-14 mx-auto bg-red-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hours Per Audit</h3>
                    <p class="text-gray-600">Manual WCAG audits take 4&ndash;8 hours per site. Multiply that by your client count and it's a full-time job.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-14 h-14 mx-auto bg-amber-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Client Liability</h3>
                    <p class="text-gray-600">Your clients face real legal exposure. If you built their site, the question is coming &mdash; "Is our site ADA compliant?"</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-14 h-14 mx-auto bg-indigo-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Missed Revenue</h3>
                    <p class="text-gray-600">Accessibility monitoring is a service clients will pay for monthly. Without automation, you can't offer it profitably.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ROI Calculator --}}
    <div id="roi" class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">The Math Speaks for Itself</h2>
                <p class="text-lg text-gray-600">One Lifetime license. Unlimited upside.</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden max-w-3xl mx-auto">
                <div class="grid md:grid-cols-2">
                    <div class="p-8 border-b md:border-b-0 md:border-r border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-6">Your Cost</h3>
                        <div class="text-5xl font-bold text-gray-900 mb-2">$197</div>
                        <p class="text-gray-500">One-time Lifetime license</p>
                        <ul class="mt-6 space-y-3 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                1,000 scans per month
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                500 pages per scan
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                White-label PDF &amp; CSV
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Full REST API
                            </li>
                        </ul>
                    </div>
                    <div class="p-8 bg-green-50">
                        <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wider mb-6">Potential Revenue</h3>
                        <div class="space-y-5">
                            <div>
                                <div class="flex items-baseline justify-between mb-1">
                                    <span class="text-sm text-gray-600">Charge $99/mo per client</span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-sm text-gray-500">10 clients</span>
                                    <span class="text-2xl font-bold text-green-700">$990/mo</span>
                                </div>
                            </div>
                            <div class="border-t border-green-200 pt-5">
                                <div class="flex items-baseline justify-between mb-1">
                                    <span class="text-sm text-gray-600">Annual recurring revenue</span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-sm text-gray-500">10 clients &times; 12 months</span>
                                    <span class="text-3xl font-bold text-green-700">$11,880</span>
                                </div>
                            </div>
                            <div class="border-t border-green-200 pt-5">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-sm font-semibold text-green-800">ROI on $197 investment</span>
                                    <span class="text-lg font-bold text-green-700">5,930%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Agency Features --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Everything an Agency Needs</h2>
                <p class="text-lg text-gray-600">Deliver professional accessibility services at scale.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">White-Label Reports</h3>
                    <p class="text-gray-600">Export polished PDF reports you can deliver to clients under your own brand. No "Access Report Card" branding.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">REST API</h3>
                    <p class="text-gray-600">Trigger scans, pull results, and integrate accessibility checks into your existing tools and workflows programmatically.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Scheduled Monitoring</h3>
                    <p class="text-gray-600">Set up daily, weekly, or monthly scans per client. Get alerted when a score drops so you can notify them proactively.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Issues by Priority</h3>
                    <p class="text-gray-600">Every issue is categorized and ranked. Show clients the critical items first, then work through the rest methodically.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Score Trends</h3>
                    <p class="text-gray-600">Track progress over time for each client. Show them the value of your ongoing work with clear before-and-after data.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">CSV &amp; JSON Export</h3>
                    <p class="text-gray-600">Export raw data in CSV or JSON for your project management tools, Jira imports, or custom client dashboards.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- How Agencies Use It --}}
    <div class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">How Agencies Package This</h2>
                <p class="text-lg text-gray-600">Three ways to add accessibility to your service offerings.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center font-bold mb-4">1</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">The Audit Upsell</h3>
                    <p class="text-gray-600 mb-4">Run a scan during your sales process. Show prospects their current grade and offer to fix it. Close the deal with data, not guesswork.</p>
                    <div class="text-sm text-indigo-600 font-medium">Avg. deal value: $2,000&ndash;$5,000</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <div class="w-10 h-10 bg-green-100 text-green-700 rounded-lg flex items-center justify-center font-bold mb-4">2</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Monthly Retainer Add-On</h3>
                    <p class="text-gray-600 mb-4">Add "accessibility monitoring" to existing retainers. Schedule automated scans, send monthly reports, and charge $49&ndash;$149/mo per client.</p>
                    <div class="text-sm text-indigo-600 font-medium">Avg. monthly revenue: $99/client</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center font-bold mb-4">3</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Compliance Package</h3>
                    <p class="text-gray-600 mb-4">Bundle initial remediation + ongoing monitoring into an annual compliance package. Position yourself as the accessibility expert in your market.</p>
                    <div class="text-sm text-indigo-600 font-medium">Avg. package value: $3,000&ndash;$8,000/yr</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Social Proof --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3">Agency Testimonials</p>
                <h2 class="text-3xl font-bold text-gray-900">Agencies Are Already Using This</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4">"We added a $99/month accessibility monitoring service to 23 existing clients. That's $27K in new annual revenue from a $197 tool. The ROI is absurd."</p>
                    <div class="text-sm"><span class="font-semibold text-gray-900">James P.</span> <span class="text-gray-500">&mdash; Digital Agency Owner, 40+ clients</span></div>
                </div>
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-4">"The white-label PDF reports are a game changer. I scan a prospect's site during the call, share the report card, and close the deal right there. It's become our best sales tool."</p>
                    <div class="text-sm"><span class="font-semibold text-gray-900">Danielle T.</span> <span class="text-gray-500">&mdash; Web Consultancy, 15 clients</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">One Price. No Per-Client Fees.</h2>
                <p class="text-lg text-gray-600">The Lifetime plan is built for agencies who manage multiple sites.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
                {{-- Monthly --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Pro Monthly</h3>
                    <p class="text-sm text-gray-500 mb-4">Getting started</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">$29</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="space-y-3 mb-8 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            50 scans per month
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            100 pages per scan
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Scheduled scans &amp; PDF export
                        </li>
                    </ul>
                    <a href="{{ route('register') }}?plan=monthly" class="block w-full py-3 text-center border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-100 transition-colors">
                        Start Monthly
                    </a>
                </div>

                {{-- Lifetime --}}
                <div class="bg-white rounded-2xl border-2 border-indigo-600 p-8 relative shadow-lg shadow-indigo-600/10">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-indigo-600 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Best for Agencies</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Lifetime</h3>
                    <p class="text-sm text-gray-500 mb-4">Pay once, use forever</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">$197</span>
                        <span class="text-gray-500">/one-time</span>
                    </div>
                    <ul class="space-y-3 mb-8 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-medium">1,000 scans per month</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-medium">500 pages per scan</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            White-label PDF reports
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Full REST API access
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Priority support
                        </li>
                    </ul>
                    <a href="{{ route('register') }}?plan=lifetime" class="block w-full py-3 text-center bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                        Get Lifetime Access
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="py-16 sm:py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-10">Agency FAQ</h2>

            <div class="space-y-4" x-data="{ open: null }">
                @php
                    $faqs = [
                        ['q' => 'Can I use one account for all my clients?', 'a' => 'Yes. Scan any URL from your dashboard. There are no per-client fees &mdash; just scan limits based on your plan.'],
                        ['q' => 'Are the PDF reports really white-label?', 'a' => 'On the Lifetime plan, yes. PDF exports are clean, professional reports with no Access Report Card branding. Ready to deliver to clients as-is.'],
                        ['q' => 'How does the API work?', 'a' => 'Generate an API key from your profile. Use our REST API to create scans, check status, and retrieve results programmatically. Full documentation is available in your dashboard.'],
                        ['q' => 'What WCAG standard does it test against?', 'a' => 'WCAG 2.1 Level AA &mdash; the standard referenced in most ADA web accessibility lawsuits and regulatory guidance.'],
                        ['q' => 'Can I schedule scans for multiple client sites?', 'a' => 'Absolutely. Set up daily, weekly, or monthly scans for each client domain. You\'ll be alerted if any score drops.'],
                        ['q' => 'Is there a money-back guarantee?', 'a' => 'Yes. Lifetime purchases include a 30-day money-back guarantee. Monthly plans can be cancelled anytime.'],
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                    <div class="bg-gray-50 rounded-xl border border-gray-200">
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
    <div class="py-16 sm:py-20 bg-gradient-to-br from-indigo-700 to-indigo-900 text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Add Accessibility to Your Service Stack</h2>
            <p class="text-xl text-indigo-100 mb-8 max-w-xl mx-auto">
                One $197 investment. 1,000 scans a month. White-label reports. API access. No monthly fees, ever.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}?plan=lifetime" class="px-10 py-4 bg-white text-indigo-800 font-bold rounded-xl hover:bg-indigo-50 transition-all hover:scale-105 text-lg shadow-lg shadow-indigo-900/30">
                    Get Lifetime Access &mdash; $197
                </a>
                <a href="{{ route('api.docs') }}" class="px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white font-semibold rounded-xl hover:bg-white/20 transition-all text-lg">
                    View API Docs
                </a>
            </div>
            <p class="text-sm text-indigo-200 mt-4">30-day money-back guarantee. No questions asked.</p>
        </div>
    </div>
</div>
@endsection
