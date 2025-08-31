@extends('frontend.layouts.app')

@section('title', 'Search Results')

@section('content')
<!-- Search Header -->
<div class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                @if($query)
                    Search Results for "{{ $query }}"
                @else
                    Browse All Services
                @endif
            </h1>
            <p class="text-gray-600">
                {{ $offerings->total() }} {{ Str::plural('service', $offerings->total()) }} found
            </p>
        </div>
        
        <!-- Search Form -->
        <div class="max-w-4xl mx-auto">
            <form action="{{ route('search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="q" value="{{ $query }}" 
                           placeholder="What service are you looking for?" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:w-48">
                    <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Search
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Filters & Sort -->
<div class="bg-gray-50 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Active Filters -->
            <div class="flex items-center space-x-2">
                @if($query)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                        "{{ $query }}"
                        <a href="{{ route('search', array_filter(['category' => $category])) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                    </span>
                @endif
                @if($category)
                    @php
                        $selectedCategory = $categories->find($category);
                    @endphp
                    @if($selectedCategory)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            {{ $selectedCategory->name }}
                            <a href="{{ route('search', array_filter(['q' => $query])) }}" class="ml-2 text-green-600 hover:text-green-800">×</a>
                        </span>
                    @endif
                @endif
                @if($query || $category)
                    <a href="{{ route('search') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear all</a>
                @endif
            </div>
            
            <!-- Sort Options -->
            <div class="flex items-center space-x-4 text-sm">
                <span class="text-gray-500">Sort by:</span>
                <select class="border border-gray-300 rounded px-3 py-1 text-sm" onchange="updateSort(this.value)">
                    <option value="relevance">Relevance</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="rating">Rating</option>
                    <option value="newest">Newest</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Results -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($offerings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($offerings as $offering)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <!-- Service Image -->
                    @if($offering->image_url)
                        <img src="{{ $offering->image_url }}" alt="{{ $offering->title }}" 
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                            <span class="text-4xl">🎯</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <!-- Service Title -->
                        <h3 class="font-bold text-lg mb-2 text-gray-900 line-clamp-1">
                            {{ $offering->title }}
                        </h3>
                        
                        <!-- Merchant Info -->
                        <div class="flex items-center mb-3">
                            @if($offering->merchant->user->avatar)
                                <img src="{{ $offering->merchant->user->avatar }}" 
                                     class="w-6 h-6 rounded-full mr-2">
                            @else
                                <div class="w-6 h-6 rounded-full bg-gray-300 mr-2 flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($offering->merchant->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-sm text-blue-600 hover:text-blue-800">
                                <a href="{{ route('merchant.show', $offering->merchant->slug ?? $offering->merchant->id) }}">
                                    {{ $offering->merchant->business_name ?? $offering->merchant->user->name }}
                                </a>
                            </span>
                        </div>
                        
                        <!-- Description -->
                        @if($offering->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($offering->description, 100) }}
                            </p>
                        @endif
                        
                        <!-- Features/Tags -->
                        @if($offering->features && is_array($offering->features))
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach(array_slice($offering->features, 0, 3) as $feature)
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Rating & Reviews -->
                        <div class="flex items-center mb-4 text-sm">
                            <div class="flex items-center text-yellow-500 mr-2">
                                ⭐ {{ $offering->rating ?? '5.0' }}
                            </div>
                            <span class="text-gray-500">
                                ({{ $offering->reviews_count ?? '0' }} reviews)
                            </span>
                        </div>
                        
                        <!-- Price & Book Button -->
                        <div class="flex items-center justify-between">
                            <div>
                                @if($offering->price)
                                    <span class="text-lg font-bold text-green-600">
                                        ${{ number_format($offering->price, 2) }}
                                    </span>
                                    @if($offering->price_unit)
                                        <span class="text-sm text-gray-500">{{ $offering->price_unit }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">Price on request</span>
                                @endif
                            </div>
                            
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
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-12">
            {{ $offerings->appends(request()->query())->links() }}
        </div>
        
    @else
        <!-- No Results -->
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No services found</h3>
            @if($query || $category)
                <p class="text-gray-600 mb-8">Try adjusting your search criteria or browse all services.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('search') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Browse All Services
                    </a>
                    <a href="{{ route('merchants.index') }}" 
                       class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition">
                        View All Merchants
                    </a>
                </div>
            @else
                <p class="text-gray-600 mb-8">No services are currently available.</p>
            @endif
        </div>
    @endif
</div>

<!-- Booking Modal (same as merchant-show) -->
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

function updateSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location = url;
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