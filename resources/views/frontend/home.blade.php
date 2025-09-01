@extends('frontend.layouts.app')

@section('title', 'Tickets FBR-M - Book Services & Events Made Simple')

@section('head')
<style>
    /* Orange Theme Variables */
    :root {
        --primary-orange: #F97316;
        --orange-light: #FB923C;
        --orange-dark: #EA580C;
        --orange-50: #FFF7ED;
        --orange-100: #FFEDD5;
        --orange-900: #9A3412;
    }

    /* Floating Animation */
    @keyframes floating {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    .floating-animation {
        animation: floating 3s ease-in-out infinite;
    }

    /* Gradient Backgrounds */
    .orange-gradient {
        background: linear-gradient(135deg, var(--primary-orange) 0%, var(--orange-dark) 100%);
    }
    
    .orange-gradient-soft {
        background: linear-gradient(135deg, var(--orange-50) 0%, var(--orange-100) 100%);
    }

    /* Custom Animations */
    .slide-in-left {
        animation: slideInLeft 0.8s ease-out;
    }
    
    .slide-in-right {
        animation: slideInRight 0.8s ease-out;
    }
    
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes fadeInUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Tab Styles */
    .tab-button {
        transition: all 0.3s ease;
    }
    .tab-button.active {
        background: var(--primary-orange);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
    }

    /* Card Hover Effects */
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    /* Stats Counter Animation */
    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-orange);
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="orange-gradient text-white py-20 lg:py-28 overflow-hidden relative">
    <!-- Background Decorations -->
    <div class="absolute inset-0 opacity-10">
        <div class="floating-animation absolute top-20 left-10 w-16 h-16 bg-white rounded-full"></div>
        <div class="floating-animation absolute top-40 right-20 w-8 h-8 bg-yellow-300 rounded-full" style="animation-delay: 1s;"></div>
        <div class="floating-animation absolute bottom-20 left-1/4 w-12 h-12 bg-orange-200 rounded-full" style="animation-delay: 2s;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Hero Content -->
            <div class="slide-in-left">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                    <span class="text-sm font-medium">🎉 Platform Launched!</span>
                    <span class="ml-2 text-xs bg-yellow-400 text-orange-900 px-2 py-1 rounded-full">NEW</span>
                </div>
                
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                    Book Services &
                    <span class="text-yellow-300 block">Events Made</span>
                    <span class="text-orange-200 block">Simple! 🎟️</span>
                </h1>
                
                <p class="text-xl md:text-2xl mb-8 text-orange-100 leading-relaxed">
                    Discover and book amazing services from trusted merchants across Saudi Arabia. 
                    <strong>Simple</strong>, <strong>Secure</strong>, <strong>Instant</strong>.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="{{ route('customer.register') }}" 
                       class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center group">
                        <span class="mr-2">🚀</span> Get Started Free
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    
                    <a href="{{ route('search') }}" 
                       class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition transform hover:scale-105 flex items-center justify-center">
                        <span class="mr-2">🔍</span> Browse Services
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="flex items-center gap-6 text-orange-100">
                    <div class="flex items-center">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 bg-white rounded-full border-2 border-orange-300"></div>
                            <div class="w-8 h-8 bg-yellow-400 rounded-full border-2 border-orange-300"></div>
                            <div class="w-8 h-8 bg-orange-200 rounded-full border-2 border-orange-300"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium">500+ Happy Customers</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-yellow-300 text-lg">⭐⭐⭐⭐⭐</span>
                        <span class="ml-2 text-sm font-medium">4.9/5 Rating</span>
                    </div>
                </div>
            </div>

            <!-- Hero Image/Illustration -->
            <div class="slide-in-right hidden lg:block">
                <div class="relative">
                    <div class="floating-animation bg-white rounded-2xl p-8 shadow-2xl">
                        <div class="text-center">
                            <div class="text-6xl mb-4">🎫</div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Easy Booking</h3>
                            <p class="text-gray-600">Book in 3 simple steps</p>
                            
                            <div class="mt-6 space-y-3">
                                <div class="flex items-center bg-orange-50 p-3 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">1</div>
                                    <span class="text-gray-700">Choose Service</span>
                                </div>
                                <div class="flex items-center bg-orange-50 p-3 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">2</div>
                                    <span class="text-gray-700">Select Time</span>
                                </div>
                                <div class="flex items-center bg-orange-50 p-3 rounded-lg">
                                    <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">3</div>
                                    <span class="text-gray-700">Pay & Enjoy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 floating-animation bg-yellow-400 rounded-full p-3" style="animation-delay: 0.5s;">
                        <span class="text-2xl">✨</span>
                    </div>
                    <div class="absolute -bottom-4 -left-4 floating-animation bg-orange-200 rounded-full p-3" style="animation-delay: 1.5s;">
                        <span class="text-2xl">🎉</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="fade-in-up">
                <div class="stats-number" data-count="500">0</div>
                <p class="text-gray-600 font-medium">Happy Customers</p>
            </div>
            <div class="fade-in-up" style="animation-delay: 0.2s;">
                <div class="stats-number" data-count="50">0</div>
                <p class="text-gray-600 font-medium">Trusted Merchants</p>
            </div>
            <div class="fade-in-up" style="animation-delay: 0.4s;">
                <div class="stats-number" data-count="1000">0</div>
                <p class="text-gray-600 font-medium">Services Booked</p>
            </div>
            <div class="fade-in-up" style="animation-delay: 0.6s;">
                <div class="stats-number" data-count="99">0</div>
                <p class="text-gray-600 font-medium">Satisfaction %</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section with Tabs -->
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Why Choose <span class="text-orange-500">Our Platform?</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Built for customers, merchants, and admins. Experience the power of modern booking technology.
            </p>
        </div>

        <!-- Feature Tabs -->
        <div class="flex flex-col lg:flex-row justify-center mb-8">
            <div class="flex flex-wrap justify-center gap-2 mb-6 lg:mb-0">
                <button class="tab-button active px-6 py-3 rounded-lg font-semibold border-2 border-orange-200" onclick="showTab('customers')">
                    👥 For Customers
                </button>
                <button class="tab-button px-6 py-3 rounded-lg font-semibold border-2 border-orange-200" onclick="showTab('merchants')">
                    🏪 For Merchants  
                </button>
                <button class="tab-button px-6 py-3 rounded-lg font-semibold border-2 border-orange-200" onclick="showTab('admins')">
                    👨‍💼 For Admins
                </button>
            </div>
        </div>

        <!-- Tab Contents -->
        <div id="customers-tab" class="tab-content">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Easy Discovery</h3>
                    <p class="text-gray-600">Find services and events near you with advanced search filters</p>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">⚡</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Instant Booking</h3>
                    <p class="text-gray-600">Book services in seconds with our streamlined checkout process</p>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">🔒</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Secure Payments</h3>
                    <p class="text-gray-600">Multiple payment options with bank-level security</p>
                </div>
            </div>
        </div>

        <div id="merchants-tab" class="tab-content hidden">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Analytics Dashboard</h3>
                    <p class="text-gray-600">Track bookings, revenue, and customer insights</p>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">🎨</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Custom Storefront</h3>
                    <p class="text-gray-600">Beautiful branded pages for your business</p>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">💰</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Easy Payouts</h3>
                    <p class="text-gray-600">Automated payments directly to your bank account</p>
                </div>
            </div>
        </div>

        <div id="admins-tab" class="tab-content hidden">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">🎛️</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Full Control</h3>
                    <p class="text-gray-600">Manage all users, merchants, and transactions</p>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">📈</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Advanced Reports</h3>
                    <p class="text-gray-600">Comprehensive analytics and business intelligence</p>
                </div>
                <div class="hover-card bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-5xl mb-4">🛡️</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Security Management</h3>
                    <p class="text-gray-600">Role-based access and security monitoring</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                How It <span class="text-orange-500">Works</span>
            </h2>
            <p class="text-xl text-gray-600">Simple steps to get started</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-20 h-20 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        1
                    </div>
                    <div class="hidden md:block absolute top-10 left-full w-full h-0.5 bg-orange-200" style="width: calc(100% - 2.5rem);"></div>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Sign Up</h3>
                <p class="text-gray-600">Create your free account in seconds</p>
            </div>
            
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-20 h-20 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        2
                    </div>
                    <div class="hidden md:block absolute top-10 left-full w-full h-0.5 bg-orange-200" style="width: calc(100% - 2.5rem);"></div>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Choose Service</h3>
                <p class="text-gray-600">Browse and select from thousands of services</p>
            </div>
            
            <div class="text-center">
                <div class="relative mb-8">
                    <div class="w-20 h-20 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        3
                    </div>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Book & Enjoy</h3>
                <p class="text-gray-600">Pay securely and enjoy your experience</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="orange-gradient text-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Ready to Get Started?
        </h2>
        <p class="text-xl mb-8 text-orange-100">
            Join thousands of satisfied customers and merchants today!
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('customer.register') }}" 
               class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition transform hover:scale-105 shadow-lg">
                🚀 Start as Customer
            </a>
            <a href="{{ route('merchant.login') }}" 
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition transform hover:scale-105">
                🏪 Join as Merchant
            </a>
        </div>
    </div>
</section>

<script>
// Tab Functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

// Stats Counter Animation
function animateStats() {
    const stats = document.querySelectorAll('.stats-number');
    
    stats.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-count'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(current) + (target === 99 ? '%' : '+');
        }, 16);
    });
}

// Trigger stats animation when in view
const observerOptions = {
    threshold: 0.5
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            observer.disconnect();
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', () => {
    const statsSection = document.querySelector('.stats-number').closest('section');
    if (statsSection) {
        observer.observe(statsSection);
    }
});
</script>
@endsection