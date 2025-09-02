@extends('layouts.app')

@section('title', 'نظام نقاط البيع - POS')
@section('description', 'نظام نقاط البيع المتطور لإدارة المبيعات المباشرة')

@push('styles')
<style>
    .pos-container {
        height: calc(100vh - 120px);
    }
    
    .product-grid {
        height: 70vh;
        overflow-y: auto;
    }
    
    .cart-panel {
        height: 70vh;
        overflow-y: auto;
    }
    
    .pos-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .pos-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .calculator-btn {
        transition: all 0.2s ease;
    }
    
    .calculator-btn:hover {
        transform: scale(1.05);
    }
    
    .qr-scanner {
        max-width: 300px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-warm" x-data="posSystem()">
    <!-- 🔥 Enhanced Header with Fire Effects -->
    <div class="bg-white shadow-xl border-b-4 border-primary-500 sticky top-0 z-40 backdrop-blur-lg bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6 space-x-reverse">
                    <!-- ✨ Creative Logo with Animation -->
                    <div class="relative group">
                        <div class="w-16 h-16 bg-gradient-fire rounded-2xl flex items-center justify-center shadow-lg 
                                   group-hover:shadow-glow transition-all duration-300 fire-glow floating">
                            <i class="fas fa-cash-register text-3xl text-white"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-orange-fire rounded-full 
                                   animate-pulse border-3 border-white sparkle"></div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 mb-1">نظام نقاط البيع</h1>
                        <p class="text-orange-fire font-semibold flex items-center">
                            <i class="fas fa-store ml-2"></i>
                            إدارة المبيعات المباشرة والذكية
                        </p>
                    </div>
                </div>
                
                <!-- 🌟 Enhanced Quick Actions -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- Real-time Stats -->
                    <div class="hidden md:flex items-center space-x-6 space-x-reverse bg-gradient-soft px-6 py-3 rounded-2xl shadow-md">
                        <div class="text-center">
                            <div class="text-xl font-bold text-primary-600" x-text="'ر.س ' + formatPrice(cart.reduce((sum, item) => sum + (item.price * item.quantity), 0))">ر.س 0</div>
                            <div class="text-xs text-gray-600">إجمالي السلة</div>
                        </div>
                        <div class="w-px h-8 bg-orange-200"></div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600" x-text="cart.length">0</div>
                            <div class="text-xs text-gray-600">العناصر</div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons with Creative Styles -->
                    <button @click="openCustomerSearch()" 
                            class="btn btn-outline px-6 py-3 interactive-element">
                        <i class="fas fa-users ml-2"></i>
                        البحث عن عميل
                    </button>
                    
                    <button @click="openQrScanner()" 
                            class="btn btn-fire px-6 py-3 fire-trail">
                        <i class="fas fa-qrcode ml-2"></i>
                        مسح QR
                    </button>
                    
                    <button @click="newSale()" 
                            class="btn btn-primary px-8 py-3 sparkle fire-glow pulse-orange">
                        <i class="fas fa-plus ml-2"></i>
                        <span>عملية بيع جديدة</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🎨 Main POS Interface with Creative Design -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pos-container">
            
            <!-- 🛍️ Services/Products Panel with Enhanced Design -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl border border-orange-100 p-8 card hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <div class="w-12 h-12 bg-gradient-fire rounded-2xl flex items-center justify-center ml-4 shadow-lg">
                            <i class="fas fa-store text-2xl text-white"></i>
                        </div>
                        الخدمات المتاحة
                    </h2>
                    
                    <!-- 🔍 Enhanced Search Bar -->
                    <div class="relative">
                        <input type="text" 
                               x-model="searchTerm" 
                               placeholder="🔍 البحث في الخدمات..."
                               class="w-72 pl-12 pr-6 py-4 border-2 border-orange-200 rounded-2xl 
                                      focus:ring-4 focus:ring-orange-100 focus:border-primary-500 
                                      transition-all duration-300 text-lg placeholder-gray-400">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center">
                            <i class="fas fa-search text-orange-400 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- 🏷️ Creative Category Filters -->
                <div class="flex flex-wrap gap-3 mb-8">
                    <button @click="selectedCategory = 'all'" 
                            :class="selectedCategory === 'all' ? 'bg-gradient-primary text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-orange-50 hover:text-primary-600'"
                            class="px-6 py-3 rounded-2xl font-semibold transition-all duration-300 hover:scale-105 interactive-element">
                        <i class="fas fa-star ml-2"></i>
                        جميع الخدمات
                    </button>
                    <template x-for="category in categories" :key="category.id">
                        <button @click="selectedCategory = category.id" 
                                :class="selectedCategory === category.id ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-lg font-medium transition-all"
                                x-text="category.name">
                        </button>
                    </template>
                </div>

                <!-- Services Grid -->
                <div class="product-grid">
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        <template x-for="service in filteredServices" :key="service.id">
                            <div @click="addToCart(service)" 
                                 class="pos-card bg-gradient-to-br from-white to-gray-50 rounded-xl border-2 border-gray-200 hover:border-orange-300 p-4 text-center">
                                
                                <!-- Service Icon -->
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl" x-text="service.icon || '🎯'"></span>
                                </div>
                                
                                <!-- Service Info -->
                                <h3 class="font-bold text-gray-800 mb-2 text-sm" x-text="service.name"></h3>
                                <div class="text-2xl font-bold text-orange-600 mb-2">
                                    <span x-text="formatPrice(service.price)"></span>
                                    <span class="text-xs text-gray-500">ريال</span>
                                </div>
                                
                                <!-- Quick Add Button -->
                                <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-3 rounded-lg text-sm font-medium transition-all">
                                    إضافة للسلة
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Cart Panel -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="text-2xl mr-3">🛒</span>
                        سلة المشتريات
                    </h2>
                    <button @click="clearCart()" 
                            x-show="cart.length > 0"
                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                        مسح الكل
                    </button>
                </div>

                <!-- Customer Info -->
                <div x-show="selectedCustomer" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-blue-800" x-text="selectedCustomer?.name"></p>
                            <p class="text-sm text-blue-600" x-text="selectedCustomer?.phone"></p>
                        </div>
                        <button @click="selectedCustomer = null" class="text-blue-400 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="cart-panel">
                    <div x-show="cart.length === 0" class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m9.5-6v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                        </svg>
                        <p class="text-lg font-medium">السلة فارغة</p>
                        <p class="text-sm">اختر خدمة لبدء البيع</p>
                    </div>

                    <div x-show="cart.length > 0" class="space-y-3">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-800" x-text="item.name"></h4>
                                    <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button @click="updateQuantity(index, item.quantity - 1)" 
                                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-medium" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(index, item.quantity + 1)" 
                                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Item Total -->
                                    <div class="text-right">
                                        <div class="font-bold text-orange-600" x-text="formatPrice(item.price * item.quantity) + ' ريال'"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Cart Totals -->
                <div x-show="cart.length > 0" class="border-t border-gray-200 pt-4 mt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">المجموع الفرعي:</span>
                            <span class="font-medium" x-text="formatPrice(subtotal) + ' ريال'"></span>
                        </div>
                        
                        <!-- Discount Input -->
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">الخصم:</span>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <input type="number" 
                                       x-model="discount" 
                                       placeholder="0"
                                       class="w-20 px-2 py-1 border border-gray-300 rounded text-center text-sm">
                                <select x-model="discountType" class="px-2 py-1 border border-gray-300 rounded text-sm">
                                    <option value="amount">ريال</option>
                                    <option value="percentage">%</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>الإجمالي:</span>
                            <span class="text-orange-600" x-text="formatPrice(total) + ' ريال'"></span>
                        </div>
                    </div>

                    <!-- Payment Buttons -->
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <button @click="processPayment('cash')" 
                                class="bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-bold transition-all">
                            💵 نقدي
                        </button>
                        <button @click="processPayment('card')" 
                                class="bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-bold transition-all">
                            💳 بطاقة
                        </button>
                    </div>
                    
                    <button @click="processPayment('mixed')" 
                            class="w-full mt-3 bg-purple-500 hover:bg-purple-600 text-white py-3 px-4 rounded-lg font-bold transition-all">
                        💰 دفع مختلط
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Search Modal -->
    <div x-show="showCustomerSearch" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click="showCustomerSearch = false">
        
        <div @click.stop class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">البحث عن عميل</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف أو الاسم</label>
                    <input type="text" 
                           x-model="customerSearch" 
                           @input="searchCustomers()"
                           placeholder="966xxxxxxxxx أو اسم العميل"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                
                <!-- Search Results -->
                <div x-show="customerResults.length > 0" class="max-h-40 overflow-y-auto">
                    <template x-for="customer in customerResults" :key="customer.id">
                        <div @click="selectCustomer(customer)" 
                             class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                            <div class="font-medium" x-text="customer.name"></div>
                            <div class="text-sm text-gray-600" x-text="customer.phone"></div>
                        </div>
                    </template>
                </div>
                
                <!-- New Customer Form -->
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-800 mb-3">عميل جديد</h4>
                    <div class="space-y-3">
                        <input type="text" 
                               x-model="newCustomer.name" 
                               placeholder="اسم العميل"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <input type="tel" 
                               x-model="newCustomer.phone" 
                               placeholder="رقم الهاتف"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button @click="createNewCustomer()" 
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition-all">
                            إضافة عميل جديد
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3 space-x-reverse mt-6">
                <button @click="showCustomerSearch = false" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-all">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div x-show="showQrScanner" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click="showQrScanner = false">
        
        <div @click.stop class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">مسح رمز QR</h3>
            
            <div class="qr-scanner">
                <div class="bg-gray-100 rounded-lg p-8 text-center">
                    <div class="w-48 h-48 mx-auto bg-white border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <p class="text-gray-600">ضع الكود أمام الكاميرا</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3 space-x-reverse mt-6">
                <button @click="showQrScanner = false" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-all">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div x-show="showPaymentModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">تأكيد الدفع</h3>
            
            <div class="space-y-4">
                <!-- Payment Summary -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">المبلغ الإجمالي:</span>
                        <span class="text-2xl font-bold text-orange-600" x-text="formatPrice(total) + ' ريال'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">طريقة الدفع:</span>
                        <span class="font-medium" x-text="getPaymentMethodName(selectedPaymentMethod)"></span>
                    </div>
                </div>

                <!-- Cash Payment Calculation -->
                <div x-show="selectedPaymentMethod === 'cash'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ المستلم</label>
                    <input type="number" 
                           x-model="cashReceived" 
                           @input="calculateChange()"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg font-bold">
                    
                    <div x-show="change > 0" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-green-700 font-medium">المتبقي للعميل:</span>
                            <span class="text-2xl font-bold text-green-600" x-text="formatPrice(change) + ' ريال'"></span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختيارية)</label>
                    <textarea x-model="paymentNotes" 
                              rows="2" 
                              placeholder="أي ملاحظات إضافية..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                </div>
            </div>
            
            <div class="flex space-x-3 space-x-reverse mt-6">
                <button @click="showPaymentModal = false" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-bold transition-all">
                    إلغاء
                </button>
                <button @click="confirmPayment()" 
                        :disabled="!canConfirmPayment()"
                        :class="canConfirmPayment() ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 cursor-not-allowed'"
                        class="flex-1 text-white py-3 px-4 rounded-lg font-bold transition-all">
                    تأكيد البيع
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function posSystem() {
    return {
        // State
        cart: [],
        selectedCustomer: null,
        searchTerm: '',
        selectedCategory: 'all',
        discount: 0,
        discountType: 'amount',
        
        // Modal states
        showCustomerSearch: false,
        showQrScanner: false,
        showPaymentModal: false,
        selectedPaymentMethod: '',
        
        // Customer search
        customerSearch: '',
        customerResults: [],
        newCustomer: { name: '', phone: '' },
        
        // Payment
        cashReceived: 0,
        change: 0,
        paymentNotes: '',
        
        // Data
        categories: [
            { id: 'venues', name: 'قاعات ومواقع' },
            { id: 'catering', name: 'تموين وضيافة' },
            { id: 'photography', name: 'تصوير' },
            { id: 'entertainment', name: 'ترفيه' },
            { id: 'planning', name: 'تخطيط فعاليات' },
            { id: 'decoration', name: 'تزيين' }
        ],
        
        services: @json($services ?? []),
        
        // Computed properties
        get filteredServices() {
            let filtered = this.services;
            
            if (this.selectedCategory !== 'all') {
                filtered = filtered.filter(service => service.category === this.selectedCategory);
            }
            
            if (this.searchTerm) {
                filtered = filtered.filter(service => 
                    service.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                    service.description?.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            }
            
            return filtered;
        },
        
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        
        get discountAmount() {
            if (this.discountType === 'percentage') {
                return (this.subtotal * this.discount) / 100;
            }
            return this.discount || 0;
        },
        
        get total() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },
        
        // Methods
        addToCart(service) {
            const existingItem = this.cart.find(item => item.id === service.id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                this.cart.push({
                    id: service.id,
                    name: service.name,
                    price: service.price,
                    quantity: 1,
                    icon: service.icon || '🎯'
                });
            }
            
            this.showNotification('تم إضافة الخدمة للسلة', 'success');
        },
        
        removeFromCart(index) {
            this.cart.splice(index, 1);
        },
        
        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(index);
            } else {
                this.cart[index].quantity = newQuantity;
            }
        },
        
        clearCart() {
            this.cart = [];
            this.selectedCustomer = null;
            this.discount = 0;
            this.showNotification('تم مسح السلة', 'info');
        },
        
        newSale() {
            this.clearCart();
        },
        
        openCustomerSearch() {
            this.showCustomerSearch = true;
            this.customerSearch = '';
            this.customerResults = [];
        },
        
        openQrScanner() {
            this.showQrScanner = true;
        },
        
        searchCustomers() {
            if (this.customerSearch.length < 3) {
                this.customerResults = [];
                return;
            }
            
            // Simulate API call - replace with actual implementation
            this.customerResults = [
                { id: 1, name: 'أحمد محمد', phone: '966501234567' },
                { id: 2, name: 'فاطمة علي', phone: '966502345678' }
            ].filter(customer => 
                customer.name.includes(this.customerSearch) || 
                customer.phone.includes(this.customerSearch)
            );
        },
        
        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.showCustomerSearch = false;
            this.showNotification(`تم اختيار العميل: ${customer.name}`, 'success');
        },
        
        createNewCustomer() {
            if (!this.newCustomer.name || !this.newCustomer.phone) {
                this.showNotification('يرجى إدخال اسم العميل ورقم الهاتف', 'error');
                return;
            }
            
            // Simulate API call - replace with actual implementation
            const customer = {
                id: Date.now(),
                name: this.newCustomer.name,
                phone: this.newCustomer.phone
            };
            
            this.selectCustomer(customer);
            this.newCustomer = { name: '', phone: '' };
        },
        
        processPayment(method) {
            if (this.cart.length === 0) {
                this.showNotification('السلة فارغة', 'error');
                return;
            }
            
            this.selectedPaymentMethod = method;
            this.cashReceived = method === 'cash' ? this.total : 0;
            this.change = 0;
            this.showPaymentModal = true;
        },
        
        calculateChange() {
            this.change = Math.max(0, this.cashReceived - this.total);
        },
        
        canConfirmPayment() {
            if (this.selectedPaymentMethod === 'cash') {
                return this.cashReceived >= this.total;
            }
            return true;
        },
        
        confirmPayment() {
            // Simulate API call - replace with actual implementation
            const saleData = {
                items: this.cart,
                customer: this.selectedCustomer,
                subtotal: this.subtotal,
                discount: this.discountAmount,
                total: this.total,
                paymentMethod: this.selectedPaymentMethod,
                cashReceived: this.cashReceived,
                change: this.change,
                notes: this.paymentNotes
            };
            
            console.log('Processing sale:', saleData);
            
            // Show success and reset
            this.showNotification('تم إتمام البيع بنجاح!', 'success');
            this.showPaymentModal = false;
            this.clearCart();
            
            // TODO: Print receipt
            // TODO: Send to backend
        },
        
        getPaymentMethodName(method) {
            const methods = {
                'cash': 'نقدي 💵',
                'card': 'بطاقة 💳',
                'mixed': 'دفع مختلط 💰'
            };
            return methods[method] || method;
        },
        
        formatPrice(price) {
            return new Intl.NumberFormat('ar-SA').format(price);
        },
        
        showNotification(message, type = 'info') {
            // Simple notification - replace with toast library
            alert(message);
        }
    }
}
</script>
@endpush
@endsection
