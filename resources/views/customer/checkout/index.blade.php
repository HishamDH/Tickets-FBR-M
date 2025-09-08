@extends('layouts.app')

@section('title', 'إكمال الطلب')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">✅ إكمال الطلب</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">أكد تفاصيل حجزك واختر طريقة الدفع</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <form action="{{ route('customer.checkout.store') }}" method="POST" id="checkoutForm">
            @csrf
            
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Customer Details & Booking Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">معلومات العميل</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل *</label>
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       value="{{ old('customer_name', $user->name) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('customer_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني *</label>
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       value="{{ old('customer_email', $user->email) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('customer_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف *</label>
                                <input type="tel" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       value="{{ old('customer_phone', $user->phone) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('customer_phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                                <textarea id="customer_address" 
                                          name="customer_address" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          required>{{ old('customer_address', $user->address) }}</textarea>
                                @error('customer_address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">تفاصيل الحجز</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="booking_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الحجز المفضل *</label>
                                <input type="date" 
                                       id="booking_date" 
                                       name="booking_date" 
                                       value="{{ old('booking_date') }}" 
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('booking_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="booking_time" class="block text-sm font-medium text-gray-700 mb-2">الوقت المفضل *</label>
                                <select id="booking_time" 
                                        name="booking_time" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                    <option value="">اختر الوقت</option>
                                    <option value="morning" {{ old('booking_time') == 'morning' ? 'selected' : '' }}>صباحاً (8:00 - 12:00)</option>
                                    <option value="afternoon" {{ old('booking_time') == 'afternoon' ? 'selected' : '' }}>بعد الظهر (12:00 - 16:00)</option>
                                    <option value="evening" {{ old('booking_time') == 'evening' ? 'selected' : '' }}>مساءً (16:00 - 20:00)</option>
                                    <option value="night" {{ old('booking_time') == 'night' ? 'selected' : '' }}>ليلاً (20:00 - 24:00)</option>
                                    <option value="flexible" {{ old('booking_time') == 'flexible' ? 'selected' : '' }}>مرن (حسب توفر مقدم الخدمة)</option>
                                </select>
                                @error('booking_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="أي تفاصيل أو طلبات خاصة..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">طريقة الدفع</h2>
                        
                        <div class="space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition duration-200">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="pay_at_location" 
                                           class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300"
                                           {{ old('payment_method', 'pay_at_location') == 'pay_at_location' ? 'checked' : '' }}>
                                    <div class="mr-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">💰</span>
                                            <span class="font-semibold text-gray-800">الدفع في الموقع</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">ادفع مباشرة لمقدم الخدمة في الموقع</p>
                                    </div>
                                </label>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition duration-200">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="pay_when_visit" 
                                           class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300"
                                           {{ old('payment_method') == 'pay_when_visit' ? 'checked' : '' }}>
                                    <div class="mr-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">📍</span>
                                            <span class="font-semibold text-gray-800">الدفع عند الزيارة</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">ادفع عندما تصل إلى مقدم الخدمة</p>
                                    </div>
                                </label>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition duration-200">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="bank_transfer" 
                                           class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300"
                                           {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                    <div class="mr-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">🏦</span>
                                            <span class="font-semibold text-gray-800">تحويل بنكي</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">حول المبلغ مباشرة إلى حساب مقدم الخدمة</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="terms_accepted" 
                                   name="terms_accepted" 
                                   value="1"
                                   class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('terms_accepted') ? 'checked' : '' }}
                                   required>
                            <label for="terms_accepted" class="mr-3 text-sm text-gray-700">
                                أوافق على <a href="#" class="text-blue-600 hover:underline">الشروط والأحكام</a> و<a href="#" class="text-blue-600 hover:underline">سياسة الخصوصية</a>
                            </label>
                        </div>
                        @error('terms_accepted')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-4">ملخص الطلب</h3>
                        
                        <!-- Cart Items -->
                        <div class="space-y-4 mb-6">
                            @foreach($cartData['items'] as $cartItem)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 text-sm">{{ $cartItem->item->name }}</h4>
                                        <p class="text-xs text-gray-600">{{ $cartItem->item->merchant->business_name ?? $cartItem->item->merchant->name }}</p>
                                        <p class="text-xs text-gray-500">الكمية: {{ $cartItem->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-gray-800">{{ number_format($cartItem->price * $cartItem->quantity, 0) }} ريال</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Totals -->
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>المجموع الفرعي:</span>
                                <span>{{ number_format($cartData['subtotal'], 0) }} ريال</span>
                            </div>
                            @if($cartData['discount'] > 0)
                            <div class="flex justify-between text-green-600">
                                <span>الخصم:</span>
                                <span>-{{ number_format($cartData['discount'], 0) }} ريال</span>
                            </div>
                            @endif
                            <div class="border-t pt-3">
                                <div class="flex justify-between text-xl font-bold text-gray-800">
                                    <span>الإجمالي:</span>
                                    <span>{{ number_format($cartData['total'], 0) }} ريال</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                            <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            تأكيد الطلب
                        </button>

                        <div class="mt-4 text-center">
                            <a href="{{ route('customer.cart.index') }}" 
                               class="text-gray-600 hover:text-gray-800 text-sm flex items-center justify-center">
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                العودة إلى العربة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            جاري معالجة الطلب...
        `;
    });
});
</script>

@endsection