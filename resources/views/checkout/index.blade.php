@extends('layouts.app')

@section('title', 'Checkout - Complete Your Order')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Order</h1>
            <p class="text-gray-600">Review your order and provide billing information</p>
        </div>

        <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            
            <!-- Billing Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Billing Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="billing_first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" 
                                   id="billing_first_name" 
                                   name="billing_first_name" 
                                   value="{{ old('billing_first_name', $user->first_name ?? '') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="billing_last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" 
                                   id="billing_last_name" 
                                   name="billing_last_name" 
                                   value="{{ old('billing_last_name', $user->last_name ?? '') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" 
                                   id="billing_email" 
                                   name="billing_email" 
                                   value="{{ old('billing_email', $user->email) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" 
                                   id="billing_phone" 
                                   name="billing_phone" 
                                   value="{{ old('billing_phone', $user->phone ?? '') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <input type="text" 
                                   id="billing_address" 
                                   name="billing_address" 
                                   value="{{ old('billing_address') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                            <input type="text" 
                                   id="billing_city" 
                                   name="billing_city" 
                                   value="{{ old('billing_city') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-2">State/Province *</label>
                            <input type="text" 
                                   id="billing_state" 
                                   name="billing_state" 
                                   value="{{ old('billing_state') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                            <input type="text" 
                                   id="billing_postal_code" 
                                   name="billing_postal_code" 
                                   value="{{ old('billing_postal_code') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            @error('billing_postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Order Notes (Optional)</label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Any special instructions or notes for your order..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Payment Method
                        </h3>

                        <div class="space-y-4">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="stripe" class="text-orange-500 focus:ring-orange-500" checked>
                                <div class="ml-3 flex items-center">
                                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13.976 9.15c-2.172-.806-3.596-1.191-3.596-2.705 0-1.114.62-2.17 2.525-2.17 1.265 0 2.293.54 3.18 1.342l.989-1.23c-.987-.952-2.367-1.564-4.089-1.564-2.476 0-4.456 1.458-4.456 4.122 0 2.476 1.652 3.407 4.365 4.293 2.172.806 3.456 1.153 3.456 2.705 0 1.304-.955 2.17-2.686 2.17-1.435 0-2.758-.668-3.696-1.59l-.989 1.23c1.077 1.077 2.686 1.845 4.606 1.845 2.686 0 4.647-1.459 4.647-4.122 0-2.897-1.652-3.827-4.256-4.526z"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium">Credit/Debit Card</p>
                                        <p class="text-sm text-gray-600">Visa, MasterCard, American Express</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="paypal" class="text-orange-500 focus:ring-orange-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.421c0 2.215-.23 4.006-1.226 5.392-.987 1.371-2.498 2.27-4.378 2.27h-1.71l-.429 2.444A.625.625 0 0 1 12.25 17H8.07a.641.641 0 0 1-.633-.74l.604-3.852h.646c4.312 0 7.684-1.748 8.667-6.797.023-.12.036-.235.048-.349.053-.581.024-1.143-.18-1.665z"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium">PayPal</p>
                                        <p class="text-sm text-gray-600">Pay securely with your PayPal account</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="bank_transfer" class="text-orange-500 focus:ring-orange-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium">Bank Transfer</p>
                                        <p class="text-sm text-gray-600">Direct bank transfer with instructions</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="radio" name="payment_method" value="cash_on_delivery" class="text-orange-500 focus:ring-orange-500">
                                <div class="ml-3 flex items-center">
                                    <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-medium">Cash on Delivery</p>
                                        <p class="text-sm text-gray-600">Pay when the service is delivered</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-6">
                        <label class="flex items-start">
                            <input type="checkbox" name="terms_accepted" required class="mt-1 text-orange-500 focus:ring-orange-500">
                            <span class="ml-2 text-sm text-gray-700">
                                I agree to the <a href="#" class="text-orange-600 hover:text-orange-500">Terms and Conditions</a> and <a href="#" class="text-orange-600 hover:text-orange-500">Privacy Policy</a> *
                            </span>
                        </label>
                        @error('terms_accepted')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Order Summary
                    </h2>

                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        @foreach($cartData['items'] as $item)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">
                                        {{ $item->getItemName() }}
                                    </h4>
                                    <p class="text-xs text-gray-500">
                                        Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                    </p>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    ${{ number_format($item->subtotal, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="space-y-3 pt-6 border-t border-gray-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">${{ number_format($cartData['subtotal'], 2) }}</span>
                        </div>
                        
                        @if($cartData['discount'] > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount:</span>
                                <span class="font-medium">-${{ number_format($cartData['discount'], 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-3 border-t border-gray-200">
                            <span>Total:</span>
                            <span>${{ number_format($cartData['total'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full mt-6 bg-orange-500 text-white py-4 px-6 rounded-lg font-semibold hover:bg-orange-600 transition-colors duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Complete Order</span>
                    </button>

                    <p class="text-xs text-gray-500 text-center mt-3">
                        🔒 Your payment information is secure and encrypted
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection