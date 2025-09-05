<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Breadcrumb -->
        <div class="bg-white border-b shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 space-x-reverse">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                الرئيسية
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('services.show', $service) }}" class="mr-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:mr-2">
                                    {{ $service->name }}
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-sm font-medium text-gray-500 md:mr-2">تقييمات الخدمة</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Rating Summary Card -->
                <div class="bg-white rounded-2xl shadow-sm border p-6 lg:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">ملخص التقييمات</h2>
                    
                    @php
                        $avgRating = \App\Models\Review::getAverageRating($service->id);
                        $ratingDistribution = \App\Models\Review::getRatingDistribution($service->id);
                        $totalRatings = array_sum($ratingDistribution);
                    @endphp
                    
                    <!-- Average Rating -->
                    <div class="flex flex-col items-center mb-8 pb-8 border-b">
                        <div class="text-5xl font-bold text-orange-500 mb-2">
                            {{ number_format($avgRating, 1) }}
                        </div>
                        <div class="flex items-center mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($avgRating))
                                    <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <p class="text-gray-600">
                            {{ $totalRatings }} {{ $totalRatings == 1 ? 'تقييم' : ($totalRatings >= 3 && $totalRatings <= 10 ? 'تقييمات' : 'تقييم') }}
                        </p>
                    </div>
                    
                    <!-- Rating Distribution -->
                    <div class="space-y-3">
                        @for ($i = 5; $i >= 1; $i--)
                            <div class="flex items-center">
                                <span class="text-sm text-gray-700 w-12">{{ $i }} نجوم</span>
                                <div class="flex-grow mx-3">
                                    <div class="h-2.5 rounded-full bg-gray-200">
                                        @php
                                            $percentage = $totalRatings > 0 ? ($ratingDistribution[$i] / $totalRatings) * 100 : 0;
                                        @endphp
                                        <div class="h-2.5 rounded-full {{ $i >= 4 ? 'bg-green-500' : ($i == 3 ? 'bg-yellow-400' : 'bg-red-500') }}" 
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 min-w-[2rem] text-left">{{ $ratingDistribution[$i] }}</span>
                            </div>
                        @endfor
                    </div>
                    
                    <!-- Write Review Button -->
                    @auth
                        <div class="mt-8">
                            <a href="{{ route('reviews.create', $service) }}" class="w-full block text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition-colors">
                                كتابة تقييم جديد
                            </a>
                        </div>
                    @else
                        <div class="mt-8">
                            <a href="{{ route('login') }}?redirect_to={{ urlencode(route('reviews.create', $service)) }}" class="w-full block text-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition-colors">
                                سجل دخول لكتابة تقييم
                            </a>
                        </div>
                    @endauth
                </div>
                
                <!-- Reviews List -->
                <div class="lg:col-span-2">
                    @if ($reviews->isEmpty())
                        <div class="bg-white rounded-2xl shadow-sm border p-8 text-center">
                            <div class="text-6xl mb-4">📝</div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">لا توجد تقييمات بعد</h3>
                            <p class="text-gray-600 mb-6">كن أول من يقيم هذه الخدمة ومساعدة الآخرين باتخاذ قرارهم!</p>
                            
                            @auth
                                <a href="{{ route('reviews.create', $service) }}" class="inline-block px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition-colors">
                                    كتابة تقييم الآن
                                </a>
                            @else
                                <a href="{{ route('login') }}?redirect_to={{ urlencode(route('reviews.create', $service)) }}" class="inline-block px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition-colors">
                                    سجل دخول لكتابة تقييم
                                </a>
                            @endauth
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($reviews as $review)
                                <div class="bg-white rounded-2xl shadow-sm border p-6">
                                    <div class="flex items-start mb-4">
                                        <div class="mr-4">
                                            @if ($review->customer->avatar)
                                                <img src="{{ asset('storage/' . $review->customer->avatar) }}" alt="{{ $review->customer->name }}" class="w-12 h-12 rounded-full object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                                    <span class="text-orange-600 font-semibold text-lg">
                                                        {{ substr($review->customer->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-lg text-gray-900">{{ $review->customer->name }}</h4>
                                            <div class="flex items-center mt-1">
                                                <div class="flex">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $review->rating)
                                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-500 mr-2">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if ($review->review)
                                        <div class="text-gray-700 mb-4">
                                            {{ $review->review }}
                                        </div>
                                    @endif
                                    
                                    @if ($review->images->isNotEmpty())
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 mb-4">
                                            @foreach ($review->images as $image)
                                                <a href="{{ $image->url }}" data-lightbox="review-{{ $review->id }}" class="block">
                                                    <img src="{{ $image->url }}" alt="مرفق التقييم" class="h-24 w-full object-cover rounded-lg hover:opacity-90 transition">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <!-- Actions -->
                                    @auth
                                        @if ($review->user_id == auth()->id())
                                            <div class="flex space-x-4 space-x-reverse pt-4 border-t mt-4">
                                                <a href="{{ route('reviews.edit', $review) }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                                    <i class="fas fa-edit ml-1"></i> تعديل
                                                </a>
                                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">
                                                        <i class="fas fa-trash ml-1"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            @endforeach
                            
                            <!-- Pagination -->
                            <div class="mt-8">
                                {{ $reviews->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    @endpush
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': "صورة %1 من %2"
            });
        });
    </script>
    @endpush
</x-app-layout>
