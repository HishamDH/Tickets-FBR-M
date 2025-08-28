<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'شباك التذاكر')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        .primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b-2 border-primary/10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 primary-gradient rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">شباك التذاكر</h1>
                        <p class="text-xs text-gray-500">منصة الحجوزات الذكية</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6 space-x-reverse">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-primary transition-colors">الرئيسية</a>
                    <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-primary transition-colors">الخدمات</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary transition-colors">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                            إنشاء حساب
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <button class="md:hidden text-gray-600" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-primary transition-colors py-2">الرئيسية</a>
                    <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-primary transition-colors py-2">الخدمات</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-center">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary transition-colors py-2">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-center">
                            إنشاء حساب
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">شباك التذاكر</h3>
                    <p class="text-gray-400">منصة الحجوزات الذكية التي تربط بين التجار والعملاء</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">روابط مهمة</h3>
                    <div class="space-y-2">
                        <a href="#" class="block text-gray-400 hover:text-white transition-colors">عن الشركة</a>
                        <a href="#" class="block text-gray-400 hover:text-white transition-colors">الشروط والأحكام</a>
                        <a href="#" class="block text-gray-400 hover:text-white transition-colors">سياسة الخصوصية</a>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">تواصل معنا</h3>
                    <div class="space-y-2 text-gray-400">
                        <p>البريد الإلكتروني: info@tickets.sa</p>
                        <p>الهاتف: +966 11 123 4567</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} شباك التذاكر. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
