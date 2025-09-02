{{-- 
====================================
🎨 CREATIVE UI COMPONENTS
====================================
مكونات واجهة مستخدم إبداعية بالطابع البرتقالي
--}}

{{-- 🔥 Hero Section with Fire Effects --}}
@if($slot ?? false)
<div class="hero-section relative overflow-hidden bg-gradient-fire text-white py-24">
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
        {{ $slot }}
    </div>
</div>
@endif

{{-- ✨ Sparkle Card Component --}}
@php
$cardClass = 'card sparkle fire-glow interactive-card ' . ($class ?? '');
@endphp

<div class="{{ $cardClass }}" @if($animation ?? true) data-aos="fade-in-up" @endif>
    @if($header ?? false)
    <div class="card-header bg-gradient-warm p-6 text-white">
        @if($icon ?? false)
        <div class="flex items-center space-x-4 space-x-reverse">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="{{ $icon }} text-xl"></i>
            </div>
            <div>
                @if($title ?? false)<h3 class="font-bold text-xl mb-1">{{ $title }}</h3>@endif
                @if($subtitle ?? false)<p class="opacity-90">{{ $subtitle }}</p>@endif
            </div>
        </div>
        @else
        @if($title ?? false)<h3 class="font-bold text-xl mb-2">{{ $title }}</h3>@endif
        @if($subtitle ?? false)<p class="opacity-90">{{ $subtitle }}</p>@endif
        @endif
    </div>
    @endif
    
    <div class="card-body p-6">
        {{ $slot }}
    </div>
    
    @if($footer ?? false)
    <div class="card-footer p-6 pt-0">
        {{ $footer }}
    </div>
    @endif
</div>

{{-- 🌈 Creative Stats Grid --}}
@if(isset($stats) && is_array($stats))
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach($stats as $index => $stat)
    <div class="stat-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-primary-500 bounce-in" 
         style="animation-delay: {{ $index * 0.1 }}s;">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">{{ $stat['label'] }}</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $stat['value'] }}</h3>
                @if($stat['change'] ?? false)
                <p class="text-sm mt-2 flex items-center">
                    @if($stat['change_type'] === 'increase')
                    <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                    <span class="text-green-500">+{{ $stat['change'] }}</span>
                    @else
                    <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                    <span class="text-red-500">-{{ $stat['change'] }}</span>
                    @endif
                    <span class="text-gray-500 mr-1">من الشهر الماضي</span>
                </p>
                @endif
            </div>
            <div class="w-16 h-16 bg-gradient-primary rounded-full flex items-center justify-center">
                <i class="{{ $stat['icon'] }} text-2xl text-white"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- 🎨 Creative Button Group --}}
@if(isset($buttons) && is_array($buttons))
<div class="flex flex-wrap gap-4 {{ $align ?? 'justify-center' }}">
    @foreach($buttons as $button)
    <a href="{{ $button['url'] ?? '#' }}" 
       class="btn {{ $button['style'] ?? 'btn-primary' }} {{ $button['size'] ?? '' }} {{ $button['class'] ?? '' }}">
        @if($button['icon'] ?? false)
        <i class="{{ $button['icon'] }} ml-2"></i>
        @endif
        {{ $button['text'] }}
        @if($button['badge'] ?? false)
        <span class="bg-white bg-opacity-20 text-xs px-2 py-1 rounded-full mr-2">{{ $button['badge'] }}</span>
        @endif
    </a>
    @endforeach
</div>
@endif

