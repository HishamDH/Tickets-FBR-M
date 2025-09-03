<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تأكيد كلمة المرور - شباك التذاكر</title>
    
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
                    <h2 class="text-2xl font-bold text-dark">تأكيد كلمة المرور</h2>
                    <p class="mt-2 text-gray">يرجى تأكيد كلمة المرور للمتابعة</p>
                </div>

                <!-- Security Message -->
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-amber-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-amber-800">منطقة آمنة</h3>
                            <p class="mt-1 text-sm text-amber-700">
                                هذه منطقة آمنة من التطبيق. يرجى تأكيد كلمة المرور الخاصة بك قبل المتابعة.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

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
                            placeholder="ادخل كلمة المرور الحالية"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full">
                        تأكيد كلمة المرور
                    </button>
                </form>

                <!-- Cancel Action -->
                <div class="mt-6 text-center">
                    <a href="{{ url()->previous() }}" class="text-sm text-gray hover:text-dark">
                        ← إلغاء والعودة
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
                    <h3 class="text-3xl font-bold mb-4">الأمان أولاً</h3>
                    <p class="text-xl text-primary-light max-w-md">
                        نحرص على حماية بياناتك من خلال التحقق الإضافي عند الحاجة
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
