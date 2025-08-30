@extends('layouts.public')

@section('title', 'شباك التذاكر - منصة الحجوزات الذكية')
@section('description', 'منصة متطورة تربط بين مقدمي الخدمات والعملاء - احجز خدماتك بسهولة وأمان مع تجربة استثنائية')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden hero-pattern">
    <!-- Background Animation Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="floating-animation absolute top-1/4 left-1/4 w-32 h-32 bg-orange-500/10 rounded-full"></div>
        <div class="floating-animation absolute top-1/3 right-1/4 w-24 h-24 bg-orange-400/10 rounded-full" style="animation-delay: 1s;"></div>
        <div class="floating-animation absolute bottom-1/4 left-1/3 w-40 h-40 bg-orange-300/10 rounded-full" style="animation-delay: 2s;"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 text-center">
        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
            <!-- Main Heading -->
            <div x-show="loaded" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 transform translate-y-12"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 class="mb-8">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                    <span class="gradient-text">شباك التذاكر</span>
                    <br>
                    <span class="text-gray-800 text-4xl md:text-5xl">منصة الحجوزات الذكية</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                    اكتشف عالماً من الخدمات المتميزة واحجز تجربتك المثالية بنقرة واحدة
                </p>
            </div>

            <!-- CTA Buttons -->
            <div x-show="loaded" 
                 x-transition:enter="transition ease-out duration-1000 delay-300"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="{{ route('services.index') }}" 
                   class="group btn-primary px-8 py-4 text-lg font-bold pulse-glow">
                    <span class="flex items-center">
                        اكتشف الخدمات
                        <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </a>
                <button @click="document.getElementById('features').scrollIntoView({behavior: 'smooth'})" 
                        class="border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-8 py-4 rounded-lg text-lg font-bold transition-all duration-300 bg-white/80 backdrop-blur-sm">
                    كيف نعمل
                </button>
            </div>

            <!-- Quick Stats -->
            <div x-show="loaded" 
                 x-transition:enter="transition ease-out duration-1000 delay-500"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                @php
                    $stats = [
                        ['number' => '1000+', 'label' => 'عميل سعيد', 'icon' => '😊'],
                        ['number' => '150+', 'label' => 'خدمة متاحة', 'icon' => '🎯'],
                        ['number' => '20+', 'label' => 'مدينة مغطاة', 'icon' => '🏙️'],
                        ['number' => '24/7', 'label' => 'دعم متواصل', 'icon' => '🚀']
                    ]
                @endphp
                @foreach($stats as $stat)
                <div class="glass-effect rounded-xl p-6 text-center card-hover">
                    <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
                    <div class="text-2xl font-bold gradient-text mb-1">{{ $stat['number'] }}</div>
                    <div class="text-sm text-gray-600">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <span class="gradient-text">لماذا شباك التذاكر؟</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                نحن نقدم تجربة حجز متكاملة تجمع بين السهولة والأمان والجودة
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @php
                $features = [
                    [
                        'icon' => '🎯',
                        'title' => 'سهولة الحجز',
                        'description' => 'واجهة بسيطة وسهلة الاستخدام تمكنك من الحجز في دقائق معدودة',
                        'gradient' => 'from-blue-500 to-purple-600'
                    ],
                    [
                        'icon' => '🔒',
                        'title' => 'دفع آمن',
                        'description' => 'نظام دفع متقدم ومتعدد الخيارات مع ضمان أمان المعاملات',
                        'gradient' => 'from-green-500 to-teal-600'
                    ],
                    [
                        'icon' => '⭐',
                        'title' => 'جودة مضمونة',
                        'description' => 'شركاء معتمدين وخدمات عالية الجودة مع ضمان الرضا',
                        'gradient' => 'from-orange-500 to-red-600'
                    ]
                ]
            @endphp

            @foreach($features as $index => $feature)
            <div x-data="{ inView: false }" 
                 x-intersect="inView = true"
                 x-show="inView"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 style="transition-delay: {{ $index * 200 }}ms"
                 class="text-center card-hover">
                <div class="bg-gradient-to-br {{ $feature['gradient'] }} w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 text-white text-3xl shadow-lg">
                    {{ $feature['icon'] }}
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-800">{{ $feature['title'] }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $feature['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <span class="gradient-text">الخدمات المميزة</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                اختر من مجموعة واسعة من الخدمات المتميزة المقدمة من شركائنا المعتمدين
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @php
                $services = [
                    [
                        'name' => 'قاعات الأفراح الفاخرة',
                        'description' => 'قاعات مجهزة بأحدث التقنيات والديكورات الأنيقة لجعل مناسبتك لا تُنسى',
                        'price' => '15,000',
                        'image' => '🏰',
                        'badge' => 'الأكثر طلباً',
                        'features' => ['إضاءة متطورة', 'نظام صوتي عالي الجودة', 'ديكورات حصرية']
                    ],
                    [
                        'name' => 'خدمات الطعام المتميزة',
                        'description' => 'أشهى الأطباق التراثية والعالمية مع خدمة راقية',
                        'price' => '250',
                        'image' => '🍽️',
                        'badge' => 'جودة عالية',
                        'features' => ['طعام حلال معتمد', 'شيفات محترفين', 'خدمة VIP']
                    ],
                    [
                        'name' => 'التصوير الاحترافي',
                        'description' => 'التقاط أجمل اللحظات بجودة سينمائية عالية',
                        'price' => '3,500',
                        'image' => '📸',
                        'badge' => 'محترف',
                        'features' => ['كاميرات 4K', 'مونتاج احترافي', 'فريق متخصص']
                    ]
                ]
            @endphp

            @foreach($services as $index => $service)
            <div x-data="{ inView: false }" 
                 x-intersect="inView = true"
                 x-show="inView"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 style="transition-delay: {{ $index * 200 }}ms"
                 class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover group">
                
                <!-- Service Image/Icon -->
                <div class="h-48 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center relative">
                    <div class="text-6xl text-white/80">{{ $service['image'] }}</div>
                    <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-medium">
                        {{ $service['badge'] }}
                    </div>
                </div>

                <!-- Service Details -->
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-orange-600 transition-colors">
                        {{ $service['name'] }}
                    </h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">{{ $service['description'] }}</p>
                    
                    <!-- Features List -->
                    <ul class="space-y-2 mb-6">
                        @foreach($service['features'] as $feature)
                        <li class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <!-- Price and CTA -->
                    <div class="flex justify-between items-center">
                        <div>
                            @php
                                $price = $service['price'];
                                // Convert string with commas to integer
                                if (is_string($price)) {
                                    $price = (int) str_replace(',', '', $price);
                                }
                            @endphp
                            <span class="text-2xl font-bold gradient-text">{{ number_format($price) }}</span>
                            <span class="text-gray-500 text-sm">ريال سعودي</span>
                        </div>
                        <a href="{{ route('services.index') }}" 
                           class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            احجز الآن
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- View All Services Button -->
        <div class="text-center">
            <a href="{{ route('services.index') }}" 
               class="btn-primary px-8 py-4 text-lg font-bold inline-flex items-center">
                عرض جميع الخدمات
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <span class="gradient-text">كيف تعمل المنصة؟</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                عملية بسيطة في ثلاث خطوات للحصول على خدمتك المثالية
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            @php
                $steps = [
                    [
                        'number' => '1',
                        'title' => 'اكتشف واختر',
                        'description' => 'تصفح مجموعتنا الواسعة من الخدمات واختر ما يناسب احتياجاتك',
                        'icon' => '🔍'
                    ],
                    [
                        'number' => '2', 
                        'title' => 'احجز وادفع',
                        'description' => 'أكمل عملية الحجز بسهولة وادفع بأمان عبر وسائل الدفع المتعددة',
                        'icon' => '💳'
                    ],
                    [
                        'number' => '3',
                        'title' => 'استمتع بالخدمة',
                        'description' => 'استرخِ ودع فريقنا المحترف يقدم لك تجربة استثنائية',
                        'icon' => '✨'
                    ]
                ]
            @endphp

            @foreach($steps as $index => $step)
            <div x-data="{ inView: false }" 
                 x-intersect="inView = true"
                 x-show="inView"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-1 transform translate-x-0"
                 style="transition-delay: {{ $index * 300 }}ms"
                 class="flex items-center mb-12 {{ $index % 2 == 1 ? 'flex-row-reverse' : '' }}">
                
                <!-- Step Content -->
                <div class="flex-1 {{ $index % 2 == 1 ? 'text-right pr-12' : 'pl-12' }}">
                    <div class="flex items-center mb-4 {{ $index % 2 == 1 ? 'justify-end' : '' }}">
                        <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center text-white font-bold text-lg {{ $index % 2 == 1 ? 'order-2 mr-4' : 'ml-4' }}">
                            {{ $step['number'] }}
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $step['title'] }}</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-lg">{{ $step['description'] }}</p>
                </div>

                <!-- Step Visual -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center text-4xl shadow-lg">
                        {{ $step['icon'] }}
                    </div>
                </div>
            </div>
            @if($index < count($steps) - 1)
            <div class="flex justify-center mb-8">
                <svg class="w-8 h-8 text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-orange-500 to-orange-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 hero-pattern opacity-20"></div>
    
    <div class="relative container mx-auto px-4 text-center text-white">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            جاهز لبدء رحلتك معنا؟
        </h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto opacity-90">
            انضم إلى آلاف العملاء السعداء واكتشف تجربة حجز جديدة مع شباك التذاكر
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('services.index') }}" 
               class="bg-white text-orange-600 hover:bg-gray-100 px-8 py-4 rounded-lg text-lg font-bold transition-colors shadow-lg">
                ابدأ الحجز الآن
            </a>
            <a href="#" 
               class="border-2 border-white text-white hover:bg-white hover:text-orange-600 px-8 py-4 rounded-lg text-lg font-bold transition-colors">
               تواصل معنا
            </a>
        </div>
    </div>
</section>
@endsection
