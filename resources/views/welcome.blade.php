<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة التذاكر FBR-M</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-orange-600">منصة التذاكر FBR-M</h1>
                </div>
                <div class="flex items-center space-x-reverse space-x-4">
                    <div class="relative group">
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                            تسجيل دخول
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="{{ route('customer.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول عميل</a>
                            <a href="{{ route('merchant.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول تاجر</a>
                            <a href="{{ route('partner.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول شريك</a>
                            <a href="{{ route('admin.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول الإدارة</a>
                        </div>
                    </div>
                    <div class="relative group">
                        <button class="bg-white hover:bg-orange-50 text-orange-600 border border-orange-500 px-4 py-2 rounded-lg transition duration-200 flex items-center">
                            انشاء حساب
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="{{ route('customer.register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">تسجيل عميل</a>
                            <a href="{{ route('merchant.register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">تسجيل تاجر</a>
                            <a href="{{ route('partner.register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">تسجيل شريك</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-orange-500 to-orange-600 overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    منصة التذاكر الذكية
                </h1>
                <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
                    حجز وإدارة التذاكر بسهولة ويسر مع نظام شامل للعملاء والتجار والشركاء
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('customer.register') }}" class="bg-white text-orange-600 px-8 py-3 rounded-lg font-semibold hover:bg-orange-50 transition duration-200">
                        انضم كعميل
                    </a>
                    <a href="{{ route('merchant.register') }}" class="bg-orange-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-800 transition duration-200">
                        انضم كتاجر
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">لماذا منصة FBR-M؟</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    نوفر حلول شاملة ومتطورة لإدارة التذاكر والحجوزات بطريقة احترافية وآمنة
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- For Customers -->
                <div class="text-center p-8 rounded-xl border border-orange-100 hover:shadow-lg transition duration-200">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">للعملاء</h3>
                    <p class="text-gray-600 mb-6">
                        حجز سهل وسريع للتذاكر مع إمكانية إدارة الحجوزات وتتبع الطلبات
                    </p>
                    <a href="{{ route('customer.register') }}" class="text-orange-600 font-semibold hover:text-orange-700">
                        ابدأ الآن ←
                    </a>
                </div>

                <!-- For Merchants -->
                <div class="text-center p-8 rounded-xl border border-orange-100 hover:shadow-lg transition duration-200">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">للتجار</h3>
                    <p class="text-gray-600 mb-6">
                        إدارة شاملة للفعاليات والتذاكر مع أدوات تحليل ومتابعة المبيعات
                    </p>
                    <a href="{{ route('merchant.register') }}" class="text-orange-600 font-semibold hover:text-orange-700">
                        انضم الآن ←
                    </a>
                </div>

                <!-- For Partners -->
                <div class="text-center p-8 rounded-xl border border-orange-100 hover:shadow-lg transition duration-200">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">للشركاء</h3>
                    <p class="text-gray-600 mb-6">
                        فرص شراكة مميزة مع عمولات تنافسية وأدوات تسويق متقدمة
                    </p>
                    <a href="{{ route('partner.register') }}" class="text-orange-600 font-semibold hover:text-orange-700">
                        كن شريكاً ←
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="py-16 bg-orange-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-orange-600 mb-2">1000+</div>
                    <div class="text-gray-600">عميل راضي</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600 mb-2">500+</div>
                    <div class="text-gray-600">تاجر نشط</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600 mb-2">50+</div>
                    <div class="text-gray-600">شريك موثوق</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600 mb-2">10000+</div>
                    <div class="text-gray-600">تذكرة مباعة</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">منصة FBR-M</h3>
                    <p class="text-gray-400">
                        منصة شاملة لإدارة التذاكر والحجوزات بطريقة احترافية ومبتكرة
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">روابط سريعة</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('customer.register') }}" class="hover:text-white">تسجيل عميل</a></li>
                        <li><a href="{{ route('merchant.register') }}" class="hover:text-white">تسجيل تاجر</a></li>
                        <li><a href="{{ route('partner.register') }}" class="hover:text-white">تسجيل شريك</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">دخول الحسابات</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('customer.login') }}" class="hover:text-white">دخول العملاء</a></li>
                        <li><a href="{{ route('merchant.login') }}" class="hover:text-white">دخول التجار</a></li>
                        <li><a href="{{ route('partner.login') }}" class="hover:text-white">دخول الشركاء</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-white">دخول الإدارة</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">تواصل معنا</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>البريد الإلكتروني: info@fbr-m.com</li>
                        <li>الهاتف: +966 50 123 4567</li>
                        <li>العنوان: الرياض، المملكة العربية السعودية</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 منصة التذاكر FBR-M. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
</body>
</html>