@extends('layouts.app')

@section('title', 'API Documentation - AccessScan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">API Documentation</h1>
            <p class="text-gray-600 mt-2">Integrate AccessScan accessibility testing into your applications.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="md:col-span-1">
                <nav class="bg-white rounded-lg shadow p-4 sticky top-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Contents</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#authentication" class="text-blue-600 hover:text-blue-700">Authentication</a></li>
                        <li><a href="#endpoints" class="text-blue-600 hover:text-blue-700">Endpoints</a></li>
                        <li><a href="#rate-limits" class="text-blue-600 hover:text-blue-700">Rate Limits</a></li>
                        <li><a href="#examples" class="text-blue-600 hover:text-blue-700">Examples</a></li>
                        <li><a href="#errors" class="text-blue-600 hover:text-blue-700">Errors</a></li>
                        <li><a href="#sdks" class="text-blue-600 hover:text-blue-700">SDKs</a></li>
                    </ul>

                    <div class="mt-6 pt-6 border-t">
                        <h3 class="font-semibold text-gray-900 mb-3">Your API Key</h3>
                        @auth
                            @if(Auth::user()->tokens->count() > 0)
                                <div class="bg-gray-100 rounded p-2 text-xs font-mono break-all">
                                    {{ Auth::user()->tokens->first()->plainTextToken }}
                                </div>
                                <form method="POST" action="{{ route('profile.api-key.revoke') }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm">Revoke Key</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('profile.api-key.create') }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                        Generate API Key
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="text-gray-500 text-sm"><a href="{{ route('login') }}" class="text-blue-600">Sign in</a> to manage your API keys.</p>
                        @endauth
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-3 space-y-8">
                <!-- Authentication -->
                <section id="authentication" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Authentication</h2>
                    <p class="text-gray-600 mb-4">All API requests require authentication using a Bearer token. Include your API key in the <code>Authorization</code> header.</p>

                    <div class="bg-gray-900 text-gray-100 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                        <span class="text-purple-400">Authorization</span>: <span class="text-green-400">Bearer</span> YOUR_API_KEY
                    </div>
                </section>

                <!-- Endpoints -->
                <section id="endpoints" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Endpoints</h2>

                    <!-- List Scans -->
                    <div class="border-b pb-6 mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-800">/api/v1/scans</code>
                        </div>
                        <p class="text-gray-600 mb-3">List all scans for the authenticated user.</p>

                        <h4 class="font-semibold text-gray-900 mb-2">Query Parameters</h4>
                        <table class="min-w-full text-sm mb-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Parameter</th>
                                    <th class="px-3 py-2 text-left">Type</th>
                                    <th class="px-3 py-2 text-left">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr>
                                    <td class="px-3 py-2"><code>page</code></td>
                                    <td class="px-3 py-2">integer</td>
                                    <td class="px-3 py-2">Page number (default: 1)</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2"><code>per_page</code></td>
                                    <td class="px-3 py-2">integer</td>
                                    <td class="px-3 py-2">Items per page (default: 20)</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="font-semibold text-gray-900 mb-2">Response</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>{
  "success": true,
  "data": [
    {
      "id": 1,
      "url": "https://example.com",
      "status": "completed",
      "score": 85,
      "grade": "B",
      "completed_at": "2026-02-09T12:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}</code></pre>
                    </div>

                    <!-- Create Scan -->
                    <div class="border-b pb-6 mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-mono">POST</span>
                            <code class="text-gray-800">/api/v1/scans</code>
                        </div>
                        <p class="text-gray-600 mb-3">Initiate a new accessibility scan.</p>

                        <h4 class="font-semibold text-gray-900 mb-2">Request Body</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>{
  "url": "https://example.com",
  "pages": 10  // optional, max 100
}</code></pre>

                        <h4 class="font-semibold text-gray-900 mb-2 mt-4">Response</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>{
  "success": true,
  "data": {
    "id": 123,
    "url": "https://example.com",
    "status": "pending",
    "status_url": "/api/v1/scans/123/status"
  }
}</code></pre>
                    </div>

                    <!-- Get Scan -->
                    <div class="border-b pb-6 mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-800">/api/v1/scans/{id}</code>
                        </div>
                        <p class="text-gray-600 mb-3">Get detailed scan results including all issues.</p>

                        <h4 class="font-semibold text-gray-900 mb-2">Response (Completed Scan)</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>{
  "success": true,
  "data": {
    "id": 123,
    "url": "https://example.com",
    "status": "completed",
    "score": 85,
    "grade": "B",
    "issues_found": 15,
    "errors_count": 3,
    "warnings_count": 8,
    "notices_count": 4,
    "pages_scanned": 5,
    "completed_at": "2026-02-09T12:30:00Z",
    "issues": [
      {
        "id": 1,
        "type": "error",
        "wcag_reference": "WCAG 1.1.1",
        "message": "Image missing alt text",
        "code": "ImgAltIsMissing",
        "impact": "critical",
        "recommendation": "Add descriptive alt attribute"
      }
    ]
  }
}</code></pre>
                    </div>

                    <!-- Check Status -->
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-800">/api/v1/scans/{id}/status</code>
                        </div>
                        <p class="text-gray-600">Poll this endpoint to check scan progress.</p>
                    </div>
                </section>

                <!-- Rate Limits -->
                <section id="rate-limits" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Rate Limits</h2>
                    <p class="text-gray-600 mb-4">API requests are limited based on your subscription tier.</p>

                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Plan</th>
                                <th class="px-3 py-2 text-left">Scans/Month</th>
                                <th class="px-3 py-2 text-left">Requests/Hour</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr>
                                <td class="px-3 py-2">Free</td>
                                <td class="px-3 py-2">5</td>
                                <td class="px-3 py-2">60</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2">Pro ($29/mo)</td>
                                <td class="px-3 py-2">50</td>
                                <td class="px-3 py-2">300</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2">Lifetime</td>
                                <td class="px-3 py-2">Unlimited</td>
                                <td class="px-3 py-2">1,000</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- Error Codes -->
                <section id="errors" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Error Responses</h2>
                    <p class="text-gray-600 mb-4">All errors return a JSON response with an error code.</p>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">401 Unauthorized</h4>
                            <p class="text-gray-600 text-sm">Invalid or missing API key.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">403 Forbidden</h4>
                            <p class="text-gray-600 text-sm">Rate limit exceeded or plan upgrade required.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">422 Unprocessable Entity</h4>
                            <p class="text-gray-600 text-sm">Invalid request parameters (e.g., invalid URL).</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">429 Too Many Requests</h4>
                            <p class="text-gray-600 text-sm">Rate limit exceeded. Retry after the specified time.</p>
                        </div>
                    </div>
                </section>

                <!-- Examples -->
                <section id="examples" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Code Examples</h2>

                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-2">cURL</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>curl -X POST https://api.accessscan.app/v1/scans \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"url": "https://example.com"}'</code></pre>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-2">JavaScript (Node.js)</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>const response = await fetch('https://api.accessscan.app/v1/scans', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_API_KEY',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ url: 'https://example.com' })
});

const data = await response.json();
console.log(data);</code></pre>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Python</h4>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-sm overflow-x-auto"><code>import requests

response = requests.post(
    'https://api.accessscan.app/v1/scans',
    headers={'Authorization': 'Bearer YOUR_API_KEY'},
    json={'url': 'https://example.com'}
)

print(response.json())</code></pre>
                    </div>
                </section>

                <!-- SDKs -->
                <section id="sdks" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Official SDKs</h2>
                    <p class="text-gray-600 mb-4">Official client libraries for popular languages.</p>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <a href="#" class="block p-4 border rounded-lg hover:bg-gray-50">
                            <h4 class="font-semibold text-gray-900">Python</h4>
                            <p class="text-gray-500 text-sm">pip install accessscan</p>
                        </a>
                        <a href="#" class="block p-4 border rounded-lg hover:bg-gray-50">
                            <h4 class="font-semibold text-gray-900">Node.js</h4>
                            <p class="text-gray-500 text-sm">npm install @accessscan/sdk</p>
                        </a>
                        <a href="#" class="block p-4 border rounded-lg hover:bg-gray-50">
                            <h4 class="font-semibold text-gray-900">Ruby</h4>
                            <p class="text-gray-500 text-sm">gem install accessscan</p>
                        </a>
                        <a href="#" class="block p-4 border rounded-lg hover:bg-gray-50">
                            <h4 class="font-semibold text-gray-900">PHP</h4>
                            <p class="text-gray-500 text-sm">composer require accessscan/sdk</p>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
