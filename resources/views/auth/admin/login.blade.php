<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة الإدارة - شباك التذاكر</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="min-h-screen flex">
        <!-- Left Side - Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-xl font-bold text-dark">شباك التذاكر</span>
                    </div>
                    <h2 class="text-2xl font-bold text-dark">لوحة الإدارة</h2>
                    <p class="mt-2 text-gray">دخول آمن للمشرفين</p>
                </div>

                <!-- Security Notice -->
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-red-800">منطقة آمنة</h3>
                            <p class="mt-1 text-sm text-red-700">
                                هذه منطقة محظورة للمشرفين فقط. جميع محاولات الدخول مراقبة ومسجلة.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
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
                            placeholder="ادخل بريد المشرف الإلكتروني"
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
                            placeholder="ادخل كلمة مرور المشرف"
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
                            class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2"
                        >
                        <label for="remember_me" class="mr-2 text-sm text-gray">
                            تذكرني
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                        دخول لوحة الإدارة
                    </button>
                </form>

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
            <div class="absolute inset-0 h-full w-full bg-red-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="w-32 h-32 mx-auto mb-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">لوحة التحكم الرئيسية</h3>
                    <p class="text-xl text-red-100 max-w-md">
                        إدارة شاملة ومراقبة دقيقة لجميع جوانب المنصة والمستخدمين
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
        
        .admin-bg {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 25%, #991b1b 50%, #7f1d1d 75%, #450a0a 100%);
            background-size: 400% 400%;
            animation: admin-animation 10s ease-in-out infinite;
        }
        
        @keyframes admin-animation {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .floating {
            animation: floating 5s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-8px) rotate(1deg); }
            66% { transform: translateY(4px) rotate(-1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        
        .admin-glow {
            box-shadow: 0 0 40px rgba(220, 38, 38, 0.4);
        }
        
        .sparkle-admin {
            animation: sparkle-admin 2s ease-in-out infinite alternate;
        }
        
        @keyframes sparkle-admin {
            from { opacity: 0.3; transform: scale(0.7) rotate(0deg); }
            to { opacity: 1; transform: scale(1.3) rotate(180deg); }
        }
        
        .glass-effect-admin {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .admin-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(220, 38, 38, 0.3);
            transition: all 0.3s ease;
        }
        
        .admin-input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #dc2626;
            box-shadow: 0 0 25px rgba(220, 38, 38, 0.3);
        }
        
        .admin-btn {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .admin-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(220, 38, 38, 0.4);
        }
        
        .admin-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.7s;
        }
        
        .admin-btn:hover::before {
            left: 100%;
        }
        
        .shield-pulse {
            animation: shield-pulse 3s ease-in-out infinite;
        }
        
        @keyframes shield-pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }
    </style>
