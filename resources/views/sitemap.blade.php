@extends('layouts.public')

@section('title', 'خريطة الموقع - شباك التذاكر')
@section('description', 'تصفح جميع صفحات وخدمات منصة شباك التذاكر')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-to-br from-orange-50 to-red-50 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                <span class="bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent">خريطة الموقع</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                تصفح جميع صفحات وخدمات منصة شباك التذاكر
            </p>
        </div>
    </div>
</section>

<!-- Sitemap Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Main Pages -->
            <div class="bg-white rounded-2xl shadow-lg border p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-3xl mr-3">🏠</span>
                    الصفحات الرئيسية
                </h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('home') }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-orange-400 rounded-full mr-3"></span>
                        الرئيسية
                    </a></li>
                    <li><a href="{{ route('services.index') }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-orange-400 rounded-full mr-3"></span>
                        الخدمات
                    </a></li>
                    <li><a href="{{ route('merchants.index') }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-orange-400 rounded-full mr-3"></span>
                        التجار
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-orange-400 rounded-full mr-3"></span>
                        من نحن
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-orange-400 rounded-full mr-3"></span>
                        اتصل بنا
                    </a></li>
                </ul>
            </div>

            <!-- Authentication -->
            <div class="bg-white rounded-2xl shadow-lg border p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-3xl mr-3">🔐</span>
                    المصادقة
                </h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                        تسجيل الدخول
                    </a></li>
                    <li><a href="{{ route('register') }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                        إنشاء حساب
                    </a></li>
                    <li><a href="{{ route('password.request') }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-3"></span>
                        استعادة كلمة المرور
                    </a></li>
                </ul>
            </div>

            <!-- Service Categories -->
            <div class="bg-white rounded-2xl shadow-lg border p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-3xl mr-3">🎯</span>
                    فئات الخدمات
                </h3>
                <ul class="space-y-3">
                    @php
                        $categories = [
                            'Venues' => '🏰',
                            'Catering' => '🍽️',
                            'Photography' => '📸',
                            'Entertainment' => '🎵',
                            'Planning' => '📋',
                            'Transportation' => '🚗',
                            'Decoration' => '🎨'
                        ];
                    @endphp
                    @foreach($categories as $category => $icon)
                        <li><a href="{{ route('services.index', ['category' => $category]) }}" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                            <span class="text-lg mr-3">{{ $icon }}</span>
                            {{ $category }}
                        </a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Customer Dashboard -->
            <div class="bg-white rounded-2xl shadow-lg border p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-3xl mr-3">👤</span>
                    لوحة العميل
                </h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                        لوحة التحكم
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                        حجوزاتي
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                        المفضلة
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-3"></span>
                        الملف الشخصي
                    </a></li>
                </ul>
            </div>

            <!-- Merchant Dashboard -->
            <div class="bg-white rounded-2xl shadow-lg border p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-3xl mr-3">🏪</span>
                    لوحة التاجر
                </h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                        لوحة التحكم
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                        خدماتي
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                        الحجوزات
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-3"></span>
                        التقارير
                    </a></li>
                </ul>
            </div>

            <!-- Legal Pages -->
            <div class="bg-white rounded-2xl shadow-lg border p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-3xl mr-3">📋</span>
                    الصفحات القانونية
                </h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-3"></span>
                        شروط الاستخدام
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-3"></span>
                        سياسة الخصوصية
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-3"></span>
                        سياسة الاسترداد
                    </a></li>
                    <li><a href="#" class="text-gray-600 hover:text-orange-600 transition-colors flex items-center">
                        <span class="w-2 h-2 bg-red-400 rounded-full mr-3"></span>
                        الأسئلة الشائعة
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-br from-orange-500 to-red-500">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            هل تحتاج مساعدة؟
        </h2>
        <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
            فريق دعمنا جاهز لمساعدتك في العثور على ما تبحث عنه
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" class="bg-white text-orange-600 px-8 py-4 rounded-2xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                تواصل معنا
            </a>
            <a href="#" class="bg-orange-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 border-2 border-white/20">
                الدعم الفني
            </a>
        </div>
    </div>
</section>
@endsection
