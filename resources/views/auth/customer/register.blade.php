<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء حساب عميل - منصة التذاكر FBR-M</title>
    
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
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
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
                        <div class="w-14 h-14 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">انضم كعميل</h1>
                    <p class="text-gray-600">أنشئ حسابك للاستمتاع بخدماتنا المتميزة</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('customer.register') }}" class="space-y-5">
                    @csrf

                    <!-- Name Fields Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div>
                            <label for="f_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                الاسم الأول *
                            </label>
                            <input 
                                id="f_name" 
                                type="text" 
                                name="f_name" 
                                value="{{ old('f_name') }}" 
                                required 
                                autofocus 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                placeholder="الاسم الأول"
                            >
                            @error('f_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="l_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                اسم العائلة *
                            </label>
                            <input 
                                id="l_name" 
                                type="text" 
                                name="l_name" 
                                value="{{ old('l_name') }}" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                placeholder="اسم العائلة"
                            >
                            @error('l_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                            placeholder="example@email.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone and Gender Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                placeholder="05xxxxxxxx"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                                الجنس
                            </label>
                            <select 
                                id="gender" 
                                name="gender" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                            >
                                <option value="">اختر الجنس</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">
                            تاريخ الميلاد
                        </label>
                        <input 
                            id="date_of_birth" 
                            type="date" 
                            name="date_of_birth" 
                            value="{{ old('date_of_birth') }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                        >
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Fields Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                placeholder="كلمة مرور قوية"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                تأكيد كلمة المرور *
                            </label>
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                placeholder="إعادة كلمة المرور"
                            >
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start">
                        <input 
                            id="terms" 
                            type="checkbox" 
                            name="terms" 
                            required
                            class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2 mt-1"
                        >
                        <label for="terms" class="mr-2 text-sm text-gray-600">
                            أوافق على 
                            <a href="#" class="text-orange-600 hover:text-orange-700 font-semibold">شروط الخدمة</a>
                            و
                            <a href="#" class="text-orange-600 hover:text-orange-700 font-semibold">سياسة الخصوصية</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        إنشاء الحساب
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center space-y-4">
                    <p class="text-sm text-gray-600">
                        لديك حساب بالفعل؟
                        <a href="{{ route('customer.login') }}" class="text-orange-600 hover:text-orange-700 font-semibold">
                            سجل دخولك
                        </a>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500 mb-3">تسجيل للأدوار الأخرى</p>
                        <div class="flex justify-center space-x-reverse space-x-4">
                            <a href="{{ route('merchant.register') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                تاجر
                            </a>
                            <a href="{{ route('partner.register') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                شريك
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-bold mb-4">انضم إلى عائلتنا</h2>
                        <p class="text-xl text-orange-100 mb-6 leading-relaxed">
                            اكتشف عالماً من الخدمات المتميزة والعروض الحصرية
                        </p>
                        <div class="flex justify-center space-x-reverse space-x-8 text-sm">
                            <div class="text-center">
                                <div class="text-2xl font-bold">1000+</div>
                                <div class="text-orange-200">عميل راضي</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">500+</div>
                                <div class="text-orange-200">خدمة متاحة</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="text-orange-200">دعم فني</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-xl font-bold text-dark">شباك التذاكر</span>
                    </div>
                    <h2 class="text-2xl font-bold text-dark">إنشاء حساب عميل</h2>
                    <p class="mt-2 text-gray">انضم إلينا للاستفادة من خدماتنا</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('customer.register') }}" class="space-y-6">
                    @csrf

                    <!-- First Name -->
                    <div>
                        <label for="f_name" class="block text-sm font-medium text-dark mb-2">
                            الاسم الأول
                        </label>
                        <input 
                            id="f_name" 
                            type="text" 
                            name="f_name" 
                            value="{{ old('f_name') }}" 
                            required 
                            autofocus 
                            autocomplete="given-name"
                            class="input-field w-full"
                            placeholder="ادخل اسمك الأول"
                        >
                        @error('f_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="l_name" class="block text-sm font-medium text-dark mb-2">
                            اسم العائلة
                        </label>
                        <input 
                            id="l_name" 
                            type="text" 
                            name="l_name" 
                            value="{{ old('l_name') }}" 
                            required 
                            autocomplete="family-name"
                            class="input-field w-full"
                            placeholder="ادخل اسم العائلة"
                        >
                        @error('l_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

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
                            autocomplete="username"
                            class="input-field w-full"
                            placeholder="ادخل بريدك الإلكتروني"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-dark mb-2">
                            رقم الهاتف
                        </label>
                        <input 
                            id="phone" 
                            type="tel" 
                            name="phone" 
                            value="{{ old('phone') }}" 
                            class="input-field w-full"
                            placeholder="ادخل رقم هاتفك"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-dark mb-2">
                            تاريخ الميلاد (اختياري)
                        </label>
                        <input 
                            id="date_of_birth" 
                            type="date" 
                            name="date_of_birth" 
                            value="{{ old('date_of_birth') }}" 
                            class="input-field w-full"
                        >
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-dark mb-2">
                            الجنس (اختياري)
                        </label>
                        <select 
                            id="gender" 
                            name="gender" 
                            class="input-field w-full"
                        >
                            <option value="">اختر الجنس</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-dark mb-2">
                            رقم الهاتف
                        </label>
                        <input 
                            id="phone" 
                            type="tel" 
                            name="phone" 
                            value="{{ old('phone') }}" 
                            required
                            class="input-field w-full"
                            placeholder="ادخل رقم هاتفك"
                        >
                        @error('phone')
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
                            autocomplete="new-password"
                            class="input-field w-full"
                            placeholder="ادخل كلمة مرور قوية"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-dark mb-2">
                            تأكيد كلمة المرور
                        </label>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="input-field w-full"
                            placeholder="أعد إدخال كلمة المرور"
                        >
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms Agreement -->
                    <div class="flex items-start">
                        <input 
                            id="terms" 
                            type="checkbox" 
                            name="terms" 
                            required
                            class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2 mt-1"
                        >
                        <label for="terms" class="mr-2 text-sm text-gray">
                            أوافق على 
                            <a href="#" class="text-green-600 hover:text-green-700 font-medium">شروط الخدمة</a>
                            و
                            <a href="#" class="text-green-600 hover:text-green-700 font-medium">سياسة الخصوصية</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        إنشاء الحساب
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 space-y-4">
                    <div class="flex flex-col items-center space-y-2">
                        <a href="{{ route('customer.login') }}" class="text-sm text-gray hover:text-green-600">
                            لديك حساب بالفعل؟ سجل دخولك
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
            <div class="absolute inset-0 h-full w-full bg-green-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="w-32 h-32 mx-auto mb-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">مرحباً بك في عائلتنا</h3>
                    <p class="text-xl text-green-100 max-w-md">
                        انضم إلى آلاف العملاء الذين يثقون بخدماتنا المتميزة
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>