<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل التجار - نافذة التذاكر | انضم للمنصة الرائدة في إدارة التذاكر</title>
    <meta name="description" content="انضم لآلاف التجار الناجحين في نافذة التذاكر واحصل على أدوات متطورة لإدارة أعمالك وزيادة أرباحك">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        
        /* 🎨 Enhanced Blue Design System */
        :root {
            --primary-blue: #3B82F6;
            --blue-dark: #1D4ED8;
            --blue-light: #60A5FA;
            --blue-50: #EFF6FF;
            --blue-100: #DBEAFE;
            --blue-900: #1E3A8A;
        }

        .blue-gradient {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--blue-dark) 100%);
        }
        
        .blue-gradient-soft {
            background: linear-gradient(135deg, var(--blue-50) 0%, var(--blue-100) 100%);
        }
        
        /* ✨ Advanced Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.4); }
        }
        
        @keyframes slide-in-right {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slide-in-left {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fade-in-up {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }
        
        .slide-in-right {
            animation: slide-in-right 0.8s ease-out;
        }
        
        .slide-in-left {
            animation: slide-in-left 0.8s ease-out;
        }
        
        .fade-in-up {
            animation: fade-in-up 0.8s ease-out;
        }
        
        /* Enhanced Form Styles */
        .form-input, .form-select {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .form-input:focus, .form-select:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        
        .form-step {
            opacity: 0;
            animation: fade-in-up 0.6s ease-out forwards;
        }
        
        .form-step:nth-child(1) { animation-delay: 0.1s; }
        .form-step:nth-child(2) { animation-delay: 0.2s; }
        .form-step:nth-child(3) { animation-delay: 0.3s; }
        .form-step:nth-child(4) { animation-delay: 0.4s; }
        .form-step:nth-child(5) { animation-delay: 0.5s; }
        .form-step:nth-child(6) { animation-delay: 0.6s; }
        .form-step:nth-child(7) { animation-delay: 0.7s; }
        .form-step:nth-child(8) { animation-delay: 0.8s; }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--blue-dark) 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.4);
        }
        
        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover:before {
            left: 100%;
        }
        
        /* Trust indicators */
        .trust-badge {
            background: linear-gradient(90deg, #fff 0%, var(--blue-50) 50%, #fff 100%);
            border: 2px solid var(--blue-100);
        }
        
        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--blue-light) 100%);
            transition: width 0.3s ease;
        }
        
        /* Side panel decorations */
        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .decoration-1 {
            width: 120px;
            height: 120px;
            top: 15%;
            right: 10%;
            animation: float 4s ease-in-out infinite;
        }
        
        .decoration-2 {
            width: 80px;
            height: 80px;
            top: 45%;
            right: 25%;
            animation: float 5s ease-in-out infinite reverse;
        }
        
        .decoration-3 {
            width: 60px;
            height: 60px;
            top: 75%;
            right: 15%;
            animation: float 6s ease-in-out infinite;
        }
        
        .decoration-4 {
            width: 40px;
            height: 40px;
            top: 25%;
            right: 40%;
            animation: float 7s ease-in-out infinite reverse;
        }
        
        /* Success animation */
        @keyframes success-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .success-animation {
            animation: success-pulse 0.6s ease-in-out;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
                opacity: 0.7;
            }
            33% { 
                transform: translateY(-20px) rotate(120deg); 
                opacity: 1;
            }
            66% { 
                transform: translateY(-10px) rotate(240deg); 
                opacity: 0.8;
            }
        }

        .floating-animation {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Enhanced Form -->
        <div class="flex-1 flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-16 relative">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-10 left-10 w-32 h-32 rounded-full" style="background: radial-gradient(circle, var(--primary-blue) 0%, transparent 70%); animation: pulse-glow 4s ease-in-out infinite;"></div>
                <div class="absolute bottom-20 right-20 w-24 h-24 rounded-full" style="background: radial-gradient(circle, var(--blue-dark) 0%, transparent 70%); animation: pulse-glow 5s ease-in-out infinite reverse;"></div>
                <div class="absolute top-1/2 left-1/4 w-16 h-16 rounded-full" style="background: radial-gradient(circle, var(--blue-light) 0%, transparent 70%); animation: pulse-glow 6s ease-in-out infinite;"></div>
            </div>
            
            <div class="mx-auto w-full max-w-4xl relative z-10">
                <!-- Logo and Title -->
                <div class="text-center mb-8 slide-in-left">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-18 h-18 blue-gradient rounded-2xl flex items-center justify-center shadow-xl pulse-glow">
                            <span class="text-3xl">🏪</span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <h1 class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-4">
                            انضم لعائلة التجار! 🚀
                        </h1>
                        <p class="text-xl text-gray-600 mb-2">أنشئ حسابك التجاري واربح معنا</p>
                        <p class="text-sm text-blue-600 font-medium">✨ أدوات متطورة لنمو أعمالك</p>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="flex justify-center gap-3 mb-8 text-xs">
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-green-500">✓</span>
                            <span class="text-gray-700">تسجيل مجاني</span>
                        </div>
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-blue-500">🛡️</span>
                            <span class="text-gray-700">محمي</span>
                        </div>
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-blue-500">⚡</span>
                            <span class="text-gray-700">سريع</span>
                        </div>
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-orange-500">🎯</span>
                            <span class="text-gray-700">مربح</span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="bg-gray-200 rounded-full h-1 mb-6">
                        <div class="progress-bar rounded-full" style="width: 0%" id="progressBar"></div>
                    </div>
                </div>

                <!-- Enhanced Form -->
                <form method="POST" action="{{ route('merchant.register') }}" class="space-y-8" id="registrationForm">
                    @csrf

                    <!-- Grid Layout for Better Organization -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="form-step">
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-3">
                                👤 الاسم الكامل *
                            </label>
                            <div class="relative">
                                <input 
                                    id="name" 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="name"
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="ادخل اسمك الكامل"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">👤</span>
                                </div>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Business Name -->
                        <div class="form-step">
                            <label for="business_name" class="block text-sm font-bold text-gray-700 mb-3">
                                🏢 اسم الشركة/المؤسسة *
                            </label>
                            <div class="relative">
                                <input 
                                    id="business_name" 
                                    type="text" 
                                    name="business_name" 
                                    value="{{ old('business_name') }}" 
                                    required 
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="ادخل اسم شركتك أو مؤسستك"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">🏢</span>
                                </div>
                            </div>
                            @error('business_name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Commercial Registration Number -->
                        <div class="form-step">
                            <label for="commercial_registration_number" class="block text-sm font-bold text-gray-700 mb-3">
                                📋 رقم السجل التجاري *
                            </label>
                            <div class="relative">
                                <input 
                                    id="commercial_registration_number" 
                                    type="text" 
                                    name="commercial_registration_number" 
                                    value="{{ old('commercial_registration_number') }}" 
                                    required 
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="مثال: 1010123456"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">📋</span>
                                </div>
                            </div>
                            @error('commercial_registration_number')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Tax Number -->
                        <div class="form-step">
                            <label for="tax_number" class="block text-sm font-bold text-gray-700 mb-3">
                                💰 الرقم الضريبي *
                            </label>
                            <div class="relative">
                                <input 
                                    id="tax_number" 
                                    type="text" 
                                    name="tax_number" 
                                    value="{{ old('tax_number') }}" 
                                    required 
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="مثال: 300123456789003"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">💰</span>
                                </div>
                            </div>
                            @error('tax_number')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Business City -->
                        <div class="form-step">
                            <label for="business_city" class="block text-sm font-bold text-gray-700 mb-3">
                                🏙️ مدينة النشاط التجاري *
                            </label>
                            <div class="relative">
                                <select 
                                    id="business_city" 
                                    name="business_city" 
                                    required 
                                    class="form-select w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    onblur="updateProgress()"
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
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">🏙️</span>
                                </div>
                            </div>
                            @error('business_city')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-step">
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-3">
                                📧 البريد الإلكتروني *
                            </label>
                            <div class="relative">
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="username"
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="merchant@example.com"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">📧</span>
                                </div>
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-step">
                            <label for="phone" class="block text-sm font-bold text-gray-700 mb-3">
                                📞 رقم الهاتف
                            </label>
                            <div class="relative">
                                <input 
                                    id="phone" 
                                    type="tel" 
                                    name="phone" 
                                    value="{{ old('phone') }}" 
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="+966 50 123 4567"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">📞</span>
                                </div>
                            </div>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-step">
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-3">
                                🔐 كلمة المرور *
                            </label>
                            <div class="relative">
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="••••••••"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">🔐</span>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                يجب أن تحتوي على 8 أحرف على الأقل
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-step">
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-3">
                                🔒 تأكيد كلمة المرور *
                            </label>
                            <div class="relative">
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                    placeholder="••••••••"
                                    onblur="updateProgress()"
                                >
                                <div class="absolute top-4 left-4">
                                    <span class="text-gray-400 text-lg">🔒</span>
                                </div>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Verification Notice -->
                    <div class="mt-8 p-6 blue-gradient-soft border-2 border-blue-200 rounded-2xl fade-in-up" style="animation-delay: 0.9s;">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-lg">ℹ️</span>
                                </div>
                            </div>
                            <div class="mr-4">
                                <h4 class="text-lg font-bold text-blue-900 mb-2">📋 عملية المراجعة</h4>
                                <p class="text-blue-800 leading-relaxed">
                                    <strong>سيتم مراجعة طلبك من قبل فريقنا المختص</strong> للتأكد من صحة البيانات المقدمة. 
                                    ستتلقى إشعاراً عبر البريد الإلكتروني عند الموافقة على حسابك خلال <strong>24-48 ساعة</strong>.
                                </p>
                                <div class="mt-3 flex items-center text-blue-700">
                                    <span class="text-green-500 mr-2">✓</span>
                                    <span class="text-sm font-medium">عملية آمنة ومعتمدة</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" 
                                class="btn-primary w-full text-white font-bold py-5 px-8 rounded-2xl text-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300"
                                id="submitBtn">
                            🚀 إنشاء حسابي التجاري الآن
                        </button>
                    </div>
                </form>

                <!-- Features Preview -->
                <div class="mt-8 p-6 blue-gradient-soft rounded-2xl fade-in-up" style="animation-delay: 1s;">
                    <h3 class="text-lg font-bold text-blue-900 mb-4 text-center">🎁 ما ستحصل عليه مجاناً:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">📊</span>
                            <span>تحليلات ذكية</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">💳</span>
                            <span>دفع آمن</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">📱</span>
                            <span>تطبيق جوال</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">🎯</span>
                            <span>تسويق ذكي</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">💬</span>
                            <span>دعم 24/7</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">📈</span>
                            <span>نمو مبيعات</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">🛡️</span>
                            <span>حماية كاملة</span>
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2 text-lg">⚡</span>
                            <span>سرعة فائقة</span>
                        </div>
                    </div>
                </div>

                <!-- Links -->
                <div class="mt-8 text-center space-y-4 fade-in-up" style="animation-delay: 1.1s;">
                    <p class="text-sm text-gray-600">
                        لديك حساب بالفعل؟
                        <a href="{{ route('merchant.login') }}" class="text-blue-600 hover:text-blue-800 font-bold transition-colors">
                            🔑 تسجيل الدخول
                        </a>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500 mb-3">انضم بأدوار أخرى</p>
                        <div class="flex justify-center gap-6 text-sm">
                            <a href="{{ route('customer.register') }}" class="text-orange-600 hover:text-orange-800 font-medium transition-colors">
                                👤 العملاء
                            </a>
                            <a href="{{ route('partner.register') }}" class="text-green-600 hover:text-green-800 font-medium transition-colors">
                                🤝 الشركاء
                            </a>
                            <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">
                                🏠 الرئيسية
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Enhanced Design Panel -->
        <div class="hidden lg:block relative w-96 xl:w-1/2">
            <div class="absolute inset-0 blue-gradient"></div>
            
            <!-- Decorative Elements -->
            <div class="decoration-circle decoration-1"></div>
            <div class="decoration-circle decoration-2"></div>
            <div class="decoration-circle decoration-3"></div>
            <div class="decoration-circle decoration-4"></div>
            
            <!-- Content Overlay -->
            <div class="relative h-full flex flex-col justify-center items-center text-white p-12 slide-in-right">
                <div class="text-center max-w-md">
                    <!-- Main Illustration -->
                    <div class="mb-8 floating-animation">
                        <div class="w-36 h-36 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm shadow-2xl">
                            <span class="text-7xl">🏪</span>
                        </div>
                    </div>
                    
                    <!-- Title and Description -->
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                        ابدأ رحلتك
                        <br>
                        <span class="text-blue-200">التجارية</span>
                    </h2>
                    
                    <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                        انضم إلى آلاف التجار الناجحين
                        <br>
                        <strong>واحصل على أدوات متطورة</strong>
                    </p>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">500+</div>
                            <div class="text-xs text-blue-200">تاجر نشط</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">50K+</div>
                            <div class="text-xs text-blue-200">حجز ناجح</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">99%</div>
                            <div class="text-xs text-blue-200">رضا العملاء</div>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="space-y-3 text-right">
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100 text-sm">إدارة حجوزات متطورة وسهلة</span>
                            <span class="text-xl mr-3">📅</span>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100 text-sm">تقارير مالية دقيقة ومفصلة</span>
                            <span class="text-xl mr-3">📊</span>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100 text-sm">دعم فني 24/7 باللغة العربية</span>
                            <span class="text-xl mr-3">🎧</span>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100 text-sm">واجهة استخدام حديثة ومميزة</span>
                            <span class="text-xl mr-3">🎨</span>
                        </div>
                    </div>
                    
                    <!-- Success Stories -->
                    <div class="mt-8 p-4 bg-white/10 rounded-2xl backdrop-blur-sm">
                        <div class="flex items-center justify-center mb-3">
                            <div class="flex -space-x-reverse -space-x-2">
                                <div class="w-8 h-8 bg-yellow-400 rounded-full border-2 border-white"></div>
                                <div class="w-8 h-8 bg-green-400 rounded-full border-2 border-white"></div>
                                <div class="w-8 h-8 bg-purple-400 rounded-full border-2 border-white"></div>
                            </div>
                            <span class="mr-3 text-sm font-medium text-blue-100">+500 تاجر ناجح</span>
                        </div>
                        <p class="text-xs text-blue-100 leading-relaxed">
                            💡 <strong>نصيحة:</strong> التجار الذين انضموا لمنصتنا زادت مبيعاتهم بمعدل <strong>40%</strong> خلال الشهر الأول!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced JavaScript -->
    <script>
        // Form progress tracking
        function updateProgress() {
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            let filled = 0;
            
            requiredFields.forEach(field => {
                if (field.value.trim() !== '') {
                    filled++;
                }
            });
            
            const progress = (filled / requiredFields.length) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }
        
        // Form submission handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Update progress on page load
            updateProgress();
            
            // Form submission
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '🔄 جاري إنشاء حسابك...';
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75');
                
                // Success animation
                submitBtn.classList.add('success-animation');
            });
            
            // Enhanced input interactions
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.form-step').classList.add('scale-102');
                });
                
                input.addEventListener('blur', function() {
                    this.closest('.form-step').classList.remove('scale-102');
                });
                
                // Real-time validation feedback
                input.addEventListener('input', function() {
                    if (this.hasAttribute('required')) {
                        if (this.value.trim() !== '') {
                            this.classList.remove('border-red-300');
                            this.classList.add('border-green-300');
                        } else {
                            this.classList.remove('border-green-300');
                            this.classList.add('border-red-300');
                        }
                    }
                    updateProgress();
                });
            });
            
            // Password strength indicator
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');
            
            password.addEventListener('input', function() {
                const strength = checkPasswordStrength(this.value);
                // You can add visual feedback here
            });
            
            passwordConfirm.addEventListener('input', function() {
                if (this.value === password.value) {
                    this.classList.add('border-green-300');
                    this.classList.remove('border-red-300');
                } else {
                    this.classList.add('border-red-300');
                    this.classList.remove('border-green-300');
                }
            });
        });
        
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            return strength;
        }
        
        // Add floating animation to decorative elements
        function addFloatingAnimations() {
            const floatingElements = document.querySelectorAll('.floating-animation, .decoration-circle');
            floatingElements.forEach((el, index) => {
                el.style.animation = `float 4s ease-in-out infinite ${index * 0.8}s`;
            });
        }

        // Initialize floating animations
        setTimeout(addFloatingAnimations, 1000);

        // Add particle effect on form completion
        function celebrateCompletion() {
            // Create confetti effect when form is 100% complete
            const progress = document.getElementById('progressBar');
            if (progress.style.width === '100%') {
                createConfetti();
            }
        }

        function createConfetti() {
            const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'];
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.style.cssText = `
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    left: ${Math.random() * 100}%;
                    animation: confetti-fall 3s linear forwards;
                    z-index: 1000;
                    border-radius: 50%;
                `;
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 3000);
            }
        }

        // Enhanced progress tracking with celebration
        const originalUpdateProgress = updateProgress;
        updateProgress = function() {
            originalUpdateProgress();
            const progressBar = document.getElementById('progressBar');
            const progressWidth = parseFloat(progressBar.style.width);
            
            if (progressWidth === 100) {
                setTimeout(celebrateCompletion, 500);
            }
        };
    </script>
</body>
</html>