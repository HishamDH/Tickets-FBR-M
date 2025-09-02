@extends('layouts.app')

@section('title', 'إدارة السحوبات - Withdrawals Management')
@section('description', 'إدارة طلبات السحب والمحفظة الرقمية')

@push('styles')
<style>
    .wallet-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        color: white;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    }
    
    .withdrawal-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .withdrawal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .status-pending {
        border-left-color: #f59e0b;
        background: linear-gradient(45deg, #fffbeb, #fef3c7);
    }
    
    .status-approved {
        border-left-color: #10b981;
        background: linear-gradient(45deg, #ecfdf5, #d1fae5);
    }
    
    .status-completed {
        border-left-color: #3b82f6;
        background: linear-gradient(45deg, #eff6ff, #dbeafe);
    }
    
    .status-rejected {
        border-left-color: #ef4444;
        background: linear-gradient(45deg, #fef2f2, #fecaca);
    }
    
    .status-cancelled {
        border-left-color: #6b7280;
        background: linear-gradient(45deg, #f9fafb, #f3f4f6);
    }
    
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-pending { background-color: #fef3c7; color: #92400e; }
    .badge-approved { background-color: #d1fae5; color: #065f46; }
    .badge-completed { background-color: #dbeafe; color: #1e40af; }
    .badge-rejected { background-color: #fecaca; color: #991b1b; }
    .badge-cancelled { background-color: #f3f4f6; color: #374151; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-indigo-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">💰</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">إدارة السحوبات</h1>
                        <p class="text-sm text-gray-600">محفظتك الرقمية وطلبات السحب</p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    @if($wallet->balance > 0)
                    <a href="{{ route('withdrawals.create') }}" 
                       class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-6 py-3 rounded-lg font-bold flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>طلب سحب جديد</span>
                    </a>
                    @endif
                    
                    <button onclick="location.reload()" 
                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-3 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>تحديث</span>
                    </button>
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
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
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
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Wallet Overview -->
        <div class="wallet-card p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold mb-2">محفظتك الرقمية</h2>
                    <p class="text-blue-100 text-lg">إجمالي أرباحك ومعاملاتك المالية</p>
                </div>
                <div class="text-right">
                    <div class="text-5xl font-bold">{{ number_format($wallet->balance, 2) }}</div>
                    <div class="text-blue-100 text-lg">ريال سعودي</div>
                    @if($wallet->pending_balance > 0)
                    <div class="text-yellow-200 text-sm mt-2">
                        في الانتظار: {{ number_format($wallet->pending_balance, 2) }} ريال
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white text-xl">💳</span>
                        </div>
                        <div>
                            <p class="text-blue-100 text-sm">الرصيد المتاح</p>
                            <p class="text-white text-xl font-bold">{{ number_format($wallet->balance, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white text-xl">⏳</span>
                        </div>
                        <div>
                            <p class="text-blue-100 text-sm">في المعالجة</p>
                            <p class="text-white text-xl font-bold">{{ number_format($wallet->pending_balance, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-30 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white text-xl">📊</span>
                        </div>
                        <div>
                            <p class="text-blue-100 text-sm">إجمالي السحوبات</p>
                            <p class="text-white text-xl font-bold">{{ $withdrawals->where('status', 'completed')->sum('amount') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Withdrawals List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="text-2xl mr-3">📋</span>
                        سجل طلبات السحب
                    </h2>
                    
                    <!-- Status Filter -->
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <span class="text-sm text-gray-600">فلتر حسب الحالة:</span>
                        <select onchange="filterByStatus(this.value)" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">جميع الحالات</option>
                            <option value="pending">في الانتظار</option>
                            <option value="approved">موافق عليه</option>
                            <option value="completed">مكتمل</option>
                            <option value="rejected">مرفوض</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @forelse($withdrawals as $withdrawal)
                <div class="withdrawal-card status-{{ $withdrawal->status }} rounded-xl p-6 mb-4" data-status="{{ $withdrawal->status }}">
                    <div class="flex items-center justify-between">
                        
                        <!-- Withdrawal Info -->
                        <div class="flex-1">
                            <div class="flex items-start space-x-4 space-x-reverse">
                                <!-- Status Icon -->
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0
                                    @if($withdrawal->status === 'pending') bg-yellow-100 
                                    @elseif($withdrawal->status === 'approved') bg-green-100
                                    @elseif($withdrawal->status === 'completed') bg-blue-100
                                    @elseif($withdrawal->status === 'rejected') bg-red-100
                                    @else bg-gray-100 @endif">
                                    <span class="text-xl">
                                        @if($withdrawal->status === 'pending') ⏳
                                        @elseif($withdrawal->status === 'approved') ✅
                                        @elseif($withdrawal->status === 'completed') 💰
                                        @elseif($withdrawal->status === 'rejected') ❌
                                        @else ⏹️ @endif
                                    </span>
                                </div>
                                
                                <!-- Details -->
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-bold text-gray-800">
                                            طلب سحب #{{ $withdrawal->id }}
                                        </h3>
                                        
                                        <!-- Status Badge -->
                                        <span class="status-badge badge-{{ $withdrawal->status }}">
                                            @switch($withdrawal->status)
                                                @case('pending') في الانتظار @break
                                                @case('approved') موافق عليه @break
                                                @case('completed') مكتمل @break
                                                @case('rejected') مرفوض @break
                                                @case('cancelled') ملغي @break
                                                @default {{ $withdrawal->status }} @break
                                            @endswitch
                                        </span>
                                    </div>
                                    
                                    <!-- Bank Details -->
                                    <div class="text-sm text-gray-600 mb-3">
                                        @if(isset($withdrawal->bank_details['bank_name']))
                                        <div class="flex items-center space-x-4 space-x-reverse">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                {{ $withdrawal->bank_details['bank_name'] }}
                                            </span>
                                            
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {{ $withdrawal->bank_details['account_holder_name'] }}
                                            </span>
                                            
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                حساب: ****{{ substr($withdrawal->bank_details['account_number'], -4) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Timeline -->
                                    <div class="flex items-center space-x-6 space-x-reverse text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            تم الطلب: {{ $withdrawal->created_at->format('Y-m-d H:i') }}
                                        </span>
                                        
                                        @if($withdrawal->approved_at)
                                        <span class="flex items-center text-green-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            تمت الموافقة: {{ \Carbon\Carbon::parse($withdrawal->approved_at)->format('Y-m-d H:i') }}
                                        </span>
                                        @endif
                                        
                                        @if($withdrawal->completed_at)
                                        <span class="flex items-center text-blue-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                            مكتمل: {{ \Carbon\Carbon::parse($withdrawal->completed_at)->format('Y-m-d H:i') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amount & Actions -->
                        <div class="text-right space-y-3">
                            <div class="text-3xl font-bold text-green-600">
                                {{ number_format($withdrawal->amount, 2) }}
                                <span class="text-sm text-gray-500">ريال</span>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('withdrawals.show', $withdrawal) }}" 
                                   class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center space-x-2 space-x-reverse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>التفاصيل</span>
                                </a>
                                
                                @if($withdrawal->status === 'pending')
                                <form action="{{ route('withdrawals.cancel', $withdrawal) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            onclick="return confirm('هل أنت متأكد من إلغاء طلب السحب؟')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>إلغاء</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes if any -->
                    @if($withdrawal->notes)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600"><strong>ملاحظات:</strong> {{ $withdrawal->notes }}</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-4xl text-gray-400">💰</span>
                    </div>
                    <h3 class="text-xl font-medium text-gray-600 mb-2">لا توجد طلبات سحب</h3>
                    <p class="text-gray-500 mb-4">لم تقم بإنشاء أي طلبات سحب حتى الآن</p>
                    @if($wallet->balance > 0)
                    <a href="{{ route('withdrawals.create') }}" 
                       class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        إنشاء طلب سحب
                    </a>
                    @else
                    <p class="text-sm text-gray-500">لا يوجد رصيد متاح للسحب</p>
                    @endif
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($withdrawals->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $withdrawals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterByStatus(status) {
    const cards = document.querySelectorAll('.withdrawal-card');
    
    cards.forEach(card => {
        if (status === '' || card.dataset.status === status) {
            card.style.display = 'block';
            // Add a slight animation
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, 100);
        } else {
            card.style.display = 'none';
        }
    });
}

// Auto-refresh every 30 seconds for pending withdrawals
if (document.querySelector('[data-status="pending"]')) {
    setTimeout(() => {
        location.reload();
    }, 30000);
}
</script>
@endpush
@endsection
