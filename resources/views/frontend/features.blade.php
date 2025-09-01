@extends('frontend.layouts.app')

@section('title', 'Features - Tickets FBR-M')

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

    /* Feature Card Hover Effects */
    .feature-card {
        transition: all 0.4s ease;
        border: 2px solid transparent;
    }
    .feature-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px rgba(249, 115, 22, 0.15);
        border-color: var(--primary-orange);
    }

    /* Tab Animation */
    .tab-content {
        opacity: 0;
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeIn {
        to { opacity: 1; }
    }

    /* Progress Bars */
    .progress-bar {
        background: linear-gradient(90deg, var(--primary-orange) 0%, var(--orange-light) 100%);
        height: 8px;
        border-radius: 4px;
        transition: width 2s ease-in-out;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="orange-gradient text-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Powerful Features for
            <span class="text-yellow-300 block">Modern Businesses</span>
        </h1>
        <p class="text-xl md:text-2xl mb-12 text-orange-100 max-w-4xl mx-auto">
            Everything you need to manage bookings, grow your business, and delight your customers.
            <strong>All in one platform</strong>.
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16">
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">500+</div>
                <div class="text-orange-200">Active Merchants</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">50K+</div>
                <div class="text-orange-200">Bookings Made</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">99.9%</div>
                <div class="text-orange-200">Uptime</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">24/7</div>
                <div class="text-orange-200">Support</div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Categories -->
<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">
                Features by <span class="text-orange-500">User Type</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Tailored experiences for customers, merchants, and administrators
            </p>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <button class="tab-btn active px-8 py-3 rounded-full font-semibold transition-all" data-tab="customers">
                👥 Customer Features
            </button>
            <button class="tab-btn px-8 py-3 rounded-full font-semibold transition-all" data-tab="merchants">
                🏪 Merchant Features
            </button>
            <button class="tab-btn px-8 py-3 rounded-full font-semibold transition-all" data-tab="admins">
                👨‍💼 Admin Features
            </button>
        </div>

        <!-- Customer Features -->
        <div id="customers" class="tab-content">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Smart Search</h3>
                    <p class="text-gray-600 mb-4">Find exactly what you're looking for with AI-powered search and advanced filters</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-4/5"></div>
                    </div>
                    <span class="text-sm text-gray-500">95% Search Accuracy</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">⚡</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">One-Click Booking</h3>
                    <p class="text-gray-600 mb-4">Book services in seconds with our streamlined, mobile-first checkout process</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">3 Second Average Booking</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🔒</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Secure Payments</h3>
                    <p class="text-gray-600 mb-4">Multiple payment options with bank-level security and fraud protection</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">256-bit SSL Encryption</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Mobile App</h3>
                    <p class="text-gray-600 mb-4">Native mobile apps for iOS and Android with offline booking capabilities</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-4/5"></div>
                    </div>
                    <span class="text-sm text-gray-500">4.8★ App Store Rating</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🔔</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Smart Notifications</h3>
                    <p class="text-gray-600 mb-4">Real-time updates about bookings, reminders, and special offers</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-5/6"></div>
                    </div>
                    <span class="text-sm text-gray-500">Push, Email & SMS</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">⭐</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Reviews & Ratings</h3>
                    <p class="text-gray-600 mb-4">Share experiences and help others make informed decisions</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-4/5"></div>
                    </div>
                    <span class="text-sm text-gray-500">Verified Reviews Only</span>
                </div>
            </div>
        </div>

        <!-- Merchant Features -->
        <div id="merchants" class="tab-content hidden">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Advanced Analytics</h3>
                    <p class="text-gray-600 mb-4">Track bookings, revenue, customer behavior with real-time dashboards</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">15+ Key Metrics</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🎨</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Custom Storefronts</h3>
                    <p class="text-gray-600 mb-4">Beautiful, mobile-optimized pages that showcase your brand</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-5/6"></div>
                    </div>
                    <span class="text-sm text-gray-500">10+ Templates</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">💰</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Automated Payouts</h3>
                    <p class="text-gray-600 mb-4">Get paid automatically with flexible payout schedules and low fees</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">Same-day Transfers</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">📅</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Smart Scheduling</h3>
                    <p class="text-gray-600 mb-4">AI-powered scheduling that optimizes your availability and pricing</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-4/5"></div>
                    </div>
                    <span class="text-sm text-gray-500">Dynamic Pricing</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">💬</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Customer Chat</h3>
                    <p class="text-gray-600 mb-4">Built-in messaging system to communicate with customers</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-5/6"></div>
                    </div>
                    <span class="text-sm text-gray-500">Real-time Messaging</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🏪</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Multi-Location</h3>
                    <p class="text-gray-600 mb-4">Manage multiple locations and branches from one dashboard</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">Unlimited Locations</span>
                </div>
            </div>
        </div>

        <!-- Admin Features -->
        <div id="admins" class="tab-content hidden">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🎛️</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">System Control</h3>
                    <p class="text-gray-600 mb-4">Complete control over users, merchants, and platform settings</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">Full Admin Access</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">📈</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Platform Analytics</h3>
                    <p class="text-gray-600 mb-4">Comprehensive reports on platform performance and growth</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-5/6"></div>
                    </div>
                    <span class="text-sm text-gray-500">50+ Report Types</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🛡️</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Security Management</h3>
                    <p class="text-gray-600 mb-4">Advanced security controls and fraud detection systems</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">99.99% Secure</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">👥</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">User Management</h3>
                    <p class="text-gray-600 mb-4">Advanced user role management and permission systems</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-4/5"></div>
                    </div>
                    <span class="text-sm text-gray-500">Role-based Access</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">🔧</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">System Monitoring</h3>
                    <p class="text-gray-600 mb-4">Real-time system health monitoring and performance alerts</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-5/6"></div>
                    </div>
                    <span class="text-sm text-gray-500">24/7 Monitoring</span>
                </div>

                <div class="feature-card bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-8">
                    <div class="text-5xl mb-4">💼</div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Revenue Management</h3>
                    <p class="text-gray-600 mb-4">Track platform revenue, commissions, and financial performance</p>
                    <div class="bg-gray-200 rounded-full h-2 mb-2">
                        <div class="progress-bar w-full"></div>
                    </div>
                    <span class="text-sm text-gray-500">Real-time Revenue</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Integration Section -->
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">
                Seamless <span class="text-orange-500">Integrations</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Connect with the tools you already use and love
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-2">💳</div>
                <div class="text-sm font-semibold">Stripe</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-2">🏦</div>
                <div class="text-sm font-semibold">PayPal</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-2">📱</div>
                <div class="text-sm font-semibold">WhatsApp</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-2">📧</div>
                <div class="text-sm font-semibold">Email</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-2">🔗</div>
                <div class="text-sm font-semibold">API</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-2">☁️</div>
                <div class="text-sm font-semibold">Cloud</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="orange-gradient text-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            Experience All Features
        </h2>
        <p class="text-xl mb-8 text-orange-100">
            Start your free trial today and discover the power of our platform
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('merchant.login') }}" 
               class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition transform hover:scale-105 shadow-lg">
                🚀 Start Free Trial
            </a>
            <a href="{{ route('pricing') }}" 
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition transform hover:scale-105">
                💰 View Pricing
            </a>
        </div>
    </div>
</section>

<script>
// Tab functionality
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        const tabId = button.getAttribute('data-tab');
        
        // Update button styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-orange-500', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-600');
        });
        
        button.classList.remove('bg-gray-200', 'text-gray-600');
        button.classList.add('active', 'bg-orange-500', 'text-white');
        
        // Update content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        document.getElementById(tabId).classList.remove('hidden');
    });
});

// Initialize first tab
document.querySelector('.tab-btn').classList.add('bg-orange-500', 'text-white');
document.querySelector('.tab-btn').classList.remove('bg-gray-200', 'text-gray-600');
</script>
@endsection