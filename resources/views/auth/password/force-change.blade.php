@extends('layouts.app')

@section('title', __('تغيير كلمة المرور'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ __('تغيير كلمة المرور مطلوب') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('كلمة المرور الخاصة بك منتهية الصلاحية أو تحتاج إلى تحديث. يرجى تعيين كلمة مرور جديدة للمتابعة.') }}
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-sm font-medium text-red-800">{{ __('يوجد أخطاء في النموذج') }}</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.force-change') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                        {{ __('كلمة المرور الحالية') }}
                    </label>
                    <div class="mt-1">
                        <input id="current_password" name="current_password" type="password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="{{ __('أدخل كلمة المرور الحالية') }}">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        {{ __('كلمة المرور الجديدة') }}
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="{{ __('أدخل كلمة المرور الجديدة') }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل، وتشمل أحرف كبيرة وصغيرة وأرقام ورموز خاصة.') }}
                    </p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        {{ __('تأكيد كلمة المرور الجديدة') }}
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                               placeholder="{{ __('أعد إدخال كلمة المرور الجديدة') }}">
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-sm font-medium text-blue-800">{{ __('متطلبات كلمة المرور') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>{{ __('8 أحرف على الأقل') }}</li>
                                <li>{{ __('حرف كبير واحد على الأقل (A-Z)') }}</li>
                                <li>{{ __('حرف صغير واحد على الأقل (a-z)') }}</li>
                                <li>{{ __('رقم واحد على الأقل (0-9)') }}</li>
                                <li>{{ __('رمز خاص واحد على الأقل (!@#$%^&*)') }}</li>
                                <li>{{ __('لا تستخدم كلمات مرور شائعة') }}</li>
                                <li>{{ __('لا تكرر كلمات المرور السابقة') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    {{ __('تحديث كلمة المرور') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    
    function validatePassword() {
        const password = passwordInput.value;
        const requirements = [
            { test: password.length >= 8, message: '8 أحرف على الأقل' },
            { test: /[A-Z]/.test(password), message: 'حرف كبير واحد على الأقل' },
            { test: /[a-z]/.test(password), message: 'حرف صغير واحد على الأقل' },
            { test: /[0-9]/.test(password), message: 'رقم واحد على الأقل' },
            { test: /[!@#$%^&*(),.?":{}|<>]/.test(password), message: 'رمز خاص واحد على الأقل' }
        ];
        
        // Visual feedback could be added here
    }
    
    function validateConfirmation() {
        const password = passwordInput.value;
        const confirmation = confirmInput.value;
        
        if (confirmation && password !== confirmation) {
            confirmInput.setCustomValidity('كلمات المرور غير متطابقة');
        } else {
            confirmInput.setCustomValidity('');
        }
    }
    
    passwordInput.addEventListener('input', validatePassword);
    confirmInput.addEventListener('input', validateConfirmation);
});
</script>
@endsection
