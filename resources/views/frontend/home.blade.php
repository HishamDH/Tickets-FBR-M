@extends('frontend.layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<div class="brand-gradient text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                🎟️ Book Services & Events
                <span class="block text-yellow-300">Made Simple</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-purple-100">
                Discover and book amazing services from trusted merchants in Saudi Arabia
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('search') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition btn-brand">
                    🔍 Find Services
                </a>
                <a href="{{ route('merchants.index') }}" class="border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition">
                    🏪 Browse Merchants
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="bg-white py-8 border-b">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="q" placeholder="What service are you looking for?" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:w-48">
                <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-brand text-white px-8 py-3 rounded-lg font-semibold">
                🔍 Search
            </button>
        </form>
    </div>
</div>

<!-- Categories Section -->
@if($categories->count() > 0)
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Popular Categories</h2>
            <p class="text-gray-600">Discover services across different categories</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('search', ['category' => $category->id]) }}" 
                   class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition group">
                    <div class="text-3xl mb-3">{{ $category->icon ?? '📋' }}</div>
                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->offerings_count ?? 0 }} services</p>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Featured Offerings -->
@if($featuredOfferings->count() > 0)
<div class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Services</h2>
            <p class="text-gray-600">Top-rated services from our trusted merchants</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredOfferings as $offering)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    @if($offering->image_url)
                        <img src="{{ $offering->image_url }}" alt="{{ $offering->title }}" 
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                            <span class="text-4xl">🎯</span>
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-gray-900">{{ $offering->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($offering->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm text-gray-500">by</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $offering->user->name }}</span>
                            </div>
                            @if($offering->price)
                                <span class="text-lg font-bold text-green-600">${{ number_format($offering->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="mt-4">
                            <a href="#" 
                               class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-center block">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Featured Merchants -->
@if($featuredMerchants->count() > 0)
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Trusted Merchants</h2>
            <p class="text-gray-600">Discover amazing businesses and service providers</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredMerchants as $merchant)
                <a href="{{ route('merchant.show', $merchant->slug ?? $merchant->id) }}" 
                   class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition block">
                    @if($merchant->logo_url)
                        <img src="{{ $merchant->logo_url }}" alt="{{ $merchant->business_name }}" 
                             class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($merchant->business_name ?? $merchant->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <h3 class="font-bold text-gray-900 mb-2">{{ $merchant->business_name ?? $merchant->user->name }}</h3>
                    @if($merchant->description)
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($merchant->description, 80) }}</p>
                    @endif
                    <div class="flex items-center justify-center text-yellow-500 text-sm">
                        ⭐ {{ $merchant->rating ?? '5.0' }} ({{ $merchant->reviews_count ?? '0' }} reviews)
                    </div>
                </a>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('merchants.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                View All Merchants →
            </a>
        </div>
    </div>
</div>
@endif

<!-- CTA Section -->
<div class="bg-blue-600 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-xl text-blue-100 mb-8">Join thousands of customers who trust our platform</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/customer/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                Sign Up as Customer
            </a>
            <a href="/merchant/login" class="border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                Become a Merchant
            </a>
        </div>
    </div>
</div>
@endsection