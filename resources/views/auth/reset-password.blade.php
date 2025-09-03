<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إعادة تعيين كلمة المرور - شباك التذاكر</title>
    
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
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-xl font-bold text-dark">شباك التذاكر</span>
                    </div>
                    <h2 class="text-2xl font-bold text-dark">إعادة تعيين كلمة المرور</h2>
                    <p class="mt-2 text-gray">ادخل كلمة مرور جديدة لحسابك</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-dark mb-2">
                            البريد الإلكتروني
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $request->email) }}" 
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
                            كلمة المرور الجديدة
                        </label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="input-field w-full"
                            placeholder="ادخل كلمة المرور الجديدة"
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

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                        إعادة تعيين كلمة المرور
                    </button>
                </form>

                <!-- Password Requirements -->
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-yellow-800">متطلبات كلمة المرور</h3>
                            <ul class="mt-1 text-sm text-yellow-700 space-y-1">
                                <li>• يجب أن تحتوي على 8 أحرف على الأقل</li>
                                <li>• يُفضل استخدام مزيج من الأحرف والأرقام</li>
                                <li>• تجنب استخدام معلومات شخصية واضحة</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-primary hover:text-primary-hover">
                        ← العودة لتسجيل الدخول
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 h-full w-full bg-primary flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="w-32 h-32 mx-auto mb-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">كلمة مرور جديدة</h3>
                    <p class="text-xl text-primary-light max-w-md">
                        اختر كلمة مرور قوية وآمنة لحماية حسابك وبياناتك
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
        
        .reset-bg {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 25%, #991b1b 50%, #7f1d1d 75%, #450a0a 100%);
            background-size: 400% 400%;
            animation: reset-animation 13s ease-in-out infinite;
        }
        
        @keyframes reset-animation {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .floating-reset {
            animation: floating-reset 9s ease-in-out infinite;
        }
        
        @keyframes floating-reset {
            0% { transform: translateY(0px) scale(1) rotate(0deg); }
            33% { transform: translateY(-20px) scale(1.12) rotate(8deg); }
            66% { transform: translateY(12px) scale(0.88) rotate(-8deg); }
            100% { transform: translateY(0px) scale(1) rotate(0deg); }
        }
        
        .reset-glow {
            box-shadow: 0 0 50px rgba(220, 38, 38, 0.7);
        }
        
        .sparkle-reset {
            animation: sparkle-reset 3.2s ease-in-out infinite alternate;
        }
        
        @keyframes sparkle-reset {
            from { opacity: 0.5; transform: scale(0.5) rotate(0deg); }
            to { opacity: 1; transform: scale(1.5) rotate(360deg); }
        }
        
        .glass-effect-reset {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        
        .reset-input {
            background: rgba(255, 255, 255, 0.99);
            border: 2px solid rgba(220, 38, 38, 0.3);
            transition: all 0.6s ease;
        }
        
        .reset-input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #dc2626;
            box-shadow: 0 0 35px rgba(220, 38, 38, 0.6);
            transform: scale(1.04);
        }
        
        .reset-btn {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        
        .reset-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(220, 38, 38, 0.7);
        }
        
        .reset-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
            transition: left 0.9s;
        }
        
        .reset-btn:hover::before {
            left: 100%;
        }
        
        .shield-spin {
            animation: shield-spin 8s linear infinite;
        }
        
        @keyframes shield-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .pulse-strong {
            animation: pulse-strong 3s ease-in-out infinite;
        }
        
        @keyframes pulse-strong {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.3); opacity: 1; }
        }
        
        .password-strength {
            height: 4px;
            background: linear-gradient(90deg, #ef4444, #f97316, #eab308, #22c55e);
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .security-badge {
            background: linear-gradient(45deg, #dc2626, #f59e0b);
            background-size: 200% 200%;
            animation: security-gradient 4s ease infinite;
        }
        
        @keyframes security-gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="reset-bg">
    <!-- Floating Reset Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-32 left-24 w-8 h-8 bg-red-300 rounded-full opacity-60 floating-reset"></div>
        <div class="absolute top-48 right-32 w-10 h-10 bg-red-200 rounded-full opacity-50 floating-reset" style="animation-delay: 2.1s;"></div>
        <div class="absolute bottom-40 left-1/6 w-7 h-7 bg-red-400 rounded-full opacity-70 floating-reset" style="animation-delay: 2.9s;"></div>
        <div class="absolute bottom-56 right-1/5 w-9 h-9 bg-red-300 rounded-full opacity-40 floating-reset" style="animation-delay: 1.2s;"></div>
        <div class="absolute top-2/5 right-24 w-8 h-8 bg-red-500 rounded-full opacity-55 floating-reset" style="animation-delay: 2.4s;"></div>
        <div class="absolute top-1/3 left-1/4 w-6 h-6 bg-red-200 rounded-full opacity-65 floating-reset" style="animation-delay: 3.7s;"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" x-data="{ 
        showPassword: false, 
        showConfirmPassword: false,
        password: '',
        confirmPassword: '',
        passwordStrength: 0,
        
        calculateStrength() {
            let strength = 0;
            if (this.password.length >= 8) strength += 25;
            if (/[A-Z]/.test(this.password)) strength += 25;
            if (/[0-9]/.test(this.password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(this.password)) strength += 25;
            this.passwordStrength = strength;
        },
        
        getStrengthText() {
            if (this.passwordStrength < 25) return 'ضعيفة جداً';
            if (this.passwordStrength < 50) return 'ضعيفة';
            if (this.passwordStrength < 75) return 'متوسطة';
            return 'قوية';
        },
        
        getStrengthColor() {
            if (this.passwordStrength < 25) return 'bg-red-500';
            if (this.passwordStrength < 50) return 'bg-orange-500';
            if (this.passwordStrength < 75) return 'bg-yellow-500';
            return 'bg-green-500';
        }
    }">
        <!-- Logo Section -->
        <div class="mb-8 text-center">
            <div class="relative inline-block">
                <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mb-5 reset-glow floating-reset shadow-2xl">
                    <i class="fas fa-shield-alt text-5xl text-red-600 shield-spin"></i>
                </div>
                <div class="absolute -top-5 -right-5 w-12 h-12 bg-red-400 rounded-full sparkle-reset"></div>
                <div class="absolute -bottom-4 -left-4 w-9 h-9 bg-red-300 rounded-full sparkle-reset" style="animation-delay: 2s;"></div>
                <div class="absolute top-3 left-3 w-7 h-7 bg-red-200 rounded-full sparkle-reset" style="animation-delay: 3.5s;"></div>
            </div>
            <h1 class="text-4xl font-black text-white mb-2 drop-shadow-lg">🔐 إعادة تعيين كلمة المرور</h1>
            <p class="text-red-100 text-lg">أنشئ كلمة مرور جديدة وقوية</p>
        </div>

        <!-- Reset Card -->
        <div class="w-full sm:max-w-lg mt-6 px-8 py-10 glass-effect-reset overflow-hidden sm:rounded-3xl shadow-2xl reset-glow">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="security-badge inline-block px-4 py-2 rounded-full text-white font-bold text-sm mb-3">
                    🛡️ تحديث آمن
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">🔑 كلمة مرور جديدة</h2>
                <p class="text-red-100 text-sm">أنشئ كلمة مرور قوية لحماية حسابك</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-bold text-white mb-2">
                        📧 البريد الإلكتروني
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email', $request->email) }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           readonly
                           class="reset-input block w-full px-4 py-4 rounded-xl font-medium text-gray-600 bg-gray-100 cursor-not-allowed">
                    @error('email')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-white mb-2">
                        🔒 كلمة المرور الجديدة
                    </label>
                    <div class="relative">
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'"
                               name="password" 
                               x-model="password"
                               @input="calculateStrength()"
                               required 
                               autocomplete="new-password"
                               class="reset-input block w-full px-4 py-4 rounded-xl font-medium text-gray-800 placeholder-gray-500 pr-12"
                               placeholder="أدخل كلمة مرور قوية">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m6.121-3.121A9.957 9.957 0 0121 12c0 4.478-2.943 8.268-7 9.543m-.121-6.422a3 3 0 11-4.243-4.243m4.243 4.243L12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div x-show="password.length > 0" class="mt-2">
                        <div class="flex justify-between text-xs text-red-200 mb-1">
                            <span>قوة كلمة المرور:</span>
                            <span x-text="getStrengthText()"></span>
                        </div>
                        <div class="w-full bg-red-200 rounded-full h-2">
                            <div :class="getStrengthColor()" 
                                 class="h-2 rounded-full transition-all duration-300" 
                                 :style="`width: ${passwordStrength}%`"></div>
                        </div>
                    </div>
                    
                    @error('password')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-white mb-2">
                        🔐 تأكيد كلمة المرور
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" 
                               :type="showConfirmPassword ? 'text' : 'password'"
                               name="password_confirmation" 
                               x-model="confirmPassword"
                               required 
                               autocomplete="new-password"
                               class="reset-input block w-full px-4 py-4 rounded-xl font-medium text-gray-800 placeholder-gray-500 pr-12"
                               placeholder="أعد إدخال كلمة المرور">
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m6.121-3.121A9.957 9.957 0 0121 12c0 4.478-2.943 8.268-7 9.543m-.121-6.422a3 3 0 11-4.243-4.243m4.243 4.243L12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Password Match Indicator -->
                    <div x-show="confirmPassword.length > 0" class="mt-2">
                        <div x-show="password === confirmPassword && password.length > 0" class="text-green-200 text-xs flex items-center">
                            <i class="fas fa-check-circle mr-1"></i>
                            كلمات المرور متطابقة
                        </div>
                        <div x-show="password !== confirmPassword" class="text-red-200 text-xs flex items-center">
                            <i class="fas fa-times-circle mr-1"></i>
                            كلمات المرور غير متطابقة
                        </div>
                    </div>
                    
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Security Tips -->
                <div class="p-4 bg-red-600 bg-opacity-30 rounded-xl border border-red-400 border-opacity-30">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-red-200 text-lg mr-3 mt-1 pulse-strong"></i>
                        <div>
                            <h3 class="text-white font-bold mb-2">💪 نصائح لكلمة مرور قوية:</h3>
                            <ul class="text-red-100 text-xs space-y-1">
                                <li>• 8 أحرف على الأقل</li>
                                <li>• حروف كبيرة وصغيرة</li>
                                <li>• أرقام ورموز خاصة</li>
                                <li>• تجنب المعلومات الشخصية</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="password !== confirmPassword || passwordStrength < 50"
                        :class="password === confirmPassword && passwordStrength >= 50 ? 'reset-btn' : 'bg-gray-400 cursor-not-allowed'"
                        class="w-full py-4 px-6 rounded-xl text-white font-bold text-lg shadow-lg transition-all duration-300">
                    🔐 تحديث كلمة المرور
                </button>
            </form>

            <!-- Links -->
            <div class="mt-8 space-y-4">
                <div class="border-t border-red-300 pt-4">
                    <div class="flex justify-center text-sm">
                        <a href="{{ route('login') }}" 
                           class="text-red-200 hover:text-white transition-colors duration-300 flex items-center">
                            ← العودة لتسجيل الدخول
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-red-200 text-sm">
                © {{ date('Y') }} شباك التذاكر - تأمين حسابك أولويتنا
            </p>
        </div>
    </div>

    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>
