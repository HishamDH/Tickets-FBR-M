<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ getLanguageDirection() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f97316">

    <title>{{ config('app.name', 'تذاكر FBR-M') }} - @yield('title', 'منصة إدارة التذاكر')</title>
    <meta name="description" content="@yield('description', 'منصة متقدمة لإدارة التذاكر والمدفوعات مع تحليلات ذكية')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Creative Design System -->
    <link rel="stylesheet" href="{{ asset('resources/css/creative-design-system.css') }}">
    
    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-streaming"></script>

    <!-- Scripts -->
    @vite(['resources/css/rtl.css', 'resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="font-display antialiased bg-gray-50">
    <!-- 🔥 Fire Loading Screen -->
    <div id="loading-screen" class="fixed inset-0 bg-gradient-fire flex items-center justify-center z-50 transition-opacity duration-500">
        <div class="text-center text-white">
            <div class="relative mb-4">
                <div class="w-20 h-20 border-4 border-white border-opacity-30 rounded-full"></div>
                <div class="w-20 h-20 border-4 border-white rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-fire text-3xl animate-pulse"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-2">تذاكر FBR-M</h3>
            <p class="text-white text-opacity-90">جاري التحميل...</p>
        </div>
    </div>

    <!-- 🌟 Main Application -->
    <div id="app" class="min-h-screen bg-gray-50 opacity-0 transition-opacity duration-500">
        
        <!-- ✨ Creative Navigation -->
        <nav class="bg-white shadow-xl border-b-4 border-primary-500 sticky top-0 z-40 backdrop-blur-md bg-opacity-95">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    
                    <!-- 🎨 Logo Section -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center group hover:scale-105 transition-transform duration-300">
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-fire rounded-2xl flex items-center justify-center ml-4 shadow-lg 
                                           group-hover:shadow-xl transition-shadow duration-300 fire-glow">
                                    <i class="fas fa-ticket-alt text-2xl text-white"></i>
                                </div>
                                <div class="absolute -top-1 -right-1 w-6 h-6 bg-orange-fire rounded-full 
                                           animate-pulse border-2 border-white"></div>
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-gray-800 group-hover:text-primary-500 transition-colors">
                                    {{ config('app.name', 'تذاكر FBR-M') }}
                                </h1>
                                <p class="text-sm text-orange-fire font-medium">منصة إدارة التذاكر</p>
                            </div>
                        </a>
                    </div>
                    
                    <!-- 🔥 Navigation Links -->
                    <div class="hidden md:flex items-center space-x-6 space-x-reverse">
                        
                        <!-- Quick Stats -->
                        <div class="flex items-center space-x-4 space-x-reverse bg-orange-50 px-4 py-2 rounded-xl">
                            <div class="text-center">
                                <div class="text-lg font-bold text-primary-600">{{ number_format(1250) }}</div>
                                <div class="text-xs text-gray-600">التذاكر</div>
                            </div>
                            <div class="w-px h-8 bg-orange-200"></div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600">{{ number_format(85) }}%</div>
                                <div class="text-xs text-gray-600">الإنجاز</div>
                            </div>
                        </div>
                        
                        <!-- Navigation Items -->
                        @auth
                        <div class="flex items-center space-x-4 space-x-reverse">
                            
                            <!-- Notifications -->
                            <div class="relative">
                                <button class="relative p-3 bg-orange-50 rounded-xl hover:bg-orange-100 
                                             transition-colors duration-200 group">
                                    <i class="fas fa-bell text-primary-500 group-hover:text-primary-600"></i>
                                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white 
                                               text-xs rounded-full flex items-center justify-center animate-bounce">3</span>
                                </button>
                            </div>
                            
                            <!-- Language Switcher -->
                            <div class="mx-2">
                                @include('components.language-switcher')
                            </div>
                            
                            <!-- User Menu -->
                            <div class="relative group">
                                <button class="flex items-center space-x-3 space-x-reverse bg-gradient-soft 
                                             px-4 py-2 rounded-xl hover:bg-gradient-warm transition-all duration-300 
                                             shadow-md hover:shadow-lg">
                                    <div class="w-10 h-10 bg-gradient-fire rounded-full flex items-center justify-center 
                                               text-white font-bold shadow-md">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-gray-600">{{ __('app.' . (auth()->user()->role ?? 'user')) }}</div>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 group-hover:text-gray-600"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 
                                           opacity-0 invisible group-hover:opacity-100 group-hover:visible 
                                           transition-all duration-200 z-50">
                                    <div class="py-2">
                                        <a href="{{ dashboard_route() }}" 
                                           class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 
                                                  hover:text-primary-600 transition-colors">
                                            <i class="fas fa-tachometer-alt ml-3 text-primary-500"></i>
                                            لوحة التحكم
                                        </a>
                                        <a href="{{ route('profile.edit') }}" 
                                           class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 
                                                  hover:text-primary-600 transition-colors">
                                            <i class="fas fa-user-edit ml-3 text-primary-500"></i>
                                            الملف الشخصي
                                        </a>
                                        <hr class="my-2 border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}" class="block">
                                            @csrf
                                            <button type="submit" 
                                                    class="flex items-center w-full px-4 py-3 text-red-600 
                                                           hover:bg-red-50 transition-colors text-right">
                                                <i class="fas fa-sign-out-alt ml-3"></i>
                                                تسجيل الخروج
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <a href="{{ route('customer.login') }}" 
                               class="btn btn-outline px-6 py-2 text-sm">
                                <i class="fas fa-sign-in-alt ml-2"></i>
                                تسجيل الدخول
                            </a>
                            <a href="{{ route('customer.register') }}" 
                               class="btn btn-primary px-6 py-2 text-sm">
                                <i class="fas fa-user-plus ml-2"></i>
                                إنشاء حساب
                            </a>
                        </div>
                        @endauth
                    </div>
                    
                    <!-- 📱 Mobile Menu Button -->
                    <div class="md:hidden flex items-center">
                        <button class="mobile-menu-btn p-3 bg-orange-50 rounded-xl hover:bg-orange-100 
                                     transition-colors duration-200">
                            <i class="fas fa-bars text-primary-500"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- � Mobile Menu -->
            <div class="mobile-menu hidden md:hidden bg-white border-t border-gray-100 shadow-lg">
                <div class="px-4 py-6 space-y-4">
                    @auth
                    <div class="flex items-center space-x-4 space-x-reverse p-4 bg-gradient-soft rounded-xl">
                        <div class="w-12 h-12 bg-gradient-fire rounded-full flex items-center justify-center 
                                   text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm text-gray-600">المدير</div>
                        </div>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="{{ dashboard_route() }}" 
                           class="flex items-center p-3 text-gray-700 hover:bg-orange-50 
                                  hover:text-primary-600 rounded-lg transition-colors">
                            <i class="fas fa-tachometer-alt ml-3 text-primary-500"></i>
                            لوحة التحكم
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center p-3 text-gray-700 hover:bg-orange-50 
                                  hover:text-primary-600 rounded-lg transition-colors">
                            <i class="fas fa-user-edit ml-3 text-primary-500"></i>
                            الملف الشخصي
                        </a>
                    </nav>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center w-full p-3 text-red-600 hover:bg-red-50 
                                       rounded-lg transition-colors text-right">
                            <i class="fas fa-sign-out-alt ml-3"></i>
                            تسجيل الخروج
                        </button>
                    </form>
                    @else
                    <div class="space-y-3">
                        <a href="{{ route('customer.login') }}" 
                           class="btn btn-outline w-full justify-center">
                            <i class="fas fa-sign-in-alt ml-2"></i>
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('customer.register') }}" 
                           class="btn btn-primary w-full justify-center">
                            <i class="fas fa-user-plus ml-2"></i>
                            إنشاء حساب
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </nav>

        
        <!-- 🎯 Page Heading with Creative Design -->
        @if (isset($header))
        <header class="bg-white shadow-xl border-b-4 border-primary-500 mb-8">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-fire rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-star text-2xl text-white"></i>
                    </div>
                    <div>
                        {{ $header }}
                    </div>
                </div>
            </div>
        </header>
        @endif

        <!-- 🌟 Main Content with Enhanced Styling -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(isset($slot))
                    <div class="reveal-on-scroll">
                        {{ $slot }}
                    </div>
                @else
                    <div class="reveal-on-scroll">
                        @yield('content')
                    </div>
                @endif
            </div>
        </main>
        
        <!-- 🎉 Creative Footer -->
        <footer class="bg-gradient-fire text-white mt-20 relative overflow-hidden">
            <!-- Floating Shapes -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-orange-warm opacity-20 rounded-full floating"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-white opacity-10 shape-hexagon floating" style="animation-delay: 1s;"></div>
                <div class="absolute top-10 left-1/4 w-16 h-16 bg-orange-sunset opacity-30 shape-diamond floating" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Wave Decoration -->
            <div class="wave-decoration"></div>
            
            <!-- Footer Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    
                    <!-- Brand Section -->
                    <div class="md:col-span-2">
                        <div class="flex items-center mb-6">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center ml-4">
                                <i class="fas fa-ticket-alt text-3xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black">تذاكر FBR-M</h3>
                                <p class="text-orange-100">منصة إدارة التذاكر المتطورة</p>
                            </div>
                        </div>
                        <p class="text-orange-100 mb-6 leading-relaxed">
                            منصة متكاملة لإدارة الحجوزات والمدفوعات مع تحليلات ذكية وتصميم عصري. 
                            نساعدك في إدارة أعمالك بكفاءة وسهولة.
                        </p>
                        
                        <!-- Social Links -->
                        <div class="flex space-x-4 space-x-reverse">
                            <a href="#" class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center 
                                             hover:bg-opacity-30 transition-all duration-300 interactive-element">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center 
                                             hover:bg-opacity-30 transition-all duration-300 interactive-element">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center 
                                             hover:bg-opacity-30 transition-all duration-300 interactive-element">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-xl font-bold mb-6">روابط سريعة</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ route('home') }}" class="text-orange-100 hover:text-white transition-colors flex items-center">
                                <i class="fas fa-home ml-2"></i> الرئيسية
                            </a></li>
                            <li><a href="{{ dashboard_route() }}" class="text-orange-100 hover:text-white transition-colors flex items-center">
                                <i class="fas fa-tachometer-alt ml-2"></i> لوحة التحكم
                            </a></li>
                            <li><a href="#" class="text-orange-100 hover:text-white transition-colors flex items-center">
                                <i class="fas fa-headset ml-2"></i> الدعم الفني
                            </a></li>
                            <li><a href="#" class="text-orange-100 hover:text-white transition-colors flex items-center">
                                <i class="fas fa-file-contract ml-2"></i> الشروط والأحكام
                            </a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact Info -->
                    <div>
                        <h4 class="text-xl font-bold mb-6">تواصل معنا</h4>
                        <div class="space-y-3">
                            <div class="flex items-center text-orange-100">
                                <i class="fas fa-envelope ml-3"></i>
                                <span>support@fbr-m.com</span>
                            </div>
                            <div class="flex items-center text-orange-100">
                                <i class="fas fa-phone ml-3"></i>
                                <span>+966 50 123 4567</span>
                            </div>
                            <div class="flex items-center text-orange-100">
                                <i class="fas fa-map-marker-alt ml-3"></i>
                                <span>الرياض، المملكة العربية السعودية</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Bar -->
                <div class="border-t border-white border-opacity-20 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-orange-100 text-sm">
                        © {{ date('Y') }} تذاكر FBR-M. جميع الحقوق محفوظة.
                    </p>
                    <div class="flex items-center space-x-6 space-x-reverse mt-4 md:mt-0">
                        <span class="text-orange-100 text-sm">صُنع بـ</span>
                        <i class="fas fa-heart text-red-400 mx-2 animate-pulse"></i>
                        <span class="text-orange-100 text-sm">في السعودية</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- 🚀 Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('resources/js/creative-interactions.js') }}"></script>
    
    @livewireScripts
    
    <!-- Additional Scripts -->
    @stack('scripts')
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true,
            offset: 100
        });
        
        // Add custom animations to elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add reveal-on-scroll class to cards
            document.querySelectorAll('.card').forEach(card => {
                card.classList.add('reveal-on-scroll');
            });
        });
    </script>
</body>
</html>
