@extends('layouts.app')

@section('title', 'تفاصيل طلب السحب #' . $withdrawal->id)
@section('description', 'عرض تفاصيل طلب السحب والتتبع')

@push('styles')
<style>
    .withdrawal-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .status-processing {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: white;
    }
    
    .timeline-item {
        position: relative;
        padding-right: 3rem;
        padding-bottom: 2rem;
    }
    
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 1.5rem;
        top: 2rem;
        bottom: -1rem;
        width: 2px;
        background: linear-gradient(to bottom, #d1d5db, #f3f4f6);
    }
    
    .timeline-icon {
        position: absolute;
        right: 0.75rem;
        top: 0.25rem;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: white;
        z-index: 10;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .info-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
    }
    
    .amount-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    }
    
    .bank-details {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #0ea5e9;
        border-radius: 12px;
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-blue-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">📋</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">طلب السحب #{{ $withdrawal->id }}</h1>
                        <p class="text-sm text-gray-600">تفاصيل طلب السحب والحالة الحالية</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- Status Badge -->
                    <span class="status-badge status-{{ $withdrawal->status }}">
                        {{ $withdrawal->status_label }}
                    </span>
                    
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
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Amount Card -->
                <div class="amount-display">
                    <h3 class="text-lg font-medium mb-2 text-blue-100">المبلغ المطلوب</h3>
                    <p class="text-4xl font-bold mb-2">{{ number_format($withdrawal->amount, 2) }} ريال</p>
                    <p class="text-blue-200 text-sm">
                        تاريخ الطلب: {{ $withdrawal->created_at->format('d/m/Y - H:i') }}
                    </p>
                </div>

                <!-- Bank Details -->
                <div class="withdrawal-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        تفاصيل الحساب البنكي
                    </h3>
                    
                    <div class="bank-details">
                        <div class="info-grid">
                            <div class="info-item">
                                <p class="text-sm text-gray-600 mb-1">اسم البنك</p>
                                <p class="font-bold text-gray-800">{{ $withdrawal->bank_name }}</p>
                            </div>
                            
                            <div class="info-item">
                                <p class="text-sm text-gray-600 mb-1">اسم صاحب الحساب</p>
                                <p class="font-bold text-gray-800">{{ $withdrawal->account_holder_name }}</p>
                            </div>
                            
                            <div class="info-item">
                                <p class="text-sm text-gray-600 mb-1">رقم الحساب</p>
                                <p class="font-bold text-gray-800 font-mono">{{ $withdrawal->account_number }}</p>
                            </div>
                            
                            @if($withdrawal->iban)
                            <div class="info-item">
                                <p class="text-sm text-gray-600 mb-1">رقم الآيبان</p>
                                <p class="font-bold text-gray-800 font-mono">{{ $withdrawal->iban }}</p>
                            </div>
                            @endif
                            
                            @if($withdrawal->swift_code)
                            <div class="info-item">
                                <p class="text-sm text-gray-600 mb-1">كود SWIFT</p>
                                <p class="font-bold text-gray-800 font-mono">{{ $withdrawal->swift_code }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="withdrawal-card p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        معلومات إضافية
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <p class="text-sm text-gray-600 mb-1">رقم المرجع</p>
                            <p class="font-bold text-gray-800 font-mono">WD-{{ $withdrawal->id }}-{{ date('Y', strtotime($withdrawal->created_at)) }}</p>
                        </div>
                        
                        <div class="info-item">
                            <p class="text-sm text-gray-600 mb-1">طريقة السحب</p>
                            <p class="font-bold text-gray-800">حوالة بنكية</p>
                        </div>
                        
                        <div class="info-item">
                            <p class="text-sm text-gray-600 mb-1">الرسوم</p>
                            <p class="font-bold text-green-600">مجاني</p>
                        </div>
                        
                        <div class="info-item">
                            <p class="text-sm text-gray-600 mb-1">وقت المعالجة المتوقع</p>
                            <p class="font-bold text-gray-800">1-3 أيام عمل</p>
                        </div>
                    </div>

                    @if($withdrawal->notes)
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">ملاحظات</p>
                        <p class="text-gray-800">{{ $withdrawal->notes }}</p>
                    </div>
                    @endif

                    @if($withdrawal->admin_notes)
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            ملاحظات الإدارة
                        </p>
                        <p class="text-gray-800">{{ $withdrawal->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline Sidebar -->
            <div class="lg:col-span-1">
                <div class="withdrawal-card p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        تتبع الطلب
                    </h3>
                    
                    <div class="space-y-0">
                        <!-- Timeline Item 1: Created -->
                        <div class="timeline-item">
                            <div class="timeline-icon bg-blue-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">تم إنشاء الطلب</h4>
                                <p class="text-sm text-gray-600">{{ $withdrawal->created_at->format('d/m/Y - H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-1">تم استلام طلب السحب وإضافته للمراجعة</p>
                            </div>
                        </div>

                        <!-- Timeline Item 2: Pending Review -->
                        @if(in_array($withdrawal->status, ['pending', 'approved', 'processing', 'completed']))
                        <div class="timeline-item">
                            <div class="timeline-icon bg-yellow-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">قيد المراجعة</h4>
                                <p class="text-sm text-gray-600">{{ $withdrawal->created_at->addMinutes(5)->format('d/m/Y - H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-1">جاري مراجعة الطلب والتحقق من البيانات</p>
                            </div>
                        </div>
                        @endif

                        <!-- Timeline Item 3: Approved -->
                        @if(in_array($withdrawal->status, ['approved', 'processing', 'completed']))
                        <div class="timeline-item">
                            <div class="timeline-icon bg-green-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">تمت الموافقة</h4>
                                <p class="text-sm text-gray-600">
                                    @if($withdrawal->approved_at)
                                        {{ $withdrawal->approved_at->format('d/m/Y - H:i') }}
                                    @else
                                        في انتظار الموافقة
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">تمت الموافقة على الطلب وإرساله للمعالجة</p>
                            </div>
                        </div>
                        @endif

                        <!-- Timeline Item 4: Processing -->
                        @if(in_array($withdrawal->status, ['processing', 'completed']))
                        <div class="timeline-item">
                            <div class="timeline-icon bg-blue-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">قيد المعالجة</h4>
                                <p class="text-sm text-gray-600">
                                    @if($withdrawal->processing_at)
                                        {{ $withdrawal->processing_at->format('d/m/Y - H:i') }}
                                    @else
                                        في انتظار المعالجة
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">جاري تنفيذ الحوالة البنكية</p>
                            </div>
                        </div>
                        @endif

                        <!-- Timeline Item 5: Completed -->
                        @if($withdrawal->status === 'completed')
                        <div class="timeline-item">
                            <div class="timeline-icon bg-emerald-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">تم السحب بنجاح</h4>
                                <p class="text-sm text-gray-600">
                                    @if($withdrawal->completed_at)
                                        {{ $withdrawal->completed_at->format('d/m/Y - H:i') }}
                                    @else
                                        مكتمل
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">تم تحويل المبلغ إلى حسابك البنكي</p>
                            </div>
                        </div>
                        @endif

                        <!-- Timeline Item: Cancelled/Rejected -->
                        @if(in_array($withdrawal->status, ['cancelled', 'rejected']))
                        <div class="timeline-item">
                            <div class="timeline-icon bg-red-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">
                                    @if($withdrawal->status === 'cancelled')
                                        تم الإلغاء
                                    @else
                                        تم الرفض
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-600">
                                    @if($withdrawal->cancelled_at)
                                        {{ $withdrawal->cancelled_at->format('d/m/Y - H:i') }}
                                    @else
                                        {{ $withdrawal->updated_at->format('d/m/Y - H:i') }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    @if($withdrawal->status === 'cancelled')
                                        تم إلغاء الطلب وإرجاع المبلغ للمحفظة
                                    @else
                                        تم رفض الطلب وإرجاع المبلغ للمحفظة
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($withdrawal->status === 'pending')
                <div class="withdrawal-card p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">الإجراءات المتاحة</h3>
                    
                    <form action="{{ route('withdrawals.cancel', $withdrawal->id) }}" method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من إلغاء طلب السحب؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition-all flex items-center justify-center space-x-2 space-x-reverse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>إلغاء الطلب</span>
                        </button>
                    </form>
                    
                    <p class="text-xs text-gray-500 mt-2 text-center">
                        يمكن إلغاء الطلب فقط قبل الموافقة عليه
                    </p>
                </div>
                @endif

                <!-- Contact Support -->
                <div class="withdrawal-card p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        تحتاج مساعدة؟
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        إذا كانت لديك أي استفسارات حول طلب السحب، لا تتردد في التواصل معنا.
                    </p>
                    <a href="#" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-all text-center block">
                        اتصل بالدعم الفني
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
