<div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-primary to-orange-500 px-6 py-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">إتمام عملية الدفع</h1>
                <p class="text-primary-100 mt-1">{{ $booking->service->name }}</p>
            </div>
            <div class="text-left">
                <p class="text-lg font-semibold">{{ number_format($totalAmount, 2) }} ريال</p>
                <p class="text-primary-100 text-sm">المبلغ الإجمالي</p>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="px-6 py-4 bg-slate-50 border-b">
        <div class="flex items-center justify-center space-x-4 space-x-reverse">
            <!-- Step 1 -->
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold
                    {{ $step >= 1 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-600' }}">
                    1
                </div>
                <span class="mr-2 text-sm {{ $step >= 1 ? 'text-primary font-semibold' : 'text-slate-600' }}">
                    اختيار طريقة الدفع
                </span>
            </div>
            
            <div class="w-12 h-0.5 {{ $step > 1 ? 'bg-primary' : 'bg-slate-300' }}"></div>
            
            <!-- Step 2 -->
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold
                    {{ $step >= 2 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-600' }}">
                    2
                </div>
                <span class="mr-2 text-sm {{ $step >= 2 ? 'text-primary font-semibold' : 'text-slate-600' }}">
                    إدخال البيانات
                </span>
            </div>
            
            <div class="w-12 h-0.5 {{ $step > 2 ? 'bg-primary' : 'bg-slate-300' }}"></div>
            
            <!-- Step 3 -->
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold
                    {{ $step >= 3 ? 'bg-primary text-white' : 'bg-slate-200 text-slate-600' }}">
                    3
                </div>
                <span class="mr-2 text-sm {{ $step >= 3 ? 'text-primary font-semibold' : 'text-slate-600' }}">
                    تأكيد الدفع
                </span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @if ($step == 1)
            <!-- Payment Gateway Selection -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold text-slate-800 mb-6">اختر طريقة الدفع المناسبة</h2>
                
                @if (empty($availableGateways))
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.502 0L4.312 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">لا توجد طرق دفع متاحة</h3>
                        <p class="text-slate-600">يرجى التواصل مع التاجر لتفعيل طرق الدفع</p>
                    </div>
                @else
                    <div class="grid gap-4">
                        @foreach ($availableGateways as $gateway)
                            <div wire:click="selectGateway({{ $gateway['id'] }})" 
                                 class="relative p-4 border-2 border-slate-200 rounded-xl cursor-pointer transition-all hover:border-primary hover:bg-primary/5
                                        {{ $selectedGateway == $gateway['id'] ? 'border-primary bg-primary/10' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 space-x-reverse">
                                        @if ($gateway['icon'])
                                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                                <img src="{{ asset('images/payment-icons/' . $gateway['icon']) }}" 
                                                     alt="{{ $gateway['name'] }}" 
                                                     class="w-8 h-8 object-contain"
                                                     onerror="this.src='{{ asset('images/payment-icons/default.svg') }}'">
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold text-slate-800">{{ $gateway['name'] }}</h3>
                                            <p class="text-sm text-slate-600">
                                                رسوم الخدمة: {{ number_format($gateway['fee'], 2) }} ريال
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        @if ($gateway['supports_refund'])
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                                قابل للاسترداد
                                            </span>
                                        @endif
                                        
                                        @if (!$gateway['is_configured'])
                                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">
                                                غير مفعل
                                            </span>
                                        @endif
                                        
                                        <div class="w-5 h-5 border-2 border-slate-300 rounded-full flex items-center justify-center
                                                    {{ $selectedGateway == $gateway['id'] ? 'border-primary bg-primary' : '' }}">
                                            @if ($selectedGateway == $gateway['id'])
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                @error('selectedGateway')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

        @elseif ($step == 2)
            <!-- Payment Details Form -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">إدخال بيانات الدفع</h2>
                    <button wire:click="goBack" class="text-primary hover:text-primary-dark">
                        ← العودة
                    </button>
                </div>

                @php
                    $selectedGatewayData = collect($availableGateways)->firstWhere('id', $selectedGateway);
                @endphp

                @if ($selectedGatewayData)
                    <!-- Selected Gateway Info -->
                    <div class="bg-primary/10 border border-primary/20 rounded-lg p-4 mb-6">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            @if ($selectedGatewayData['icon'])
                                <img src="{{ asset('images/payment-icons/' . $selectedGatewayData['icon']) }}" 
                                     alt="{{ $selectedGatewayData['name'] }}" 
                                     class="w-8 h-8 object-contain">
                            @endif
                            <div>
                                <h3 class="font-semibold text-slate-800">{{ $selectedGatewayData['name'] }}</h3>
                                <p class="text-sm text-slate-600">رسوم الخدمة: {{ number_format($selectedGatewayData['fee'], 2) }} ريال</p>
                            </div>
                        </div>
                    </div>

                    @if (in_array($selectedGatewayData['code'], ['visa', 'mastercard']))
                        <!-- Card Payment Form -->
                        <form wire:submit.prevent="processPayment" class="space-y-6">
                            <div>
                                <label for="cardNumber" class="block text-sm font-medium text-slate-700 mb-2">رقم البطاقة</label>
                                <input type="text" 
                                       id="cardNumber" 
                                       wire:model="cardNumber" 
                                       placeholder="1234 5678 9012 3456"
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                @error('cardNumber') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiryDate" class="block text-sm font-medium text-slate-700 mb-2">تاريخ الانتهاء</label>
                                    <input type="text" 
                                           id="expiryDate" 
                                           wire:model="expiryDate" 
                                           placeholder="MM/YY"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('expiryDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="cvv" class="block text-sm font-medium text-slate-700 mb-2">رمز الأمان</label>
                                    <input type="text" 
                                           id="cvv" 
                                           wire:model="cvv" 
                                           placeholder="123"
                                           maxlength="4"
                                           class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('cvv') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="cardHolder" class="block text-sm font-medium text-slate-700 mb-2">اسم حامل البطاقة</label>
                                <input type="text" 
                                       id="cardHolder" 
                                       wire:model="cardHolder" 
                                       placeholder="Ahmed Mohammed"
                                       class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                @error('cardHolder') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="saveCard" 
                                       wire:model="saveCard"
                                       class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                                <label for="saveCard" class="mr-2 text-sm text-slate-700">حفظ البطاقة للمدفوعات المستقبلية</label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="w-full bg-primary text-white py-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors disabled:opacity-50">
                                <span wire:loading.remove>دفع {{ number_format($totalAmount, 2) }} ريال</span>
                                <span wire:loading>جاري المعالجة...</span>
                            </button>
                        </form>

                    @elseif ($selectedGatewayData['code'] == 'apple_pay')
                        <!-- Apple Pay -->
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-600" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-2">دفع عبر Apple Pay</h3>
                            <p class="text-slate-600 mb-6">استخدم جهازك لإتمام الدفع بأمان</p>
                            
                            <button wire:click="processPayment"
                                    wire:loading.attr="disabled"
                                    class="bg-black text-white px-8 py-4 rounded-lg font-semibold hover:bg-gray-800 transition-colors disabled:opacity-50">
                                <span wire:loading.remove>دفع بـ Apple Pay</span>
                                <span wire:loading>جاري المعالجة...</span>
                            </button>
                        </div>

                    @else
                        <!-- Other Payment Methods -->
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ $selectedGatewayData['name'] }}</h3>
                            <p class="text-slate-600 mb-6">سيتم توجيهك لإتمام عملية الدفع</p>
                            
                            <button wire:click="processPayment"
                                    wire:loading.attr="disabled"
                                    class="bg-primary text-white px-8 py-4 rounded-lg font-semibold hover:bg-primary-dark transition-colors disabled:opacity-50">
                                <span wire:loading.remove>متابعة الدفع</span>
                                <span wire:loading>جاري المعالجة...</span>
                            </button>
                        </div>
                    @endif
                @endif

                @error('payment')
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700">{{ $message }}</p>
                    </div>
                @enderror
            </div>

        @elseif ($step == 3)
            <!-- Processing/Result -->
            <div class="text-center py-12">
                @if ($isProcessing)
                    <div class="animate-spin w-16 h-16 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-2">جاري معالجة الدفع</h3>
                    <p class="text-slate-600">يرجى الانتظار...</p>
                @elseif ($paymentResult)
                    @if ($paymentResult['success'])
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">تم الدفع بنجاح!</h3>
                        <p class="text-slate-600">{{ $paymentResult['message'] }}</p>
                    @else
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">فشل في الدفع</h3>
                        <p class="text-slate-600 mb-4">{{ $paymentResult['message'] }}</p>
                        <button wire:click="goBack" class="bg-primary text-white px-6 py-2 rounded-lg">المحاولة مرة أخرى</button>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- Summary Sidebar -->
    <div class="bg-slate-50 border-t p-6">
        <h3 class="font-semibold text-slate-800 mb-4">ملخص الطلب</h3>
        
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-600">{{ $booking->service->name }}</span>
                <span class="font-medium">{{ number_format($booking->total_amount, 2) }} ريال</span>
            </div>
            
            @if ($selectedGateway)
                @php
                    $selectedGatewayData = collect($availableGateways)->firstWhere('id', $selectedGateway);
                @endphp
                @if ($selectedGatewayData)
                    <div class="flex justify-between">
                        <span class="text-slate-600">رسوم {{ $selectedGatewayData['name'] }}</span>
                        <span class="font-medium">{{ number_format($selectedGatewayData['fee'], 2) }} ريال</span>
                    </div>
                @endif
                
                <div class="flex justify-between">
                    <span class="text-slate-600">رسوم المنصة</span>
                    <span class="font-medium">{{ number_format($booking->total_amount * (config('payment.platform_fee_rate', 1.0) / 100), 2) }} ريال</span>
                </div>
            @endif
            
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between font-semibold text-lg">
                    <span>المجموع</span>
                    <span class="text-primary">{{ number_format($totalAmount, 2) }} ريال</span>
                </div>
            </div>
        </div>
    </div>
</div>
