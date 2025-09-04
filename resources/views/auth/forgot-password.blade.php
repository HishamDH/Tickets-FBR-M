<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نسيت كلمة المرور - منصة التذاكر FBR-M</title>
    
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
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
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
                        <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">نسيت كلمة المرور؟</h1>
                    <p class="text-gray-600">لا تقلق، سنرسل لك رابط إعادة التعيين</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                            placeholder="ادخل بريدك الإلكتروني"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        إرسال رابط إعادة التعيين
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mt-0.5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-purple-700">
                            <p class="font-semibold">معلومة مهمة:</p>
                            <p class="mt-1">سنرسل لك رابط إعادة تعيين كلمة المرور عبر البريد الإلكتروني. تحقق من صندوق الرسائل والبريد المزعج.</p>
                        </div>
                    </div>
                </div>

                <!-- Links -->
                <div class="mt-6 text-center space-y-4">
                    <p class="text-sm text-gray-600">
                        تذكرت كلمة المرور؟
                        <a href="{{ route('home') }}" class="text-purple-600 hover:text-purple-700 font-semibold">
                            العودة لتسجيل الدخول
                        </a>
                    </p>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                            العودة للصفحة الرئيسية
                        </a>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-bold mb-4">استعادة كلمة المرور</h2>
                        <p class="text-xl text-purple-100 mb-6 leading-relaxed">
                            نساعدك في استعادة الوصول لحسابك بخطوات بسيطة وآمنة
                        </p>
                        <div class="bg-white bg-opacity-20 rounded-lg p-6 backdrop-blur-sm">
                            <h3 class="font-semibold mb-4 text-lg">خطوات بسيطة</h3>
                            <div class="grid grid-cols-1 gap-3 text-sm text-purple-100">
                                <div class="flex items-center text-right">
                                    <span class="w-6 h-6 bg-purple-400 rounded-full flex items-center justify-center text-xs font-bold ml-2">1</span>
                                    <span>أدخل بريدك الإلكتروني</span>
                                </div>
                                <div class="flex items-center text-right">
                                    <span class="w-6 h-6 bg-purple-400 rounded-full flex items-center justify-center text-xs font-bold ml-2">2</span>
                                    <span>تحقق من بريدك الإلكتروني</span>
                                </div>
                                <div class="flex items-center text-right">
                                    <span class="w-6 h-6 bg-purple-400 rounded-full flex items-center justify-center text-xs font-bold ml-2">3</span>
                                    <span>اضغط على الرابط المرسل</span>
                                </div>
                                <div class="flex items-center text-right">
                                    <span class="w-6 h-6 bg-purple-400 rounded-full flex items-center justify-center text-xs font-bold ml-2">4</span>
                                    <span>أنشئ كلمة مرور جديدة</span>
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
