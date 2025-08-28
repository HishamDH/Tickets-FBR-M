<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>شباك التذاكر - Shubak Tickets</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            <!-- Header -->
            <header class="bg-white shadow-lg relative z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4 lg:py-6">
                        <div class="flex items-center space-x-reverse space-x-4">
                            <div class="text-2xl lg:text-3xl font-bold text-orange-600">شباك التذاكر</div>
                            <span class="text-xl lg:text-2xl font-bold text-gray-800">Shubak Tickets</span>
                        </div>
                        <div class="flex items-center space-x-reverse space-x-2 lg:space-x-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-lg font-semibold transition duration-300 text-sm lg:text-base">
                                    لوحة التحكم
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-orange-600 font-semibold transition duration-300 text-sm lg:text-base">
                                    تسجيل الدخول
                                </a>
                                <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-lg font-semibold transition duration-300 text-sm lg:text-base">
                                    إنشاء حساب
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <section class="bg-gradient-to-br from-orange-50 to-red-50 py-12 lg:py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        اكتشف أفضل الخدمات
                        <br class="hidden sm:block">
                        <span class="text-orange-600">لمناسباتك</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-gray-600 mb-8 max-w-4xl mx-auto leading-relaxed">
                        منصة شباك التذاكر توفر لك مجموعة متنوعة من الخدمات المميزة لجعل مناسباتك لا تُنسى
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('services.index') }}" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl">
                            استكشف الخدمات
                        </a>
                        <a href="#featured" class="w-full sm:w-auto border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-8 py-4 rounded-lg text-lg font-semibold transition duration-300">
                            الخدمات المميزة
                        </a>
                    </div>
                </div>
            </section>

            <!-- Featured Services Section -->
            <section id="featured" class="py-16 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12 lg:mb-16">
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">الخدمات المميزة</h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">اختر من بين أفضل الخدمات المتاحة على منصتنا</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        @php
                            $featuredServices = [
                                [
                                    'name' => 'قاعات الأفراح الفاخرة',
                                    'name_en' => 'Luxury Wedding Halls',
                                    'description' => 'قاعات مجهزة بأحدث التقنيات لجعل زفافك مميزاً',
                                    'price' => 'SAR 15,000',
                                    'image_class' => 'bg-gradient-to-br from-pink-400 to-red-500',
                                    'icon' => '🏰'
                                ],
                                [
                                    'name' => 'خدمات الطعام المتميزة',
                                    'name_en' => 'Gourmet Catering',
                                    'description' => 'أشهى الأطباق التراثية والعالمية',
                                    'price' => 'SAR 500 / شخص',
                                    'image_class' => 'bg-gradient-to-br from-green-400 to-teal-500',
                                    'icon' => '🍽️'
                                ],
                                [
                                    'name' => 'تخطيط الفعاليات',
                                    'name_en' => 'Event Planning',
                                    'description' => 'تنظيم كامل لفعالياتك من الألف إلى الياء',
                                    'price' => 'SAR 8,000',
                                    'image_class' => 'bg-gradient-to-br from-purple-400 to-indigo-500',
                                    'icon' => '📋'
                                ]
                            ];
                        @endphp

                        @foreach($featuredServices as $service)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group">
                                <div class="h-48 lg:h-56 {{ $service['image_class'] }} flex items-center justify-center relative overflow-hidden">
                                    <div class="text-white text-6xl lg:text-7xl opacity-20 group-hover:opacity-30 transition-opacity duration-300">
                                        {{ $service['icon'] }}
                                    </div>
                                    <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-semibold">
                                        مميز
                                    </div>
                                </div>
                                <div class="p-6 lg:p-8">
                                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">{{ $service['name'] }}</h3>
                                    <p class="text-sm text-gray-500 mb-3">{{ $service['name_en'] }}</p>
                                    <p class="text-gray-600 mb-6 leading-relaxed">{{ $service['description'] }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-orange-500 font-bold text-lg lg:text-xl">{{ $service['price'] }}</span>
                                        <a href="{{ route('services.index') }}" class="bg-orange-100 hover:bg-orange-200 text-orange-600 px-4 lg:px-6 py-2 lg:py-3 rounded-lg font-semibold transition duration-300 text-sm lg:text-base">
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Statistics Section -->
            <section class="py-16 lg:py-20 bg-gray-900 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-orange-500 mb-2">500+</div>
                            <div class="text-gray-300">عميل راض</div>
                        </div>
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-orange-500 mb-2">50+</div>
                            <div class="text-gray-300">خدمة متاحة</div>
                        </div>
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-orange-500 mb-2">10+</div>
                            <div class="text-gray-300">مدن مغطاة</div>
                        </div>
                        <div>
                            <div class="text-3xl lg:text-4xl font-bold text-orange-500 mb-2">24/7</div>
                            <div class="text-gray-300">دعم فني</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Latest Offers Section -->
            <section class="py-16 lg:py-20 bg-gradient-to-r from-orange-500 to-red-500">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-8">العروض الحصرية</h2>
                    <div class="bg-white rounded-xl p-6 lg:p-10 max-w-4xl mx-auto shadow-2xl">
                        <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">عرض خاص - خصم 25%</h3>
                        <p class="text-gray-600 mb-6 lg:mb-8 text-lg leading-relaxed">احجز باكيت كامل (قاعة + طعام + تصوير) واحصل على خصم حتى 25%</p>
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ route('services.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 lg:py-4 rounded-lg font-semibold transition duration-300 text-lg">
                                احجز الآن
                            </a>
                            <a href="{{ route('services.index') }}" class="border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-8 py-3 lg:py-4 rounded-lg font-semibold transition duration-300 text-lg">
                                تفاصيل العرض
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="bg-gray-900 text-white py-12 lg:py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div class="lg:col-span-2">
                            <h3 class="text-2xl lg:text-3xl font-bold mb-4">شباك التذاكر</h3>
                            <p class="text-gray-400 mb-6 leading-relaxed max-w-md">منصتك الموثوقة لحجز أفضل الخدمات والاستمتاع بتجربة لا تُنسى</p>
                            <div class="flex space-x-reverse space-x-4">
                                <a href="#" class="bg-orange-500 hover:bg-orange-600 text-white w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                                    ف
                                </a>
                                <a href="#" class="bg-orange-500 hover:bg-orange-600 text-white w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                                    ت
                                </a>
                                <a href="#" class="bg-orange-500 hover:bg-orange-600 text-white w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                                    إ
                                </a>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-6 text-lg">الخدمات</h4>
                            <ul class="space-y-3 text-gray-400">
                                <li><a href="{{ route('services.index', ['category' => 'Venues']) }}" class="hover:text-white transition duration-300">قاعات الأفراح</a></li>
                                <li><a href="{{ route('services.index', ['category' => 'Catering']) }}" class="hover:text-white transition duration-300">خدمات الطعام</a></li>
                                <li><a href="{{ route('services.index', ['category' => 'Photography']) }}" class="hover:text-white transition duration-300">التصوير</a></li>
                                <li><a href="{{ route('services.index', ['category' => 'Entertainment']) }}" class="hover:text-white transition duration-300">الترفيه</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-6 text-lg">الدعم</h4>
                            <ul class="space-y-3 text-gray-400">
                                <li><a href="#" class="hover:text-white transition duration-300">مساعدة</a></li>
                                <li><a href="#" class="hover:text-white transition duration-300">اتصل بنا</a></li>
                                <li><a href="#" class="hover:text-white transition duration-300">الأسئلة الشائعة</a></li>
                                <li><a href="#" class="hover:text-white transition duration-300">سياسة الخصوصية</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                        <p>&copy; 2025 شباك التذاكر. جميع الحقوق محفوظة.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