</head>
<body class="admin-bg">
    <!-- Floating Security Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-16 left-8 w-6 h-6 bg-red-400 rounded opacity-50 floating"></div>
        <div class="absolute top-32 right-16 w-4 h-4 bg-red-300 rounded-full opacity-60 floating" style="animation-delay: 1.5s;"></div>
        <div class="absolute bottom-24 left-1/5 w-5 h-5 bg-red-500 rounded opacity-40 floating" style="animation-delay: 2.5s;"></div>
        <div class="absolute bottom-40 right-1/4 w-3 h-3 bg-red-600 rounded-full opacity-70 floating" style="animation-delay: 0.8s;"></div>
        <div class="absolute top-1/2 right-8 w-4 h-4 bg-red-400 rounded opacity-50 floating" style="animation-delay: 1.8s;"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" x-data="{ showPassword: false }">
        <!-- Logo Section -->
        <div class="mb-10 text-center">
            <div class="relative inline-block">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 admin-glow floating shadow-2xl shield-pulse">
                    <i class="fas fa-shield-alt text-5xl text-red-600"></i>
                </div>
                <div class="absolute -top-3 -right-3 w-10 h-10 bg-red-500 rounded-full sparkle-admin"></div>
                <div class="absolute -bottom-2 -left-2 w-6 h-6 bg-red-400 rounded-full sparkle-admin" style="animation-delay: 1s;"></div>
            </div>
            <h1 class="text-5xl font-black text-white mb-3 drop-shadow-2xl">🛡️ لوحة الإدارة</h1>
            <p class="text-red-100 text-xl font-medium">دخول آمن للمشرفين</p>
        </div>

        <!-- Login Card -->
        <div class="w-full sm:max-w-lg mt-6 px-10 py-12 glass-effect-admin overflow-hidden sm:rounded-3xl shadow-2xl admin-glow">
            <!-- Header -->
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-white mb-3">🔐 تسجيل دخول آمن</h2>
                <p class="text-red-100 text-lg">الوصول للوحة التحكم الرئيسية</p>
                <div class="w-16 h-1 bg-red-400 rounded-full mx-auto mt-4"></div>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-8">
                @csrf

                <!-- Admin Email -->
                <div>
                    <label for="email" class="block text-sm font-bold text-white mb-3">
                        👤 البريد الإلكتروني للمشرف
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="admin-input block w-full px-5 py-4 rounded-2xl font-medium text-gray-800 placeholder-gray-500 text-lg"
                           placeholder="أدخل بريد المشرف الإلكتروني">
                    @error('email')
                        <p class="mt-3 text-sm text-red-200 bg-red-500 bg-opacity-20 px-3 py-2 rounded-lg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Admin Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-white mb-3">
                        🔑 كلمة مرور المشرف
                    </label>
                    <div class="relative">
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'"
                               name="password" 
                               required 
                               autocomplete="current-password"
                               class="admin-input block w-full px-5 py-4 rounded-2xl font-medium text-gray-800 placeholder-gray-500 pr-14 text-lg"
                               placeholder="أدخل كلمة مرور المشرف">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                            <svg x-show="!showPassword" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m6.121-3.121A9.957 9.957 0 0121 12c0 4.478-2.943 8.268-7 9.543m-.121-6.422a3 3 0 11-4.243-4.243m4.243 4.243L12 12"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-3 text-sm text-red-200 bg-red-500 bg-opacity-20 px-3 py-2 rounded-lg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember"
                           class="rounded border-red-300 text-red-600 shadow-sm focus:ring-red-500 focus:ring-offset-0 w-5 h-5">
                    <label for="remember_me" class="mr-3 text-sm text-red-100 font-medium">
                        🔒 تذكر بيانات الدخول
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="admin-btn w-full py-5 px-8 rounded-2xl text-white font-bold text-xl shadow-2xl">
                    ⚡ دخول لوحة الإدارة
                </button>
            </form>

            <!-- Security Notice -->
            <div class="mt-8 p-4 bg-red-900 bg-opacity-30 rounded-xl border border-red-500 border-opacity-30">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-300 text-xl ml-3"></i>
                    <div>
                        <h4 class="text-red-200 font-bold text-sm">تنبيه أمني</h4>
                        <p class="text-red-300 text-xs mt-1">هذه منطقة محظورة للمشرفين فقط. جميع الأنشطة مراقبة ومسجلة.</p>
                    </div>
                </div>
            </div>

            <!-- Links -->
            <div class="mt-8 text-center">
                <a href="{{ route('home') }}" 
                   class="text-red-200 hover:text-white text-sm transition-colors duration-300 flex items-center justify-center">
                    <i class="fas fa-arrow-left ml-2"></i>
                    العودة للصفحة الرئيسية
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-10 text-center">
            <p class="text-red-200 text-sm">
                🔐 نظام آمن محمي بتقنيات الحماية المتقدمة
            </p>
            <p class="text-red-300 text-xs mt-2">
                © {{ date('Y') }} شباك التذاكر - جميع الحقوق محفوظة
            </p>
        </div>
    </div>

    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>