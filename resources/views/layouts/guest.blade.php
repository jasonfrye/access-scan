<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AccessScan'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --color-primary: #1e40af;
            --color-primary-dark: #1e3a8a;
            --color-success: #059669;
            --color-warning: #d97706;
            --color-error: #dc2626;
            --color-info: #0891b2;
            --gradient-hero: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        body {
            font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .font-mono {
            font-family: 'IBM Plex Mono', monospace;
        }

        /* Grade Badge Animation */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .grade-badge {
            animation: fadeInScale 0.4s ease-out;
        }

        /* Scan Progress Animation */
        @keyframes scan-pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }

        .scanning {
            animation: scan-pulse 2s ease-in-out infinite;
        }

        /* Gradient Text */
        .gradient-text {
            background: var(--gradient-hero);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card Hover Effect */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Focus Visible States (Accessibility) */
        *:focus-visible {
            outline: 3px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Skip to Content Link (Accessibility) */
        .skip-to-content {
            position: absolute;
            top: -100px;
            left: 0;
            background: #1e40af;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            z-index: 100;
        }

        .skip-to-content:focus {
            top: 0;
        }
    </style>
</head>
<body class="h-full bg-gray-50 antialiased">
    <!-- Skip to Content (Accessibility) -->
    <a href="#main-content" class="skip-to-content">Skip to main content</a>

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200" role="navigation" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">AccessScan</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                        <a href="{{ route('billing.pricing') }}" class="text-gray-600 hover:text-gray-900 font-medium">Pricing</a>
                    @else
                        <a href="{{ route('billing.pricing') }}" class="text-gray-600 hover:text-gray-900 font-medium">Pricing</a>
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Sign In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" tabindex="-1">
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="text-lg font-bold text-gray-900">AccessScan</span>
                    </div>
                    <p class="text-gray-600 max-w-md">
                        Making website accessibility simple and affordable for small businesses. WCAG 2.1 AA compliance made easy.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Product</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('billing.pricing') }}" class="text-gray-600 hover:text-gray-900">Pricing</a></li>
                        <li><a href="{{ route('api.docs') }}" class="text-gray-600 hover:text-gray-900">API Docs</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="mailto:support@accessscan.app" class="text-gray-600 hover:text-gray-900">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} AccessScan. Making the web accessible for everyone.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
