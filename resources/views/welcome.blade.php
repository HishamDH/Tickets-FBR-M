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
                            <a href="{{ route('customer.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول العملاء</a>
                            <a href="{{ route('merchant.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول التجار</a>
                            <a href="{{ route('partner.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول الشركاء</a>
                            <a href="{{ route('filament.admin.auth.login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50">دخول الإدارة</a>
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
                        <li><a href="{{ route('filament.admin.auth.login') }}" class="hover:text-white">دخول الإدارة</a></li>
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
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container flex justify-between items-center py-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-dark">شباك التذاكر</h1>
                </div>
            </div>
            
            <nav class="hidden md:flex items-center gap-6">
                <a href="#" class="nav-link">الرئيسية</a>
                <a href="#services" class="nav-link">الخدمات</a>
                <a href="#" class="nav-link">من نحن</a>
                <a href="#" class="nav-link">تواصل معنا</a>
            </nav>
            
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">لوحة التحكم</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20 bg-white">
        <div class="container text-center">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-dark mb-6">
                    أسعار شفافة تناسب الجميع
                </h1>
                <p class="text-lg text-gray mb-8 leading-relaxed">
                    ادخر المال التي تضم احتياجاتك ومتطلباتك. نحن فريق طويلة الأمد مع أسعار حسرة
                </p>
                <a href="#pricing" class="btn btn-primary">
                    اختر الخطة المناسبة
                </a>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-16 bg-gray-50">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                
                <!-- الاحترافية Card -->
                <div class="card bg-white p-8">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-dark mb-2">الاحترافية</h3>
                        <p class="text-gray">للتجار الصاعدين والمؤسسات الناشئة</p>
                    </div>
                    
                    <div class="mb-8">
                        <div class="text-center mb-6">
                            <span class="text-4xl font-bold text-primary">0.00</span>
                            <span class="text-gray mr-2">ريال</span>
                        </div>
                        
                        <ul class="space-y-3">
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                دعم وتوجيه الطلاب والموظفين الجامعيين
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                نظام ذكي مشارك
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                تقارير وإحصائيات متقدمة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                دعم من خبير متمتع
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                زيارة المعادلة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                سطح الموقع على الحراج الآليات المطعونة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                تتضمن الآمان علي عقار شهر لتحديد العدد
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                جودة وقسم المسائل المالية وسلة المباني
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                نظام الارشدعم في الخلجير
                            </li>
                        </ul>
                    </div>
                    
                    <a href="{{ route('register') }}" class="btn btn-primary w-full">
                        ابدأ تجربتك المجانية
                    </a>
                    <p class="text-center text-sm text-gray mt-3">
                        ليست بحاجة إلى بطاقة ائتمان
                    </p>
                </div>

                <!-- الأساسية Card -->
                <div class="card bg-white p-8 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-primary text-white px-4 py-1 rounded-full text-sm font-medium">
                            الأكثر شعبية
                        </span>
                    </div>
                    
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-dark mb-2">الأساسية</h3>
                        <p class="text-gray">بنحان و المبشرسات الدبيرة والمؤسسات</p>
                    </div>
                    
                    <div class="mb-8">
                        <div class="text-center mb-6">
                            <div class="mb-2">
                                <span class="text-lg text-gray line-through">9,99</span>
                                <span class="text-sm text-gray mr-1">بدلاً من</span>
                            </div>
                            <div>
                                <span class="text-4xl font-bold text-blue-500">3.5%</span>
                                <span class="text-gray mr-2">فقط رسوم على المعاملة الناجحة</span>
                            </div>
                        </div>
                        
                        <ul class="space-y-3">
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                جميع ميزات ومكن مواصفات الخطة الاحترافية
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                نظام ذكي مشارك
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                تقارير وإحصائيات متقدمة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                زيارة المعادلة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                دعم من خبير متمتع
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                جموع وتشمل ووجود المعلومات
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                سطح الموقع الأحكام الحرافة قائمة الصلتصفة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                جدول وسائل متابعة من الشركة
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                نظام الادخدعم في الفخرن
                            </li>
                        </ul>
                    </div>
                    
                    <a href="{{ route('register') }}" class="btn btn-primary w-full">
                        اختر الخطة الأساسية
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-white">
        <div class="container">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">مزايا شباك التذاكر</h2>
                <p class="text-gray max-w-2xl mx-auto">
                    توفر لك إل بتوفير تجربة سهلة وموثوقة، مع فريق متخصص من الخبراء
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- موقع سهل الاستخدام -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">أمان متقدم</h3>
                    <p class="text-gray">
                        ضمان عدم سلامة المعلومات الشخصية والمالية، 
                        تشخیص التحدید المخصص سطح المؤسسة
                    </p>
                </div>

                <!-- واجهة سهلة الاستخدام -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">واجهة سهلة الاستخدام</h3>
                    <p class="text-gray">
                        تصميم بسيط ومعصر، يمكن لكل شخص التبرع فيتاح
                        عدد حصين المعدد رضط تقنية القياس المعطط
                    </p>
                </div>

                <!-- دعم فني متميز -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">دعم فني متميز</h3>
                    <p class="text-gray">
                        خدمة دعم بمتطلبات تدخل جال الساحت كين الخوادم
                        عدصمر ولبيست الهحة بدمنات شركة التجيدة
                    </p>
                </div>

                <!-- تقارير تحليلية -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">تقارير تحليلية</h3>
                    <p class="text-gray">
                        تحليل مُفصل للبيانات وإحصانات مفيدة بشهادة السيق
                        بأسعار تنصيب وديلة ديمية ئيت الاعتماخاك سحجة
                    </p>
                </div>

                <!-- سرعة في الأداء -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">سرعة في الأداء</h3>
                    <p class="text-gray">
                        شكل تطبيقات سريعة التحليل المتقدمة والتنداور غيمر دراعة
                        مستود تحديد تتيلة فبرقاتو حجم تتصور السطح
                    </p>
                </div>

                <!-- خدماتنا المتكاملة -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-dark mb-2">خدماتنا المتكاملة</h3>
                    <p class="text-gray">
                        حلول شاملة ومتخصصة تقنيات تطوير المؤسسات وتطبيقات معاصرة
                        حالات التكامول الوعضطر راحة كدونضا منصائط الاختبائية
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primary">
        <div class="container text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                هل أنت جاهز لبدء رحلتك معنا؟
            </h2>
            <p class="text-primary-light mb-8 max-w-2xl mx-auto">
                انضم إلى آلاف العملاء الراضين واكتشف الحلول المبتكرة التي نقدمها
            </p>
            <a href="{{ route('register') }}" class="btn bg-white text-primary hover:bg-gray-100">
                ابدأ تجربتك المجانية
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <h3 class="text-xl font-bold mb-4">شباك التذاكر</h3>
                    <p class="text-gray-400 mb-6 max-w-md">
                        منصة متكاملة لحجوزات الفعاليات والمناسبات والخدمات
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-primary-hover">
                            <span class="text-white">ف</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-primary-hover">
                            <span class="text-white">ت</span>
                        </a>
                        <a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center hover:bg-primary-hover">
                            <span class="text-white">ي</span>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">الرئيسية</a></li>
                        <li><a href="#" class="hover:text-white">الخدمات</a></li>
                        <li><a href="#" class="hover:text-white">من نحن</a></li>
                        <li><a href="#" class="hover:text-white">اتصل بنا</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">الدعم</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">مركز المساعدة</a></li>
                        <li><a href="#" class="hover:text-white">الأسئلة الشائعة</a></li>
                        <li><a href="#" class="hover:text-white">سياسة الخصوصية</a></li>
                        <li><a href="#" class="hover:text-white">شروط الاستخدام</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 شباك التذاكر. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>
