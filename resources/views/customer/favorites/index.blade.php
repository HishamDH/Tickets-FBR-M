@extends('layouts.app')

@section('title', 'الخدمات المفضلة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 to-pink-100" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-pink-500 to-pink-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">❤️ خدماتي المفضلة</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">الخدمات التي أضفتها إلى قائمة المفضلة</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        @auth('customer')
            @php
                $favoriteServices = Auth::guard('customer')->user()->favoriteServices()
                    ->with(['merchant'])
                    ->where('is_active', true)
                    ->whereHas('merchant', function($q) {
                        $q->where('status', 'active');
                    })
                    ->get();
            @endphp

            @if($favoriteServices->count() > 0)
                <!-- Services Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($favoriteServices as $service)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                            <!-- Service Image -->
                            <div class="h-48 bg-gradient-to-br from-pink-100 to-pink-200 flex items-center justify-center relative">
                                @if($service->image)
                                    <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-6xl">❤️</div>
                                @endif
                                
                                <!-- Remove from favorites button -->
                                <button onclick="removeFromFavorites({{ $service->id }})" 
                                        class="absolute top-3 right-3 bg-white/90 text-red-500 p-2 rounded-full shadow-lg hover:bg-white transition duration-200"
                                        title="إزالة من المفضلة">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-6">
                                <!-- Service Name -->
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $service->name }}</h3>
                                
                                <!-- Merchant Name -->
                                <p class="text-sm text-gray-600 mb-3">
                                    🏪 {{ $service->merchant->business_name ?? $service->merchant->name }}
                                </p>

                                <!-- Description -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $service->description }}</p>

                                <!-- Price -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-2xl font-bold text-pink-600">
                                        {{ number_format($service->price, 0) }} ريال
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ route('customer.services.show', $service) }}" 
                                       class="flex-1 bg-pink-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-pink-700 transition duration-200 flex items-center justify-center">
                                        عرض التفاصيل
                                    </a>
                                    <button onclick="addToCart({{ $service->id }})" 
                                            class="bg-green-600 text-white px-4 py-3 rounded-xl hover:bg-green-700 transition duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.8 8.2M7 13v6a2 2 0 002 2h6a2 2 0 002-2v-6M7 13H5.4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-8xl mb-6">💔</div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">لا توجد خدمات مفضلة</h3>
                    <p class="text-xl text-gray-600 mb-8">ابدأ بإضافة الخدمات التي تعجبك إلى قائمة المفضلة</p>
                    <a href="{{ route('customer.services.index') }}" 
                       class="inline-flex items-center bg-pink-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-pink-700 transition duration-200">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        تصفح الخدمات
                    </a>
                </div>
            @endif
        @else
            <!-- Not Authenticated -->
            <div class="text-center py-16">
                <div class="text-8xl mb-6">🔒</div>
                <h3 class="text-3xl font-bold text-gray-800 mb-4">تحتاج إلى تسجيل الدخول</h3>
                <p class="text-xl text-gray-600 mb-8">سجل دخولك لعرض قائمة خدماتك المفضلة</p>
                <a href="{{ route('customer.login') }}" 
                   class="inline-flex items-center bg-pink-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-pink-700 transition duration-200">
                    تسجيل الدخول
                </a>
            </div>
        @endauth
    </div>
</div>

<script>
function removeFromFavorites(serviceId) {
    fetch('/customer/services/' + serviceId + '/favorite', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to update favorites list
            location.reload();
        } else {
            alert('حدث خطأ أثناء إزالة الخدمة من المفضلة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}

function addToCart(serviceId) {
    fetch('/customer/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_id: serviceId,
            item_type: 'App\\Models\\Service',
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم إضافة الخدمة إلى السلة بنجاح! 🛒');
        } else {
            alert('حدث خطأ أثناء إضافة الخدمة إلى السلة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال');
    });
}
</script>

@endsection