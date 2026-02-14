@extends('layouts.guest')

@section('title', 'Terms of Use - Access Report Card')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Use</h1>
            <p class="text-sm text-gray-500 mb-8">Last updated: {{ now()->format('F j, Y') }}</p>

            <div class="prose prose-gray max-w-none space-y-6">
                <p>By accessing or using Access Report Card ("the Service"), you agree to be bound by these terms. If you do not agree, do not use the Service.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">1. Description of Service</h2>
                <p>Access Report Card is a web accessibility scanning tool that tests websites for WCAG 2.1 AA compliance. The Service provides automated reports identifying accessibility issues, along with recommendations for remediation.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">2. Accounts</h2>
                <p>You must provide accurate information when creating an account. You are responsible for maintaining the security of your account credentials. You must notify us immediately of any unauthorized use of your account.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">3. Acceptable Use</h2>
                <p>You agree to use the Service only for lawful purposes. You may only scan websites that you own or have explicit authorization to scan. You may not:</p>
                <ul class="list-disc pl-6 space-y-1 text-gray-700">
                    <li>Scan websites without the owner's permission</li>
                    <li>Use the Service to disrupt, damage, or gain unauthorized access to any system</li>
                    <li>Attempt to bypass scan limits, rate limits, or other restrictions</li>
                    <li>Resell or redistribute scan results as a competing service</li>
                    <li>Use automated tools to access the Service beyond the provided API</li>
                </ul>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">4. Plans and Billing</h2>
                <p>Free accounts are subject to scan and page limits. Paid plans are billed according to the pricing displayed at the time of purchase. All subscriptions renew automatically until canceled.</p>
                <p>You may cancel your subscription at any time from your billing page. Cancellation takes effect at the end of the current billing period. We do not offer refunds for partial billing periods.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">5. Scan Results and Accuracy</h2>
                <p>Scan results are provided on an "as-is" basis. While we strive for accuracy, automated accessibility testing cannot detect all issues. Our reports:</p>
                <ul class="list-disc pl-6 space-y-1 text-gray-700">
                    <li>Are not a substitute for manual accessibility auditing</li>
                    <li>Do not constitute legal advice regarding ADA, Section 508, or other accessibility laws</li>
                    <li>May not capture issues that require human judgment (e.g., whether alt text is meaningful)</li>
                </ul>
                <p>We recommend using our reports as a starting point and consulting with accessibility professionals for comprehensive compliance.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">6. Intellectual Property</h2>
                <p>The Service, including its design, features, and content, is owned by Access Report Card. Your scan results belong to you, and you may use, export, and share them as you see fit.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">7. API Usage</h2>
                <p>API access is governed by your plan's limits. API keys are confidential and should not be shared. We reserve the right to revoke API access for abuse or violation of these terms.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">8. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, Access Report Card shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or business opportunities, arising from your use of the Service.</p>
                <p>Our total liability for any claim arising from the Service shall not exceed the amount you paid us in the 12 months preceding the claim.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">9. Disclaimer of Warranties</h2>
                <p>The Service is provided "as is" and "as available" without warranties of any kind, whether express or implied, including but not limited to implied warranties of merchantability, fitness for a particular purpose, and non-infringement.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">10. Termination</h2>
                <p>We may suspend or terminate your account if you violate these terms. You may delete your account at any time from your profile page. Upon termination, your data will be handled according to our <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">11. Changes to These Terms</h2>
                <p>We may update these terms from time to time. Continued use of the Service after changes constitutes acceptance of the new terms. We will notify you of significant changes by email or by posting a notice on the site.</p>

                <h2 class="text-xl font-semibold text-gray-900 mt-8">12. Contact Us</h2>
                <p>If you have questions about these terms, contact us at <a href="mailto:support@accessreportcard.com" class="text-blue-600 hover:text-blue-800">support@accessreportcard.com</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
