@extends('layouts.app')

@section('title', 'إعدادات الإشعارات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl mb-8 p-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                    ⚙️ إعدادات الإشعارات
                </h1>
                <p class="text-slate-600 text-lg">تحكم في طريقة وصول الإشعارات إليك</p>
            </div>
        </div>

        <form method="POST" action="{{ route('notifications.update-preferences') }}" class="space-y-8">
            @csrf
            
            <!-- General Notification Settings -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-2xl">🔔</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">الإعدادات العامة</h3>
                        <p class="text-slate-600">تحديد القنوات المفضلة لاستقبال الإشعارات</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email Notifications -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">📧</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">البريد الإلكتروني</h4>
                                    <p class="text-sm text-slate-600">استقبال الإشعارات عبر الإيميل</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['email_notifications'] ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- SMS Notifications -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">📱</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">الرسائل النصية</h4>
                                    <p class="text-sm text-slate-600">استقبال الإشعارات عبر SMS</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['sms_notifications'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Push Notifications -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">🔔</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">الإشعارات الفورية</h4>
                                    <p class="text-sm text-slate-600">إشعارات المتصفح الفورية</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="push_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['push_notifications'] ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Marketing Notifications -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">📢</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">الإشعارات التسويقية</h4>
                                    <p class="text-sm text-slate-600">العروض والحملات الترويجية</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="marketing_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['marketing_notifications'] ?? false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Types -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-2xl">📋</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">أنواع الإشعارات</h3>
                        <p class="text-slate-600">اختر الأنواع التي تريد استقبال إشعارات عنها</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Booking Notifications -->
                    <div class="bg-blue-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-2xl mr-4">📅</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">إشعارات الحجوزات</h4>
                                    <p class="text-sm text-slate-600">تأكيد الحجز، تغيير الحالة، إلغاء الحجز</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="booking_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['booking_notifications'] ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Notifications -->
                    <div class="bg-green-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-2xl mr-4">💰</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">إشعارات المدفوعات</h4>
                                    <p class="text-sm text-slate-600">تأكيد الدفع، استرداد المبالغ، فواتير الدفع</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="payment_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['payment_notifications'] ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- System Notifications -->
                    <div class="bg-purple-50 rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-2xl mr-4">⚙️</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-800">إشعارات النظام</h4>
                                    <p class="text-sm text-slate-600">تحديثات النظام، صيانة، أمان الحساب</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="system_notifications" value="1" 
                                       {{ (Auth::user()->notification_preferences['system_notifications'] ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiet Hours -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-2xl">🌙</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800">ساعات الهدوء</h3>
                        <p class="text-slate-600">تحديد الأوقات التي لا تريد استقبال إشعارات فيها</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">من الساعة</label>
                        <input type="time" name="quiet_hours_start" 
                               value="{{ Auth::user()->notification_preferences['quiet_hours_start'] ?? '22:00' }}"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">إلى الساعة</label>
                        <input type="time" name="quiet_hours_end" 
                               value="{{ Auth::user()->notification_preferences['quiet_hours_end'] ?? '08:00' }}"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="enable_quiet_hours" value="1" 
                               {{ (Auth::user()->notification_preferences['enable_quiet_hours'] ?? false) ? 'checked' : '' }}
                               class="text-blue-600 focus:ring-blue-500 rounded">
                        <span class="mr-2 text-sm text-slate-600">تفعيل ساعات الهدوء</span>
                    </label>
                </div>
            </div>

            <!-- Save Settings -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">حفظ الإعدادات</h3>
                        <p class="text-sm text-slate-600">سيتم تطبيق الإعدادات فوراً على جميع الإشعارات الجديدة</p>
                    </div>
                    <div class="flex space-x-3 space-x-reverse">
                        <a href="{{ route('notifications.index') }}" 
                           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-medium hover:bg-gray-200 transition-colors duration-200">
                            إلغاء
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                            💾 حفظ الإعدادات
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Reset to Defaults -->
        <div class="bg-red-50 border border-red-200 rounded-3xl p-6 mt-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-red-800 mb-2">إعادة تعيين الإعدادات</h3>
                    <p class="text-sm text-red-600">سيتم إعادة جميع الإعدادات إلى الحالة الافتراضية</p>
                </div>
                <button onclick="resetToDefaults()" 
                        class="bg-red-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-red-700 transition-colors duration-200">
                    🔄 إعادة تعيين
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetToDefaults() {
    if (confirm('هل أنت متأكد من إعادة تعيين جميع إعدادات الإشعارات إلى الحالة الافتراضية؟')) {
        // Reset all checkboxes to default values
        document.querySelector('input[name="email_notifications"]').checked = true;
        document.querySelector('input[name="sms_notifications"]').checked = false;
        document.querySelector('input[name="push_notifications"]').checked = true;
        document.querySelector('input[name="marketing_notifications"]').checked = false;
        document.querySelector('input[name="booking_notifications"]').checked = true;
        document.querySelector('input[name="payment_notifications"]').checked = true;
        document.querySelector('input[name="system_notifications"]').checked = true;
        document.querySelector('input[name="enable_quiet_hours"]').checked = false;
        document.querySelector('input[name="quiet_hours_start"]').value = '22:00';
        document.querySelector('input[name="quiet_hours_end"]').value = '08:00';
        
        alert('تم إعادة تعيين الإعدادات. اضغط "حفظ الإعدادات" لتطبيق التغييرات.');
    }
}

// Enable browser notifications if push notifications are enabled
document.querySelector('input[name="push_notifications"]').addEventListener('change', function() {
    if (this.checked && 'Notification' in window) {
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(function (permission) {
                if (permission !== 'granted') {
                    alert('يرجى السماح للموقع بإرسال الإشعارات الفورية من إعدادات المتصفح');
                }
            });
        }
    }
});
</script>
@endpush
@endsection
