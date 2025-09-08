<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دخول التجار - نافذة التذاكر | منصة إدارة التذاكر والحجوزات</title>
    <meta name="description" content="سجل الدخول لحسابك التجاري في نافذة التذاكر وابدأ في إدارة خدماتك وحجوزاتك بسهولة">

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
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
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
        
        .float-animation {
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
        
        /* Enhanced Form Styles */
        .form-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
        }
        
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
        
        /* Side panel decorations */
        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .decoration-1 {
            width: 120px;
            height: 120px;
            top: 20%;
            right: 10%;
            animation: float 4s ease-in-out infinite;
        }
        
        .decoration-2 {
            width: 80px;
            height: 80px;
            top: 60%;
            right: 20%;
            animation: float 5s ease-in-out infinite reverse;
        }
        
        .decoration-3 {
            width: 60px;
            height: 60px;
            top: 80%;
            right: 15%;
            animation: float 6s ease-in-out infinite;
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
            </div>
            
            <div class="mx-auto w-full max-w-md relative z-10 slide-in-left">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-16 h-16 blue-gradient rounded-2xl flex items-center justify-center shadow-xl pulse-glow">
                            <span class="text-2xl">🏪</span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-3">
                            مرحباً بعودتك! 👋
                        </h1>
                        <p class="text-lg text-gray-600 mb-2">سجل الدخول لحسابك التجاري</p>
                        <p class="text-sm text-blue-600 font-medium">✨ إدارة خدماتك وحجوزاتك بسهولة</p>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="flex justify-center gap-4 mb-6 text-xs">
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-green-500">✓</span>
                            <span class="text-gray-700">آمن 100%</span>
                        </div>
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-blue-500">🛡️</span>
                            <span class="text-gray-700">محمي</span>
                        </div>
                        <div class="flex items-center gap-1 trust-badge px-3 py-2 rounded-full">
                            <span class="text-blue-500">⚡</span>
                            <span class="text-gray-700">سريع</span>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Form -->
                <form method="POST" action="{{ route('merchant.login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-3">
                            📧 البريد الإلكتروني
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="username"
                                class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                placeholder="merchant@example.com"
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

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-3">
                            🔐 كلمة المرور
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="form-input w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all text-lg"
                                placeholder="••••••••"
                            >
                            <div class="absolute top-4 left-4">
                                <span class="text-gray-400 text-lg">🔐</span>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" 
                                   class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg">
                            <label for="remember_me" class="mr-3 block text-sm text-gray-900 font-medium">
                                تذكرني
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" 
                               class="font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                نسيت كلمة المرور؟ 🤔
                            </a>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="btn-primary w-full text-white font-bold py-4 px-6 rounded-xl text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        🚀 دخول لوحة التحكم
                    </button>
                </form>

                <!-- Features Preview -->
                <div class="mt-8 p-4 blue-gradient-soft rounded-xl">
                    <h3 class="text-sm font-bold text-blue-900 mb-3 text-center">✨ ما ستحصل عليه:</h3>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2">📊</span> تحليلات مفصلة
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2">💰</span> إدارة المدفوعات
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2">📱</span> تطبيق جوال
                        </div>
                        <div class="flex items-center text-blue-800">
                            <span class="ml-2">🎯</span> تسويق ذكي
                        </div>
                    </div>
                </div>

                <!-- Links -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        ليس لديك حساب تجاري؟
                        <a href="{{ route('merchant.register') }}" 
                           class="text-blue-600 hover:text-blue-800 font-bold transition-colors">
                            🎯 أنشئ حساباً جديداً مجاناً
                        </a>
                    </p>
                    
                    <div class="mt-4 flex justify-center gap-4 text-xs">
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-blue-600 transition-colors">
                            🏠 الصفحة الرئيسية
                        </a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('customer.login') }}" class="text-gray-500 hover:text-blue-600 transition-colors">
                            👤 دخول العملاء
                        </a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('partner.login') }}" class="text-gray-500 hover:text-blue-600 transition-colors">
                            🤝 دخول الشركاء
                        </a>
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
            
            <!-- Content Overlay -->
            <div class="relative h-full flex flex-col justify-center items-center text-white p-12 slide-in-right">
                <div class="text-center">
                    <!-- Main Illustration -->
                    <div class="mb-8 float-animation">
                        <div class="w-32 h-32 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm shadow-xl">
                            <span class="text-6xl">🏪</span>
                        </div>
                    </div>
                    
                    <!-- Title and Description -->
                    <h2 class="text-4xl font-bold mb-4 leading-tight">
                        نافذة التذاكر
                        <br>
                        <span class="text-blue-200">للتجار</span>
                    </h2>
                    
                    <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                        منصة شاملة لإدارة أعمالك التجارية
                        <br>
                        <strong>بذكاء وسهولة</strong>
                    </p>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 mb-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white">500+</div>
                            <div class="text-sm text-blue-200">تاجر نشط</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white">50K+</div>
                            <div class="text-sm text-blue-200">حجز ناجح</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-white">99%</div>
                            <div class="text-sm text-blue-200">رضا العملاء</div>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="space-y-4 text-right">
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100">لوحة تحكم متقدمة مع تحليلات فورية</span>
                            <span class="text-2xl mr-3">📊</span>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100">نظام دفع آمن ومتعدد الخيارات</span>
                            <span class="text-2xl mr-3">💳</span>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100">دعم فني 24/7 باللغة العربية</span>
                            <span class="text-2xl mr-3">🎧</span>
                        </div>
                        <div class="flex items-center justify-end">
                            <span class="text-blue-100">تطبيق جوال مجاني للإدارة السريعة</span>
                            <span class="text-2xl mr-3">📱</span>
                        </div>
                    </div>
                    
                    <!-- CTA -->
                    <div class="mt-8 p-4 bg-white/10 rounded-xl backdrop-blur-sm">
                        <p class="text-sm text-blue-100">
                            ✨ <strong>جديد!</strong> أدوات الذكاء الاصطناعي لزيادة مبيعاتك
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Animation -->
    <script>
        // Add smooth loading animation
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission loading
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '🔄 جاري تسجيل الدخول...';
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75');
            });
            
            // Enhanced input interactions
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('scale-102');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('scale-102');
                });
            });
        });
    </script>
</body>
</html>