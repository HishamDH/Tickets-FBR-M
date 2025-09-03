<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نسيت كلمة المرور - شباك التذاكر</title>
    
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
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-xl font-bold text-dark">شباك التذاكر</span>
                    </div>
                    <h2 class="text-2xl font-bold text-dark">نسيت كلمة المرور؟</h2>
                    <p class="mt-2 text-gray">لا تقلق، سنرسل لك رابط إعادة التعيين</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                            class="input-field w-full"
                            placeholder="ادخل بريدك الإلكتروني"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                        إرسال رابط إعادة التعيين
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-blue-800">معلومة مهمة</h3>
                            <p class="mt-1 text-sm text-blue-700">
                                سنرسل لك رابط إعادة تعيين كلمة المرور عبر البريد الإلكتروني. تحقق من صندوق الرسائل والبريد المزعج.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-primary hover:text-primary-hover">
                        ← العودة لتسجيل الدخول
                    </a>
                </div>

                <!-- Back to Home -->
                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray hover:text-dark">
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 h-full w-full bg-primary flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="w-32 h-32 mx-auto mb-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">استعادة كلمة المرور</h3>
                    <p class="text-xl text-primary-light max-w-md">
                        نساعدك في استعادة الوصول لحسابك بخطوات بسيطة وآمنة
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
        
        .recovery-bg {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 25%, #5b21b6 50%, #4c1d95 75%, #3730a3 100%);
            background-size: 400% 400%;
            animation: recovery-animation 10s ease-in-out infinite;
        }
        
        @keyframes recovery-animation {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .floating-recovery {
            animation: floating-recovery 8s ease-in-out infinite;
        }
        
        @keyframes floating-recovery {
            0% { transform: translateY(0px) scale(1) rotate(0deg); }
            33% { transform: translateY(-18px) scale(1.1) rotate(5deg); }
            66% { transform: translateY(10px) scale(0.9) rotate(-5deg); }
            100% { transform: translateY(0px) scale(1) rotate(0deg); }
        }
        
        .recovery-glow {
            box-shadow: 0 0 45px rgba(124, 58, 237, 0.6);
        }
        
        .sparkle-recovery {
            animation: sparkle-recovery 2.8s ease-in-out infinite alternate;
        }
        
        @keyframes sparkle-recovery {
            from { opacity: 0.4; transform: scale(0.6) rotate(0deg); }
            to { opacity: 1; transform: scale(1.4) rotate(360deg); }
        }
        
        .glass-effect-recovery {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid rgba(255, 255, 255, 0.22);
        }
        
        .recovery-input {
            background: rgba(255, 255, 255, 0.98);
            border: 2px solid rgba(124, 58, 237, 0.3);
            transition: all 0.5s ease;
        }
        
        .recovery-input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #7c3aed;
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.5);
            transform: scale(1.03);
        }
        
        .recovery-btn {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .recovery-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(124, 58, 237, 0.6);
        }
        
        .recovery-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.8s;
        }
        
        .recovery-btn:hover::before {
            left: 100%;
        }
        
        .spin-recovery {
            animation: spin-recovery 6s linear infinite;
        }
        
        @keyframes spin-recovery {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .breathe-recovery {
            animation: breathe-recovery 4s ease-in-out infinite;
        }
        
        @keyframes breathe-recovery {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
        }
    </style>
</head>
<body class="recovery-bg">
    <!-- Floating Recovery Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-28 left-20 w-7 h-7 bg-purple-300 rounded-full opacity-60 floating-recovery"></div>
        <div class="absolute top-44 right-28 w-9 h-9 bg-purple-200 rounded-full opacity-50 floating-recovery" style="animation-delay: 1.8s;"></div>
        <div class="absolute bottom-36 left-1/6 w-6 h-6 bg-purple-400 rounded-full opacity-70 floating-recovery" style="animation-delay: 2.6s;"></div>
        <div class="absolute bottom-52 right-1/5 w-8 h-8 bg-purple-300 rounded-full opacity-40 floating-recovery" style="animation-delay: 0.9s;"></div>
        <div class="absolute top-2/5 right-20 w-7 h-7 bg-purple-500 rounded-full opacity-55 floating-recovery" style="animation-delay: 2.1s;"></div>
        <div class="absolute top-1/3 left-1/4 w-5 h-5 bg-purple-200 rounded-full opacity-65 floating-recovery" style="animation-delay: 3.4s;"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" x-data="{ emailSent: false }">
        <!-- Logo Section -->
        <div class="mb-8 text-center">
            <div class="relative inline-block">
                <div class="w-26 h-26 bg-white rounded-full flex items-center justify-center mb-5 recovery-glow floating-recovery shadow-2xl">
                    <i class="fas fa-key text-5xl text-purple-600 spin-recovery"></i>
                </div>
                <div class="absolute -top-4 -right-4 w-10 h-10 bg-purple-400 rounded-full sparkle-recovery"></div>
                <div class="absolute -bottom-3 -left-3 w-8 h-8 bg-purple-300 rounded-full sparkle-recovery" style="animation-delay: 1.7s;"></div>
                <div class="absolute top-2 left-2 w-6 h-6 bg-purple-200 rounded-full sparkle-recovery" style="animation-delay: 3.2s;"></div>
            </div>
            <h1 class="text-4xl font-black text-white mb-2 drop-shadow-lg">🔑 استعادة كلمة المرور</h1>
            <p class="text-purple-100 text-lg">لا تقلق، سنساعدك في استعادة حسابك</p>
        </div>

        <!-- Recovery Card -->
        <div class="w-full sm:max-w-lg mt-6 px-8 py-10 glass-effect-recovery overflow-hidden sm:rounded-3xl shadow-2xl recovery-glow">
            <!-- Header -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-white mb-2">💌 إرسال رابط الاستعادة</h2>
                <p class="text-purple-100 text-sm leading-relaxed">
                    نسيت كلمة المرور؟ لا مشكلة! أدخل بريدك الإلكتروني وسنرسل لك رابط لإعادة تعيين كلمة مرور جديدة.
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl text-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6" @submit="emailSent = true">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-bold text-white mb-2">
                        📧 البريد الإلكتروني
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           class="recovery-input block w-full px-4 py-4 rounded-xl font-medium text-gray-800 placeholder-gray-500"
                           placeholder="أدخل بريدك الإلكتروني المسجل">
                    @error('email')
                        <p class="mt-2 text-sm text-purple-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="recovery-btn w-full py-4 px-6 rounded-xl text-white font-bold text-lg shadow-lg"
                        x-bind:disabled="emailSent">
                    <span x-show="!emailSent">
                        📤 إرسال رابط الاستعادة
                    </span>
                    <span x-show="emailSent" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري الإرسال...
                    </span>
                </button>
            </form>

            <!-- Information Box -->
            <div class="mt-8 p-4 bg-purple-600 bg-opacity-30 rounded-xl border border-purple-400 border-opacity-30">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-purple-200 text-lg mr-3 mt-1 breathe-recovery"></i>
                    <div>
                        <h3 class="text-white font-bold mb-1">💡 معلومة مهمة</h3>
                        <p class="text-purple-100 text-sm">
                            تحقق من صندوق البريد الوارد وربما مجلد الرسائل غير المرغوب فيها. الرابط صالح لمدة 60 دقيقة فقط.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Links -->
            <div class="mt-8 space-y-4">
                <div class="border-t border-purple-300 pt-4">
                    <div class="flex justify-between text-sm">
                        <a href="{{ route('home') }}" 
                           class="text-purple-200 hover:text-white transition-colors duration-300 flex items-center">
                            ← العودة للرئيسية
                        </a>
                        <a href="{{ route('login') }}" 
                           class="text-purple-200 hover:text-white transition-colors duration-300">
                            تذكرت كلمة المرور؟
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back to Login -->
            <div class="mt-6 p-4 bg-purple-700 bg-opacity-30 rounded-xl border border-purple-500 border-opacity-30">
                <p class="text-purple-100 text-sm text-center">
                    تذكرت كلمة المرور؟ 
                    <a href="{{ route('login') }}" 
                       class="text-white font-bold hover:underline">
                        🔐 سجل دخولك الآن
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-purple-200 text-sm">
                © {{ date('Y') }} شباك التذاكر - أمان حسابك أولويتنا
            </p>
        </div>
    </div>

    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>
