<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار إضافة خدمة</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">اختبار إضافة خدمة</h1>
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('merchant.services.store') }}" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">اسم الخدمة</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">وصف الخدمة</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" required></textarea>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700">التصنيف</label>
                <select name="category" id="category" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" required>
                    <option value="">اختر التصنيف</option>
                    <option value="events">فعاليات</option>
                    <option value="venues">قاعات ومواقع</option>
                    <option value="entertainment">ترفيه</option>
                    <option value="food">طعام وشراب</option>
                    <option value="services">خدمات</option>
                    <option value="tourism">سياحة</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">السعر</label>
                <input type="number" name="price" id="price" min="0" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">الموقع</label>
                <input type="text" name="location" id="location" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div class="mb-4">
                <label for="capacity" class="block text-sm font-medium text-gray-700">السعة</label>
                <input type="number" name="capacity" id="capacity" min="1" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div class="mb-4">
                <label for="features" class="block text-sm font-medium text-gray-700">المميزات</label>
                <textarea name="features" id="features" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" placeholder="افصل المميزات بفواصل"></textarea>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="mr-2" checked>
                    <span class="text-sm text-gray-700">خدمة نشطة</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_available" value="1" class="mr-2" checked>
                    <span class="text-sm text-gray-700">متاحة للحجز</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" class="mr-2">
                    <span class="text-sm text-gray-700">خدمة مميزة</span>
                </label>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">صورة الخدمة</label>
                <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                إضافة الخدمة
            </button>
        </form>
    </div>
</body>
</html>