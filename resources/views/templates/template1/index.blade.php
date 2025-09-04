<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $merchant->business_name ?? 'متجر التاجر' }} - شباك التذاكر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .merchant-theme {
            --primary-color: {{ $merchant->theme_color ?? '#F97316' }};
            --secondary-color: {{ $merchant->secondary_color ?? '#FB923C' }};
        }
        
        .bg-merchant-primary { background-color: var(--primary-color); }
        .text-merchant-primary { color: var(--primary-color); }
        .border-merchant-primary { border-color: var(--primary-color); }
        
        .merchant-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
    </style>
</head>
<body class="bg-gray-50 merchant-theme">
    <!-- Merchant Header -->
    <header class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Merchant Logo & Name -->
                <div class="flex items-center space-x-reverse space-x-4">
                    @if($merchant->logo)
                        <img src="{{ asset('storage/' . $merchant->logo) }}" 
                             alt="{{ $merchant->business_name }}" 
                             class="h-12 w-12 rounded-full object-cover">
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-merchant-primary">{{ $merchant->business_name }}</h1>
                        <p class="text-gray-600 text-sm">{{ $merchant->business_type }}</p>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-reverse space-x-8">
                    <a href="#services" class="text-gray-700 hover:text-merchant-primary transition duration-300">الخدمات</a>
                    <a href="#about" class="text-gray-700 hover:text-merchant-primary transition duration-300">عن المتجر</a>
                    <a href="#contact" class="text-gray-700 hover:text-merchant-primary transition duration-300">تواصل معنا</a>
                </nav>
                
                <!-- Cart & Login -->
                <div class="flex items-center space-x-reverse space-x-4">
                    <livewire:cart-icon />
                    @guest
                        <a href="{{ route('customer.login') }}" class="text-gray-700 hover:text-merchant-primary">تسجيل الدخول</a>
                    @else
                        <div class="relative">
                            <button class="flex items-center text-gray-700 hover:text-merchant-primary">
                                {{ Auth::user()->name }}
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Merchant Hero Section -->
    <section class="merchant-gradient py-16 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></g></svg>');"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                @if($merchant->cover_image)
                    <div class="mb-8">
                        <img src="{{ asset('storage/' . $merchant->cover_image) }}" 
                             alt="Cover" 
                             class="w-full max-w-4xl mx-auto rounded-lg shadow-2xl">
                    </div>
                @endif
                
                <h2 class="text-4xl md:text-6xl font-bold mb-6">
                    {{ $merchant->slogan ?? 'مرحباً بكم في متجرنا' }}
                </h2>
                
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
                    {{ $merchant->description ?? 'نقدم لكم أفضل الخدمات بجودة عالية وأسعار منافسة' }}
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#services" class="bg-white text-merchant-primary px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition duration-300 shadow-lg">
                        تصفح الخدمات
                    </a>
                    <a href="#contact" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-merchant-primary transition duration-300">
                        تواصل معنا
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">خدماتنا</h3>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">اكتشف مجموعة متنوعة من الخدمات المميزة التي نقدمها</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($offerings as $offering)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300 group">
                        @if($offering->featured_image)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ asset('storage/' . $offering->featured_image) }}" 
                                     alt="{{ $offering->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $offering->title }}</h4>
                            <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($offering->description, 120) }}</p>
                            
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-2xl font-bold text-merchant-primary">{{ number_format($offering->price) }} ر.س</span>
                                @if($offering->rating)
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
                                        </svg>
                                        <span class="mr-1 text-gray-600">{{ number_format($offering->rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex space-x-reverse space-x-2">
                                <a href="{{ route('services.show', $offering) }}" class="flex-1 bg-merchant-primary text-white py-2 px-4 rounded-lg text-center font-semibold hover:opacity-90 transition duration-300">
                                    عرض التفاصيل
                                </a>
                                <button onclick="addToCart({{ $offering->id }})" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13v8a2 2 0 002 2h8a2 2 0 002-2v-8"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">لا توجد خدمات متاحة حالياً</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">عن {{ $merchant->business_name }}</h3>
                    <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                        {{ $merchant->about ?? 'نحن شركة رائدة في تقديم أفضل الخدمات بجودة عالية وأسعار منافسة. نسعى دائماً لتحقيق رضا عملائنا وتقديم تجربة استثنائية.' }}
                    </p>
                    
                    @if($merchant->branches->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-3">فروعنا</h4>
                            @foreach($merchant->branches as $branch)
                                <div class="mb-2">
                                    <p class="text-gray-700"><strong>{{ $branch->name }}:</strong> {{ $branch->address }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="flex space-x-reverse space-x-4">
                        @if($merchant->facebook_url)
                            <a href="{{ $merchant->facebook_url }}" class="text-blue-600 hover:text-blue-800 transition duration-300">
                                <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        @endif
                        
                        @if($merchant->instagram_url)
                            <a href="{{ $merchant->instagram_url }}" class="text-pink-600 hover:text-pink-800 transition duration-300">
                                <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.611-3.197-1.559-.748-.948-1.007-2.18-.693-3.328.314-1.148 1.170-2.135 2.305-2.656 1.135-.521 2.448-.521 3.583 0 1.135.521 1.991 1.508 2.305 2.656.314 1.148.055 2.38-.693 3.328-.749.948-1.9 1.559-3.197 1.559H8.449z"/>
                                </svg>
                            </a>
                        @endif
                        
                        @if($merchant->whatsapp_number)
                            <a href="https://wa.me/{{ $merchant->whatsapp_number }}" class="text-green-600 hover:text-green-800 transition duration-300">
                                <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.520-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.51 3.687"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                
                @if($merchant->profile_image)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $merchant->profile_image) }}" 
                             alt="Profile" 
                             class="max-w-full h-auto rounded-lg shadow-xl">
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">تواصل معنا</h3>
                <p class="text-xl text-gray-600">نحن هنا للمساعدة والإجابة على جميع استفساراتكم</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h4 class="text-2xl font-bold text-gray-900 mb-6">معلومات التواصل</h4>
                    
                    @if($merchant->phone)
                        <div class="flex items-center mb-4">
                            <svg class="h-6 w-6 text-merchant-primary ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-gray-700">{{ $merchant->phone }}</span>
                        </div>
                    @endif
                    
                    @if($merchant->email)
                        <div class="flex items-center mb-4">
                            <svg class="h-6 w-6 text-merchant-primary ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.78c.33.2.74.2 1.07 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-700">{{ $merchant->email }}</span>
                        </div>
                    @endif
                    
                    @if($merchant->business_address)
                        <div class="flex items-start">
                            <svg class="h-6 w-6 text-merchant-primary ml-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-gray-700">{{ $merchant->business_address }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h4 class="text-2xl font-bold text-gray-900 mb-6">أرسل رسالة</h4>
                    
                    <form class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">الاسم</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-merchant-primary">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">البريد الإلكتروني</label>
                            <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-merchant-primary">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">الرسالة</label>
                            <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-merchant-primary"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-merchant-primary text-white py-3 px-6 rounded-lg font-bold hover:opacity-90 transition duration-300">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">
                جميع الحقوق محفوظة © {{ date('Y') }} {{ $merchant->business_name }} - مدعوم بواسطة شباك التذاكر
            </p>
        </div>
    </footer>

    <!-- Cart Functionality -->
    <script>
        function addToCart(offeringId) {
            // Add to cart functionality
            fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    item_id: offeringId,
                    item_type: 'App\\Models\\Offering',
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart UI
                    showNotification('تم إضافة العنصر إلى السلة', 'success');
                } else {
                    showNotification('فشل في إضافة العنصر', 'error');
                }
            });
        }
        
        function showNotification(message, type) {
            // Simple notification system
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>