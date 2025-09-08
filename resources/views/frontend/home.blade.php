@extends('frontend.layouts.app')

@section('title', 'نافذة التذاكر - منصة حجز الخدمات والفعاليات الموثوقة')

@section('head')
<style>
    /* 🎨 نظام التصميم البرتقالي الإبداعي */
    :root {
        --primary-fire: #ff5722;
        --primary-orange: #F97316;
        --orange-sunset: #ff7043;
        --orange-warm: #ff8a65;
        --orange-light: #FB923C;
        --orange-dark: #EA580C;
        --orange-50: #FFF7ED;
        --orange-100: #FFEDD5;
        --orange-900: #9A3412;
    }

    /* ✨ حركات محسّنة */
    @keyframes floating {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(2deg); }
    }
    
    @keyframes sparkle {
        0%, 100% { opacity: 0; transform: scale(0); }
        50% { opacity: 1; transform: scale(1); }
    }
    
    @keyframes glow-pulse {
        0%, 100% { box-shadow: 0 0 20px rgba(249, 115, 22, 0.3); }
        50% { box-shadow: 0 0 40px rgba(249, 115, 22, 0.6), 0 0 60px rgba(249, 115, 22, 0.4); }
    }
    
    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-10deg); }
        75% { transform: rotate(10deg); }
    }

    .floating-animation {
        animation: floating 4s ease-in-out infinite;
    }
    
    .sparkle-effect {
        animation: sparkle 2s ease-in-out infinite;
    }
    
    .glow-effect {
        animation: glow-pulse 3s ease-in-out infinite;
    }
    
    .wave-animation {
        animation: wave 2s ease-in-out infinite;
    }

    /* 🔥 خلفيات متدرجة محسّنة */
    .orange-gradient {
        background: linear-gradient(135deg, var(--primary-orange) 0%, var(--orange-dark) 100%);
    }
    
    .fire-gradient {
        background: linear-gradient(135deg, var(--primary-fire) 0%, var(--primary-orange) 50%, var(--orange-sunset) 100%);
    }
    
    .sunset-gradient {
        background: linear-gradient(135deg, var(--orange-sunset) 0%, var(--orange-warm) 100%);
    }
    
    .orange-gradient-soft {
        background: linear-gradient(135deg, var(--orange-50) 0%, var(--orange-100) 100%);
    }

    /* حركات مخصصة */
    .slide-in-left {
        animation: slideInLeft 0.8s ease-out;
    }
    
    .slide-in-right {
        animation: slideInRight 0.8s ease-out;
    }
    
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeInUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* أنماط التبويبات */
    .tab-button {
        transition: all 0.3s ease;
    }
    .tab-button.active {
        background: var(--primary-orange);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
    }

    /* تأثيرات البطاقات عند التمرير */
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    /* حركة عداد الإحصائيات */
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-orange);
    }
    
    /* بطاقة المميزات الخاصة */
    .feature-card {
        position: relative;
        overflow: hidden;
    }
    .feature-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(249,115,22,0.1) 0%, transparent 70%);
        animation: floating 6s ease-in-out infinite;
    }
    
    /* شريط الثقة */
    .trust-badge {
        background: linear-gradient(90deg, #fff 0%, #FFF7ED 50%, #fff 100%);
        border: 2px solid var(--orange-100);
    }
</style>
@endsection

@section('content')
<!-- قسم البطل الرئيسي -->
<section class="orange-gradient text-white py-20 lg:py-28 overflow-hidden relative" dir="rtl">
    <!-- زخارف الخلفية -->
    <div class="absolute inset-0 opacity-10">
        <div class="floating-animation absolute top-20 right-10 w-16 h-16 bg-white rounded-full"></div>
        <div class="floating-animation absolute top-40 left-20 w-8 h-8 bg-yellow-300 rounded-full" style="animation-delay: 1s;"></div>
        <div class="floating-animation absolute bottom-20 right-1/4 w-12 h-12 bg-orange-200 rounded-full" style="animation-delay: 2s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- محتوى البطل -->
            <div class="slide-in-left">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                    <span class="text-sm font-medium">🎉 منصة موثوقة ومعتمدة!</span>
                    <span class="mr-2 text-xs bg-yellow-400 text-orange-900 px-2 py-1 rounded-full">جديد</span>
                </div>
                
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                    احجز خدماتك
                    <span class="text-yellow-300 block">وفعالياتك</span>
                    <span class="text-orange-200 block">بكل سهولة! 🎟️</span>
                </h1>
                
                <p class="text-xl md:text-2xl mb-8 text-orange-100 leading-relaxed">
                    اكتشف واحجز أفضل الخدمات من التجار الموثوقين في جميع أنحاء المملكة. 
                    <strong>بسيط</strong>، <strong>آمن</strong>، <strong>فوري</strong>.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="{{ route('customer.register') }}" 
                       class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center group">
                        <span class="ml-2">🚀</span> ابدأ مجاناً الآن
                        <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    
                    <a href="{{ route('search') }}" 
                       class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition transform hover:scale-105 flex items-center justify-center">
                        <span class="ml-2">🔍</span> تصفح الخدمات
                    </a>
                </div>

                <!-- مؤشرات الثقة -->
                <div class="flex items-center gap-6 text-orange-100">
                    <div class="flex items-center">
                        <div class="flex -space-x-reverse -space-x-2">
                            <div class="w-8 h-8 bg-white rounded-full border-2 border-orange-300"></div>
                            <div class="w-8 h-8 bg-yellow-400 rounded-full border-2 border-orange-300"></div>
                            <div class="w-8 h-8 bg-orange-200 rounded-full border-2 border-orange-300"></div>
                        </div>
                        <span class="mr-3 text-sm font-medium">+10,000 عميل سعيد</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-yellow-300 text-lg">⭐⭐⭐⭐⭐</span>
                        <span class="mr-2 text-sm font-medium">4.9/5 تقييم</span>
                    </div>
                </div>
            </div>

            <!-- صورة/رسم توضيحي البطل -->
            <div class="slide-in-right hidden lg:block">
                <div class="relative">
                    <div class="floating-animation bg-white rounded-2xl p-8 shadow-2xl">
                        <div class="text-center">
                            <div class="text-6xl mb-4">🎫</div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">حجز سهل</h3>
                            <p class="text-gray-600">احجز في 3 خطوات بسيطة</p>
                            
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center bg-orange-50 p-3 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold ml-3">1</div>
                                    <span class="text-gray-700">اختر الخدمة</span>
                                </div>
                                <div class="flex items-center bg-orange-50 p-3 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold ml-3">2</div>
                                    <span class="text-gray-700">حدد الوقت</span>
                                </div>
                                <div class="flex items-center bg-orange-50 p-3 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold ml-3">3</div>
                                    <span class="text-gray-700">ادفع واستمتع</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- عناصر عائمة -->
                    <div class="absolute -top-4 -left-4 floating-animation bg-yellow-400 rounded-full p-3" style="animation-delay: 0.5s;">
                        <span class="text-2xl">✨</span>
                    </div>
                    <div class="absolute -bottom-4 -right-4 floating-animation bg-orange-200 rounded-full p-3" style="animation-delay: 1.5s;">
                        <span class="text-2xl">🎉</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- شريط الثقة والأمان -->
<section class="bg-white py-6 border-y-2 border-orange-100" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center items-center gap-8 text-gray-600">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🔒</span>
                <span class="font-medium">دفع آمن 100%</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">⚡</span>
                <span class="font-medium">تأكيد فوري</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">📱</span>
                <span class="font-medium">متوفر على الجوال</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">🏆</span>
                <span class="font-medium">ضمان الجودة</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">💬</span>
                <span class="font-medium">دعم 24/7</span>
            </div>
        </div>
    </div>
</section>

<!-- قسم الإحصائيات -->
<section class="bg-orange-50 py-16" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="fade-in-up">
                <div class="stats-number" data-count="10000">0</div>
                <p class="text-gray-600 font-medium">عميل سعيد</p>
            </div>
            <div class="fade-in-up" style="animation-delay: 0.2s;">
                <div class="stats-number" data-count="500">0</div>
                <p class="text-gray-600 font-medium">تاجر موثوق</p>
            </div>
            <div class="fade-in-up" style="animation-delay: 0.4s;">
                <div class="stats-number" data-count="50000">0</div>
                <p class="text-gray-600 font-medium">خدمة محجوزة</p>
            </div>
            <div class="fade-in-up" style="animation-delay: 0.6s;">
                <div class="stats-number" data-count="99">0</div>
                <p class="text-gray-600 font-medium">نسبة الرضا %</p>
            </div>
        </div>
    </div>
</section>

<!-- قسم المميزات مع التبويبات -->
<section class="bg-gray-50 py-20" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                لماذا تختار <span class="text-orange-500">نافذة التذاكر؟</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                منصة متكاملة للعملاء والتجار والشركاء. استمتع بقوة تقنية الحجز الحديثة.
            </p>
        </div>

        <!-- تبويبات المميزات -->
        <div class="flex flex-col lg:flex-row justify-center mb-8">
            <div class="flex flex-wrap justify-center gap-2 mb-6 lg:mb-0">
                <button class="tab-button active px-6 py-3 rounded-lg font-semibold border-2 border-orange-200" onclick="showTab('customers')">
                    👥 للعملاء
                </button>
                <button class="tab-button px-6 py-3 rounded-lg font-semibold border-2 border-orange-200" onclick="showTab('merchants')">
                    🏪 للتجار  
                </button>
                <button class="tab-button px-6 py-3 rounded-lg font-semibold border-2 border-orange-200" onclick="showTab('admins')">
                    👨‍💼 للإدارة
                </button>
            </div>
        </div>

        <!-- محتويات التبويبات -->
        <div id="customers-tab" class="tab-content">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">🔍</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">اكتشاف سهل</h3>
                    <p class="text-gray-600">ابحث عن الخدمات والفعاليات القريبة منك بفلاتر بحث متقدمة</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">+1000 خدمة متاحة</span>
                    </div>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">⚡</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">حجز فوري</h3>
                    <p class="text-gray-600">احجز الخدمات في ثوانٍ مع عملية دفع مبسطة وسريعة</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">تأكيد في 30 ثانية</span>
                    </div>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">🔒</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">دفع آمن</h3>
                    <p class="text-gray-600">خيارات دفع متعددة مع أمان على مستوى البنوك</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">تشفير SSL 256-bit</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="merchants-tab" class="tab-content hidden">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">📊</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">لوحة تحليلات</h3>
                    <p class="text-gray-600">تتبع الحجوزات والإيرادات ورؤى العملاء</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">تقارير يومية مفصلة</span>
                    </div>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">🎨</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">واجهة مخصصة</h3>
                    <p class="text-gray-600">صفحات جميلة بعلامتك التجارية</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">قوالب احترافية</span>
                    </div>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">💰</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">دفعات سهلة</h3>
                    <p class="text-gray-600">مدفوعات تلقائية مباشرة لحسابك البنكي</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">تحويل يومي</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="admins-tab" class="tab-content hidden">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">🎛️</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">تحكم كامل</h3>
                    <p class="text-gray-600">إدارة جميع المستخدمين والتجار والمعاملات</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">لوحة تحكم شاملة</span>
                    </div>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">📈</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">تقارير متقدمة</h3>
                    <p class="text-gray-600">تحليلات شاملة وذكاء الأعمال</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">رؤى فورية</span>
                    </div>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg feature-card">
                    <div class="text-5xl mb-4 relative z-10">🛡️</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">إدارة الأمان</h3>
                    <p class="text-gray-600">صلاحيات مبنية على الأدوار ومراقبة أمنية</p>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 font-medium">حماية متعددة الطبقات</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الخدمات المميزة -->
<section class="bg-white py-20" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                خدماتنا <span class="text-orange-500">المميزة</span>
            </h2>
            <p class="text-xl text-gray-600">اكتشف مجموعة واسعة من الخدمات المتاحة على منصتنا</p>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <div class="text-center group cursor-pointer">
                <div class="w-20 h-20 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-orange-500 transition-colors">
                    <span class="text-3xl group-hover:scale-110 transition-transform">🎭</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">الفعاليات والحفلات</h3>
                <p class="text-sm text-gray-600">حفلات موسيقية، مسرحيات، ومعارض</p>
            </div>
            
            <div class="text-center group cursor-pointer">
                <div class="w-20 h-20 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                    <span class="text-3xl group-hover:scale-110 transition-transform">🏃</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">الرياضة واللياقة</h3>
                <p class="text-sm text-gray-600">صالات رياضية، ملاعب، ودروس لياقة</p>
            </div>
            
            <div class="text-center group cursor-pointer">
                <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-500 transition-colors">
                    <span class="text-3xl group-hover:scale-110 transition-transform">🍽️</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">المطاعم والمقاهي</h3>
                <p class="text-sm text-gray-600">حجوزات طاولات وطلبات خاصة</p>
            </div>
            
            <div class="text-center group cursor-pointer">
                <div class="w-20 h-20 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-500 transition-colors">
                    <span class="text-3xl group-hover:scale-110 transition-transform">🎓</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">التعليم والتدريب</h3>
                <p class="text-sm text-gray-600">دورات، ورش عمل، ومحاضرات</p>
            </div>
        </div>
    </div>
</section>

<!-- قسم كيف يعمل -->
<section class="bg-gray-50 py-20" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                كيف <span class="text-orange-500">يعمل؟</span>
            </h2>
            <p class="text-xl text-gray-600">خطوات بسيطة للبدء</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-20 h-20 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg glow-effect">
                        1
                    </div>
                    <div class="hidden md:block absolute top-10 right-full w-full h-0.5 bg-orange-200" style="width: calc(100% - 2.5rem);"></div>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">سجل حسابك</h3>
                <p class="text-gray-600">أنشئ حسابك المجاني في ثوانٍ</p>
            </div>
            
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-20 h-20 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg glow-effect">
                        2
                    </div>
                    <div class="hidden md:block absolute top-10 right-full w-full h-0.5 bg-orange-200" style="width: calc(100% - 2.5rem);"></div>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">اختر الخدمة</h3>
                <p class="text-gray-600">تصفح واختر من آلاف الخدمات</p>
            </div>
            
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-20 h-20 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg glow-effect">
                        3
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">احجز واستمتع</h3>
                <p class="text-gray-600">ادفع بأمان واستمتع بتجربتك</p>
            </div>
        </div>
    </div>
</section>

<!-- قسم آراء العملاء -->
<section class="bg-white py-20" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                ماذا يقول <span class="text-orange-500">عملاؤنا</span>
            </h2>
            <p class="text-xl text-gray-600">آلاف العملاء السعداء يثقون بنا</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-2xl p-8 relative">
                <div class="absolute -top-4 right-8">
                    <div class="bg-orange-500 text-white rounded-full p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center mb-4 mt-4">
                    <div class="flex text-yellow-400">
                        ⭐⭐⭐⭐⭐
                    </div>
                </div>
                <p class="text-gray-700 mb-6">"منصة رائعة! سهلت علي حجز جميع احتياجاتي من مكان واحد. الخدمة سريعة والدعم ممتاز."</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-200 rounded-full ml-3"></div>
                    <div>
                        <p class="font-semibold text-gray-900">أحمد الشمري</p>
                        <p class="text-sm text-gray-600">عميل منذ 2023</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-8 relative">
                <div class="absolute -top-4 right-8">
                    <div class="bg-orange-500 text-white rounded-full p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center mb-4 mt-4">
                    <div class="flex text-yellow-400">
                        ⭐⭐⭐⭐⭐
                    </div>
                </div>
                <p class="text-gray-700 mb-6">"كتاجر، زادت مبيعاتي بنسبة 40% بعد الانضمام للمنصة. أدوات التحليل ممتازة!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-200 rounded-full ml-3"></div>
                    <div>
                        <p class="font-semibold text-gray-900">فاطمة العلي</p>
                        <p class="text-sm text-gray-600">صاحبة متجر</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-8 relative">
                <div class="absolute -top-4 right-8">
                    <div class="bg-orange-500 text-white rounded-full p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center mb-4 mt-4">
                    <div class="flex text-yellow-400">
                        ⭐⭐⭐⭐⭐
                    </div>
                </div>
                <p class="text-gray-700 mb-6">"الواجهة سهلة الاستخدام والحجز سريع جداً. أنصح الجميع بتجربة المنصة."</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-200 rounded-full ml-3"></div>
                    <div>
                        <p class="font-semibold text-gray-900">محمد الدوسري</p>
                        <p class="text-sm text-gray-600">عميل VIP</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الدعوة للعمل -->
<section class="orange-gradient text-white py-20" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-block mb-6">
            <span class="text-6xl wave-animation inline-block">👋</span>
        </div>
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            هل أنت مستعد للبدء؟
        </h2>
        <p class="text-xl mb-8 text-orange-100">
            انضم لآلاف العملاء والتجار السعداء اليوم!
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('customer.register') }}" 
               class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition transform hover:scale-105 shadow-lg">
                🚀 ابدأ كعميل
            </a>
            <a href="{{ route('merchant.login') }}" 
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition transform hover:scale-105">
                🏪 انضم كتاجر
            </a>
        </div>
        
        <div class="mt-12 flex justify-center gap-8 text-orange-100">
            <div class="text-center">
                <p class="text-3xl font-bold text-white">30</p>
                <p class="text-sm">يوم تجربة مجانية</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-white">0</p>
                <p class="text-sm">رسوم خفية</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-white">24/7</p>
                <p class="text-sm">دعم فني</p>
            </div>
        </div>
    </div>
</section>

<script>
// وظيفة التبويبات
function showTab(tabName) {
    // إخفاء جميع محتويات التبويبات
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // إزالة الفئة النشطة من جميع الأزرار
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // إظهار التبويب المحدد
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // إضافة الفئة النشطة للزر المضغوط
    event.target.classList.add('active');
}

// حركة عداد الإحصائيات
function animateStats() {
    const stats = document.querySelectorAll('.stats-number');
    
    stats.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-count'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(current).toLocaleString('ar-SA') + (stat.parentElement.textContent.includes('%') ? '%' : '+');
        }, 16);
    });
}

// تشغيل حركة الإحصائيات عند الظهور
const observerOptions = {
    threshold: 0.5
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            observer.disconnect();
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', () => {
    const statsSection = document.querySelector('.stats-number')?.closest('section');
    if (statsSection) {
        observer.observe(statsSection);
    }
});
</script>
@endsection