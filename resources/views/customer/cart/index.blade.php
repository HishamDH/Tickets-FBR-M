@extends('layouts.app')

@section('title', 'عربة التسوق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">🛒 عربة التسوق</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">راجع وأكد طلبك للخدمات المختارة</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        @auth('customer')
            <!-- Cart Items Container -->
            <div id="cartContainer">
                <!-- Loading State -->
                <div id="loadingState" class="text-center py-16">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-green-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">جاري تحميل العربة...</p>
                </div>

                <!-- Empty Cart State -->
                <div id="emptyCartState" class="text-center py-16 hidden">
                    <div class="text-8xl mb-6">🛒</div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">عربة التسوق فارغة</h3>
                    <p class="text-xl text-gray-600 mb-8">أضف بعض الخدمات الرائعة إلى عربتك</p>
                    <a href="{{ route('customer.services.index') }}" 
                       class="inline-flex items-center bg-green-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-green-700 transition duration-200">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        تصفح الخدمات
                    </a>
                </div>

                <!-- Cart Items -->
                <div id="cartItems" class="hidden">
                    <div class="grid lg:grid-cols-3 gap-8">
                        <!-- Items List -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                                <div class="p-6 border-b border-gray-200">
                                    <h2 class="text-2xl font-bold text-gray-800">العناصر في العربة</h2>
                                </div>
                                <div id="itemsList" class="divide-y divide-gray-200">
                                    <!-- Cart items will be inserted here -->
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4">ملخص الطلب</h3>
                                
                                <div class="space-y-3 mb-6">
                                    <div class="flex justify-between text-gray-600">
                                        <span>المجموع الفرعي:</span>
                                        <span id="subtotal">0 ريال</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>الخصم:</span>
                                        <span id="discount">0 ريال</span>
                                    </div>
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between text-xl font-bold text-gray-800">
                                            <span>الإجمالي:</span>
                                            <span id="total">0 ريال</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <button onclick="proceedToCheckout()" 
                                            class="w-full bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition duration-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        متابعة الطلب
                                    </button>
                                    
                                    <button onclick="clearCart()" 
                                            class="w-full bg-red-600 text-white py-3 rounded-xl font-semibold hover:bg-red-700 transition duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        إفراغ العربة
                                    </button>

                                    <a href="{{ route('customer.services.index') }}" 
                                       class="w-full bg-gray-600 text-white py-3 rounded-xl font-semibold hover:bg-gray-700 transition duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        إضافة المزيد
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Not Authenticated -->
            <div class="text-center py-16">
                <div class="text-8xl mb-6">🔒</div>
                <h3 class="text-3xl font-bold text-gray-800 mb-4">تحتاج إلى تسجيل الدخول</h3>
                <p class="text-xl text-gray-600 mb-8">سجل دخولك لعرض عربة التسوق الخاصة بك</p>
                <a href="{{ route('customer.login') }}" 
                   class="inline-flex items-center bg-green-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-green-700 transition duration-200">
                    تسجيل الدخول
                </a>
            </div>
        @endauth
    </div>
</div>

<!-- Item Template (Hidden) -->
<template id="cartItemTemplate">
    <div class="cart-item p-6" data-item-id="">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Service Image -->
            <div class="w-full md:w-32 h-32 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <img class="service-image w-full h-full object-cover rounded-lg hidden" alt="">
                <div class="service-placeholder text-4xl">🎯</div>
            </div>

            <!-- Service Details -->
            <div class="flex-1">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="service-name text-lg font-bold text-gray-800 mb-2"></h3>
                        <p class="merchant-name text-sm text-gray-600 mb-2"></p>
                        <p class="service-description text-sm text-gray-600 line-clamp-2 mb-3"></p>
                        
                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600">الكمية:</span>
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button class="quantity-decrease px-3 py-2 hover:bg-gray-100 transition duration-200">-</button>
                                <input type="number" class="quantity-input w-16 text-center border-0 focus:outline-none" min="1" max="50">
                                <button class="quantity-increase px-3 py-2 hover:bg-gray-100 transition duration-200">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Price and Actions -->
                    <div class="text-right">
                        <div class="service-price text-2xl font-bold text-green-600 mb-3"></div>
                        <button class="remove-item bg-red-100 text-red-600 px-4 py-2 rounded-lg hover:bg-red-200 transition duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
let cartData = null;

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});

