@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
                    <p class="text-gray-600 mt-1">Track and manage your orders</p>
                </div>
                <a href="{{ route('services.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Order
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('dashboard.user.orders') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Order number..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select id="payment_status" name="payment_status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Payment Statuses</option>
                        @foreach($paymentStatusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('payment_status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'payment_status']))
                        <a href="{{ route('dashboard.user.orders') }}" 
                           class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Orders List -->
        @if($orders->count() > 0)
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Order Header -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Order #{{ $order->order_number }}
                                    </h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                          @if($order->status === 'delivered') bg-green-100 text-green-800
                                          @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                          @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                          @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                          @elseif($order->status === 'processing') bg-purple-100 text-purple-800
                                          @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-800
                                          @else bg-gray-100 text-gray-800
                                          @endif">
                                        {{ $order->status_label }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                          @if($order->payment_status === 'completed') bg-green-100 text-green-800
                                          @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                          @elseif($order->payment_status === 'pending_cod') bg-blue-100 text-blue-800
                                          @else bg-red-100 text-red-800
                                          @endif">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-500">{{ $order->created_at->format('M j, Y \a\t g:i A') }}</span>
                                    <a href="{{ route('dashboard.user.orders.show', $order) }}" 
                                       class="text-orange-600 hover:text-orange-500 font-medium">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Order Content -->
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Items -->
                                <div class="lg:col-span-2">
                                    <h4 class="font-medium text-gray-900 mb-3">Items ({{ $order->items->count() }})</h4>
                                    <div class="space-y-3">
                                        @foreach($order->items->take(3) as $item)
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->item_name }}</p>
                                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    ${{ number_format($item->total, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($order->items->count() > 3)
                                            <p class="text-xs text-gray-500 italic">
                                                + {{ $order->items->count() - 3 }} more items
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-3">Order Summary</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Subtotal:</span>
                                            <span>${{ number_format($order->subtotal, 2) }}</span>
                                        </div>
                                        @if($order->discount > 0)
                                            <div class="flex justify-between text-green-600">
                                                <span>Discount:</span>
                                                <span>-${{ number_format($order->discount, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between font-semibold pt-2 border-t border-gray-200">
                                            <span>Total:</span>
                                            <span>${{ number_format($order->total, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs text-gray-600">Payment Method</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $order->payment_method_label }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                                @if($order->canBeCancelled())
                                    <button onclick="openCancelModal({{ $order->id }}, '{{ $order->order_number }}')"
                                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                        Cancel Order
                                    </button>
                                @endif
                                
                                @if($order->payment_status === 'pending' && $order->payment_method === 'stripe')
                                    <a href="{{ route('checkout.payment.stripe', $order) }}" 
                                       class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                                        Complete Payment
                                    </a>
                                @endif

                                <a href="{{ route('dashboard.user.orders.show', $order) }}" 
                                   class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Orders Found</h3>
                <p class="text-gray-600 mb-8">
                    @if(request()->hasAny(['search', 'status', 'payment_status']))
                        No orders match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't placed any orders yet. Start shopping to see your orders here.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if(request()->hasAny(['search', 'status', 'payment_status']))
                        <a href="{{ route('dashboard.user.orders') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('services.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2"></path>
                        </svg>
                        Browse Services
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Order</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to cancel order <span id="cancelOrderNumber" class="font-medium"></span>?</p>
        
        <form id="cancelForm" method="POST">
            @csrf
            @method('POST')
            
            <div class="mb-4">
                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for cancellation (required)
                </label>
                <textarea id="cancellation_reason" 
                          name="cancellation_reason" 
                          rows="3" 
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                          placeholder="Please let us know why you're cancelling this order..."></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeCancelModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Keep Order
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Cancel Order
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCancelModal(orderId, orderNumber) {
    document.getElementById('cancelOrderNumber').textContent = '#' + orderNumber;
    document.getElementById('cancelForm').action = `/dashboard/user/orders/${orderId}/cancel`;
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancellation_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endpush