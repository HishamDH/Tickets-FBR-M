@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إضافة خدمة جديدة</h1>
                    <p class="text-gray-600 mt-1">أضف خدمة جديدة لتتمكن من استقبال الحجوزات</p>
                </div>
                <a href="{{ route('merchant.services.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('merchant.services.store') }}" enctype="multipart/form-data" class="space-y-6" onsubmit="console.log('Form submitted')">
                @csrf

                <!-- Service Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        اسم الخدمة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل اسم الخدمة">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        وصف الخدمة <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4" 
                              required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="اكتب وصفاً مفصلاً عن الخدمة">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price and Category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                            السعر (ريال) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="{{ old('price') }}" 
                               min="0" 
                               step="0.01" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                            التصنيف <span class="text-red-500">*</span>
                        </label>
                        <select id="category" 
                                name="category" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">اختر التصنيف</option>
                            <option value="events" {{ old('category') == 'events' ? 'selected' : '' }}>فعاليات</option>
                            <option value="venues" {{ old('category') == 'venues' ? 'selected' : '' }}>قاعات ومواقع</option>
                            <option value="entertainment" {{ old('category') == 'entertainment' ? 'selected' : '' }}>ترفيه</option>
                            <option value="food" {{ old('category') == 'food' ? 'selected' : '' }}>طعام وشراب</option>
                            <option value="services" {{ old('category') == 'services' ? 'selected' : '' }}>خدمات</option>
                            <option value="tourism" {{ old('category') == 'tourism' ? 'selected' : '' }}>سياحة</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location and Capacity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                            الموقع
                        </label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="أدخل موقع الخدمة">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-semibold text-gray-700 mb-2">
                            السعة (عدد الأشخاص)
                        </label>
                        <input type="number" 
                               id="capacity" 
                               name="capacity" 
                               value="{{ old('capacity') }}" 
                               min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="العدد الأقصى للحضور">
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Service Image -->
                <div>
                    <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                        صورة الخدمة
                    </label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">اختر صورة واضحة تعبر عن الخدمة (اختياري)</p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Features -->
                <div>
                    <label for="features" class="block text-sm font-semibold text-gray-700 mb-2">
                        المميزات والخصائص
                    </label>
                    <textarea id="features" 
                              name="features" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="اكتب مميزات الخدمة مفصولة بفواصل (مثال: موقف مجاني، واي فاي، مكيف)">{{ old('features') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">اكتب كل ميزة في سطر منفصل أو افصل بينها بفواصل</p>
                    @error('features')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">إعدادات الخدمة</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="mr-2 block text-sm text-gray-900">
                                خدمة نشطة
                            </label>
                        </div>

                        <!-- Available -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_available" 
                                   name="is_available" 
                                   value="1" 
                                   {{ old('is_available', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_available" class="mr-2 block text-sm text-gray-900">
                                متاحة للحجز
                            </label>
                        </div>

                        <!-- Featured -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_featured" class="mr-2 block text-sm text-gray-900">
                                خدمة مميزة
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-gray-200">
                    <a href="{{ route('merchant.services.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-semibold transition duration-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                        إضافة الخدمة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection