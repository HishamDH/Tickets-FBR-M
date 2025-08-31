@extends('frontend.layouts.app')

@section('title', 'Browse Merchants')

@section('content')
<!-- Header Section -->
<div class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Browse Merchants</h1>
            <p class="text-lg text-gray-600">Discover amazing businesses and service providers</p>
        </div>
    </div>
</div>

<!-- Search & Filter Section -->
<div class="bg-gray-50 py-6 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('merchants.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search merchants..." 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:w-48">
                <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option value="events" {{ request('category') == 'events' ? 'selected' : '' }}>Events</option>
                    <option value="services" {{ request('category') == 'services' ? 'selected' : '' }}>Services</option>
                    <option value="restaurants" {{ request('category') == 'restaurants' ? 'selected' : '' }}>Restaurants</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Filter
            </button>
        </form>
    </div>
</div>

<!-- Merchants Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($merchants->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($merchants as $merchant)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <!-- Merchant Header -->
                    <div class="relative">
                        @if($merchant->cover_image)
                            <img src="{{ $merchant->cover_image }}" alt="{{ $merchant->business_name }}" 
                                 class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 bg-gradient-to-r from-blue-500 to-purple-600"></div>
                        @endif
                        
                        <!-- Merchant Logo -->
                        <div class="absolute -bottom-8 left-6">
                            @if($merchant->logo_url)
                                <img src="{{ $merchant->logo_url }}" alt="{{ $merchant->business_name }}" 
                                     class="w-16 h-16 rounded-full border-4 border-white object-cover shadow-lg">
                            @else
                                <div class="w-16 h-16 rounded-full border-4 border-white bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                    {{ strtoupper(substr($merchant->business_name ?? $merchant->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Merchant Info -->
                    <div class="pt-10 px-6 pb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            {{ $merchant->business_name ?? $merchant->user->name }}
                        </h3>
                        
                        @if($merchant->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($merchant->description, 120) }}
                            </p>
                        @endif
                        
                        <!-- Location -->
                        @if($merchant->city || $merchant->country)
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $merchant->city }}{{ $merchant->city && $merchant->country ? ', ' : '' }}{{ $merchant->country }}
                            </div>
                        @endif
                        
                        <!-- Rating -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center text-yellow-500 text-sm">
                                ⭐ {{ $merchant->rating ?? '5.0' }} 
                                <span class="text-gray-500 ml-1">({{ $merchant->reviews_count ?? '0' }} reviews)</span>
                            </div>
                            @if($merchant->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @endif
                        </div>
                        
                        <!-- Services Count -->
                        <div class="text-sm text-gray-600 mb-4">
                            <span class="font-semibold">{{ $merchant->offerings_count ?? 0 }}</span> services available
                        </div>
                        
                        <!-- View Button -->
                        <a href="{{ route('merchant.show', $merchant->slug ?? $merchant->id) }}" 
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-center block font-semibold">
                            View Services
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-12">
            {{ $merchants->appends(request()->query())->links() }}
        </div>
    @else
        <!-- No Merchants Found -->
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🏪</div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No merchants found</h3>
            <p class="text-gray-600 mb-8">Try adjusting your search criteria or browse all merchants.</p>
            <a href="{{ route('merchants.index') }}" 
               class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                View All Merchants
            </a>
        </div>
    @endif
</div>

<!-- CTA Section -->
<div class="bg-blue-600 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Are You a Service Provider?</h2>
        <p class="text-xl text-blue-100 mb-8">Join our platform and reach thousands of potential customers</p>
        <a href="/merchant/login" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
            Become a Merchant
        </a>
    </div>
</div>
@endsection