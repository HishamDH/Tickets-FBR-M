<div class="max-w-4xl mx-auto" x-data="{ 
    currentStep: @entangle('step'),
    maxSteps: {{ $maxSteps }},
    showSuccessAnimation: false
}" 
x-init="
    $watch('currentStep', value => {
        if (value === {{ $maxSteps }}) {
            setTimeout(() => showSuccessAnimation = true, 500);
        }
    })
">
    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @for($i = 1; $i <= $maxSteps; $i++)
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                         :class="currentStep >= {{ $i }} ? 'bg-orange-500 border-orange-500 text-white' : 'border-gray-300 text-gray-400'">
                        <span class="font-bold">{{ $i }}</span>
                    </div>
                    @if($i < $maxSteps)
                        <div class="w-16 h-1 mx-2 rounded transition-all duration-300"
                             :class="currentStep > {{ $i }} ? 'bg-orange-500' : 'bg-gray-200'"></div>
                    @endif
                </div>
            @endfor
        </div>
        
        <div class="mt-4 text-center">
            <div x-show="currentStep === 1" class="text-lg font-semibold text-gray-800">🗓️ تفاصيل الحجز</div>
            <div x-show="currentStep === 2" class="text-lg font-semibold text-gray-800">👤 بياناتك الشخصية</div>
            <div x-show="currentStep === 3" class="text-lg font-semibold text-gray-800">💳 اختيار وسيلة الدفع</div>
            <div x-show="currentStep === 4" class="text-lg font-semibold text-gray-800">✅ تأكيد الحجز</div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-red-800">يرجى تصحيح الأخطاء التالية:</span>
            </div>
            <ul class="list-disc list-inside text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Content -->
    <div class="glass-effect rounded-2xl p-8 shadow-lg">
        
        <!-- Step 1: Service Details -->
        <div x-show="currentStep === 1" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-8"
             x-transition:enter-end="opacity-1 transform translate-x-0">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold gradient-text mb-4">{{ $service->name }}</h2>
                <p class="text-gray-600 leading-relaxed">{{ $service->description }}</p>
                <div class="mt-4 flex items-center justify-center space-x-4 space-x-reverse">
                    <span class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        📍 {{ $service->location }}
                    </span>
                    <span class="text-2xl font-bold gradient-text">{{ number_format((float) $service->price) }} ريال</span>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Date Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">🗓️ تاريخ الحجز</label>
                    <input type="date" 
                           wire:model="selectedDate"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all @error('selectedDate') border-red-500 @enderror">
                    @error('selectedDate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Time Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">🕐 وقت الحجز</label>
                    <select wire:model="selectedTime" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all @error('selectedTime') border-red-500 @enderror">
                        <option value="">اختر الوقت</option>
                        @foreach($this->availableTimes as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                        @endforeach
                    </select>
                    @error('selectedTime')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Guest Count -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">👥 عدد الضيوف</label>
                    <div class="flex items-center">
                        <button type="button" 
                                wire:click="$set('guestCount', {{ 'max(1, $guestCount - 1)' }})"
                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-l-xl flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" 
                               wire:model="guestCount"
                               min="1" max="1000"
                               class="flex-1 px-4 py-3 border-2 border-gray-200 text-center focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all @error('guestCount') border-red-500 @enderror">
                        <button type="button" 
                                wire:click="$set('guestCount', {{ '$guestCount + 1' }})"
                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-r-xl flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </div>
                    @error('guestCount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Special Requests -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">📝 طلبات خاصة (اختياري)</label>
                    <textarea wire:model="specialRequests" 
                              placeholder="أي طلبات أو ملاحظات خاصة..."
                              rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all resize-none"></textarea>
                </div>
            </div>

            <!-- Price Preview -->
            <div class="mt-8 bg-orange-50 rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <span class="text-lg text-gray-700">السعر الأساسي ({{ $guestCount }} ضيف)</span>
                    <span class="text-lg font-semibold">{{ number_format((float) ($service->price * $guestCount)) }} ريال</span>
                </div>
                @if($guestCount > 100)
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600">رسوم الفعاليات الكبيرة</span>
                        <span class="font-semibold">500 ريال</span>
                    </div>
                @endif
                <hr class="my-4 border-orange-200">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-800">المجموع</span>
                    <span class="text-2xl font-bold gradient-text">{{ number_format((float) $totalAmount) }} ريال</span>
                </div>
            </div>
        </div>

        <!-- Step 2: Customer Information -->
        <div x-show="currentStep === 2" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-8"
             x-transition:enter-end="opacity-1 transform translate-x-0">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold gradient-text mb-4">بياناتك الشخصية</h2>
                <p class="text-gray-600">نحتاج بعض المعلومات لإتمام الحجز</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Customer Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">👤 الاسم الكامل</label>
                    <input type="text" 
                           wire:model="customerName"
                           placeholder="أدخل اسمك الكامل"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all @error('customerName') border-red-500 @enderror">
                    @error('customerName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">📧 البريد الإلكتروني</label>
                    <input type="email" 
                           wire:model="customerEmail"
                           placeholder="example@domain.com"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all @error('customerEmail') border-red-500 @enderror">
                    @error('customerEmail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer Phone -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">📱 رقم الهاتف</label>
                    <input type="tel" 
                           wire:model="customerPhone"
                           placeholder="+966 5X XXX XXXX"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all @error('customerPhone') border-red-500 @enderror">
                    @error('customerPhone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer Address -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">🏠 العنوان</label>
                    <textarea wire:model="customerAddress" 
                              placeholder="العنوان بالتفصيل..."
                              rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all resize-none @error('customerAddress') border-red-500 @enderror"></textarea>
                    @error('customerAddress')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 3: Payment Gateway Selection -->
        <div x-show="currentStep === 3" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-8"
             x-transition:enter-end="opacity-1 transform translate-x-0">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold gradient-text mb-4">اختر وسيلة الدفع</h2>
                <p class="text-gray-600">اختر الطريقة المناسبة لإتمام الدفع</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mb-8">
                @foreach($paymentGateways as $gateway)
                    <label class="relative cursor-pointer">
                        <input type="radio" 
                               wire:model="selectedGateway" 
                               value="{{ $gateway->id }}"
                               class="sr-only">
                        <div class="border-2 rounded-xl p-6 transition-all card-hover"
                             :class="$wire.selectedGateway == {{ $gateway->id }} ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-orange-300'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">{{ $gateway->name }}</h3>
                                    <p class="text-gray-600 text-sm">{{ $gateway->description }}</p>
                                    @if($gateway->fixed_fee || $gateway->percentage_fee)
                                        <p class="text-xs text-gray-500 mt-2">
                                            رسوم: {{ $gateway->calculateFee($totalAmount) }} ريال
                                        </p>
                                    @endif
                                </div>
                                <div class="text-3xl">
                                    @switch($gateway->provider)
                                        @case('visa')
                                            💳
                                            @break
                                        @case('mastercard')
                                            💳
                                            @break
                                        @case('mada')
                                            🏧
                                            @break
                                        @case('apple_pay')
                                            📱
                                            @break
                                        @case('stc_pay')
                                            📲
                                            @break
                                        @default
                                            💰
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('selectedGateway')
                <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Final Amount -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                <div class="text-center">
                    <h3 class="text-2xl font-bold mb-2">المبلغ الإجمالي للدفع</h3>
                    <div class="text-4xl font-bold">{{ number_format((float) $totalAmount) }} ريال سعودي</div>
                    <p class="text-orange-100 mt-2">شامل جميع الرسوم والضرائب</p>
                </div>
            </div>
        </div>

        <!-- Step 4: Confirmation -->
        <div x-show="currentStep === 4" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-8"
             x-transition:enter-end="opacity-1 transform translate-x-0">
            
            <div class="text-center">
                <div x-show="showSuccessAnimation" 
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 transform scale-75"
                     x-transition:enter-end="opacity-1 transform scale-100"
                     class="mb-8">
                    <div class="w-24 h-24 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-6 pulse-glow">
                        <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-4xl font-bold gradient-text mb-4">🎉 تم الحجز بنجاح!</h2>
                    <p class="text-xl text-gray-600 mb-6">شكراً لك! تم إنشاء حجزك وسيتم التواصل معك قريباً</p>
                    
                    @if($booking)
                        <div class="bg-white rounded-xl p-6 shadow-lg max-w-md mx-auto">
                            <h3 class="font-bold text-lg mb-4">تفاصيل الحجز</h3>
                            <div class="space-y-3 text-right">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">رقم الحجز:</span>
                                    <span class="font-bold gradient-text">{{ $booking->booking_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">الخدمة:</span>
                                    <span class="font-semibold">{{ $service->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">التاريخ:</span>
                                    <span class="font-semibold">{{ \Carbon\Carbon::parse($selectedDate)->format('Y/m/d') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">الوقت:</span>
                                    <span class="font-semibold">{{ $selectedTime }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">عدد الضيوف:</span>
                                    <span class="font-semibold">{{ $guestCount }}</span>
                                </div>
                                <hr class="border-gray-200">
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-600">المبلغ المدفوع:</span>
                                    <span class="font-bold gradient-text">{{ number_format((float) $totalAmount) }} ريال</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 space-y-4">
                            <a href="{{ route('services.index') }}" 
                               class="btn-primary px-8 py-4 text-lg font-bold inline-block">
                                تصفح المزيد من الخدمات
                            </a>
                            <div class="text-sm text-gray-500">
                                سيتم إرسال تفاصيل الحجز إلى بريدك الإلكتروني
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200" x-show="currentStep < 4">
            <button type="button" 
                    wire:click="previousStep"
                    x-show="currentStep > 1"
                    class="flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-colors">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                رجوع
            </button>
            
            <div x-show="currentStep === 1"></div>
            
            <button type="button" 
                    wire:click="nextStep"
                    x-show="currentStep < 3"
                    class="flex items-center btn-primary px-8 py-3 text-lg font-bold">
                متابعة
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            
            <button type="button" 
                    wire:click="processBooking"
                    x-show="currentStep === 3"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="flex items-center btn-primary px-8 py-3 text-lg font-bold pulse-glow">
                <span wire:loading.remove>
                    💳 تأكيد الحجز والدفع
                </span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    جاري المعالجة...
                </span>
            </button>
        </div>
    </div>
</div>
