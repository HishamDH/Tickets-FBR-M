@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100" dir="rtl">
    <div class="container mx-auto px-6 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('customer.services.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        🏠 الخدمات
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $service->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Service Image -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="h-96 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                        @if($service->image)
                            <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-8xl">🛍️</div>
                        @endif
                    </div>
                </div>

                <!-- Service Details -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $service->name }}</h1>
                    
                    <!-- Merchant Info -->
                    <div class="flex items-center mb-6 p-4 bg-gray-50 rounded-xl">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-2xl">🏪</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $service->merchant->business_name ?? $service->merchant->name }}</h3>
                            <p class="text-sm text-gray-600">مقدم الخدمة</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">وصف الخدمة</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
                    </div>

                    <!-- Features -->
                    @if($service->features)
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-3">المميزات</h3>
                            <div class="space-y-2">
                                @foreach(explode("\n", $service->features) as $feature)
                                    @if(trim($feature))
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                            <span class="text-gray-700">{{ trim($feature) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Service Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        @if($service->category)
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-blue-800 mb-1">الفئة</h4>
                                <p class="text-blue-700">{{ $service->category }}</p>
                            </div>
                        @endif

                        @if($service->location)
                            <div class="bg-green-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-green-800 mb-1">الموقع</h4>
                                <p class="text-green-700">📍 {{ $service->location }}</p>
                            </div>
                        @endif

                        @if($service->capacity)
                            <div class="bg-purple-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-purple-800 mb-1">السعة</h4>
                                <p class="text-purple-700">👥 {{ $service->capacity }} شخص</p>
                            </div>
                        @endif

                        <div class="bg-orange-50 p-4 rounded-xl">
                            <h4 class="font-semibold text-orange-800 mb-1">الحالة</h4>
                            <p class="text-orange-700">
                                @if($service->is_available)
                                    ✅ متاح للحجز
                                @else
                                    ❌ غير متاح حالياً
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Booking Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 sticky top-6">
                    <div class="text-center mb-6">
                        <div class="text-4xl font-bold text-blue-600 mb-2">
                            {{ number_format($service->price, 0) }} ريال
                        </div>
                        @if($service->rating)
                            <div class="flex items-center justify-center text-yellow-500 mb-4">
                                <span class="mr-2 text-2xl">⭐</span>
                                <span class="text-lg font-semibold">{{ number_format($service->rating, 1) }}</span>
                                <span class="text-gray-600 mr-2">({{ $service->reviews_count ?? 0 }} تقييم)</span>
                            </div>
                        @endif
                    </div>

                    @if($service->is_available)
                        <form action="{{ route('customer.services.book', $service) }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الكمية</label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $service->capacity ?? 100 }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Booking Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الحجز</label>
                                <input type="datetime-local" name="booking_date" required
                                       min="{{ date('Y-m-d\TH:i') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
                                <textarea name="notes" rows="3" placeholder="أي ملاحظات أو طلبات خاصة..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition duration-200">
                                    📅 احجز الآن
                                </button>
                                
                                <button type="button" onclick="addToCart({{ $service->id }})" 
                                        class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition duration-200">
                                    🛒 إضافة للسلة
                                </button>

                                <button type="button" onclick="toggleFavorite({{ $service->id }})" 
                                        class="w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition duration-200">
                                    ❤️ إضافة للمفضلة
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <div class="text-6xl mb-4">😔</div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">غير متاح حالياً</h3>
                            <p class="text-gray-600">هذه الخدمة غير متاحة للحجز في الوقت الحالي</p>
                        </div>
                    @endif
                </div>

                <!-- Contact Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">تواصل مع مقدم الخدمة</h3>
                    
                    @if($service->merchant->phone)
                        <a href="tel:{{ $service->merchant->phone }}" 
                           class="flex items-center w-full bg-green-100 text-green-800 p-3 rounded-xl hover:bg-green-200 transition duration-200 mb-3">
                            <span class="text-2xl mr-3">📞</span>
                            <span class="font-semibold">{{ $service->merchant->phone }}</span>
                        </a>
                    @endif

                    @if($service->merchant->email)
                        <a href="mailto:{{ $service->merchant->email }}" 
                           class="flex items-center w-full bg-blue-100 text-blue-800 p-3 rounded-xl hover:bg-blue-200 transition duration-200">
                            <span class="text-2xl mr-3">📧</span>
                            <span class="font-semibold">إرسال رسالة</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Services -->
        @if(isset($relatedServices) && $relatedServices->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">خدمات مشابهة</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($relatedServices as $related)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                            <div class="h-32 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                @if($related->image)
                                    <img src="{{ Storage::url($related->image) }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-4xl">🛍️</div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 mb-2">{{ $related->name }}</h3>
                                <p class="text-blue-600 font-bold">{{ number_format($related->price, 0) }} ريال</p>
                                <a href="{{ route('customer.services.show', $related) }}" 
                                   class="block w-full text-center bg-blue-600 text-white py-2 rounded-xl mt-3 hover:bg-blue-700 transition duration-200">
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function addToCart(serviceId) {
    fetch('/customer/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            service_id: serviceId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم إضافة الخدمة إلى السلة بنجاح!');
            updateCartCount();
        } else {
            alert('حدث خطأ أثناء إضافة الخدمة إلى السلة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة الخدمة إلى السلة');
    });
}

function toggleFavorite(serviceId) {
    fetch(`/customer/services/${serviceId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('حدث خطأ أثناء إضافة الخدمة للمفضلة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إضافة الخدمة للمفضلة');
    });
}

function updateCartCount() {
    fetch('/customer/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
            }
        });
}
</script>

@endsection