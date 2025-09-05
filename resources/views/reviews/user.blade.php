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
                                <a href="{{ route('profile.show') }}" class="mr-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:mr-2">
                                    حسابي
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-sm font-medium text-gray-500 md:mr-2">تقييماتي</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="bg-white rounded-2xl shadow-sm border p-6 lg:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">تقييماتي</h1>
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-medium text-gray-600">
                            إجمالي التقييمات: {{ $reviews->total() }}
                        </span>
                    </div>
                </div>
                
                @if ($reviews->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📝</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">لم تقم بإضافة أي تقييمات بعد</h3>
                        <p class="text-gray-600 mb-6">يمكنك تقييم الخدمات التي استخدمتها ومشاركة تجربتك مع الآخرين</p>
                        
                        <a href="{{ route('services.index') }}" class="inline-block px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition-colors">
                            استعرض الخدمات
                        </a>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($reviews as $review)
                            <div class="bg-white border rounded-lg shadow-sm p-5 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900 mb-1">
                                            <a href="{{ route('services.show', $review->service) }}" class="hover:text-orange-600 transition-colors">
                                                {{ $review->service->name }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center">
                                            <div class="flex mr-2">
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
                                            <span class="text-sm text-gray-500">{{ $review->created_at->format('Y/m/d') }}</span>
                                            
                                            @if (!$review->is_approved)
                                                <span class="inline-flex items-center mr-3 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    قيد المراجعة
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-2 space-x-reverse">
                                        @if ($review->created_at->diffInDays(now()) <= 7)
                                            <a href="{{ route('reviews.edit', $review) }}" class="text-blue-600 hover:text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                @if ($review->review)
                                    <div class="text-gray-700 mb-4">
                                        {{ $review->review }}
                                    </div>
                                @endif
                                
                                @if ($review->images->isNotEmpty())
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mb-4">
                                        @foreach ($review->images as $image)
                                            <a href="{{ $image->url }}" data-lightbox="review-{{ $review->id }}" class="block">
                                                <img src="{{ $image->url }}" alt="صورة التقييم" class="h-20 w-full object-cover rounded-md hover:opacity-90 transition">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
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
