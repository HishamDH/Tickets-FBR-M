@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">تفاصيل المستخدم 👤</h1>
                    <p class="mt-2 text-gray-600">معلومات مفصلة عن المستخدم</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.pending') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-500 mt-0.5 ml-3"></i>
                    <div>
                        <strong>نجح!</strong> {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- User Info Card -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-{{ $user->user_type === 'merchant' ? 'blue' : 'green' }}-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="mr-4 flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="flex items-center mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $user->user_type === 'merchant' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $user->user_type === 'merchant' ? 'تاجر' : 'شريك' }}
                            </span>
                            @if($user->user_type === 'merchant')
                                <span class="mr-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @switch($user->merchant_status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                        @case('suspended') bg-gray-100 text-gray-800 @break
                                    @endswitch">
                                    {{ 
                                        match($user->merchant_status) {
                                            'pending' => 'منتظر',
                                            'approved' => 'مفعل',
                                            'rejected' => 'مرفوض',
                                            'suspended' => 'معلق',
                                            default => $user->merchant_status
                                        }
                                    }}
                                </span>
                            @elseif($user->user_type === 'partner')
                                <span class="mr-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @switch($user->partner_status)
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('approved') bg-green-100 text-green-800 @break
                                        @case('rejected') bg-red-100 text-red-800 @break
                                        @case('suspended') bg-gray-100 text-gray-800 @break
                                    @endswitch">
                                    {{ 
                                        match($user->partner_status) {
                                            'pending' => 'منتظر',
                                            'approved' => 'مفعل',
                                            'rejected' => 'مرفوض',
                                            'suspended' => 'معلق',
                                            default => $user->partner_status
                                        }
                                    }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">المعلومات الأساسية</h3>
                        <dl class="space-y-2">
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">الاسم:</dt>
                                <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">البريد الإلكتروني:</dt>
                                <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">الهاتف:</dt>
                                <dd class="text-sm text-gray-900">{{ $user->phone ?? 'غير محدد' }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">تاريخ التسجيل:</dt>
                                <dd class="text-sm text-gray-900">{{ $user->created_at->format('Y/m/d H:i') }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">تأكيد البريد:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">تم التأكيد</span>
                                    @else
                                        <span class="text-red-600">لم يتم التأكيد</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">معلومات إضافية</h3>
                        <dl class="space-y-2">
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">آخر دخول:</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'لم يدخل بعد' }}
                                </dd>
                            </div>
                            @if($user->rejection_reason)
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">سبب الرفض:</dt>
                                <dd class="text-sm text-red-600">{{ $user->rejection_reason }}</dd>
                            </div>
                            @endif
                            @if($user->suspension_reason)
                            <div class="flex">
                                <dt class="text-sm font-medium text-gray-500 w-32">سبب التعليق:</dt>
                                <dd class="text-sm text-gray-600">{{ $user->suspension_reason }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Merchant Details -->
        @if($user->user_type === 'merchant' && $user->merchant)
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-store text-blue-600 ml-3"></i>
                    معلومات النشاط التجاري
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">اسم النشاط التجاري</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->merchant->business_name ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">نوع النشاط</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->merchant->business_type ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">رقم السجل التجاري</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->merchant->commercial_register ?? 'غير محدد' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الهاتف</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->merchant->contact_phone ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">العنوان</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->merchant->address ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الموقع الإلكتروني</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    @if($user->merchant->website)
                                        <a href="{{ $user->merchant->website }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ $user->merchant->website }}
                                        </a>
                                    @else
                                        غير محدد
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Partner Details -->
        @if($user->user_type === 'partner' && $user->partner)
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-handshake text-green-600 ml-3"></i>
                    معلومات الشراكة
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">اسم الشركة</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->partner->company_name ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الشخص المسؤول</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->partner->contact_person ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">رمز الشريك</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->partner->partner_code ?? 'غير محدد' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">معدل العمولة</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->partner->commission_rate ?? 0 }}%</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">بريد التواصل</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->partner->contact_email ?? 'غير محدد' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">هاتف التواصل</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $user->partner->contact_phone ?? 'غير محدد' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات</h3>
            <div class="flex flex-wrap gap-3">
                @if($user->user_type === 'merchant')
                    @if($user->merchant_status === 'pending')
                        <form method="POST" action="{{ route('admin.merchants.approve', $user) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من تفعيل هذا التاجر؟')"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check ml-2"></i>
                                تفعيل التاجر
                            </button>
                        </form>
                        
                        <button onclick="openRejectModal()"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times ml-2"></i>
                            رفض التاجر
                        </button>
                    @elseif($user->merchant_status === 'approved')
                        <button onclick="openSuspendModal()"
                                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-pause ml-2"></i>
                            تعليق الحساب
                        </button>
                    @elseif($user->merchant_status === 'suspended')
                        <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من إلغاء تعليق هذا الحساب؟')"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-play ml-2"></i>
                                إلغاء التعليق
                            </button>
                        </form>
                    @endif
                @elseif($user->user_type === 'partner')
                    @if($user->partner_status === 'pending')
                        <form method="POST" action="{{ route('admin.partners.approve', $user) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من تفعيل هذا الشريك؟')"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check ml-2"></i>
                                تفعيل الشريك
                            </button>
                        </form>
                        
                        <button onclick="openRejectModal()"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times ml-2"></i>
                            رفض الشريك
                        </button>
                    @elseif($user->partner_status === 'approved')
                        <button onclick="openSuspendModal()"
                                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-pause ml-2"></i>
                            تعليق الحساب
                        </button>
                    @elseif($user->partner_status === 'suspended')
                        <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من إلغاء تعليق هذا الحساب؟')"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-play ml-2"></i>
                                إلغاء التعليق
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">رفض المستخدم</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="{{ $user->user_type === 'merchant' ? route('admin.merchants.reject', $user) : route('admin.partners.reject', $user) }}">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        سبب الرفض <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" 
                              name="rejection_reason" 
                              rows="3" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="اكتب سبب رفض المستخدم..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-reverse space-x-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        رفض المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">تعليق المستخدم</h3>
                <button onclick="closeSuspendModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                @csrf
                <div class="mb-4">
                    <label for="suspension_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        سبب التعليق <span class="text-red-500">*</span>
                    </label>
                    <textarea id="suspension_reason" 
                              name="suspension_reason" 
                              rows="3" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="اكتب سبب تعليق المستخدم..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-reverse space-x-3">
                    <button type="button" 
                            onclick="closeSuspendModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                        تعليق المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

function openSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
}

function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
    document.getElementById('suspension_reason').value = '';
}

// Close modals when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.getElementById('suspendModal').addEventListener('click', function(e) {
    if (e.target === this) closeSuspendModal();
});
</script>
@endsection