</body>
</html>
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
            <section class="hero-gradient py-12 lg:py-20 relative overflow-hidden">
                <!-- Floating Elements Background -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="floating-animation absolute top-20 left-10 w-20 h-20 bg-orange-200 rounded-full opacity-20"></div>
                    <div class="floating-animation absolute top-40 right-20 w-16 h-16 bg-red-200 rounded-full opacity-30" style="animation-delay: -1s;"></div>
                    <div class="floating-animation absolute bottom-20 left-1/4 w-12 h-12 bg-orange-300 rounded-full opacity-25" style="animation-delay: -2s;"></div>
                    <div class="floating-animation absolute bottom-40 right-1/3 w-24 h-24 bg-red-100 rounded-full opacity-20" style="animation-delay: -0.5s;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    <h1 class="fade-in-up text-3xl sm:text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        اكتشف أفضل الخدمات
                        <br class="hidden sm:block">
                        <span class="text-orange-600 pulse-orange inline-block px-4 py-2 rounded-lg">لمناسباتك</span>
                    </h1>
                    <p class="fade-in-up text-lg lg:text-xl text-gray-600 mb-8 max-w-4xl mx-auto leading-relaxed" style="animation-delay: 0.2s;">
                        منصة شباك التذاكر توفر لك مجموعة متنوعة من الخدمات المميزة لجعل مناسباتك لا تُنسى
                    </p>
                    <div class="fade-in-up flex flex-col sm:flex-row gap-4 justify-center items-center" style="animation-delay: 0.4s;">
                        <a href="{{ route('services.index') }}" class="floating-animation w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 pulse-orange">
                            استكشف الخدمات
                        </a>
                        <a href="#featured" class="card-hover w-full sm:w-auto border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-300">
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
