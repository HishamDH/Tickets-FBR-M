<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دخول الشركاء - منصة التذاكر FBR-M</title>
    
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            <div class="mx-auto w-full max-w-md">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">منطقة الشركاء</h1>
                    <p class="text-gray-600">سجل دخولك لإدارة شراكاتك والعمولات</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('partner.login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
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
                            autofocus 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="أدخل بريدك الإلكتروني"
                        >
                        @error('email')
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            placeholder="أدخل كلمة المرور"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                name="remember"
                                class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                            >
                            <span class="mr-2 text-sm text-gray-600">تذكرني</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-green-600 hover:text-green-700 font-semibold" href="{{ route('password.request') }}">
                                نسيت كلمة المرور؟
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        تسجيل الدخول
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center space-y-4">
                    <p class="text-sm text-gray-600">
                        ليس لديك حساب؟
                        <a href="{{ route('partner.register') }}" class="text-green-600 hover:text-green-700 font-semibold">
                            إنشاء حساب شريك
                        </a>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500 mb-3">تسجيل دخول للأدوار الأخرى</p>
                        <div class="flex justify-center space-x-reverse space-x-4">
                            <a href="{{ route('customer.login') }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                                العملاء
                            </a>
                            <a href="{{ route('merchant.login') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                التجار
                            </a>
                            <a href="{{ route('filament.admin.auth.login') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                الإدارة
                            </a>
                            <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-gray-700 text-sm font-medium">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-bold mb-4">شراكة مُربحة</h2>
                        <p class="text-xl text-green-100 mb-6 leading-relaxed">
                            إدارة شراكاتك وتتبع العمولات مع تقارير شاملة
                        </p>
                        <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                            <h3 class="font-semibold mb-2">مميزات الشريك</h3>
                            <ul class="text-sm text-green-100 space-y-1">
                                <li>• تتبع العمولات والأرباح</li>
                                <li>• إدارة قاعدة العملاء</li>
                                <li>• تقارير مالية تفصيلية</li>
                                <li>• أدوات تسويق متقدمة</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="min-h-screen flex">
        <!-- Left Side - Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-xl font-bold text-dark">شباك التذاكر</span>
                    </div>
                    <h2 class="text-2xl font-bold text-dark">بوابة الشركاء</h2>
                    <p class="mt-2 text-gray">دخول لإدارة شبكة التجار والعمولات</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('partner.login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-dark mb-2">
                            البريد الإلكتروني
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            class="input-field w-full"
                            placeholder="ادخل بريدك الإلكتروني"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-dark mb-2">
                            كلمة المرور
                        </label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            class="input-field w-full"
                            placeholder="ادخل كلمة المرور"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember"
                            class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2"
                        >
                        <label for="remember_me" class="mr-2 text-sm text-gray">
                            تذكرني
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary w-full">
                        دخول بوابة الشركاء
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 space-y-4">
                    <div class="flex flex-col items-center space-y-2">
                        <a href="{{ route('password.request') }}" class="text-sm text-gray hover:text-primary">
                            نسيت كلمة المرور؟
                        </a>
                        <a href="{{ route('partner.register') }}" class="text-sm text-primary hover:text-primary-dark">
                            ليس لديك حساب؟ سجل كشريك
                        </a>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="mt-8 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray hover:text-dark">
                        ← العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 h-full w-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="w-32 h-32 mx-auto mb-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">شبكة الشركاء المتميزة</h3>
                    <p class="text-xl text-orange-100 max-w-md">
                        انضم لشبكة من الشركاء المتميزين واحصل على عمولات مجزية من تجارك
                    </p>
                    <div class="mt-8 grid grid-cols-1 gap-4 text-left">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-orange-100">إدارة شبكة التجار</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-orange-100">تتبع العمولات</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-200 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-orange-100">تقارير مفصلة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
