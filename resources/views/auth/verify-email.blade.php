<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تأكيد البريد الإلكتروني - شباك التذاكر</title>
    
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
        <!-- Left Side - Content -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </div>
                        <span class="mr-3 text-xl font-bold text-dark">شباك التذاكر</span>
                    </div>
                    <h2 class="text-2xl font-bold text-dark">تأكيد البريد الإلكتروني</h2>
                    <p class="mt-2 text-gray">نحتاج للتأكد من صحة بريدك الإلكتروني</p>
                </div>

                <!-- Message -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-blue-800">تم إرسال رابط التأكيد</h3>
                            <p class="mt-1 text-sm text-blue-700">
                                شكراً لك على التسجيل! قبل البدء، يمكنك تأكيد عنوان بريدك الإلكتروني بالنقر على الرابط الذي أرسلناه إليك للتو. إذا لم تتلق البريد الإلكتروني، فسنرسل لك بكل سرور رابطاً آخر.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Session Status -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        تم إرسال رابط تأكيد جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.
                    </div>
                @endif

                <!-- Resend Verification -->
                <form method="POST" action="{{ route('verification.send') }}" class="space-y-6">
                    @csrf
                    
                    <button type="submit" class="btn btn-primary w-full">
                        إعادة إرسال رابط التأكيد
                    </button>
                </form>

                <!-- Logout Option -->
                <div class="mt-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        
                        <button type="submit" class="btn btn-ghost w-full">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>

                <!-- Help Section -->
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-yellow-800">لم تتلق البريد الإلكتروني؟</h3>
                            <ul class="mt-1 text-sm text-yellow-700 space-y-1">
                                <li>• تحقق من صندوق البريد المزعج</li>
                                <li>• تأكد من صحة عنوان البريد الإلكتروني</li>
                                <li>• انتظر بضع دقائق ثم حاول مرة أخرى</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="mt-6 text-center">
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
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    <h3 class="text-3xl font-bold mb-4">تأكيد البريد الإلكتروني</h3>
                    <p class="text-xl text-primary-light max-w-md">
                        خطوة مهمة لضمان أمان حسابك وتلقي التحديثات المهمة
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
        
        .verify-bg {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 25%, #b45309 50%, #92400e 75%, #451a03 100%);
            background-size: 400% 400%;
            animation: verify-animation 11s ease-in-out infinite;
        }
        
        @keyframes verify-animation {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .floating-verify {
            animation: floating-verify 10s ease-in-out infinite;
        }
        
        @keyframes floating-verify {
            0% { transform: translateY(0px) scale(1) rotate(0deg); }
            33% { transform: translateY(-22px) scale(1.15) rotate(10deg); }
            66% { transform: translateY(14px) scale(0.85) rotate(-10deg); }
            100% { transform: translateY(0px) scale(1) rotate(0deg); }
        }
        
        .verify-glow {
            box-shadow: 0 0 55px rgba(245, 158, 11, 0.8);
        }
        
        .sparkle-verify {
            animation: sparkle-verify 3.5s ease-in-out infinite alternate;
        }
        
        @keyframes sparkle-verify {
            from { opacity: 0.6; transform: scale(0.4) rotate(0deg); }
            to { opacity: 1; transform: scale(1.6) rotate(360deg); }
        }
        
        .glass-effect-verify {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.28);
        }
        
        .verify-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            transition: all 0.6s ease;
            position: relative;
            overflow: hidden;
        }
        
        .verify-btn:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 50px rgba(245, 158, 11, 0.8);
        }
        
        .verify-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
            transition: left 1s;
        }
        
        .verify-btn:hover::before {
            left: 100%;
        }
        
        .envelope-bounce {
            animation: envelope-bounce 6s ease-in-out infinite;
        }
        
        @keyframes envelope-bounce {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-15px) rotate(5deg); }
            50% { transform: translateY(0px) rotate(0deg); }
            75% { transform: translateY(-8px) rotate(-3deg); }
        }
        
        .email-pulse {
            animation: email-pulse 2s ease-in-out infinite;
        }
        
        @keyframes email-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
        }
        
        .verification-success {
            background: linear-gradient(45deg, #10b981, #059669, #047857);
            background-size: 300% 300%;
            animation: success-gradient 3s ease infinite;
        }
        
        @keyframes success-gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .mail-fly {
            animation: mail-fly 4s ease-in-out infinite;
        }
        
        @keyframes mail-fly {
            0% { transform: translateX(0px) translateY(0px); }
            25% { transform: translateX(20px) translateY(-10px); }
            50% { transform: translateX(0px) translateY(-20px); }
            75% { transform: translateX(-20px) translateY(-10px); }
            100% { transform: translateX(0px) translateY(0px); }
        }
    </style>
</head>
<body class="verify-bg">
    <!-- Floating Verification Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-36 left-28 w-9 h-9 bg-amber-300 rounded-full opacity-60 floating-verify"></div>
        <div class="absolute top-52 right-36 w-11 h-11 bg-amber-200 rounded-full opacity-50 floating-verify" style="animation-delay: 2.3s;"></div>
        <div class="absolute bottom-44 left-1/6 w-8 h-8 bg-amber-400 rounded-full opacity-70 floating-verify" style="animation-delay: 3.1s;"></div>
        <div class="absolute bottom-60 right-1/5 w-10 h-10 bg-amber-300 rounded-full opacity-40 floating-verify" style="animation-delay: 1.4s;"></div>
        <div class="absolute top-2/5 right-28 w-9 h-9 bg-amber-500 rounded-full opacity-55 floating-verify" style="animation-delay: 2.7s;"></div>
        <div class="absolute top-1/3 left-1/4 w-7 h-7 bg-amber-200 rounded-full opacity-65 floating-verify" style="animation-delay: 4s;"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" x-data="{ emailSent: false, countdown: 0, 
        startCountdown() {
            this.countdown = 60;
            this.emailSent = true;
            const timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(timer);
                    this.emailSent = false;
                }
            }, 1000);
        }
    }">
        <!-- Logo Section -->
        <div class="mb-8 text-center">
            <div class="relative inline-block">
                <div class="w-30 h-30 bg-white rounded-full flex items-center justify-center mb-5 verify-glow floating-verify shadow-2xl">
                    <i class="fas fa-envelope text-6xl text-amber-600 envelope-bounce"></i>
                </div>
                <div class="absolute -top-6 -right-6 w-14 h-14 bg-amber-400 rounded-full sparkle-verify"></div>
                <div class="absolute -bottom-5 -left-5 w-10 h-10 bg-amber-300 rounded-full sparkle-verify" style="animation-delay: 2.2s;"></div>
                <div class="absolute top-4 left-4 w-8 h-8 bg-amber-200 rounded-full sparkle-verify" style="animation-delay: 3.8s;"></div>
                <div class="absolute -top-2 left-8 w-6 h-6 bg-amber-500 rounded-full sparkle-verify mail-fly" style="animation-delay: 1.5s;"></div>
            </div>
            <h1 class="text-4xl font-black text-white mb-2 drop-shadow-lg">📧 تأكيد البريد الإلكتروني</h1>
            <p class="text-amber-100 text-lg">تحقق من بريدك لتفعيل حسابك</p>
        </div>

        <!-- Verification Card -->
        <div class="w-full sm:max-w-xl mt-6 px-8 py-10 glass-effect-verify overflow-hidden sm:rounded-3xl shadow-2xl verify-glow">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-block p-3 bg-amber-600 bg-opacity-30 rounded-full mb-4">
                    <i class="fas fa-paper-plane text-3xl text-amber-100 email-pulse"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">🎯 تحقق من بريدك الإلكتروني</h2>
                <p class="text-amber-100 text-sm leading-relaxed">
                    شكراً لتسجيلك معنا! لقد أرسلنا رابط تأكيد إلى بريدك الإلكتروني. يرجى النقر على الرابط لتفعيل حسابك والبدء في استخدام خدماتنا.
                </p>
            </div>

            <!-- Success Status -->
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 verification-success rounded-xl text-center shadow-lg">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-check-circle text-white text-2xl mr-2"></i>
                        <span class="text-white font-bold">تم الإرسال بنجاح!</span>
                    </div>
                    <p class="text-green-100 text-sm">
                        تم إرسال رابط تأكيد جديد إلى بريدك الإلكتروني المسجل.
                    </p>
                </div>
            @endif

            <!-- Email Instructions -->
            <div class="mb-8 p-6 bg-amber-600 bg-opacity-30 rounded-xl border border-amber-400 border-opacity-30">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-amber-200 text-2xl mr-4 mt-1 email-pulse"></i>
                    <div>
                        <h3 class="text-white font-bold mb-3">📋 تعليمات مهمة:</h3>
                        <ul class="text-amber-100 text-sm space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-inbox text-amber-300 mr-2"></i>
                                تحقق من صندوق الوارد الخاص بك
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-folder text-amber-300 mr-2"></i>
                                ابحث في مجلد الرسائل غير المرغوب فيها
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-clock text-amber-300 mr-2"></i>
                                الرابط صالح لمدة 24 ساعة
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-shield-alt text-amber-300 mr-2"></i>
                                لا تشارك الرابط مع أحد
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <!-- Resend Email Button -->
                <form method="POST" action="{{ route('verification.send') }}" @submit="startCountdown()">
                    @csrf
                    <button type="submit" 
                            :disabled="emailSent"
                            :class="emailSent ? 'bg-gray-400 cursor-not-allowed' : 'verify-btn'"
                            class="w-full py-4 px-6 rounded-xl text-white font-bold text-lg shadow-lg transition-all duration-300">
                        <span x-show="!emailSent">
                            📤 إعادة إرسال رابط التأكيد
                        </span>
                        <span x-show="emailSent" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            إعادة الإرسال بعد <span x-text="countdown"></span> ثانية
                        </span>
                    </button>
                </form>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full py-3 px-6 bg-amber-700 bg-opacity-50 hover:bg-opacity-70 text-white font-medium rounded-xl transition-all duration-300 border border-amber-500 border-opacity-30">
                        🚪 تسجيل الخروج
                    </button>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 p-4 bg-amber-700 bg-opacity-30 rounded-xl border border-amber-500 border-opacity-30">
                <div class="text-center">
                    <i class="fas fa-question-circle text-amber-200 text-lg mb-2"></i>
                    <h3 class="text-white font-bold mb-2">🤝 تحتاج مساعدة؟</h3>
                    <p class="text-amber-100 text-sm mb-3">
                        إذا لم تستلم البريد الإلكتروني أو واجهت أي مشكلة، يمكنك التواصل معنا.
                    </p>
                    <a href="mailto:support@tickets-fbr.com" 
                       class="text-white font-bold hover:underline text-sm">
                        📞 تواصل مع الدعم الفني
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="mt-6 space-y-3">
                <div class="flex items-center text-amber-100 text-xs">
                    <i class="fas fa-tip text-amber-300 mr-2"></i>
                    <span>تأكد من أن بريدك الإلكتروني صحيح</span>
                </div>
                <div class="flex items-center text-amber-100 text-xs">
                    <i class="fas fa-wifi text-amber-300 mr-2"></i>
                    <span>تحقق من اتصالك بالإنترنت</span>
                </div>
                <div class="flex items-center text-amber-100 text-xs">
                    <i class="fas fa-refresh text-amber-300 mr-2"></i>
                    <span>أعد تحميل صفحة البريد الإلكتروني</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-amber-200 text-sm">
                © {{ date('Y') }} شباك التذاكر - رحلتك تبدأ بخطوة واحدة
            </p>
        </div>
    </div>

    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
</body>
</html>
