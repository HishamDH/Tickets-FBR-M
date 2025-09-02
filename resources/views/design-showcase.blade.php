@extends('layouts.app')

@section('title', 'معرض التصميم الإبداعي')
@section('description', 'استعراض التصميم الجديد بالطابع البرتقالي والأبيض مع التأثيرات الإبداعية')

@section('content')
<div class="min-h-screen bg-gradient-soft">
    
    <!-- 🎨 Hero Section -->
    <section class="relative overflow-hidden bg-gradient-fire text-white py-24 mb-16">
        <!-- Animated Background Shapes -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-orange-warm opacity-20 rounded-full floating"></div>
            <div class="absolute -bottom-32 -left-32 w-60 h-60 bg-orange-sunset opacity-30 shape-hexagon floating" style="animation-delay: 1s;"></div>
            <div class="absolute top-20 left-1/3 w-32 h-32 bg-white opacity-10 shape-diamond floating" style="animation-delay: 2s;"></div>
        </div>
        
        <!-- Wave Decoration -->
        <div class="wave-decoration"></div>
        
        <!-- Content -->
        <div class="relative z-10 container mx-auto px-6 text-center">
            <div class="mb-8">
                <h1 class="text-6xl font-black mb-6 bounce-in">
                    🎨 التصميم الإبداعي الجديد
                </h1>
                <p class="text-2xl text-orange-100 mb-8 fade-in-up" style="animation-delay: 0.3s;">
                    تصميم بسيط وإبداعي بالطابع البرتقالي والأبيض مع درجات ألوان متطورة
                </p>
                <div class="flex justify-center space-x-6 space-x-reverse">
                    <button class="btn btn-primary px-8 py-4 sparkle fire-glow pulse-orange slide-in-right" style="animation-delay: 0.5s;">
                        <i class="fas fa-rocket ml-2"></i>
                        استكشف التصميم
                    </button>
                    <button class="btn btn-outline px-8 py-4 fire-trail slide-in-right" style="animation-delay: 0.7s;">
                        <i class="fas fa-palette ml-2"></i>
                        لوحة الألوان
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div class="container mx-auto px-6 pb-16">
        
        <!-- 🌈 Color Palette Section -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-fire mb-4">
                    🌈 لوحة الألوان الإبداعية
                </h2>
                <p class="text-xl text-gray-600">درجات البرتقالي والأبيض مع تدرجات مبتكرة</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
                <!-- Primary Orange Colors -->
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.1s;">
                    <div class="w-full h-20 bg-primary-50 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-gray-800">Primary 50</p>
                    <code class="text-xs text-gray-500">#fff7ed</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.2s;">
                    <div class="w-full h-20 bg-primary-100 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-gray-800">Primary 100</p>
                    <code class="text-xs text-gray-500">#ffedd5</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.3s;">
                    <div class="w-full h-20 bg-primary-200 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-gray-800">Primary 200</p>
                    <code class="text-xs text-gray-500">#fed7aa</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.4s;">
                    <div class="w-full h-20 bg-primary-300 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-gray-800">Primary 300</p>
                    <code class="text-xs text-gray-500">#fdba74</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.5s;">
                    <div class="w-full h-20 bg-primary-400 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-gray-800">Primary 400</p>
                    <code class="text-xs text-gray-500">#fb923c</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.6s;">
                    <div class="w-full h-20 bg-primary-500 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-white">Primary 500</p>
                    <code class="text-xs text-orange-100">#f97316</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.7s;">
                    <div class="w-full h-20 bg-primary-600 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-white">Primary 600</p>
                    <code class="text-xs text-orange-100">#ea580c</code>
                </div>
                
                <div class="card p-6 text-center bounce-in" style="animation-delay: 0.8s;">
                    <div class="w-full h-20 bg-primary-700 rounded-xl mb-4 interactive-element"></div>
                    <p class="font-semibold text-white">Primary 700</p>
                    <code class="text-xs text-orange-100">#c2410c</code>
                </div>
            </div>
        </section>

        <!-- 🔥 Gradient Showcase -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-fire mb-4">
                    🔥 التدرجات الإبداعية
                </h2>
                <p class="text-xl text-gray-600">تدرجات لونية مبتكرة لتصميم عصري</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="card p-8 text-center bounce-in">
                    <div class="w-full h-32 bg-gradient-fire rounded-2xl mb-6 interactive-element flex items-center justify-center">
                        <span class="text-white font-bold text-lg">🔥 Fire</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Fire Gradient</h3>
                    <p class="text-gray-600 text-sm">تدرج النار الملتهب</p>
                </div>
                
                <div class="card p-8 text-center bounce-in" style="animation-delay: 0.1s;">
                    <div class="w-full h-32 bg-gradient-sunset rounded-2xl mb-6 interactive-element flex items-center justify-center">
                        <span class="text-white font-bold text-lg">🌅 Sunset</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Sunset Gradient</h3>
                    <p class="text-gray-600 text-sm">تدرج غروب الشمس</p>
                </div>
                
                <div class="card p-8 text-center bounce-in" style="animation-delay: 0.2s;">
                    <div class="w-full h-32 bg-gradient-warm rounded-2xl mb-6 interactive-element flex items-center justify-center">
                        <span class="text-white font-bold text-lg">🌡️ Warm</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Warm Gradient</h3>
                    <p class="text-gray-600 text-sm">تدرج الدفء</p>
                </div>
                
                <div class="card p-8 text-center bounce-in" style="animation-delay: 0.3s;">
                    <div class="w-full h-32 bg-gradient-soft rounded-2xl mb-6 interactive-element flex items-center justify-center">
                        <span class="text-gray-700 font-bold text-lg">🌸 Soft</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Soft Gradient</h3>
                    <p class="text-gray-600 text-sm">تدرج ناعم</p>
                </div>
            </div>
        </section>

        <!-- ✨ Interactive Elements -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-fire mb-4">
                    ✨ العناصر التفاعلية
                </h2>
                <p class="text-xl text-gray-600">أزرار وعناصر تفاعلية مع تأثيرات إبداعية</p>
            </div>
            
            <!-- Buttons Showcase -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="card p-8 text-center">
                    <h3 class="font-bold text-gray-800 mb-6">الأزرار الأساسية</h3>
                    <div class="space-y-4">
                        <button class="btn btn-primary w-full">
                            <i class="fas fa-rocket ml-2"></i>
                            زر أساسي
                        </button>
                        <button class="btn btn-fire w-full">
                            <i class="fas fa-fire ml-2"></i>
                            زر النار
                        </button>
                        <button class="btn btn-outline w-full">
                            <i class="fas fa-heart ml-2"></i>
                            زر محدد
                        </button>
                    </div>
                </div>
                
                <div class="card p-8 text-center">
                    <h3 class="font-bold text-gray-800 mb-6">التأثيرات الخاصة</h3>
                    <div class="space-y-4">
                        <button class="btn btn-primary w-full sparkle">
                            <i class="fas fa-star ml-2"></i>
                            زر متلألئ
                        </button>
                        <button class="btn btn-fire w-full fire-trail">
                            <i class="fas fa-bolt ml-2"></i>
                            زر مسار النار
                        </button>
                        <button class="btn btn-primary w-full pulse-orange">
                            <i class="fas fa-heartbeat ml-2"></i>
                            زر نابض
                        </button>
                    </div>
                </div>
                
                <div class="card p-8 text-center">
                    <h3 class="font-bold text-gray-800 mb-6">الأحجام المختلفة</h3>
                    <div class="space-y-4">
                        <button class="btn btn-primary w-full py-2 text-sm">
                            زر صغير
                        </button>
                        <button class="btn btn-primary w-full py-3">
                            زر عادي
                        </button>
                        <button class="btn btn-primary w-full py-4 text-lg">
                            زر كبير
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards Showcase -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card fire-glow interactive-card p-8">
                    <div class="w-16 h-16 bg-gradient-fire rounded-2xl flex items-center justify-center mb-6 floating">
                        <i class="fas fa-chart-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">بطاقة تفاعلية</h3>
                    <p class="text-gray-600 mb-6">بطاقة مع تأثير الوهج الناري والحركة التفاعلية</p>
                    <div class="flex items-center text-primary-500 font-semibold">
                        <i class="fas fa-arrow-left ml-2"></i>
                        اكتشف المزيد
                    </div>
                </div>
                
                <div class="card sparkle interactive-card p-8" style="animation-delay: 0.2s;">
                    <div class="w-16 h-16 bg-gradient-sunset rounded-2xl flex items-center justify-center mb-6 floating" style="animation-delay: 0.5s;">
                        <i class="fas fa-magic text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">بطاقة متلألئة</h3>
                    <p class="text-gray-600 mb-6">بطاقة مع تأثير التلألؤ والرموز المتحركة</p>
                    <div class="flex items-center text-primary-500 font-semibold">
                        <i class="fas fa-arrow-left ml-2"></i>
                        جرب التأثير
                    </div>
                </div>
                
                <div class="card pulse-orange interactive-card p-8" style="animation-delay: 0.4s;">
                    <div class="w-16 h-16 bg-gradient-warm rounded-2xl flex items-center justify-center mb-6 floating" style="animation-delay: 1s;">
                        <i class="fas fa-heart text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">بطاقة نابضة</h3>
                    <p class="text-gray-600 mb-6">بطاقة مع تأثير النبض البرتقالي المتواصل</p>
                    <div class="flex items-center text-primary-500 font-semibold">
                        <i class="fas fa-arrow-left ml-2"></i>
                        شاهد النبض
                    </div>
                </div>
            </div>
        </section>

        <!-- 🎭 Animations Showcase -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-fire mb-4">
                    🎭 معرض الحركات
                </h2>
                <p class="text-xl text-gray-600">حركات وانتقالات سلسة ومبتكرة</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card p-6 text-center floating">
                    <div class="text-4xl mb-4">🌊</div>
                    <h3 class="font-bold text-gray-800 mb-2">حركة الطفو</h3>
                    <p class="text-gray-600 text-sm">حركة عمودية هادئة</p>
                </div>
                
                <div class="card p-6 text-center bounce-in">
                    <div class="text-4xl mb-4">🎾</div>
                    <h3 class="font-bold text-gray-800 mb-2">دخول مرتد</h3>
                    <p class="text-gray-600 text-sm">دخول بتأثير الارتداد</p>
                </div>
                
                <div class="card p-6 text-center slide-in-right">
                    <div class="text-4xl mb-4">➡️</div>
                    <h3 class="font-bold text-gray-800 mb-2">انزلاق يميني</h3>
                    <p class="text-gray-600 text-sm">انزلاق من اليمين</p>
                </div>
                
                <div class="card p-6 text-center fade-in-up">
                    <div class="text-4xl mb-4">⬆️</div>
                    <h3 class="font-bold text-gray-800 mb-2">ظهور علوي</h3>
                    <p class="text-gray-600 text-sm">ظهور من الأسفل</p>
                </div>
            </div>
        </section>

        <!-- 🎨 Creative Shapes -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-fire mb-4">
                    🎨 الأشكال الإبداعية
                </h2>
                <p class="text-xl text-gray-600">أشكال هندسية مبتكرة لتصميم عصري</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card p-8 text-center">
                    <div class="w-32 h-32 bg-gradient-fire shape-hexagon mx-auto mb-6 interactive-element"></div>
                    <h3 class="font-bold text-gray-800 mb-2">الشكل السداسي</h3>
                    <p class="text-gray-600 text-sm">شكل سداسي هندسي متطور</p>
                </div>
                
                <div class="card p-8 text-center">
                    <div class="w-32 h-32 bg-gradient-sunset shape-diamond mx-auto mb-6 interactive-element"></div>
                    <h3 class="font-bold text-gray-800 mb-2">الشكل الماسي</h3>
                    <p class="text-gray-600 text-sm">شكل ماسي أنيق وجذاب</p>
                </div>
                
                <div class="card p-8 text-center">
                    <div class="w-32 h-32 bg-gradient-warm rounded-full mx-auto mb-6 interactive-element"></div>
                    <h3 class="font-bold text-gray-800 mb-2">الشكل الدائري</h3>
                    <p class="text-gray-600 text-sm">شكل دائري كلاسيكي</p>
                </div>
            </div>
        </section>

        <!-- 🎪 Call to Action -->
        <section class="text-center">
            <div class="card fire-glow p-12 bg-gradient-soft">
                <div class="mb-8">
                    <h2 class="text-4xl font-black text-gray-800 mb-4">
                        🎪 جاهز للاستخدام!
                    </h2>
                    <p class="text-xl text-gray-600 mb-8">
                        التصميم الجديد جاهز بكامل مميزاته الإبداعية والتفاعلية
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <button class="btn btn-primary px-12 py-4 sparkle fire-glow pulse-orange">
                        <i class="fas fa-rocket ml-2"></i>
                        ابدأ الاستخدام
                    </button>
                    <button class="btn btn-outline px-12 py-4 fire-trail">
                        <i class="fas fa-download ml-2"></i>
                        تحميل الملفات
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script>
// تأثيرات إضافية للصفحة
document.addEventListener('DOMContentLoaded', function() {
    // عد تنازلي للعناصر
    const animatedNumbers = document.querySelectorAll('.animated-number');
    animatedNumbers.forEach(el => {
        const finalNumber = parseInt(el.textContent);
        let currentNumber = 0;
        const increment = finalNumber / 30;
        
        const timer = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                currentNumber = finalNumber;
                clearInterval(timer);
            }
            el.textContent = Math.floor(currentNumber);
        }, 50);
    });
    
    // تأثيرات الألوان التفاعلية
    document.querySelectorAll('.interactive-element').forEach(element => {
        element.addEventListener('click', function() {
            // إنشاء تأثير الدوائر المتفجرة
            for (let i = 0; i < 5; i++) {
                setTimeout(() => {
                    const circle = document.createElement('div');
                    circle.style.position = 'absolute';
                    circle.style.width = '10px';
                    circle.style.height = '10px';
                    circle.style.backgroundColor = '#f97316';
                    circle.style.borderRadius = '50%';
                    circle.style.pointerEvents = 'none';
                    circle.style.zIndex = '9999';
                    
                    const rect = this.getBoundingClientRect();
                    circle.style.left = (rect.left + Math.random() * rect.width) + 'px';
                    circle.style.top = (rect.top + Math.random() * rect.height) + 'px';
                    
                    circle.style.animation = 'explode 1s ease-out forwards';
                    
                    document.body.appendChild(circle);
                    
                    setTimeout(() => {
                        if (circle.parentNode) {
                            circle.parentNode.removeChild(circle);
                        }
                    }, 1000);
                }, i * 100);
            }
        });
    });
});

// إضافة CSS للتأثيرات
const style = document.createElement('style');
style.textContent = `
    @keyframes explode {
        0% {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        100% {
            transform: scale(0) translateY(-50px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection
