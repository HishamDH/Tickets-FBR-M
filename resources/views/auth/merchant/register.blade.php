<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل التجار - منصة التذاكر FBR-M</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        .floating-shape {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Form -->
        <div class="flex-1 flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-16">
            <div class="mx-auto w-full max-w-2xl">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">انضم لمنصة التجار</h1>
                    <p class="text-gray-600">أنشئ حسابك التجاري واربح معنا</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('merchant.register') }}" class="space-y-6">
                    @csrf

                    <!-- Grid Layout for Better Organization -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                الاسم الكامل *
                            </label>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                autocomplete="name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="ادخل اسمك الكامل"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Name -->
                        <div>
                            <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                اسم الشركة/المؤسسة *
                            </label>
                            <input 
                                id="business_name" 
                                type="text" 
                                name="business_name" 
                                value="{{ old('business_name') }}" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="ادخل اسم شركتك أو مؤسستك"
                            >
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commercial Registration Number -->
                        <div>
                            <label for="commercial_registration_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                رقم السجل التجاري *
                            </label>
                            <input 
                                id="commercial_registration_number" 
                                type="text" 
                                name="commercial_registration_number" 
                                value="{{ old('commercial_registration_number') }}" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="مثال: 1010123456"
                            >
                            @error('commercial_registration_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax Number -->
                        <div>
                            <label for="tax_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                الرقم الضريبي *
                            </label>
                            <input 
                                id="tax_number" 
                                type="text" 
                                name="tax_number" 
                                value="{{ old('tax_number') }}" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="مثال: 300123456789003"
                            >
                            @error('tax_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business City -->
                        <div>
                            <label for="business_city" class="block text-sm font-semibold text-gray-700 mb-2">
                                مدينة النشاط التجاري *
                            </label>
                            <select 
                                id="business_city" 
                                name="business_city" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            >
                                <option value="">اختر المدينة</option>
                                <option value="الرياض" {{ old('business_city') == 'الرياض' ? 'selected' : '' }}>الرياض</option>
                                <option value="جدة" {{ old('business_city') == 'جدة' ? 'selected' : '' }}>جدة</option>
                                <option value="مكة المكرمة" {{ old('business_city') == 'مكة المكرمة' ? 'selected' : '' }}>مكة المكرمة</option>
                                <option value="المدينة المنورة" {{ old('business_city') == 'المدينة المنورة' ? 'selected' : '' }}>المدينة المنورة</option>
                                <option value="الدمام" {{ old('business_city') == 'الدمام' ? 'selected' : '' }}>الدمام</option>
                                <option value="الخبر" {{ old('business_city') == 'الخبر' ? 'selected' : '' }}>الخبر</option>
                                <option value="القطيف" {{ old('business_city') == 'القطيف' ? 'selected' : '' }}>القطيف</option>
                                <option value="تبوك" {{ old('business_city') == 'تبوك' ? 'selected' : '' }}>تبوك</option>
                                <option value="بريدة" {{ old('business_city') == 'بريدة' ? 'selected' : '' }}>بريدة</option>
                                <option value="خميس مشيط" {{ old('business_city') == 'خميس مشيط' ? 'selected' : '' }}>خميس مشيط</option>
                                <option value="أبها" {{ old('business_city') == 'أبها' ? 'selected' : '' }}>أبها</option>
                                <option value="حائل" {{ old('business_city') == 'حائل' ? 'selected' : '' }}>حائل</option>
                                <option value="جازان" {{ old('business_city') == 'جازان' ? 'selected' : '' }}>جازان</option>
                                <option value="نجران" {{ old('business_city') == 'نجران' ? 'selected' : '' }}>نجران</option>
                                <option value="الباحة" {{ old('business_city') == 'الباحة' ? 'selected' : '' }}>الباحة</option>
                                <option value="عرعر" {{ old('business_city') == 'عرعر' ? 'selected' : '' }}>عرعر</option>
                                <option value="سكاكا" {{ old('business_city') == 'سكاكا' ? 'selected' : '' }}>سكاكا</option>
                                <option value="القريات" {{ old('business_city') == 'القريات' ? 'selected' : '' }}>القريات</option>
                            </select>
                            @error('business_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                البريد الإلكتروني *
                            </label>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="username"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="ادخل بريدك الإلكتروني"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                رقم الهاتف
                            </label>
                            <input 
                                id="phone" 
                                type="tel" 
                                name="phone" 
                                value="{{ old('phone') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="ادخل رقم هاتفك"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                كلمة المرور *
                            </label>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="new-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="ادخل كلمة المرور"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                تأكيد كلمة المرور *
                            </label>
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="أعد إدخال كلمة المرور"
                            >
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Verification Notice -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-semibold">ملاحظة مهمة:</p>
                                <p class="mt-1">سيتم مراجعة طلبك من قبل الإدارة للتأكد من صحة البيانات المقدمة. ستتلقى إشعاراً عند الموافقة على حسابك.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        إنشاء حساب تجاري
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center space-y-4">
                    <p class="text-sm text-gray-600">
                        لديك حساب بالفعل؟
                        <a href="{{ route('merchant.login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                            تسجيل الدخول
                        </a>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500 mb-3">تسجيل حساب للأدوار الأخرى</p>
                        <div class="flex justify-center space-x-reverse space-x-4">
                            <a href="{{ route('customer.register') }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                                العملاء
                            </a>
                            <a href="{{ route('partner.register') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                الشركاء
                            </a>
                            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                                الرئيسية
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Design -->
        <div class="hidden lg:block relative w-96 xl:w-1/2">
            <div class="absolute inset-0 gradient-bg">
                <!-- Floating Shapes -->
                <div class="absolute top-20 right-20 floating-shape">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full"></div>
                </div>
                <div class="absolute top-40 right-1/3 floating-shape" style="animation-delay: -2s;">
                    <div class="w-12 h-12 bg-white bg-opacity-15 rounded-full"></div>
                </div>
                <div class="absolute bottom-32 right-16 floating-shape" style="animation-delay: -4s;">
                    <div class="w-20 h-20 bg-white bg-opacity-10 rounded-full"></div>
                </div>
                
                <!-- Main Content -->
                <div class="flex items-center justify-center h-full">
                    <div class="text-center text-white px-8">
                        <div class="mb-8">
                            <svg class="w-24 h-24 mx-auto mb-6 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-bold mb-4">ابدأ رحلتك التجارية</h2>
                        <p class="text-xl text-blue-100 mb-6 leading-relaxed">
                            انضم إلى آلاف التجار الناجحين واحصل على أدوات متطورة
                        </p>
                        <div class="bg-white bg-opacity-20 rounded-lg p-6 backdrop-blur-sm">
                            <h3 class="font-semibold mb-4 text-lg">مميزات المنصة</h3>
                            <div class="grid grid-cols-1 gap-3 text-sm text-blue-100">
                                <div class="flex items-center text-right">
                                    <svg class="w-4 h-4 text-blue-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>إدارة الحجوزات بسهولة</span>
                                </div>
                                <div class="flex items-center text-right">
                                    <svg class="w-4 h-4 text-blue-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>تقارير مالية دقيقة</span>
                                </div>
                                <div class="flex items-center text-right">
                                    <svg class="w-4 h-4 text-blue-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>دعم فني متواصل</span>
                                </div>
                                <div class="flex items-center text-right">
                                    <svg class="w-4 h-4 text-blue-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>واجهة استخدام حديثة</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
