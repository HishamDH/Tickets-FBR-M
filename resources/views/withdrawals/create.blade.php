@extends('layouts.app')

@section('title', 'طلب سحب جديد - New Withdrawal Request')
@section('description', 'إنشاء طلب سحب من محفظتك الرقمية')

@push('styles')
<style>
    .form-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    
    .wallet-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }
    
    .form-input:invalid {
        border-color: #ef4444;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    
    .amount-display {
        background: linear-gradient(45deg, #f0f9ff, #e0f2fe);
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        margin: 1rem 0;
    }
    
    .fee-info {
        background: linear-gradient(45deg, #fef3c7, #fed7aa);
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 12px;
        margin: 12px 0;
    }
    
    .bank-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 12px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-green-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">💸</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">طلب سحب جديد</h1>
                        <p class="text-sm text-gray-600">اسحب أرباحك من محفظتك الرقمية</p>
                    </div>
                </div>
                
                <!-- Back Button -->
                <a href="{{ route('withdrawals.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <ul class="text-sm text-red-700">
                        @foreach($errors->all() as $error)
                        <li class="mb-1">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Wallet Info Sidebar -->
            <div class="lg:col-span-1">
                <div class="wallet-info p-6 mb-6">
                    <h3 class="text-xl font-bold mb-4">معلومات المحفظة</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <p class="text-blue-100 text-sm">الرصيد المتاح</p>
                            <p class="text-2xl font-bold">{{ number_format($wallet->balance, 2) }} ريال</p>
                        </div>
                        
                        @if($wallet->pending_balance > 0)
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <p class="text-blue-100 text-sm">في المعالجة</p>
                            <p class="text-xl font-bold">{{ number_format($wallet->pending_balance, 2) }} ريال</p>
                        </div>
                        @endif
                        
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <p class="text-blue-100 text-sm">الحد الأدنى للسحب</p>
                            <p class="text-lg font-bold">10.00 ريال</p>
                        </div>
                        
                        <div class="bg-white bg-opacity-20 rounded-lg p-4">
                            <p class="text-blue-100 text-sm">الحد الأقصى للسحب</p>
                            <p class="text-lg font-bold">50,000.00 ريال</p>
                        </div>
                    </div>
                </div>
                
                <!-- Important Notes -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h4 class="font-bold text-amber-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        ملاحظات مهمة
                    </h4>
                    <ul class="text-sm text-amber-700 space-y-1">
                        <li>• معالجة الطلبات تتم خلال 1-3 أيام عمل</li>
                        <li>• تأكد من صحة بيانات الحساب البنكي</li>
                        <li>• لا يمكن تعديل الطلب بعد الإرسال</li>
                        <li>• يمكن إلغاء الطلب قبل الموافقة عليه</li>
                    </ul>
                </div>
            </div>

            <!-- Withdrawal Form -->
            <div class="lg:col-span-2">
                <div class="form-card p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="bank-icon mr-4">
                            <span class="text-white text-lg">🏦</span>
                        </div>
                        تفاصيل طلب السحب
                    </h2>
                    
                    <form action="{{ route('withdrawals.store') }}" method="POST" id="withdrawalForm">
                        @csrf
                        
                        <!-- Amount Section -->
                        <div class="form-group">
                            <label class="form-label" for="amount">المبلغ المطلوب سحبه *</label>
                            <div class="relative">
                                <input type="number" 
                                       id="amount" 
                                       name="amount" 
                                       class="form-input pr-16" 
                                       placeholder="أدخل المبلغ"
                                       min="10" 
                                       max="{{ $wallet->balance }}" 
                                       step="0.01"
                                       value="{{ old('amount') }}"
                                       oninput="updateAmountDisplay()"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">ريال</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                الحد الأدنى: 10 ريال | الحد الأقصى: {{ number_format($wallet->balance, 2) }} ريال
                            </p>
                        </div>

                        <!-- Amount Display -->
                        <div class="amount-display" id="amountDisplay" style="display: none;">
                            <p class="text-sm text-blue-600 mb-1">المبلغ المطلوب سحبه</p>
                            <p class="text-3xl font-bold text-blue-800" id="displayAmount">0.00 ريال</p>
                            
                            <div class="fee-info mt-4">
                                <p class="text-sm text-amber-800">
                                    <strong>ملاحظة:</strong> لا توجد رسوم على عمليات السحب
                                </p>
                            </div>
                        </div>

                        <!-- Bank Details Section -->
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                تفاصيل الحساب البنكي
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Bank Name -->
                                <div class="form-group">
                                    <label class="form-label" for="bank_name">اسم البنك *</label>
                                    <select id="bank_name" name="bank_name" class="form-input" required>
                                        <option value="">اختر البنك</option>
                                        <option value="الراجحي" {{ old('bank_name') === 'الراجحي' ? 'selected' : '' }}>مصرف الراجحي</option>
                                        <option value="الأهلي" {{ old('bank_name') === 'الأهلي' ? 'selected' : '' }}>البنك الأهلي السعودي</option>
                                        <option value="سامبا" {{ old('bank_name') === 'سامبا' ? 'selected' : '' }}>سامبا المالية</option>
                                        <option value="الرياض" {{ old('bank_name') === 'الرياض' ? 'selected' : '' }}>بنك الرياض</option>
                                        <option value="ساب" {{ old('bank_name') === 'ساب' ? 'selected' : '' }}>البنك السعودي البريطاني</option>
                                        <option value="العربي" {{ old('bank_name') === 'العربي' ? 'selected' : '' }}>البنك العربي الوطني</option>
                                        <option value="الإنماء" {{ old('bank_name') === 'الإنماء' ? 'selected' : '' }}>بنك الإنماء</option>
                                        <option value="الجزيرة" {{ old('bank_name') === 'الجزيرة' ? 'selected' : '' }}>بنك الجزيرة</option>
                                        <option value="البلاد" {{ old('bank_name') === 'البلاد' ? 'selected' : '' }}>بنك البلاد</option>
                                        <option value="آخر" {{ old('bank_name') === 'آخر' ? 'selected' : '' }}>بنك آخر</option>
                                    </select>
                                </div>

                                <!-- Account Holder Name -->
                                <div class="form-group">
                                    <label class="form-label" for="account_holder_name">اسم صاحب الحساب *</label>
                                    <input type="text" 
                                           id="account_holder_name" 
                                           name="account_holder_name" 
                                           class="form-input" 
                                           placeholder="الاسم كما هو مسجل في البنك"
                                           value="{{ old('account_holder_name') }}"
                                           required>
                                </div>

                                <!-- Account Number -->
                                <div class="form-group">
                                    <label class="form-label" for="account_number">رقم الحساب *</label>
                                    <input type="text" 
                                           id="account_number" 
                                           name="account_number" 
                                           class="form-input" 
                                           placeholder="رقم الحساب البنكي"
                                           value="{{ old('account_number') }}"
                                           required>
                                </div>

                                <!-- IBAN -->
                                <div class="form-group">
                                    <label class="form-label" for="iban">رقم الآيبان IBAN</label>
                                    <input type="text" 
                                           id="iban" 
                                           name="iban" 
                                           class="form-input" 
                                           placeholder="SA00 0000 0000 0000 0000 0000"
                                           value="{{ old('iban') }}"
                                           pattern="SA[0-9]{22}"
                                           title="يجب أن يبدأ بـ SA ويحتوي على 22 رقم">
                                    <p class="text-xs text-gray-500 mt-1">اختياري - يُفضل إدخاله لتسريع المعالجة</p>
                                </div>

                                <!-- SWIFT Code -->
                                <div class="form-group">
                                    <label class="form-label" for="swift_code">كود SWIFT</label>
                                    <input type="text" 
                                           id="swift_code" 
                                           name="swift_code" 
                                           class="form-input" 
                                           placeholder="RIBLSARI"
                                           value="{{ old('swift_code') }}">
                                    <p class="text-xs text-gray-500 mt-1">اختياري - للحوالات الدولية فقط</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="form-group mt-6">
                            <label class="form-label" for="notes">ملاحظات إضافية</label>
                            <textarea id="notes" 
                                      name="notes" 
                                      class="form-input" 
                                      rows="3" 
                                      placeholder="أي ملاحظات أو تعليمات خاصة (اختياري)"
                                      maxlength="500">{{ old('notes') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">الحد الأقصى: 500 حرف</p>
                        </div>

                        <!-- Terms Agreement -->
                        <div class="form-group">
                            <label class="flex items-start">
                                <input type="checkbox" 
                                       id="agree_terms" 
                                       name="agree_terms" 
                                       class="mt-1 mr-3 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       required>
                                <span class="text-sm text-gray-600">
                                    أوافق على <a href="#" class="text-blue-600 hover:text-blue-800 underline">شروط وأحكام</a> عمليات السحب وأؤكد صحة البيانات المدخلة
                                </span>
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center space-x-4 space-x-reverse pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-bold py-4 px-6 rounded-lg transition-all flex items-center justify-center space-x-2 space-x-reverse">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span>إرسال طلب السحب</span>
                            </button>
                            
                            <a href="{{ route('withdrawals.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-4 px-6 rounded-lg transition-all">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateAmountDisplay() {
    const amountInput = document.getElementById('amount');
    const amountDisplay = document.getElementById('amountDisplay');
    const displayAmount = document.getElementById('displayAmount');
    
    const amount = parseFloat(amountInput.value) || 0;
    
    if (amount > 0) {
        amountDisplay.style.display = 'block';
        displayAmount.textContent = amount.toLocaleString('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ريال';
    } else {
        amountDisplay.style.display = 'none';
    }
}

// Form validation
document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('amount').value);
    const maxAmount = {{ $wallet->balance }};
    
    if (amount < 10) {
        e.preventDefault();
        alert('الحد الأدنى للسحب هو 10 ريال');
        return;
    }
    
    if (amount > maxAmount) {
        e.preventDefault();
        alert('المبلغ المطلوب أكبر من الرصيد المتاح');
        return;
    }
    
    const confirmMessage = `هل أنت متأكد من طلب سحب ${amount.toLocaleString('ar-SA', {minimumFractionDigits: 2})} ريال؟`;
    if (!confirm(confirmMessage)) {
        e.preventDefault();
    }
});

// Auto-format IBAN
document.getElementById('iban').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').toUpperCase();
    
    if (value.startsWith('SA')) {
        // Format as SA00 0000 0000 0000 0000 0000
        value = value.replace(/(.{4})/g, '$1 ').trim();
    }
    
    e.target.value = value;
});

// Character counter for notes
document.getElementById('notes').addEventListener('input', function(e) {
    const maxLength = 500;
    const currentLength = e.target.value.length;
    const remaining = maxLength - currentLength;
    
    // You can add a character counter display here if needed
    if (remaining < 0) {
        e.target.value = e.target.value.substring(0, maxLength);
    }
});
</script>
@endpush
@endsection
