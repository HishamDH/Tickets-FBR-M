@extends('layouts.public')

@section('title', 'حجز ' . $service->name . ' - ' . $merchant->business_name)

@section('content')
<div class="min-h-screen bg-slate-50" dir="rtl">
    <!-- Service Header -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center space-x-3 space-x-reverse text-sm text-slate-600 mb-4">
                <a href="{{ route('merchant.booking', $merchant->id) }}" class="hover:text-primary">{{ $merchant->business_name }}</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-slate-800">{{ $service->name }}</span>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Service Image -->
                <div>
                    @if($service->images && is_array($service->images) && count($service->images) > 0)
                        <img src="{{ Storage::url($service->images[0]) }}" 
                             alt="{{ $service->name }}" 
                             class="w-full h-80 object-cover rounded-xl shadow-lg">
                    @else
                        <div class="w-full h-80 bg-gradient-to-br from-primary/20 to-primary/5 rounded-xl flex items-center justify-center">
                            <svg class="w-24 h-24 text-primary/40" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Service Info -->
                <div>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-slate-800 mb-2">{{ $service->name }}</h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary">
                                @switch($service->service_type)
                                    @case('event')
                                        فعالية
                                        @break
                                    @case('exhibition')
                                        معرض
                                        @break
                                    @case('restaurant')
                                        مطعم
                                        @break
                                    @case('experience')
                                        تجربة
                                        @break
                                    @default
                                        {{ $service->service_type }}
                                @endswitch
                            </span>
                        </div>
                        @if($service->is_featured)
                            <span class="text-yellow-500">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </span>
                        @endif
                    </div>

                    <p class="text-slate-600 mb-6 leading-relaxed">{{ $service->description }}</p>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <div class="text-sm text-slate-500 mb-1">السعر</div>
                            <div class="text-2xl font-bold text-slate-800">{{ number_format($service->base_price ?? $service->price) }} ريال</div>
                            @if(($service->pricing_model ?? $service->price_type) !== 'fixed')
                                <div class="text-xs text-slate-400">
                                    @switch($service->pricing_model ?? $service->price_type)
                                        @case('per_person')
                                            لكل شخص
                                            @break
                                        @case('per_table')
                                            لكل طاولة
                                            @break
                                        @case('hourly')
                                        @case('per_hour')
                                            بالساعة
                                            @break
                                        @case('package')
                                            باقة
                                            @break
                                    @endswitch
                                </div>
                            @endif
                        </div>

                        @if($service->capacity)
                            <div class="bg-slate-50 p-4 rounded-lg">
                                <div class="text-sm text-slate-500 mb-1">السعة القصوى</div>
                                <div class="text-2xl font-bold text-slate-800">{{ $service->capacity }}</div>
                                <div class="text-xs text-slate-400">شخص</div>
                            </div>
                        @endif
                    </div>

                    @if($service->location)
                        <div class="flex items-center text-slate-600 mb-6">
                            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $service->location }}
                        </div>
                    @endif

                    @if($service->features && is_array($service->features))
                        <div class="mb-6">
                            <h3 class="font-semibold text-slate-800 mb-3">المميزات المتاحة</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($service->features as $feature)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-slate-800 mb-6">تفاصيل الحجز</h2>

                <form action="{{ route('merchant.book', ['merchant' => $merchant->id, 'service' => $service->id]) }}" method="POST">
                    @csrf

                    <!-- Customer Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">معلومات العميل</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-2">الاسم الكامل *</label>
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       value="{{ old('customer_name') }}"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                       required>
                                @error('customer_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-2">رقم الهاتف *</label>
                                <input type="tel" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       value="{{ old('customer_phone') }}"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                       required>
                                @error('customer_phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-2">البريد الإلكتروني</label>
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       value="{{ old('customer_email') }}"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                @error('customer_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">تفاصيل الحجز</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="booking_date" class="block text-sm font-medium text-slate-700 mb-2">تاريخ الحجز *</label>
                                <input type="date" 
                                       id="booking_date" 
                                       name="booking_date" 
                                       value="{{ old('booking_date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                       required>
                                @error('booking_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="booking_time" class="block text-sm font-medium text-slate-700 mb-2">وقت الحجز *</label>
                                <input type="time" 
                                       id="booking_time" 
                                       name="booking_time" 
                                       value="{{ old('booking_time') }}"
                                       class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                       required>
                                @error('booking_time')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(($service->pricing_model ?? $service->price_type) === 'per_person' || $service->capacity)
                                <div>
                                    <label for="number_of_people" class="block text-sm font-medium text-slate-700 mb-2">عدد الأشخاص *</label>
                                    <input type="number" 
                                           id="number_of_people" 
                                           name="number_of_people" 
                                           value="{{ old('number_of_people', 1) }}"
                                           min="1"
                                           @if($service->capacity) max="{{ $service->capacity }}" @endif
                                           class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                           required>
                                    @error('number_of_people')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            @if(($service->pricing_model ?? $service->price_type) === 'per_table')
                                <div>
                                    <label for="number_of_tables" class="block text-sm font-medium text-slate-700 mb-2">عدد الطاولات *</label>
                                    <input type="number" 
                                           id="number_of_tables" 
                                           name="number_of_tables" 
                                           value="{{ old('number_of_tables', 1) }}"
                                           min="1"
                                           class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                           required>
                                    @error('number_of_tables')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            @if(($service->pricing_model ?? $service->price_type) === 'hourly' || ($service->pricing_model ?? $service->price_type) === 'per_hour')
                                <div>
                                    <label for="duration_hours" class="block text-sm font-medium text-slate-700 mb-2">عدد الساعات *</label>
                                    <input type="number" 
                                           id="duration_hours" 
                                           name="duration_hours" 
                                           value="{{ old('duration_hours', 1) }}"
                                           min="1"
                                           class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                           required>
                                    @error('duration_hours')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="mb-8">
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">ملاحظات خاصة</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary" 
                                  placeholder="أي طلبات خاصة أو ملاحظات...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-slate-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">ملخص السعر</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-slate-600">سعر الخدمة الأساسي</span>
                                <span class="font-semibold">{{ number_format($service->base_price ?? $service->price) }} ريال</span>
                            </div>
                            <div id="price-calculation" class="hidden">
                                <!-- Price calculation will be shown here via JavaScript -->
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>المجموع</span>
                                    <span id="total-price">{{ number_format($service->base_price ?? $service->price) }} ريال</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-primary text-white py-4 rounded-lg font-semibold text-lg hover:bg-primary/90 transition-colors duration-200">
                        تأكيد الحجز
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const basePrice = {{ $service->base_price ?? $service->price ?? 0 }};
    const pricingModel = '{{ $service->pricing_model ?? $service->price_type ?? 'fixed' }}';
    
    function updatePrice() {
        let totalPrice = basePrice;
        let calculationHTML = '';
        
        if (pricingModel === 'per_person') {
            const numberOfPeople = parseInt(document.getElementById('number_of_people')?.value || 1);
            totalPrice = basePrice * numberOfPeople;
            calculationHTML = `
                <div class="flex justify-between text-sm text-slate-600">
                    <span>${numberOfPeople} شخص × ${basePrice.toLocaleString()} ريال</span>
                    <span>${totalPrice.toLocaleString()} ريال</span>
                </div>
            `;
        } else if (pricingModel === 'per_table') {
            const numberOfTables = parseInt(document.getElementById('number_of_tables')?.value || 1);
            totalPrice = basePrice * numberOfTables;
            calculationHTML = `
                <div class="flex justify-between text-sm text-slate-600">
                    <span>${numberOfTables} طاولة × ${basePrice.toLocaleString()} ريال</span>
                    <span>${totalPrice.toLocaleString()} ريال</span>
                </div>
            `;
        } else if (pricingModel === 'hourly' || pricingModel === 'per_hour') {
            const durationHours = parseInt(document.getElementById('duration_hours')?.value || 1);
            totalPrice = basePrice * durationHours;
            calculationHTML = `
                <div class="flex justify-between text-sm text-slate-600">
                    <span>${durationHours} ساعة × ${basePrice.toLocaleString()} ريال</span>
                    <span>${totalPrice.toLocaleString()} ريال</span>
                </div>
            `;
        }
        
        const calculationDiv = document.getElementById('price-calculation');
        const totalPriceSpan = document.getElementById('total-price');
        
        if (calculationHTML) {
            calculationDiv.innerHTML = calculationHTML;
            calculationDiv.classList.remove('hidden');
        } else {
            calculationDiv.classList.add('hidden');
        }
        
        totalPriceSpan.textContent = totalPrice.toLocaleString() + ' ريال';
    }
    
    // Add event listeners to quantity inputs
    ['number_of_people', 'number_of_tables', 'duration_hours'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updatePrice);
        }
    });
    
    // Initial price calculation
    updatePrice();
});
</script>
@endsection
