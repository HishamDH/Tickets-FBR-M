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
                                <span class="mr-1 text-sm font-medium text-gray-500 md:mr-2">كتابة تقييم</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="bg-white rounded-2xl shadow-sm border p-6 lg:p-10">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">كتابة تقييم للخدمة</h1>
                    <div class="flex items-center">
                        <div class="mr-4">
                            <div class="w-16 h-16 bg-orange-100 text-orange-500 rounded-lg flex items-center justify-center text-2xl">
                                @if($service->category == 'Venues')
                                    🏰
                                @elseif($service->category == 'Catering')
                                    🍽️
                                @elseif($service->category == 'Photography')
                                    📸
                                @elseif($service->category == 'Entertainment')
                                    🎵
                                @elseif($service->category == 'Planning')
                                    📋
                                @else
                                    ⭐
                                @endif
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $service->name }}</h2>
                            <p class="text-gray-600">{{ $service->category }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('reviews.store', $service) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    <!-- Rating Selection -->
                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-3">تقييمك للخدمة*</label>
                        <div class="flex flex-wrap gap-4">
                            @for ($i = 5; $i >= 1; $i--)
                                <div class="relative">
                                    <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" class="peer sr-only" required {{ old('rating') == $i ? 'checked' : '' }}>
                                    <label for="rating-{{ $i }}" class="flex flex-col items-center justify-center p-4 w-20 h-24 border-2 rounded-xl cursor-pointer border-gray-300 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50 transition-all">
                                        <div class="text-2xl mb-1">
                                            @if($i == 5)
                                                😍
                                            @elseif($i == 4)
                                                😊
                                            @elseif($i == 3)
                                                😐
                                            @elseif($i == 2)
                                                😕
                                            @else
                                                😔
                                            @endif
                                        </div>
                                        <span class="font-medium text-gray-700">{{ $i }} نجوم</span>
                                    </label>
                                </div>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label for="review" class="block text-lg font-semibold text-gray-800 mb-3">اكتب تقييمك (اختياري)</label>
                        <textarea 
                            id="review" 
                            name="review" 
                            rows="5" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="شاركنا تجربتك مع هذه الخدمة..."
                        >{{ old('review') }}</textarea>
                        @error('review')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-3">أضف صور (اختياري)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="review-images" class="flex flex-col items-center justify-center w-full h-36 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">اضغط لاختيار الصور</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, JPEG (الحد الأقصى: 5 ميجابايت لكل صورة)</p>
                                </div>
                                <input id="review-images" name="images[]" type="file" class="hidden" accept="image/*" multiple />
                            </label>
                        </div>
                        <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                        @error('images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="mr-3">
                                <p class="text-sm text-yellow-700">
                                    سيتم مراجعة تقييمك من قبل الإدارة قبل نشره. يرجى الالتزام بسياسات المنصة.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-wrap gap-4">
                        <button type="submit" class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow transition-colors">
                            نشر التقييم
                        </button>
                        <a href="{{ route('services.show', $service) }}" class="px-8 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview functionality
            const input = document.getElementById('review-images');
            const previewContainer = document.getElementById('image-preview-container');
            
            input.addEventListener('change', function() {
                previewContainer.innerHTML = '';
                
                if (this.files) {
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    
                    Array.from(this.files).forEach(file => {
                        if (file.size > maxSize) {
                            alert(`الملف "${file.name}" أكبر من الحد المسموح (5 ميجابايت)`);
                            return;
                        }
                        
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative group';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'h-32 w-full object-cover rounded-lg';
                            div.appendChild(img);
                            
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'absolute top-1 right-1 bg-white rounded-full p-1 shadow opacity-0 group-hover:opacity-100 transition-opacity';
                            removeBtn.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                            removeBtn.addEventListener('click', function() {
                                div.remove();
                            });
                            div.appendChild(removeBtn);
                            
                            previewContainer.appendChild(div);
                        }
                        
                        reader.readAsDataURL(file);
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