async function loadCart() {
    try {
        const response = await fetch('/customer/cart/api');
        const result = await response.json();
        
        if (result.success) {
            cartData = result.data;
            renderCart();
        } else {
            showError('فشل في تحميل العربة');
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        showError('حدث خطأ في تحميل العربة');
    }
}

function renderCart() {
    const loadingState = document.getElementById('loadingState');
    const emptyCartState = document.getElementById('emptyCartState');
    const cartItems = document.getElementById('cartItems');

    loadingState.classList.add('hidden');

    if (!cartData.items || cartData.items.length === 0) {
        emptyCartState.classList.remove('hidden');
        cartItems.classList.add('hidden');
    } else {
        emptyCartState.classList.add('hidden');
        cartItems.classList.remove('hidden');
        renderCartItems();
        updateOrderSummary();
    }
}

function renderCartItems() {
    const itemsList = document.getElementById('itemsList');
    const template = document.getElementById('cartItemTemplate');
    
    itemsList.innerHTML = '';
    
    cartData.items.forEach(cartItem => {
        const itemElement = template.content.cloneNode(true);
        const item = cartItem.item;
        
        // Set data attributes
        const cartItemDiv = itemElement.querySelector('.cart-item');
        cartItemDiv.dataset.itemId = cartItem.id;
        
        // Fill in service details
        itemElement.querySelector('.service-name').textContent = item.name;
        itemElement.querySelector('.merchant-name').textContent = `🏪 ${item.merchant?.business_name || item.merchant?.name || 'غير محدد'}`;
        itemElement.querySelector('.service-description').textContent = item.description || '';
        itemElement.querySelector('.service-price').textContent = `${Number(cartItem.price * cartItem.quantity).toLocaleString()} ريال`;
        
        // Handle service image
        const serviceImage = itemElement.querySelector('.service-image');
        const servicePlaceholder = itemElement.querySelector('.service-placeholder');
        if (item.image) {
            serviceImage.src = item.image.startsWith('http') ? item.image : `/storage/${item.image}`;
            serviceImage.classList.remove('hidden');
            servicePlaceholder.classList.add('hidden');
        }
        
        // Set quantity
        const quantityInput = itemElement.querySelector('.quantity-input');
        quantityInput.value = cartItem.quantity;
        
        // Add event listeners
        const decreaseBtn = itemElement.querySelector('.quantity-decrease');
        const increaseBtn = itemElement.querySelector('.quantity-increase');
        const removeBtn = itemElement.querySelector('.remove-item');
        
        decreaseBtn.addEventListener('click', () => updateQuantity(cartItem.id, Math.max(1, cartItem.quantity - 1)));
        increaseBtn.addEventListener('click', () => updateQuantity(cartItem.id, cartItem.quantity + 1));
        quantityInput.addEventListener('change', (e) => updateQuantity(cartItem.id, parseInt(e.target.value) || 1));
        removeBtn.addEventListener('click', () => removeItem(cartItem.id));
        
        itemsList.appendChild(itemElement);
    });
}

function updateOrderSummary() {
    document.getElementById('subtotal').textContent = `${Number(cartData.subtotal).toLocaleString()} ريال`;
    document.getElementById('discount').textContent = `${Number(cartData.discount).toLocaleString()} ريال`;
    document.getElementById('total').textContent = `${Number(cartData.total).toLocaleString()} ريال`;
}

async function updateQuantity(cartItemId, newQuantity) {
    try {
        const response = await fetch(`/customer/cart/${cartItemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: newQuantity })
        });
        
        const result = await response.json();
        
        if (result.success) {
            cartData = result.data;
            renderCart();
        } else {
            showError(result.message || 'فشل في تحديث الكمية');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showError('حدث خطأ في تحديث الكمية');
    }
}

async function removeItem(cartItemId) {
    if (!confirm('هل تريد حذف هذا العنصر من العربة؟')) return;
    
    try {
        const response = await fetch(`/customer/cart/${cartItemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            cartData = result.data;
            renderCart();
            showSuccess('تم حذف العنصر من العربة');
        } else {
            showError(result.message || 'فشل في حذف العنصر');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        showError('حدث خطأ في حذف العنصر');
    }
}

async function clearCart() {
    if (!confirm('هل تريد إفراغ العربة بالكامل؟')) return;
    
    try {
        const response = await fetch('/customer/cart', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            cartData = result.data;
            renderCart();
            showSuccess('تم إفراغ العربة بنجاح');
        } else {
            showError(result.message || 'فشل في إفراغ العربة');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        showError('حدث خطأ في إفراغ العربة');
    }
}

function proceedToCheckout() {
    if (!cartData.items || cartData.items.length === 0) {
        showError('العربة فارغة');
        return;
    }
    
    // Redirect to checkout
    window.location.href = '/customer/checkout';
}

function showError(message) {
    alert('خطأ: ' + message);
}

function showSuccess(message) {
    alert('✅ ' + message);
}
</script>

@endsection