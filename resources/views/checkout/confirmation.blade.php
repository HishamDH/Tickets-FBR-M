@extends('layouts.app')

@section('title', 'Order Confirmation - Order #' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-600">Thank you for your order. We've sent you a confirmation email with order details.</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold">Order #{{ $order->order_number }}</h2>
                        <p class="text-orange-100">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($order->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                            {{ $order->status_label }}
                        </div>
                        <div class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($order->payment_status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment_status === 'pending_cod') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                            {{ $order->payment_status_label }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Order Items -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Order Items
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($order->items as $orderItem)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $orderItem->item_name }}</h4>
                                        <p class="text-sm text-gray-600">
                                            Quantity: {{ $orderItem->quantity }} × ${{ number_format($orderItem->price, 2) }}
                                        </p>
                                        
                                        @if($orderItem->branch_info)
                                            <p class="text-xs text-gray-500">
                                                📍 {{ $orderItem->branch_info['name'] ?? 'Branch location' }}
                                            </p>
                                        @endif
                                        
                                        @if($orderItem->time_slot)
                                            <p class="text-xs text-gray-500">
                                                🕒 {{ $orderItem->time_slot['date'] ?? 'Date' }} at {{ $orderItem->time_slot['time'] ?? 'Time' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-lg font-semibold text-gray-900">
                                    ${{ number_format($orderItem->total, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-2">
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
                                <span>Total Paid:</span>
                                <span>${{ number_format($order->total, 2) }} {{ $order->currency }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing & Payment Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Billing Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Billing Information
                        </h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="font-medium text-gray-900">{{ $order->billing_name }}</p>
                            <p class="text-sm text-gray-600">{{ $order->billing_details['email'] }}</p>
                            <p class="text-sm text-gray-600">{{ $order->billing_details['phone'] }}</p>
                            <p class="text-sm text-gray-600 mt-2">{{ $order->billing_address }}</p>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Payment Information
                        </h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="font-medium text-gray-900">{{ $order->payment_method_label }}</p>
                            <p class="text-sm text-gray-600">
                                Status: 
                                <span class="font-medium 
                                      @if($order->payment_status === 'completed') text-green-600
                                      @elseif($order->payment_status === 'pending') text-yellow-600
                                      @elseif($order->payment_status === 'pending_cod') text-blue-600
                                      @else text-red-600
                                      @endif">
                                    {{ $order->payment_status_label }}
                                </span>
                            </p>
                            
                            @if($order->payment_details && isset($order->payment_details['paid_at']))
                                <p class="text-sm text-gray-600">
                                    Paid on: {{ \Carbon\Carbon::parse($order->payment_details['paid_at'])->format('F j, Y \a\t g:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                @if($order->notes)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Notes</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2"></path>
                </svg>
                Continue Shopping
            </a>
            
            <a href="{{ dashboard_route() }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                View My Orders
            </a>

            @if($order->payment_status === 'pending' && $order->payment_method === 'stripe')
                <a href="{{ route('checkout.payment.stripe', $order) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Complete Payment
                </a>
            @endif
        </div>

        <!-- Help Section -->
        <div class="mt-12 text-center">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Need Help?</h3>
                <p class="text-gray-600 mb-4">
                    If you have any questions about your order, please don't hesitate to contact us.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="mailto:support@tickets-fbr.com" 
                       class="inline-flex items-center text-orange-600 hover:text-orange-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        support@tickets-fbr.com
                    </a>
                    <a href="tel:+966123456789" 
                       class="inline-flex items-center text-orange-600 hover:text-orange-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        +966 12 345 6789
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection