@extends('filament::page')

@section('content')
    <div>
        <h1 class="text-2xl font-bold mb-4">معالج إنشاء عرض جديد</h1>
        <div class="bg-white rounded shadow p-6">
            <form method="POST" action="{{ route('offering.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">اسم العرض</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">الموقع</label>
                        <input type="text" name="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">الخطوة 2: الأسعار والتوقيت</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">السعر</label>
                            <input type="number" name="price" id="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">وقت البداية</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">وقت النهاية</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">الخطوة 3: إعدادات المقاعد</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="has_chairs" class="block text-sm font-medium text-gray-700">يحتوي على مقاعد</label>
                            <select name="has_chairs" id="has_chairs" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div>
                            <label for="chairs_count" class="block text-sm font-medium text-gray-700">عدد المقاعد</label>
                            <input type="number" name="chairs_count" id="chairs_count" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow">التالي</button>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">الخطوة 4: إعدادات أخرى</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">الحالة</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">المالك</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <!-- خيارات المستخدمين ستُضاف هنا -->
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">الصورة</label>
                        <input type="file" name="image" id="image" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow">إنهاء</button>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">الخطوة 5: الفئات والأنواع</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">نوع العرض</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="events">فعاليات</option>
                                <option value="conference">مؤتمرات</option>
                                <option value="restaurant">مطاعم</option>
                                <option value="experiences">تجارب</option>
                            </select>
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">الفئة</label>
                            <input type="text" name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow">التالي</button>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">الخطوة 6: الإعدادات الإضافية</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="additional_settings" class="block text-sm font-medium text-gray-700">إعدادات إضافية</label>
                            <textarea name="additional_settings" id="additional_settings" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow">التالي</button>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4">مراجعة البيانات</h2>
                    <div class="bg-white shadow rounded p-6">
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>اسم العرض:</strong> <span id="review-name" class="text-gray-700"></span></li>
                            <li><strong>الموقع:</strong> <span id="review-location" class="text-gray-700"></span></li>
                            <li><strong>الوصف:</strong> <span id="review-description" class="text-gray-700"></span></li>
                            <li><strong>السعر:</strong> <span id="review-price" class="text-gray-700"></span></li>
                            <li><strong>وقت البداية:</strong> <span id="review-start-time" class="text-gray-700"></span></li>
                            <li><strong>وقت النهاية:</strong> <span id="review-end-time" class="text-gray-700"></span></li>
                            <li><strong>الحالة:</strong> <span id="review-status" class="text-gray-700"></span></li>
                            <li><strong>المالك:</strong> <span id="review-owner" class="text-gray-700"></span></li>
                        </ul>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded shadow">إرسال</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            alert('تم إرسال البيانات بنجاح!');
        });

        const steps = document.querySelectorAll('.wizard-step');
        steps.forEach((step, index) => {
            const nextButton = step.querySelector('.next-button');
            if (nextButton) {
                nextButton.addEventListener('click', function () {
                    alert(`تم الانتقال إلى الخطوة ${index + 2}`);
                });
            }
        });

        const fields = ['name', 'location', 'description', 'price', 'start_time', 'end_time', 'status', 'user_id'];
        fields.forEach(field => {
            const input = document.getElementById(field);
            const review = document.getElementById(`review-${field}`);
            if (input && review) {
                review.textContent = input.value;
            }
        });
    });
</script>

<style>
    body {
        transition: background-color 0.3s, color 0.3s;
    }

    body.dark {
        background-color: #1a202c;
        color: #cbd5e0;
    }

    .wizard-step {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        transition: background-color 0.3s, border-color 0.3s;
    }

    body.dark .wizard-step {
        background-color: #2d3748;
        border-color: #4a5568;
    }

    .dark-mode-toggle {
        cursor: pointer;
        padding: 8px 16px;
        background-color: #4a5568;
        color: #ffffff;
        border-radius: 4px;
        border: none;
        transition: background-color 0.3s;
    }

    .dark-mode-toggle:hover {
        background-color: #2d3748;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.createElement('button');
        toggleButton.textContent = 'تبديل الوضع الليلي';
        toggleButton.className = 'dark-mode-toggle';
        document.body.prepend(toggleButton);

        toggleButton.addEventListener('click', function () {
            document.body.classList.toggle('dark');
        });
    });
</script>
