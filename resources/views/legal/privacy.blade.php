@extends('layouts.guest')

@section('title', 'Privacy Policy - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
            <p class="text-sm text-gray-500 mb-8">Last updated: {{ now()->format('F j, Y') }}</p>

            <div class="prose prose-gray max-w-none space-y-6">
                <p>Access Report Card ("we", "us", or "our") operates the {{ config('app.url') }} website. This page informs you of our policies regarding the collection, use, and disclosure of personal information when you use our service.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">1. Information We Collect</h2>

                <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                <p>When you create an account, we collect your name and email address. If you subscribe to a paid plan, payment processing is handled by Stripe — we do not store your full credit card number.</p>

                <h3 class="text-lg font-medium text-gray-900">Scan Data</h3>
                <p>When you scan a website, we collect the URL you submit, the pages crawled, and the accessibility issues found. Scan results are stored so you can review them later and track progress over time.</p>

                <h3 class="text-lg font-medium text-gray-900">Usage Data</h3>
                <p>We automatically collect certain information when you visit our site, including your IP address, browser type, and pages visited. This helps us improve the service and diagnose issues.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">2. How We Use Your Information</h2>
                <ul class="list-disc pl-6 space-y-1 text-gray-700">
                    <li>To provide and maintain the service</li>
                    <li>To send scan results, alerts, and notifications you've opted into</li>
                    <li>To process payments and manage your subscription</li>
                    <li>To send marketing emails (you can opt out at any time)</li>
                    <li>To improve the service and develop new features</li>
                    <li>To detect and prevent abuse or fraud</li>
                </ul>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">3. Data Sharing</h2>
                <p>We do not sell your personal information. We share data only with:</p>
                <ul class="list-disc pl-6 space-y-1 text-gray-700">
                    <li><strong>Stripe</strong> — for payment processing</li>
                    <li><strong>Email service providers</strong> — to deliver transactional and marketing emails</li>
                    <li><strong>Law enforcement</strong> — if required by law</li>
                </ul>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">4. Data Retention</h2>
                <p>We retain your account data and scan history for as long as your account is active. If you delete your account, we will remove your personal data within 30 days. Anonymized, aggregate data may be retained for analytics purposes.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">5. Cookies</h2>
                <p>We use essential cookies to maintain your session and remember your login. We do not use third-party tracking cookies for advertising.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">6. Your Rights</h2>
                <p>You have the right to:</p>
                <ul class="list-disc pl-6 space-y-1 text-gray-700">
                    <li>Access the personal data we hold about you</li>
                    <li>Request correction or deletion of your data</li>
                    <li>Opt out of marketing emails via your <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800">profile settings</a> or the unsubscribe link in any email</li>
                    <li>Export your scan data (available via CSV/JSON export)</li>
                    <li>Delete your account from your profile page</li>
                </ul>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">7. Security</h2>
                <p>We use industry-standard security measures to protect your data, including encrypted connections (HTTPS), hashed passwords, and signed URLs for sensitive actions. However, no method of transmission over the Internet is 100% secure.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">8. Children's Privacy</h2>
                <p>Our service is not intended for anyone under the age of 13. We do not knowingly collect personal information from children.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">9. Changes to This Policy</h2>
                <p>We may update this privacy policy from time to time. We will notify you of significant changes by email or by posting a notice on the site.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">10. Contact Us</h2>
                <p>If you have questions about this privacy policy, contact us at <a href="mailto:support@accessreportcard.com" class="text-blue-600 hover:text-blue-800">support@accessreportcard.com</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
