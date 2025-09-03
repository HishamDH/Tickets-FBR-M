<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>حالة الحساب - شباك التذاكر</title>
    
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
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                    </div>
                    <span class="mr-3 text-2xl font-bold text-dark">شباك التذاكر</span>
                </div>
                <h2 class="text-3xl font-bold text-dark">حالة حساب الشريك</h2>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                @if(auth()->user()->partner && auth()->user()->partner->status === 'active')
                    <div class="text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">حسابك نشط</h3>
                        <p class="text-gray-600 mb-6">
                            مرحباً بك في منصة الشركاء! حسابك نشط ويمكنك الوصول لجميع الميزات.
                        </p>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <h4 class="font-medium text-green-800 mb-2">معلومات الشراكة:</h4>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• <strong>كود الشريك:</strong> {{ auth()->user()->partner->partner_code }}</li>
                                <li>• <strong>نسبة العمولة:</strong> {{ auth()->user()->partner->commission_rate }}%</li>
                                <li>• <strong>عدد التجار:</strong> {{ auth()->user()->partner->merchants()->count() }}</li>
                            </ul>
                        </div>
                        <a href="{{ route('partner.dashboard') }}" class="btn-primary inline-block">
                            الانتقال للوحة التحكم
                        </a>
                    </div>

                @elseif(auth()->user()->partner && auth()->user()->partner->status === 'inactive')
                    <div class="text-center">
                        <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">حسابك غير نشط</h3>
                        <p class="text-gray-600 mb-4">
                            حسابك غير نشط حالياً، يرجى التواصل مع الإدارة لتفعيله.
                        </p>
                    </div>

                @elseif(auth()->user()->partner && auth()->user()->partner->status === 'suspended')
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">تم تعليق الحساب</h3>
                        <p class="text-gray-600 mb-4">
                            تم تعليق حسابك مؤقتاً، يرجى التواصل مع الإدارة.
                        </p>
                    </div>

                @else
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">ملف الشريك غير مكتمل</h3>
                        <p class="text-gray-600 mb-4">
                            يرجى إكمال إعداد ملف الشريك أولاً.
                        </p>
                        <a href="{{ route('partner.setup') }}" class="btn-primary inline-block">
                            إكمال الإعداد
                        </a>
                    </div>
                @endif

                <!-- Contact Support -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">هل تحتاج مساعدة؟</p>
                        <div class="space-y-2">
                            <a href="mailto:partners@tickets-fbr.com" class="text-primary hover:text-primary-dark text-sm">
                                partners@tickets-fbr.com
                            </a>
                            <br>
                            <a href="tel:+966500000000" class="text-primary hover:text-primary-dark text-sm">
                                +966 50 000 0000
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center space-y-4">
                <a href="{{ route('partner.logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="text-gray-600 hover:text-gray-900 text-sm">
                    تسجيل الخروج
                </a>
                <form id="logout-form" action="{{ route('partner.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                
                <div>
                    <a href="{{ route('home') }}" class="text-primary hover:text-primary-dark text-sm">
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
