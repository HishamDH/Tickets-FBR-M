@extends('frontend.layouts.app')

@section('title', 'Book ' . $offering->title)

@section('content')
<!-- Header -->
<div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-2">Complete Your Booking</h1>
        <p class="text-blue-100">Fill in the details below to book this service</p>
    </div>
</div>

<!-- Booking Form -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Booking Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Booking Details</h2>
                
                <form action="{{ route('booking.store', $offering) }}" method="POST">
                    @csrf
                    
                    <!-- Date Selection -->
                    <div class="mb-6">
                        <label for="booking_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Date *
                        </label>
                        <input type="date" id="booking_date" name="booking_date" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('booking_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Time Selection -->
                    <div class="mb-6">
                        <label for="booking_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Time *
                        </label>
                        <select id="booking_time" name="booking_time" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                required>
                            <option value="">Select Time</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="17:00">5:00 PM</option>
                        </select>
                        @error('booking_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Quantity -->
                    <div class="mb-6">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity/Number of People *
                        </label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Customer Notes -->
                    <div class="mb-6">
                        <label for="customer_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Requirements (Optional)
                        </label>
                        <textarea id="customer_notes" name="customer_notes" rows="4" 
                                  placeholder="Any special requests or requirements..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        @error('customer_notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 mr-2">
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">terms and conditions</a> 
                                and <a href="#" class="text-blue-600 hover:text-blue-800">cancellation policy</a>.
                            </span>
                        </label>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Confirm Booking
                        </button>
                        <a href="{{ route('merchant.show', $offering->merchant->slug ?? $offering->merchant->id) }}" 
                           class="flex-1 text-center border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition font-semibold">
                            Back to Merchant
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                
                <!-- Service Details -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    @if($offering->image_url)
                        <img src="{{ $offering->image_url }}" alt="{{ $offering->title }}" 
                             class="w-full h-24 object-cover rounded-md mb-3">
                    @endif
                    
                    <h4 class="font-semibold text-gray-900 mb-2">{{ $offering->title }}</h4>
                    
                    <div class="flex items-center text-sm text-gray-600 mb-2">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $offering->merchant->business_name ?? $offering->merchant->user->name }}
                    </div>
                    
                    @if($offering->description)
                        <p class="text-sm text-gray-600 line-clamp-2">
                            {{ Str::limit($offering->description, 100) }}
                        </p>
                    @endif
                </div>
                
                <!-- Pricing -->
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Price per unit:</span>
                        <span class="font-medium">${{ number_format($offering->price, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Quantity:</span>
                        <span class="font-medium" id="summary-quantity">1</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-green-600" id="summary-total">
                                ${{ number_format($offering->price, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Info -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">Payment</p>
                            <p class="text-sm text-blue-700">Payment will be processed after merchant confirmation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update total when quantity changes
document.getElementById('quantity').addEventListener('input', function() {
    const quantity = parseInt(this.value) || 1;
    const price = {{ $offering->price }};
    const total = quantity * price;
    
    document.getElementById('summary-quantity').textContent = quantity;
    document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
});
</script>
@endsection