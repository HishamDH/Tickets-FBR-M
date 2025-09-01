<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ $title }}
        </x-slot>
        
        <x-slot name="description">
            {{ $subtitle }}
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($events as $event)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 border border-gray-200">
                    <!-- Event Image -->
                    <div class="relative h-48 bg-gray-100">
                        <img src="{{ $event['image'] }}" 
                             alt="{{ $event['title'] }}" 
                             class="w-full h-full object-cover">
                        
                        <!-- Date Badge -->
                        <div class="absolute top-3 left-3 bg-primary-600 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                            {{ \Carbon\Carbon::parse($event['start_date'])->format('M j') }}
                        </div>
                        
                        <!-- Price Badge -->
                        <div class="absolute top-3 right-3 bg-black bg-opacity-70 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                            ${{ number_format($event['price'], 2) }}
                        </div>
                    </div>

                    <!-- Event Content -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                                {{ $event['title'] }}
                            </h3>
                            @if($event['rating'] > 0)
                                <div class="flex items-center text-yellow-500 text-sm ml-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-1">{{ $event['rating'] }}</span>
                                </div>
                            @endif
                        </div>

                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            {{ $event['description'] }}
                        </p>

                        <!-- Event Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($event['start_date'])->format('M j, Y \a\t g:i A') }}
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event['location'] }}
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                by {{ $event['merchant'] }}
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $event['category'] }}
                            </span>
                            
                            <a href="{{ $event['booking_url'] }}" 
                               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-gray-500 text-lg font-medium mb-2">No upcoming events</h3>
                    <p class="text-gray-400 mb-4">Check back later for exciting events!</p>
                    <a href="{{ route('services.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse All Services
                    </a>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
