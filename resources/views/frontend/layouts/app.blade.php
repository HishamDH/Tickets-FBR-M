<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tickets Platform') }} - @yield('title', 'Book Services & Events')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Custom CSS -->
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bg-brand {
            background-color: #667eea;
        }
        .text-brand {
            color: #667eea;
        }
        .border-brand {
            border-color: #667eea;
        }
        .hover\:bg-brand:hover {
            background-color: #5a6fd8;
        }
        .btn-brand {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 brand-gradient rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-xl">🎟️</span>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">{{ config('app.name') }}</h1>
                            <p class="text-xs text-gray-500">Book Services & Events</p>
                        </div>
                    </a>
                    
                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="{{ route('home') }}" class="border-transparent text-gray-600 hover:text-orange-500 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            🏠 Home
                        </a>
                        <a href="{{ route('features') }}" class="border-transparent text-gray-600 hover:text-orange-500 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            ✨ Features
                        </a>
                        <a href="{{ route('pricing') }}" class="border-transparent text-gray-600 hover:text-orange-500 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            💰 Pricing
                        </a>
                        <a href="{{ route('merchants.index') }}" class="border-transparent text-gray-600 hover:text-orange-500 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            🏪 Browse Merchants
                        </a>
                        <a href="{{ route('search') }}" class="border-transparent text-gray-600 hover:text-orange-500 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            🔍 Search
                        </a>
                    </div>
                </div>
                
                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Authenticated User -->
                        <div class="relative">
                            <div class="flex items-center space-x-2">
                                @if(auth()->user()->isCustomer())
                                    <a href="/customer" class="btn-brand px-4 py-2 rounded-md text-sm font-medium">
                                        📊 My Dashboard
                                    </a>
                                @elseif(auth()->user()->isMerchant())
                                    <a href="/merchant" class="btn-brand px-4 py-2 rounded-md text-sm font-medium">
                                        💼 Merchant Portal
                                    </a>
                                @elseif(auth()->user()->isAdmin())
                                    <a href="/admin" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                        ⚙️ Admin Panel
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700 transition">
                                        🚪 Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest User -->
                        <a href="/customer/login" class="text-gray-600 hover:text-brand px-3 py-2 rounded-md text-sm font-medium transition">
                            👤 Customer Login
                        </a>
                        <a href="/merchant/login" class="btn-brand px-4 py-2 rounded-md text-sm font-medium">
                            🏪 Merchant Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ config('app.name') }}</h3>
                    <p class="text-gray-300">Your trusted platform for booking services and events.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4">For Customers</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="/customer/register" class="hover:text-white">Sign Up</a></li>
                        <li><a href="/customer/login" class="hover:text-white">Login</a></li>
                        <li><a href="{{ route('search') }}" class="hover:text-white">Search Services</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4">Platform</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="{{ route('features') }}" class="hover:text-orange-300">✨ Features</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-orange-300">💰 Pricing</a></li>
                        <li><a href="/merchant/login" class="hover:text-orange-300">🏪 For Merchants</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="/test-guards" class="hover:text-orange-300">🛡️ Multi-Guard Auth</a></li>
                        <li><a href="#" class="hover:text-orange-300">💬 Contact Us</a></li>
                        <li><a href="#" class="hover:text-orange-300">📚 Documentation</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>