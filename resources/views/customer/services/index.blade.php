@extends('layouts.app')

@section('title', 'الخدمات المتاحة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100" dir="rtl">
    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
        <div class="bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-r-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div id="toast-icon" class="w-6 h-6 text-green-500">✓</div>
                </div>
                <div class="mr-3">
                    <p id="toast-message" class="text-gray-900 font-medium"></p>
                </div>
                <div class="mr-auto">
                    <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
                        <span class="sr-only">إغلاق</span>
                        ✕
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-20 left-1/3 w-16 h-16 bg-white rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">🛍️ الخدمات المتاحة</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">اكتشف مجموعة واسعة من الخدمات المميزة وأضفها لسلة التسوق الخاصة بك</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('customer.services.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="البحث في الخدمات..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <!-- Category Filter -->
                    <div>
                        <select name="category" onchange="this.form.submit()" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">جميع الفئات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['value'] }}" {{ request('category') == $category['value'] ? 'selected' : '' }}>
                                    {{ $category['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter -->
                    <div>
                        <select name="city" onchange="this.form.submit()" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">جميع المدن</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition duration-200">
                            🔍 البحث
                        </button>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="number" 
                               name="min_price" 
                               value="{{ request('min_price') }}" 
                               placeholder="الحد الأدنى للسعر"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <input type="number" 
                               name="max_price" 
                               value="{{ request('max_price') }}" 
                               placeholder="الحد الأعلى للسعر"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <select name="sort" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>الأحدث</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر من الأقل للأعلى</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر من الأعلى للأقل</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>الأعلى تقييماً</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر شعبية</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            @forelse($services as $service)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <!-- Service Image -->
                    <div class="h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                        @if($service->image)
                            <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-6xl">🛍️</div>
                        @endif
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
                            <div class="text-2xl font-bold text-blue-600">
                                {{ number_format($service->price, 0) }} ريال
                            </div>
                            @if($service->rating)
                                <div class="flex items-center text-yellow-500">
                                    <span class="mr-1">⭐</span>
                                    <span class="text-sm">{{ number_format($service->rating, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Category & Location -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($service->category)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    {{ $service->category }}
                                </span>
                            @endif
                            @if($service->location)
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                    📍 {{ $service->location }}
                                </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('customer.services.show', $service) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                عرض التفاصيل
                            </a>
                            <button id="cart-btn-{{ $service->id }}" onclick="addToCart({{ $service->id }})" 
                                    class="bg-green-600 text-white px-4 py-3 rounded-xl hover:bg-green-700 transition duration-200 flex items-center justify-center min-w-[60px] relative overflow-hidden group">
                                <span class="cart-icon transition-transform group-hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.8 8.2M7 13v6a2 2 0 002 2h6a2 2 0 002-2v-6M7 13H5.4"></path>
                                    </svg>
                                </span>
                                <span class="loading-spinner hidden">
                                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
                                </span>
                            </button>
                            <button onclick="toggleFavorite({{ $service->id }})" 
                                    class="bg-red-100 text-red-600 px-4 py-3 rounded-xl hover:bg-red-200 transition duration-200 flex items-center justify-center group"
                                    title="إضافة للمفضلة">
                                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">لا توجد خدمات متاحة</h3>
                    <p class="text-gray-600">جرب تغيير فلاتر البحث للعثور على المزيد من الخدمات</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($services->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Services page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Page initialization
});

function addToCart(serviceId) {
    const button = document.getElementById('cart-btn-' + serviceId);
    const cartIcon = button.querySelector('.cart-icon');
    const loadingSpinner = button.querySelector('.loading-spinner');
    
    // Show loading state
    button.disabled = true;
    cartIcon.classList.add('hidden');
    loadingSpinner.classList.remove('hidden');
    button.classList.add('bg-gray-400');
    button.classList.remove('bg-green-600', 'hover:bg-green-700');
    
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
            showToast('تم إضافة الخدمة إلى السلة بنجاح! 🛒', 'success');
            
            // Temporarily show success state
            button.classList.add('bg-green-500');
            cartIcon.innerHTML = '✓';
            cartIcon.classList.remove('hidden');
            loadingSpinner.classList.add('hidden');
            
            // Reset after 2 seconds
            setTimeout(() => {
                resetButton(button, cartIcon);
            }, 2000);
        } else {
            showToast(data.message || 'حدث خطأ أثناء إضافة الخدمة إلى السلة', 'error');
            resetButton(button, cartIcon);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ في الاتصال. حاول مرة أخرى.', 'error');
        resetButton(button, cartIcon);
    });
}

function resetButton(button, cartIcon) {
    const loadingSpinner = button.querySelector('.loading-spinner');
    
    button.disabled = false;
    button.classList.remove('bg-gray-400', 'bg-green-500');
    button.classList.add('bg-green-600', 'hover:bg-green-700');
    cartIcon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.8 8.2M7 13v6a2 2 0 002 2h6a2 2 0 002-2v-6M7 13H5.4"></path></svg>';
    cartIcon.classList.remove('hidden');
    loadingSpinner.classList.add('hidden');
}

function toggleFavorite(serviceId) {
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
            showToast(data.message + (data.action === 'added' ? ' ❤️' : ' 💔'), 'success');
        } else {
            showToast('حدث خطأ أثناء تحديث المفضلة', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ في الاتصال', 'error');
    });
}


function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');
    
    toastMessage.textContent = message;
    
    if (type === 'success') {
        toast.querySelector('.border-r-4').classList.remove('border-red-500');
        toast.querySelector('.border-r-4').classList.add('border-green-500');
        toastIcon.textContent = '✓';
        toastIcon.classList.remove('text-red-500');
        toastIcon.classList.add('text-green-500');
    } else {
        toast.querySelector('.border-r-4').classList.remove('border-green-500');
        toast.querySelector('.border-r-4').classList.add('border-red-500');
        toastIcon.textContent = '✕';
        toastIcon.classList.remove('text-green-500');
        toastIcon.classList.add('text-red-500');
    }
    
    // Show toast
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');
    
    // Auto hide after 5 seconds
    setTimeout(hideToast, 5000);
}

function hideToast() {
    const toast = document.getElementById('toast');
    toast.classList.remove('translate-x-0');
    toast.classList.add('translate-x-full');
}
</script>

@endsection