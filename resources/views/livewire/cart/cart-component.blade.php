<div class="relative">
    <!-- Cart Toggle Button -->
    <button 
        wire:click="toggleCart" 
        class="relative p-2 text-gray-700 hover:text-orange-500 transition-colors duration-200"
        aria-label="Shopping Cart"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m9.5-6v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
            </path>
        </svg>
        
        <!-- Cart Count Badge -->
        @if($count > 0)
            <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium animate-pulse">
                {{ $count }}
            </span>
        @endif
    </button>

    <!-- Cart Dropdown -->
    @if($showCart)
        <div class="absolute right-0 top-full mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 max-h-96 overflow-hidden"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Shopping Cart</h3>
                <button 
                    wire:click="toggleCart" 
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                    aria-label="Close cart"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Cart Content -->
            <div class="flex-1 overflow-y-auto max-h-64">
                @if(count($cartItems) > 0)
                    <div class="p-4 space-y-3">
                        @foreach($cartItems as $item)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <!-- Item Image Placeholder -->
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>

                                <!-- Item Details -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item->getItemName() }}
                                    </h4>
                                    <p class="text-xs text-gray-500">
                                        ${{ number_format($item->price, 2) }} each
                                    </p>
                                    
                                    <!-- Additional Info -->
                                    @if($item->additional_data)
                                        @php $data = json_decode($item->additional_data, true); @endphp
                                        @if(isset($data['branch']['name']))
                                            <p class="text-xs text-gray-400">{{ $data['branch']['name'] }}</p>
                                        @endif
                                    @endif
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-2">
                                    <button 
                                        wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                        class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                        @if($item->quantity <= 1) disabled @endif
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    
                                    <span class="w-8 text-center text-sm font-medium">{{ $item->quantity }}</span>
                                    
                                    <button 
                                        wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                        class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Remove Button -->
                                <button 
                                    wire:click="removeItem({{ $item->id }})"
                                    class="text-red-400 hover:text-red-600 transition-colors"
                                    aria-label="Remove item"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty Cart State -->
                    <div class="p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m9.5-6v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        <p class="text-gray-500 text-sm font-medium">Your cart is empty</p>
                        <p class="text-gray-400 text-xs mt-1">Add items to get started</p>
                    </div>
                @endif
            </div>

            <!-- Cart Footer -->
            @if(count($cartItems) > 0)
                <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <!-- Cart Totals -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($discount > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount:</span>
                                <span class="font-medium">-${{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-base font-semibold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total:</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <button 
                            wire:click="checkout"
                            class="w-full bg-orange-500 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center justify-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span>Proceed to Checkout</span>
                        </button>
                        
                        <button 
                            wire:click="clearCart"
                            class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors duration-200"
                            onclick="return confirm('Are you sure you want to clear your cart?')"
                        >
                            Clear Cart
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Backdrop -->
        <div class="fixed inset-0 z-40" wire:click="toggleCart"></div>
    @endif

    <!-- Toast Notifications -->
    <div 
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:cart-success.window="show = true; message = $event.detail.message; type = 'success'; setTimeout(() => show = false, 3000)"
        x-on:cart-error.window="show = true; message = $event.detail.message; type = 'error'; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed top-4 right-4 z-50 max-w-sm"
    >
        <div 
            class="px-4 py-3 rounded-lg shadow-lg border"
            :class="type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
        >
            <div class="flex items-center space-x-2">
                <svg 
                    class="w-5 h-5"
                    :class="type === 'success' ? 'text-green-500' : 'text-red-500'"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path 
                        x-show="type === 'success'"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M5 13l4 4L19 7"
                    ></path>
                    <path 
                        x-show="type === 'error'"
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
                <span class="text-sm font-medium" x-text="message"></span>
            </div>
        </div>
    </div>
</div>