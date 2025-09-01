@extends('layouts.app')

@section('title', 'Payment - Order #' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Payment</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }} • Total: ${{ number_format($order->total, 2) }}</p>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                <h2 class="text-xl font-semibold">Secure Payment with Stripe</h2>
                <p class="text-blue-100">Your payment information is encrypted and secure</p>
            </div>

            <form method="POST" action="{{ route('checkout.payment.stripe.process', $order) }}" class="p-6">
                @csrf

                <!-- Demo Notice -->
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Demo Payment Mode</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                This is a demonstration. No real charges will be made. Use test card number: <code class="font-mono">4242 4242 4242 4242</code>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card Information -->
                <div class="space-y-4">
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                        <input type="text" 
                               id="card_number" 
                               name="card_number" 
                               placeholder="4242 4242 4242 4242"
                               value="4242 4242 4242 4242"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="expiry" class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" 
                                   id="expiry" 
                                   name="expiry" 
                                   placeholder="MM/YY"
                                   value="12/28"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                        <div>
                            <label for="cvc" class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                            <input type="text" 
                                   id="cvc" 
                                   name="cvc" 
                                   placeholder="123"
                                   value="123"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label for="cardholder_name" class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                        <input type="text" 
                               id="cardholder_name" 
                               name="cardholder_name" 
                               value="{{ $order->billing_name }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        
                        @if($order->discount > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount:</span>
                                <span class="font-medium">-${{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total to Pay:</span>
                            <span>${{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                        </div>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-green-800">
                                <strong>Secure Payment:</strong> Your payment information is encrypted using industry-standard SSL technology.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Pay ${{ number_format($order->total, 2) }}
                    </button>
                    <a href="{{ route('checkout.confirmation', $order) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                        Back to Order
                    </a>
                </div>

                <input type="hidden" name="stripeToken" value="demo_token_{{ Str::random(24) }}">
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Need help? Contact our support team at 
                <a href="mailto:support@tickets-fbr.com" class="text-orange-600 hover:text-orange-500">support@tickets-fbr.com</a>
            </p>
        </div>
    </div>
</div>
@endsection