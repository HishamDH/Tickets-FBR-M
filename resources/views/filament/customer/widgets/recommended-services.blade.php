<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ $title }}
        </x-slot>
        
        <x-slot name="description">
            {{ $subtitle }}
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($services as $service)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 border border-gray-200 hover:border-primary-300">
                    <!-- Service Image -->
                    <div class="relative h-40 bg-gray-100">
                        <img src="{{ $service['image'] }}" 
                             alt="{{ $service['title'] }}" 
                             class="w-full h-full object-cover">
                        
                        <!-- Features Badges -->
                        <div class="absolute top-3 left-3 space-y-1">
                            @foreach($service['features'] as $feature)
                                <span class="block bg-green-600 text-white px-2 py-1 rounded text-xs font-medium">
                                    {{ $feature }}
                                </span>
                            @endforeach
                        </div>
                        
                        <!-- Availability Badge -->
                        <div class="absolute top-3 right-3">
                            @if($service['available'])
                                <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                    Available
                                </span>
                            @else
                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                    Sold Out
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Service Content -->
                    <div class="p-4">
                        <!-- Header with Rating -->
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1 flex-1">
                                {{ $service['title'] }}
                            </h3>
                            @if($service['rating'] > 0)
                                <div class="flex items-center text-yellow-500 text-sm ml-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-1">{{ $service['rating'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            {{ $service['description'] }}
                        </p>

                        <!-- Merchant Info -->
                        <div class="flex items-center mb-3">
                            @if($service['merchant_logo'])
                                <img src="{{ $service['merchant_logo'] }}" 
                                     alt="{{ $service['merchant'] }}" 
                                     class="w-6 h-6 rounded-full mr-2">
                            @else
                                <div class="w-6 h-6 bg-gray-300 rounded-full mr-2 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-sm text-gray-700 font-medium">{{ $service['merchant'] }}</p>
                                <p class="text-xs text-gray-500">{{ $service['bookings_count'] }} bookings</p>
                            </div>
                        </div>

                        <!-- Price and Category -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-lg font-bold text-primary-600">
                                ${{ number_format($service['price'], 2) }}
                            </span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $service['category'] }}
                            </span>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ $service['booking_url'] }}" 
                           class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 {{ !$service['available'] ? 'opacity-50 cursor-not-allowed' : '' }}">
                            @if($service['available'])
                                Book Now
                            @else
                                View Details
                            @endif
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-gray-500 text-lg font-medium mb-2">No recommendations yet</h3>
                    <p class="text-gray-400 mb-4">Start booking services to get personalized recommendations!</p>
                    <a href="{{ route('services.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Explore Services
                    </a>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
