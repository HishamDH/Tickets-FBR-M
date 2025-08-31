@extends('frontend.layouts.app')

@section('title', 'Booking Confirmation')

@section('content')
<!-- Success Header -->
<div class="bg-green-600 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-3xl font-bold mb-2">Booking Confirmed!</h1>
        <p class="text-green-100">Your booking has been submitted successfully</p>
    </div>
</div>

<!-- Booking Details -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Booking Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Booking Information</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Booking Reference</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_reference }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium 
                                   {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                      ($booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Date & Time</h3>
                        <p class="text-gray-900">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                            at {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Quantity</h3>
                        <p class="text-gray-900">{{ $booking->quantity }} {{ Str::plural('person', $booking->quantity) }}</p>
                    </div>
                </div>
                
                @if($booking->customer_notes)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Special Requirements</h3>
                        <p class="text-gray-900">{{ $booking->customer_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Service Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Service Details</h2>
                
                <div class="flex">
                    @if($booking->service->image_url)
                        <img src="{{ $booking->service->image_url }}" alt="{{ $booking->service->title }}" 
                             class="w-20 h-20 object-cover rounded-lg mr-4">
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg mr-4 flex items-center justify-center">
                            <span class="text-2xl">🎯</span>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $booking->service->title }}</h3>
                        <p class="text-gray-600 mb-2">{{ Str::limit($booking->service->description, 150) }}</p>
                        <p class="text-sm text-blue-600">
                            by {{ $booking->merchant->business_name ?? $booking->merchant->user->name }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-blue-900 mb-4">What happens next?</h2>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">1</span>
                        <p class="text-blue-800">The merchant will review your booking request and confirm availability.</p>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">2</span>
                        <p class="text-blue-800">You'll receive an email confirmation with payment instructions.</p>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">3</span>
                        <p class="text-blue-800">Complete the payment to secure your booking.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/customer" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-semibold text-center">
                    View My Bookings
                </a>
                <a href="{{ route('home') }}" class="flex-1 border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition font-semibold text-center">
                    Continue Browsing
                </a>
                @if($booking->status === 'pending')
                    <form action="{{ route('booking.cancel', $booking) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')"
                                class="w-full border border-red-300 text-red-700 py-3 px-6 rounded-lg hover:bg-red-50 transition font-semibold">
                            Cancel Booking
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Payment Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Service Price:</span>
                        <span>${{ number_format($booking->total_amount / $booking->quantity, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Quantity:</span>
                        <span>{{ $booking->quantity }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Platform Fee:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                            <span class="text-lg font-bold text-green-600">${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($booking->status === 'pending')
                    <div class="mt-4 p-3 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <strong>Payment Status:</strong> Pending merchant confirmation
                        </p>
                    </div>
                @endif
            </div>

            <!-- Merchant Contact -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Merchant Contact</h3>
                
                <div class="flex items-center mb-4">
                    @if($booking->merchant->logo_url)
                        <img src="{{ $booking->merchant->logo_url }}" alt="{{ $booking->merchant->business_name }}" 
                             class="w-12 h-12 rounded-full mr-3">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 mr-3 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($booking->merchant->business_name ?? $booking->merchant->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="font-semibold text-gray-900">
                            {{ $booking->merchant->business_name ?? $booking->merchant->user->name }}
                        </h4>
                        <p class="text-sm text-gray-500">Service Provider</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    @if($booking->merchant->phone)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $booking->merchant->phone }}</span>
                        </div>
                    @endif
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="text-gray-600">{{ $booking->merchant->email ?? $booking->merchant->user->email }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                        Contact Merchant
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection