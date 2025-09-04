<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $currentMerchant->business_name)</title>
    <meta name="description" content="@yield('description', 'احجز خدماتك مع ' . $currentMerchant->business_name)">

    <!-- Branding CSS Variables -->
    <style>
        {!! $currentMerchant->getBrandCssVariables() !!}
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Merchant CSS -->
    @if($currentMerchant->custom_css)
        <style>
            {!! $currentMerchant->custom_css !!}
        </style>
    @endif

    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50" style="font-family: 'Tajawal', sans-serif;">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b" style="border-color: var(--primary-color);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('subdomain.home') }}" class="flex items-center">
                        <img src="{{ $currentMerchant->logo_url }}" alt="{{ $currentMerchant->business_name }}" class="h-10 w-auto">
                        <span class="mr-3 text-xl font-bold" style="color: var(--primary-color);">
                            {{ $currentMerchant->business_name }}
                        </span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex space-x-reverse space-x-8">
                    <a href="{{ route('subdomain.home') }}" class="text-gray-700 hover:text-gray-900 transition-colors {{ request()->routeIs('subdomain.home') ? 'font-semibold' : '' }}" 
                       style="hover:color: var(--primary-color);">
                        الرئيسية
                    </a>
                    <a href="{{ route('subdomain.services') }}" class="text-gray-700 hover:text-gray-900 transition-colors {{ request()->routeIs('subdomain.services') ? 'font-semibold' : '' }}"
                       style="hover:color: var(--primary-color);">
                        الخدمات
                    </a>
                    <a href="{{ route('subdomain.about') }}" class="text-gray-700 hover:text-gray-900 transition-colors {{ request()->routeIs('subdomain.about') ? 'font-semibold' : '' }}"
                       style="hover:color: var(--primary-color);">
                        من نحن
                    </a>
                    <a href="{{ route('subdomain.contact') }}" class="text-gray-700 hover:text-gray-900 transition-colors {{ request()->routeIs('subdomain.contact') ? 'font-semibold' : '' }}"
                       style="hover:color: var(--primary-color);">
                        اتصل بنا
                    </a>
                </nav>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-700 hover:text-gray-900 focus:outline-none" onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('subdomain.home') }}" class="block px-3 py-2 text-gray-700 hover:text-gray-900">الرئيسية</a>
                    <a href="{{ route('subdomain.services') }}" class="block px-3 py-2 text-gray-700 hover:text-gray-900">الخدمات</a>
                    <a href="{{ route('subdomain.about') }}" class="block px-3 py-2 text-gray-700 hover:text-gray-900">من نحن</a>
                    <a href="{{ route('subdomain.contact') }}" class="block px-3 py-2 text-gray-700 hover:text-gray-900">اتصل بنا</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">{{ $currentMerchant->business_name }}</h3>
                    <p class="text-gray-600 text-sm">
                        {{ $currentMerchant->business_address ?? 'خدمات متميزة وتجربة استثنائية' }}
                    </p>
                    @if($currentMerchant->city)
                        <p class="text-gray-600 text-sm mt-2">{{ $currentMerchant->city }}</p>
                    @endif
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">روابط سريعة</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('subdomain.home') }}" class="text-gray-600 hover:text-gray-900">الرئيسية</a></li>
                        <li><a href="{{ route('subdomain.services') }}" class="text-gray-600 hover:text-gray-900">الخدمات</a></li>
                        <li><a href="{{ route('subdomain.about') }}" class="text-gray-600 hover:text-gray-900">من نحن</a></li>
                        <li><a href="{{ route('subdomain.contact') }}" class="text-gray-600 hover:text-gray-900">اتصل بنا</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">تواصل معنا</h3>
                    <div class="text-sm text-gray-600 space-y-2">
                        @if($currentMerchant->user->email)
                            <p>البريد الإلكتروني: {{ $currentMerchant->user->email }}</p>
                        @endif
                        @if($currentMerchant->user->phone)
                            <p>الهاتف: {{ $currentMerchant->user->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="border-t mt-8 pt-6 text-center">
                <p class="text-sm text-gray-500">
                    جميع الحقوق محفوظة © {{ date('Y') }} {{ $currentMerchant->business_name }}
                    | مدعوم من <a href="{{ config('app.url') }}" class="hover:underline" style="color: var(--primary-color);">شباك التذاكر</a>
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = event.target.closest('button');
            
            if (!menu.contains(event.target) && !button) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>