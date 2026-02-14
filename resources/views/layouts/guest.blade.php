<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Access Report Card'))</title>

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
    <nav class="bg-white border-b border-gray-200" role="navigation" aria-label="Main navigation" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 2h6a1 1 0 011 1v1H8V3a1 1 0 011-1z"/>
                            <rect x="5" y="4" width="14" height="18" rx="2" stroke-width="1.75"/>
                            <text x="12" y="15.5" text-anchor="middle" font-size="8" font-weight="bold" font-family="sans-serif" fill="currentColor" stroke="none">A+</text>
                        </svg>
                        <span class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Access Report Card</span>
                    </a>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                        @unless(auth()->user()->isPaid())
                            <a href="{{ route('billing.pricing') }}" class="text-gray-600 hover:text-gray-900 font-medium">Pricing</a>
                        @endunless
                        <a href="{{ route('billing.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Billing</a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-gray-900 font-medium">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('billing.pricing') }}" class="text-gray-600 hover:text-gray-900 font-medium">Pricing</a>
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Sign In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-600 hover:text-gray-900" aria-label="Toggle menu" :aria-expanded="mobileOpen">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-collapse class="md:hidden border-t border-gray-200">
            <div class="px-4 py-3 space-y-1">
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Dashboard</a>
                    @unless(auth()->user()->isPaid())
                        <a href="{{ route('billing.pricing') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Pricing</a>
                    @endunless
                    <a href="{{ route('billing.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Billing</a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('billing.pricing') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Pricing</a>
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Sign In</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg bg-blue-600 text-white text-center font-medium hover:bg-blue-700 transition-colors">
                        Get Started
                    </a>
                @endauth
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
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 2h6a1 1 0 011 1v1H8V3a1 1 0 011-1z"/>
                            <rect x="5" y="4" width="14" height="18" rx="2" stroke-width="1.75"/>
                            <text x="12" y="15.5" text-anchor="middle" font-size="8" font-weight="bold" font-family="sans-serif" fill="currentColor" stroke="none">A+</text>
                        </svg>
                        <span class="text-lg font-bold text-gray-900">Access Report Card</span>
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
                        <li><a href="{{ route('privacy') }}" class="text-gray-600 hover:text-gray-900">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-gray-600 hover:text-gray-900">Terms of Use</a></li>
                        <li><a href="mailto:support@accessreportcard.com" class="text-gray-600 hover:text-gray-900">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-8 pt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Access Report Card. Making the web accessible for everyone.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
