@extends('layouts.dashboard')

@section('title', 'إعدادات طرق الدفع')

@section('content')
<div class="space-y-6" dir="rtl">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">إعدادات طرق الدفع</h1>
                <p class="text-slate-600 mt-1">قم بتفعيل وإدارة طرق الدفع المتاحة لعملائك</p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <span class="text-sm text-slate-500">آخر تحديث: {{ now()->format('Y/m/d H:i') }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Payment Gateways -->
    <div class="grid gap-6">
        @foreach($availableGateways as $gateway)
            @php
                $setting = $merchantSettings->get($gateway->id);
                $isEnabled = $setting ? $setting->is_enabled : false;
                $isConfigured = $setting ? $setting->isConfigured() : false;
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Gateway Header -->
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            @if($gateway->icon)
                                <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                    <img src="{{ asset('images/payment-icons/' . $gateway->icon) }}" 
                                         alt="{{ $gateway->name }}" 
                                         class="w-8 h-8 object-contain"
                                         onerror="this.src='{{ asset('images/payment-icons/default.svg') }}'">
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800">{{ $gateway->localized_name }}</h3>
                                <p class="text-sm text-slate-600">{{ $gateway->description }}</p>
                                <div class="flex items-center space-x-3 space-x-reverse mt-2">
                                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded-full">
                                        رسوم: {{ $gateway->transaction_fee }}{{ $gateway->fee_type === 'percentage' ? '%' : ' ريال' }}
                                    </span>
                                    @if($gateway->supports_refund)
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                            قابل للاسترداد
                                        </span>
                                    @endif
                                    @if($setting && $setting->last_tested_at)
                                        <span class="text-xs {{ $setting->test_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-2 py-1 rounded-full">
                                            {{ $setting->test_passed ? 'مختبر بنجاح' : 'اختبار فاشل' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <!-- Test Button -->
                            @if($isEnabled)
                                <button type="button" 
                                        onclick="testGateway({{ $gateway->id }})"
                                        class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 transition-colors">
                                    اختبار
                                </button>
                            @endif
                            
                            <!-- Enable/Disable Toggle -->
                            <form action="{{ route('merchant.dashboard.update-payment-gateway', $gateway->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="is_enabled" value="{{ $isEnabled ? '0' : '1' }}">
                                <button type="submit" 
                                        class="flex items-center space-x-2 space-x-reverse px-4 py-2 rounded-lg transition-colors
                                               {{ $isEnabled ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    @if($isEnabled)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>إلغاء التفعيل</span>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>تفعيل</span>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Gateway Settings (if enabled) -->
                @if($isEnabled)
                    <div class="p-6 bg-slate-50">
                        <form action="{{ route('merchant.dashboard.update-payment-gateway', $gateway->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="is_enabled" value="1">
                            
                            <div class="grid md:grid-cols-3 gap-4">
                                <!-- Custom Fee -->
                                <div>
                                    <label for="custom_fee_{{ $gateway->id }}" class="block text-sm font-medium text-slate-700 mb-2">
                                        رسوم مخصصة (اختياري)
                                    </label>
                                    <div class="flex">
                                        <input type="number" 
                                               id="custom_fee_{{ $gateway->id }}"
                                               name="custom_fee" 
                                               value="{{ $setting ? $setting->custom_fee : '' }}"
                                               step="0.01" 
                                               min="0"
                                               class="flex-1 px-3 py-2 border border-slate-300 rounded-r-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        <select name="custom_fee_type" 
                                                class="border border-r-0 border-slate-300 rounded-l-lg px-3 py-2 bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            <option value="percentage" {{ $setting && $setting->custom_fee_type === 'percentage' ? 'selected' : '' }}>%</option>
                                            <option value="fixed" {{ $setting && $setting->custom_fee_type === 'fixed' ? 'selected' : '' }}>ريال</option>
                                        </select>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">اتركه فارغاً لاستخدام الرسوم الافتراضية</p>
                                </div>

                                <!-- Display Order -->
                                <div>
                                    <label for="display_order_{{ $gateway->id }}" class="block text-sm font-medium text-slate-700 mb-2">
                                        ترتيب العرض
                                    </label>
                                    <input type="number" 
                                           id="display_order_{{ $gateway->id }}"
                                           name="display_order" 
                                           value="{{ $setting ? $setting->display_order : 0 }}"
                                           min="0"
                                           class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <p class="text-xs text-slate-500 mt-1">0 = الأول</p>
                                </div>

                                <!-- Save Button -->
                                <div class="flex items-end">
                                    <button type="submit" 
                                            class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                                        حفظ الإعدادات
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Help Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start space-x-3 space-x-reverse">
            <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">معلومات مهمة</h3>
                <div class="text-blue-700 space-y-2 text-sm">
                    <p><strong>الرسوم المخصصة:</strong> يمكنك تعديل رسوم البوابة حسب احتياجاتك التجارية</p>
                    <p><strong>ترتيب العرض:</strong> يحدد أولوية ظهور طرق الدفع للعملاء</p>
                    <p><strong>الاختبار:</strong> تأكد من اختبار كل بوابة قبل تفعيلها للعملاء</p>
                    <p><strong>الأمان:</strong> جميع المعاملات مشفرة ومحمية بأعلى معايير الأمان</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Gateway Modal -->
<div id="testModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">اختبار بوابة الدفع</h3>
                <button onclick="closeTestModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="testContent">
                <div class="text-center py-4">
                    <div class="animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mx-auto mb-2"></div>
                    <p class="text-slate-600">جاري الاختبار...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testGateway(gatewayId) {
    document.getElementById('testModal').classList.remove('hidden');
    
    fetch(`{{ route('merchant.dashboard.test-payment-gateway', '') }}/${gatewayId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const content = document.getElementById('testContent');
        
        if (data.success) {
            content.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800 mb-2">اختبار ناجح!</h4>
                    <p class="text-slate-600 mb-4">${data.message}</p>
                    <button onclick="closeTestModal(); location.reload()" 
                            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">
                        إغلاق
                    </button>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800 mb-2">اختبار فاشل</h4>
                    <p class="text-slate-600 mb-4">${data.message}</p>
                    <button onclick="closeTestModal()" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        إغلاق
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        const content = document.getElementById('testContent');
        content.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-slate-800 mb-2">خطأ في الاتصال</h4>
                <p class="text-slate-600 mb-4">حدث خطأ أثناء الاختبار. يرجى المحاولة مرة أخرى.</p>
                <button onclick="closeTestModal()" 
                        class="bg-slate-600 text-white px-4 py-2 rounded-lg hover:bg-slate-700">
                    إغلاق
                </button>
            </div>
        `;
    });
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}
</script>
@endpush
@endsection
