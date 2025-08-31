@extends('frontend.layouts.app')

@section('title', $merchant->business_name ?? $merchant->user->name)

@section('content')
<!-- Merchant Header -->
<div class="relative">
    @if($merchant->cover_image)
        <div class="h-64 bg-cover bg-center" style="background-image: url('{{ $merchant->cover_image }}');">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>
    @else
        <div class="h-64 bg-gradient-to-r from-blue-600 to-purple-700"></div>
    @endif
    
    <!-- Merchant Info Overlay -->
    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end space-x-6">
                <!-- Logo -->
                @if($merchant->logo_url)
                    <img src="{{ $merchant->logo_url }}" alt="{{ $merchant->business_name }}" 
                         class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-lg">
                @else
                    <div class="w-24 h-24 rounded-full border-4 border-white bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($merchant->business_name ?? $merchant->user->name, 0, 1)) }}
                    </div>
                @endif
                
                <!-- Business Info -->
                <div class="flex-1 pb-2">
                    <h1 class="text-3xl font-bold mb-2">{{ $merchant->business_name ?? $merchant->user->name }}</h1>
                    <div class="flex items-center space-x-6 text-sm">
                        @if($merchant->city || $merchant->country)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $merchant->city }}{{ $merchant->city && $merchant->country ? ', ' : '' }}{{ $merchant->country }}
                            </div>
                        @endif
                        
                        <div class="flex items-center">
                            ⭐ {{ $merchant->rating ?? '5.0' }} ({{ $merchant->reviews_count ?? '0' }} reviews)
                        </div>
                        
                        @if($merchant->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white">
                                Active
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left Column - Services -->
        <div class="lg:col-span-2">
            <!-- About Section -->
            @if($merchant->description)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">About Us</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $merchant->description }}</p>
                </div>
            @endif
            
            <!-- Services/Offerings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Our Services</h2>
                    <span class="text-sm text-gray-500">{{ $merchant->offerings->count() }} services</span>
                </div>
                
                @if($merchant->offerings->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($merchant->offerings as $offering)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                @if($offering->image_url)
                                    <img src="{{ $offering->image_url }}" alt="{{ $offering->title }}" 
                                         class="w-full h-32 object-cover rounded-md mb-4">
                                @else
                                    <div class="w-full h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-md mb-4 flex items-center justify-center">
                                        <span class="text-3xl">🎯</span>
                                    </div>
                                @endif
                                
                                <h3 class="font-semibold text-gray-900 mb-2">{{ $offering->title }}</h3>
                                
                                @if($offering->description)
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                        {{ Str::limit($offering->description, 100) }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    @if($offering->price)
                                        <span class="text-lg font-bold text-green-600">
                                            ${{ number_format($offering->price, 2) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">Price on request</span>
                                    @endif
                                    
                                    @auth
                                        @if(auth()->user()->isCustomer())
                                            <a href="{{ route('booking.show', $offering) }}" 
                                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold text-center block">
                                                Book Now
                                            </a>
                                        @else
                                            <button class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                                                Customers Only
                                            </button>
                                        @endif
                                    @else
                                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold book-service-btn" 
                                                data-offering-id="{{ $offering->id }}"
                                                data-offering-title="{{ $offering->title }}"
                                                data-offering-price="{{ $offering->price }}">
                                            Book Now
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📋</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No services available</h3>
                        <p class="text-gray-600">This merchant hasn't added any services yet.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Right Column - Sidebar -->
        <div class="lg:col-span-1">
            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                
                @if($merchant->phone)
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <span class="text-gray-600">{{ $merchant->phone }}</span>
                    </div>
                @endif
                
                @if($merchant->email || $merchant->user->email)
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="text-gray-600">{{ $merchant->email ?? $merchant->user->email }}</span>
                    </div>
                @endif
                
                @if($merchant->website)
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ $merchant->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            Visit Website
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Business Hours -->
            @if($merchant->business_hours)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Hours</h3>
                    <div class="space-y-2 text-sm">
                        <!-- This would be parsed from business_hours JSON field -->
                        <div class="flex justify-between">
                            <span class="text-gray-600">Monday - Friday</span>
                            <span class="text-gray-900">9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Saturday</span>
                            <span class="text-gray-900">10:00 AM - 4:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sunday</span>
                            <span class="text-gray-900">Closed</span>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Quick Actions -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-semibold">
                        Message Merchant
                    </button>
                    <button class="w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition font-semibold">
                        Add to Favorites
                    </button>
                    <button class="w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition font-semibold">
                        Share Merchant
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal Placeholder -->
<div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Book Service</h3>
            <button class="text-gray-400 hover:text-gray-600" onclick="closeBookingModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="booking-content">
            <p class="text-gray-600 mb-4">Please log in to book this service.</p>
            <div class="flex space-x-3">
                <a href="/customer/login" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg text-center hover:bg-green-700 transition">
                    Customer Login
                </a>
                <a href="/customer/register" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg text-center hover:bg-blue-700 transition">
                    Register
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openBookingModal(offeringId, offeringTitle, offeringPrice) {
    document.getElementById('booking-modal').classList.remove('hidden');
    document.getElementById('booking-modal').classList.add('flex');
}

function closeBookingModal() {
    document.getElementById('booking-modal').classList.add('hidden');
    document.getElementById('booking-modal').classList.remove('flex');
}

// Add event listeners to book buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.book-service-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const offeringId = this.dataset.offeringId;
            const offeringTitle = this.dataset.offeringTitle;
            const offeringPrice = this.dataset.offeringPrice;
            openBookingModal(offeringId, offeringTitle, offeringPrice);
        });
    });
});
</script>
@endsection