<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'شباك التذاكر - منصة الحجوزات الذكية')</title>
    <meta name="description" content="@yield('description', 'منصة الحجوزات الذكية التي تربط بين التجار والعملاء - احجز خدماتك بسهولة وأمان')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        :root {
            --primary-color: #f9682e;
            --primary-hover: #ff7842;
            --background: #faf7f4;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border-color: #e5e7eb;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #f9682e 0%, #ff7842 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #f9682e 0%, #ff7842 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .card-hover {
            transition: all 0.3s ease-in-out;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        
        .floating-animation {
            animation: floating 4s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .pulse-glow {
            animation: pulse-glow 2.5s ease-in-out infinite alternate;
        }
        
        @keyframes pulse-glow {
            from {
                box-shadow: 0 0 15px rgba(249, 104, 46, 0.4);
            }
            to {
                box-shadow: 0 0 25px rgba(249, 104, 46, 0.6);
            }
        }
        
        .hero-pattern {
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(249, 104, 46, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(255, 120, 66, 0.05) 0%, transparent 40%);
        }
        
        .page-transition {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<<body class="font-sans antialiased" x-data="{ mobileMenuOpen: false }">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-effect transition-all duration-300" 
         x-data="{ 
             scrolled: false,
             init() {
                 window.addEventListener('scroll', () => {
                     this.scrolled = window.scrollY > 50;
                 });
             }
         }"
         :class="scrolled ? 'shadow-lg backdrop-blur-md' : 'backdrop-blur-sm'">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center pulse-glow">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold gradient-text">شباك التذاكر</h1>
                        <p class="text-sm text-gray-600">منصة الحجوزات الذكية</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        الرئيسية
                    </a>
                    <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                        اكتشف الخدمات
                    </a>
                    <a href="#" class="nav-link">
                        عن المنصة
                    </a>
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 space-x-reverse nav-link">
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-1 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-1 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    لوحة التحكم
                                </a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    حجوزاتي
                                </a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                    الملف الشخصي
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right px-4 py-2 text-red-600 hover:bg-red-50">
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('customer.login') }}" class="nav-link">تسجيل الدخول</a>
                        <a href="{{ route('customer.register') }}" class="btn-primary">
                            إنشاء حساب
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-1 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="md:hidden bg-white/95 backdrop-blur-lg border-t border-gray-200 shadow-lg">
                <div class="px-4 py-6 space-y-4">
                    <a href="{{ url('/') }}" class="block py-3 text-gray-700 hover:text-orange-500 transition-colors">الرئيسية</a>
                    <a href="{{ route('services.index') }}" class="block py-3 text-gray-700 hover:text-orange-500 transition-colors">اكتشف الخدمات</a>
                    <a href="#" class="block py-3 text-gray-700 hover:text-orange-500 transition-colors">عن المنصة</a>
                    @auth
                        <hr class="my-4">
                        <a href="{{ route('dashboard') }}" class="block py-3 text-gray-700 hover:text-orange-500 transition-colors">لوحة التحكم</a>
                        <a href="#" class="block py-3 text-gray-700 hover:text-orange-500 transition-colors">حجوزاتي</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full bg-red-500 text-white py-3 px-4 rounded-lg hover:bg-red-600 transition-colors">
                                تسجيل الخروج
                            </button>
                        </form>
                    @else
                        <a href="{{ route('customer.login') }}" class="block py-3 text-gray-700 hover:text-orange-500 transition-colors">تسجيل الدخول</a>
                        <a href="{{ route('customer.register') }}" class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 px-4 rounded-lg text-center hover:from-orange-600 hover:to-orange-700 transition-all">
                            إنشاء حساب
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <style>
        .nav-link {
            @apply text-gray-700 hover:text-orange-500 font-medium transition-all duration-300 relative;
        }
        
        .nav-link.active {
            @apply text-orange-500;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, #f9682e 0%, #ff7842 100%);
            border-radius: 1px;
        }
        
        .btn-primary {
            @apply bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 font-medium shadow-lg hover:shadow-xl;
        }
    </style>

    <!-- Main Content -->
    <main class="pt-20">
        <!-- Success/Error Messages -->
        <div x-data="{ 
            showSuccess: {{ session('success') ? 'true' : 'false' }}, 
            showError: {{ session('error') ? 'true' : 'false' }},
            showErrors: {{ $errors->any() ? 'true' : 'false' }}
        }">
            <!-- Success Message -->
            <div x-show="showSuccess" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-1 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="fixed top-24 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg max-w-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="showSuccess = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="showError" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-1 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="fixed top-24 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg max-w-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="showError = false" class="text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Validation Errors -->
            @if($errors->any())
            <div x-show="showErrors" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-1 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="fixed top-24 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg max-w-md">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">يرجى تصحيح الأخطاء التالية:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="showErrors = false" class="text-red-500 hover:text-red-700 mt-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Page Content -->
        <div class="page-transition">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="hero-pattern h-full w-full"></div>
        </div>
        
        <div class="relative">
            <!-- Main Footer Content -->
            <div class="container mx-auto px-4 py-16">
                <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-8">
                    <!-- Company Info -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center space-x-3 space-x-reverse mb-6">
                            <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold gradient-text">شباك التذاكر</h3>
                                <p class="text-gray-400 text-sm">منصة الحجوزات الذكية</p>
                            </div>
                        </div>
                        <p class="text-gray-400 leading-relaxed mb-6 max-w-md">
                            منصة رقمية متطورة تربط بين مقدمي الخدمات والعملاء، تتيح حجز الخدمات بسهولة وأمان مع تجربة مستخدم استثنائية.
                        </p>
                        <div class="flex space-x-4 space-x-reverse">
                            <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.097.118.11.221.081.343-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.758-1.378l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001 12.017.001z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-bold text-lg mb-6 gradient-text">روابط سريعة</h4>
                        <div class="space-y-3">
                            <a href="{{ url('/') }}" class="block text-gray-400 hover:text-white transition-colors duration-300">الرئيسية</a>
                            <a href="{{ route('services.index') }}" class="block text-gray-400 hover:text-white transition-colors duration-300">اكتشف الخدمات</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">عن المنصة</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">كيف يعمل</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">مركز المساعدة</a>
                        </div>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="font-bold text-lg mb-6 gradient-text">الدعم</h4>
                        <div class="space-y-3">
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">تواصل معنا</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">الأسئلة الشائعة</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">الشروط والأحكام</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">سياسة الخصوصية</a>
                            <a href="#" class="block text-gray-400 hover:text-white transition-colors duration-300">سياسة الاسترداد</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="border-t border-gray-800">
                <div class="container mx-auto px-4 py-8">
                    <div class="grid md:grid-cols-3 gap-6 text-center md:text-right">
                        <div class="flex items-center justify-center md:justify-start space-x-3 space-x-reverse">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">البريد الإلكتروني</p>
                                <p class="text-white font-medium">info@tickets.sa</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-center md:justify-start space-x-3 space-x-reverse">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">رقم الهاتف</p>
                                <p class="text-white font-medium">+966 11 123 4567</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-center md:justify-start space-x-3 space-x-reverse">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-400 text-sm">العنوان</p>
                                <p class="text-white font-medium">الرياض، المملكة العربية السعودية</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-800">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <p class="text-gray-400 text-sm">
                            &copy; {{ date('Y') }} شباك التذاكر. جميع الحقوق محفوظة.
                        </p>
                        <div class="flex items-center space-x-6 space-x-reverse text-sm text-gray-400">
                            <span>صُنع بـ ❤️ في المملكة العربية السعودية</span>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <span>مدعوم بـ</span>
                                <span class="gradient-text font-medium">Laravel & Filament</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[x-data]');
            alerts.forEach(alert => {
                if (alert.__x && alert.__x.$data) {
                    if (alert.__x.$data.showSuccess) alert.__x.$data.showSuccess = false;
                    if (alert.__x.$data.showError) alert.__x.$data.showError = false;
                    if (alert.__x.$data.showErrors) alert.__x.$data.showErrors = false;
                }
            });
        }, 5000);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>