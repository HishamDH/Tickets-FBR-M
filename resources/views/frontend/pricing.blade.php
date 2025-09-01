@extends('frontend.layouts.app')

@section('title', 'Pricing Plans - Tickets FBR-M')

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

    /* Plan Card Hover Effects */
    .plan-card {
        transition: all 0.4s ease;
    }
    .plan-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 60px rgba(249, 115, 22, 0.15);
    }
    
    .plan-card.popular {
        border: 3px solid var(--primary-orange);
        position: relative;
        overflow: hidden;
    }
    
    .plan-card.popular::before {
        content: 'MOST POPULAR';
        position: absolute;
        top: 20px;
        right: -30px;
        background: var(--primary-orange);
        color: white;
        padding: 5px 40px;
        transform: rotate(45deg);
        font-size: 12px;
        font-weight: bold;
        z-index: 10;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 60px;
        height: 30px;
        background: #ddd;
        border-radius: 15px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .toggle-switch.active {
        background: var(--primary-orange);
    }
    
    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        background: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }
    
    .toggle-switch.active::after {
        transform: translateX(30px);
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="orange-gradient text-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Simple, Transparent
            <span class="text-yellow-300 block">Pricing</span>
        </h1>
        <p class="text-xl md:text-2xl mb-12 text-orange-100 max-w-3xl mx-auto">
            Choose the perfect plan for your business. Start free, upgrade anytime.
            <strong>No hidden fees</strong>, <strong>No setup costs</strong>.
        </p>

        <!-- Billing Toggle -->
        <div class="flex items-center justify-center gap-4 mb-12">
            <span class="text-orange-100 font-medium">Monthly</span>
            <div class="toggle-switch" id="billingToggle" onclick="toggleBilling()"></div>
            <span class="text-white font-medium">Yearly</span>
            <span class="bg-yellow-400 text-orange-900 px-3 py-1 rounded-full text-sm font-bold ml-2">
                Save 20%
            </span>
        </div>
    </div>
</section>

<!-- Pricing Plans -->
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Free Plan -->
            <div class="plan-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Free</h3>
                    <p class="text-gray-600 mb-6">Perfect for getting started</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        $0
                        <span class="text-lg text-gray-500 font-normal">/month</span>
                    </div>
                    <p class="text-sm text-gray-500">Forever free</p>
                </div>
                
                <div class="mb-8">
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Up to 10 bookings/month</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Basic merchant profile</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Email support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Mobile app access</span>
                        </li>
                    </ul>
                </div>
                
                <a href="{{ route('merchant.login') }}" class="w-full bg-gray-800 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-900 transition text-center block">
                    Get Started Free
                </a>
            </div>

            <!-- Starter Plan -->
            <div class="plan-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                    <p class="text-gray-600 mb-6">Great for small businesses</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        $<span id="starter-price">29</span>
                        <span class="text-lg text-gray-500 font-normal">/month</span>
                    </div>
                    <p class="text-sm text-gray-500">Billed <span id="starter-billing">monthly</span></p>
                </div>
                
                <div class="mb-8">
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Up to 100 bookings/month</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Custom storefront theme</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Basic analytics</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Priority email support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Payment processing</span>
                        </li>
                    </ul>
                </div>
                
                <a href="{{ route('merchant.login') }}" class="w-full bg-orange-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-orange-600 transition text-center block">
                    Choose Starter
                </a>
            </div>

            <!-- Professional Plan (Most Popular) -->
            <div class="plan-card popular bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                    <p class="text-gray-600 mb-6">Perfect for growing businesses</p>
                    <div class="text-4xl font-bold text-orange-500 mb-2">
                        $<span id="pro-price">79</span>
                        <span class="text-lg text-gray-500 font-normal">/month</span>
                    </div>
                    <p class="text-sm text-gray-500">Billed <span id="pro-billing">monthly</span></p>
                </div>
                
                <div class="mb-8">
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Up to 500 bookings/month</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Advanced storefront customization</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Advanced analytics & reports</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Multi-location support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Live chat support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-orange-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">API access</span>
                        </li>
                    </ul>
                </div>
                
                <a href="{{ route('merchant.login') }}" class="w-full bg-orange-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-orange-600 transition text-center block">
                    Choose Professional
                </a>
            </div>

            <!-- Enterprise Plan -->
            <div class="plan-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                    <p class="text-gray-600 mb-6">For large organizations</p>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        $<span id="enterprise-price">199</span>
                        <span class="text-lg text-gray-500 font-normal">/month</span>
                    </div>
                    <p class="text-sm text-gray-500">Billed <span id="enterprise-billing">monthly</span></p>
                </div>
                
                <div class="mb-8">
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Unlimited bookings</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">White-label solution</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Custom integrations</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Dedicated account manager</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">24/7 phone support</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-purple-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">SLA guarantee</span>
                        </li>
                    </ul>
                </div>
                
                <a href="#" onclick="openContactModal()" class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition text-center block">
                    Contact Sales
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="bg-white py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">
                Frequently Asked <span class="text-orange-500">Questions</span>
            </h2>
            <p class="text-xl text-gray-600">Everything you need to know about our pricing</p>
        </div>

        <div class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Can I change my plan anytime?</h3>
                <p class="text-gray-600">Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately.</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Is there a setup fee?</h3>
                <p class="text-gray-600">No setup fees, no hidden costs. What you see is what you pay.</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">What payment methods do you accept?</h3>
                <p class="text-gray-600">We accept all major credit cards, PayPal, and bank transfers for enterprise plans.</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Do you offer refunds?</h3>
                <p class="text-gray-600">Yes, we offer a 30-day money-back guarantee for all paid plans.</p>
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
            Start your free trial today. No credit card required.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('merchant.login') }}" 
               class="bg-white text-orange-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-orange-50 transition transform hover:scale-105 shadow-lg">
                🚀 Start Free Trial
            </a>
            <a href="#" 
               onclick="openContactModal()"
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition transform hover:scale-105">
                💬 Contact Sales
            </a>
        </div>
    </div>
</section>

<script>
let isYearly = false;

function toggleBilling() {
    isYearly = !isYearly;
    const toggle = document.getElementById('billingToggle');
    toggle.classList.toggle('active');
    
    // Update prices
    if (isYearly) {
        document.getElementById('starter-price').textContent = '23';
        document.getElementById('starter-billing').textContent = 'yearly';
        
        document.getElementById('pro-price').textContent = '63';
        document.getElementById('pro-billing').textContent = 'yearly';
        
        document.getElementById('enterprise-price').textContent = '159';
        document.getElementById('enterprise-billing').textContent = 'yearly';
    } else {
        document.getElementById('starter-price').textContent = '29';
        document.getElementById('starter-billing').textContent = 'monthly';
        
        document.getElementById('pro-price').textContent = '79';
        document.getElementById('pro-billing').textContent = 'monthly';
        
        document.getElementById('enterprise-price').textContent = '199';
        document.getElementById('enterprise-billing').textContent = 'monthly';
    }
}

function openContactModal() {
    alert('Contact modal would open here. Integration with your contact system needed.');
}
</script>
@endsection