{{-- 🔥 Fire Progress Bar --}}
@if($progress ?? false)
<div class="progress-container mb-6">
    <div class="flex justify-between items-center mb-2">
        <span class="text-sm font-medium text-gray-700">{{ $progressLabel ?? 'التقدم' }}</span>
        <span class="text-sm font-bold text-primary-500">{{ $progress }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
        <div class="bg-gradient-fire h-full rounded-full transition-all duration-1000 ease-out fire-trail" 
             style="width: {{ $progress }}%"></div>
    </div>
</div>
@endif

{{-- ✨ Floating Action Button --}}
@if($fab ?? false)
<div class="fixed bottom-6 left-6 z-50">
    <button class="w-16 h-16 bg-gradient-fire text-white rounded-full shadow-xl hover:shadow-2xl 
                   transition-all duration-300 flex items-center justify-center 
                   fire-glow pulse-orange floating">
        <i class="{{ $fabIcon ?? 'fas fa-plus' }} text-xl"></i>
    </button>
</div>
@endif

{{-- 🌊 Creative Timeline --}}
@if(isset($timeline) && is_array($timeline))
<div class="relative">
    <!-- Timeline Line -->
    <div class="absolute right-8 top-0 bottom-0 w-1 bg-gradient-primary"></div>
    
    @foreach($timeline as $index => $item)
    <div class="relative flex items-start mb-8 slide-in-right" style="animation-delay: {{ $index * 0.2 }}s;">
        <!-- Timeline Dot -->
        <div class="w-16 h-16 bg-gradient-fire rounded-full flex items-center justify-center 
                    shadow-lg border-4 border-white z-10 sparkle">
            <i class="{{ $item['icon'] ?? 'fas fa-star' }} text-white text-xl"></i>
        </div>
        
        <!-- Content -->
        <div class="mr-6 bg-white rounded-xl shadow-lg p-6 flex-1 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-3">
                <h4 class="font-bold text-lg text-gray-900">{{ $item['title'] }}</h4>
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $item['date'] }}</span>
            </div>
            <p class="text-gray-600 mb-3">{{ $item['description'] }}</p>
            @if($item['status'] ?? false)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                         {{ $item['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                            ($item['status'] === 'pending' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                {{ $item['status_text'] ?? $item['status'] }}
            </span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- 🎭 Creative Alert Components --}}
@if($alert ?? false)
<div class="alert alert-{{ $alertType ?? 'info' }} bg-white border-r-4 border-{{ $alertType === 'success' ? 'green' : ($alertType === 'error' ? 'red' : 'primary') }}-500 
           rounded-lg shadow-lg p-6 mb-6 fade-in-up">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-{{ $alertType === 'success' ? 'green' : ($alertType === 'error' ? 'red' : 'primary') }}-100 
                        flex items-center justify-center">
                <i class="fas fa-{{ $alertType === 'success' ? 'check' : ($alertType === 'error' ? 'exclamation-triangle' : 'info-circle') }} 
                          text-{{ $alertType === 'success' ? 'green' : ($alertType === 'error' ? 'red' : 'primary') }}-500"></i>
            </div>
        </div>
        <div class="mr-3 flex-1">
            @if($alertTitle ?? false)
            <h4 class="font-bold text-gray-900 mb-1">{{ $alertTitle }}</h4>
            @endif
            <p class="text-gray-600">{{ $alert }}</p>
        </div>
        @if($alertDismissible ?? true)
        <button class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
        @endif
    </div>
</div>
@endif

{{-- 🌟 Creative Badge System --}}
@if(isset($badges) && is_array($badges))
<div class="flex flex-wrap gap-3 mb-4">
    @foreach($badges as $badge)
    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                 {{ $badge['type'] === 'primary' ? 'bg-gradient-primary text-white' :
                    ($badge['type'] === 'fire' ? 'bg-gradient-fire text-white' :
                    ($badge['type'] === 'outline' ? 'border-2 border-primary-500 text-primary-500' : 'bg-gray-100 text-gray-800')) }}
                 hover:scale-105 transition-transform duration-200 cursor-pointer">
        @if($badge['icon'] ?? false)
        <i class="{{ $badge['icon'] }} ml-1"></i>
        @endif
        {{ $badge['text'] }}
        @if($badge['count'] ?? false)
        <span class="bg-white bg-opacity-20 text-xs px-2 py-1 rounded-full mr-2">{{ $badge['count'] }}</span>
        @endif
    </span>
    @endforeach
</div>
@endif

{{-- 🎨 Creative Navigation Pills --}}
@if(isset($navPills) && is_array($navPills))
<div class="flex flex-wrap gap-2 mb-8 bg-gray-100 p-2 rounded-2xl">
    @foreach($navPills as $index => $pill)
    <button class="nav-pill px-6 py-3 rounded-xl font-medium transition-all duration-300
                   {{ $pill['active'] ?? false ? 'bg-gradient-primary text-white shadow-lg' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }}"
            data-tab="{{ $pill['target'] ?? 'tab-' . $index }}">
        @if($pill['icon'] ?? false)
        <i class="{{ $pill['icon'] }} ml-2"></i>
        @endif
        {{ $pill['text'] }}
        @if($pill['count'] ?? false)
        <span class="bg-white bg-opacity-20 text-xs px-2 py-1 rounded-full mr-2">{{ $pill['count'] }}</span>
        @endif
    </button>
    @endforeach
</div>
@endif

{{-- 🔥 Creative Loading Spinner --}}
@if($loading ?? false)
<div class="flex items-center justify-center py-12">
    <div class="relative">
        <div class="w-16 h-16 border-4 border-primary-200 rounded-full"></div>
        <div class="w-16 h-16 border-4 border-primary-500 rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fas fa-fire text-primary-500 text-xl animate-pulse"></i>
        </div>
    </div>
</div>
@endif

{{-- 🌈 Creative Color Palette Display --}}
@if($showColorPalette ?? false)
<div class="color-palette grid grid-cols-8 gap-4 p-6 bg-white rounded-2xl shadow-lg">
    <div class="color-swatch w-full h-16 bg-primary-50 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 50"></div>
    <div class="color-swatch w-full h-16 bg-primary-100 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 100"></div>
    <div class="color-swatch w-full h-16 bg-primary-200 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 200"></div>
    <div class="color-swatch w-full h-16 bg-primary-300 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 300"></div>
    <div class="color-swatch w-full h-16 bg-primary-400 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 400"></div>
    <div class="color-swatch w-full h-16 bg-primary-500 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 500"></div>
    <div class="color-swatch w-full h-16 bg-primary-600 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 600"></div>
    <div class="color-swatch w-full h-16 bg-primary-700 rounded-lg cursor-pointer hover:scale-110 transition-transform" title="Primary 700"></div>
</div>
@endif

{{-- 📱 Mobile-First Responsive Utilities --}}
<style>
/* Additional responsive enhancements */
@media (max-width: 640px) {
    .hero-section { padding: 3rem 0; }
    .stat-card { padding: 1rem; }
    .timeline-item { margin-bottom: 1.5rem; }
    .nav-pill { padding: 0.5rem 1rem; font-size: 0.875rem; }
}

/* Creative hover effects */
.creative-hover:hover {
    transform: translateY(-5px) rotate(1deg);
    box-shadow: 0 20px 40px rgba(249, 115, 22, 0.2);
}

/* Smooth reveal animations */
.reveal-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.reveal-on-scroll.revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Interactive elements */
.interactive-element {
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.interactive-element:hover {
    transform: scale(1.05);
}

.interactive-element:active {
    transform: scale(0.95);
}
</style>

{{-- 🎭 JavaScript Enhancements --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for scroll animations
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });
    
    revealElements.forEach(el => revealObserver.observe(el));
    
    // Tab navigation for pills
    document.querySelectorAll('.nav-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            // Remove active class from all pills
            document.querySelectorAll('.nav-pill').forEach(p => {
                p.classList.remove('bg-gradient-primary', 'text-white', 'shadow-lg');
                p.classList.add('text-gray-600');
            });
            
            // Add active class to clicked pill
            this.classList.add('bg-gradient-primary', 'text-white', 'shadow-lg');
            this.classList.remove('text-gray-600');
            
            // Handle tab content switching
            const target = this.dataset.tab;
            if (target) {
                document.querySelectorAll('[data-tab-content]').forEach(content => {
                    content.classList.add('hidden');
                });
                
                const targetContent = document.querySelector(`[data-tab-content="${target}"]`);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            }
        });
    });
    
    // Auto-dismiss alerts
    document.querySelectorAll('.alert button').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.alert').style.opacity = '0';
            setTimeout(() => {
                this.closest('.alert').remove();
            }, 300);
        });
    });
    
    // Color palette interactions
    document.querySelectorAll('.color-swatch').forEach(swatch => {
        swatch.addEventListener('click', function() {
            const color = window.getComputedStyle(this).backgroundColor;
            navigator.clipboard.writeText(color);
            
            // Show feedback
            const feedback = document.createElement('div');
            feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            feedback.textContent = 'تم نسخ اللون!';
            document.body.appendChild(feedback);
            
            setTimeout(() => feedback.remove(), 2000);
        });
    });
});
</script>
