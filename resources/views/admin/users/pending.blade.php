@extends('layouts.admin')

@section('title', 'التفعيلات المنتظرة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">التفعيلات المنتظرة ⏳</h1>
            <p class="mt-2 text-gray-600">إدارة طلبات تفعيل التجار والشركاء الجدد</p>
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

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 ml-3"></i>
                    <div>
                        <strong>خطأ!</strong> {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Pending Merchants -->
            <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-store text-xl"></i>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold text-gray-900">التجار المنتظرين</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $pendingMerchants->count() }}</p>
                        <p class="text-sm text-gray-500">طلب تفعيل</p>
                    </div>
                </div>
            </div>

            <!-- Pending Partners -->
            <div class="bg-white rounded-lg shadow-sm border border-green-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-handshake text-xl"></i>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold text-gray-900">الشركاء المنتظرين</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $pendingPartners->count() }}</p>
                        <p class="text-sm text-gray-500">طلب تفعيل</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Merchants Section -->
        @if($pendingMerchants->count() > 0)
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-store text-blue-600 ml-3"></i>
                    التجار المنتظرين للتفعيل ({{ $pendingMerchants->count() }})
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاجر</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النشاط التجاري</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المعلومات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingMerchants as $merchant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($merchant->name, 0, 1) }}
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $merchant->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $merchant->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $merchant->merchant->business_name ?? 'غير محدد' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $merchant->merchant->business_type ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $merchant->created_at->format('Y/m/d') }}
                                <br>
                                <span class="text-xs">{{ $merchant->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-phone ml-2 text-gray-400"></i>
                                    {{ $merchant->merchant->contact_phone ?? 'غير محدد' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt ml-2 text-gray-400"></i>
                                    {{ $merchant->merchant->address ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-reverse space-x-2">
                                    <!-- Approve Button -->
                                    <form method="POST" action="{{ route('admin.merchants.approve', $merchant) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من تفعيل هذا التاجر؟')"
                                                class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                                            <i class="fas fa-check ml-1"></i>
                                            تفعيل
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <button onclick="openRejectModal('merchant', {{ $merchant->id }})"
                                            class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-700 transition-colors">
                                        <i class="fas fa-times ml-1"></i>
                                        رفض
                                    </button>
                                    
                                    <!-- View Details -->
                                    <a href="{{ route('admin.users.show', $merchant) }}"
                                       class="bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-eye ml-1"></i>
                                        تفاصيل
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center mb-8">
            <i class="fas fa-store text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد طلبات تفعيل للتجار</h3>
            <p class="text-gray-500">جميع التجار تم تفعيلهم أو لا توجد طلبات جديدة</p>
        </div>
        @endif

        <!-- Pending Partners Section -->
        @if($pendingPartners->count() > 0)
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-handshake text-green-600 ml-3"></i>
                    الشركاء المنتظرين للتفعيل ({{ $pendingPartners->count() }})
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشريك</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشركة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">معدل العمولة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingPartners as $partner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($partner->name, 0, 1) }}
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $partner->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $partner->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $partner->partner->company_name ?? 'غير محدد' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $partner->partner->contact_person ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $partner->created_at->format('Y/m/d') }}
                                <br>
                                <span class="text-xs">{{ $partner->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $partner->partner->commission_rate ?? '0' }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-reverse space-x-2">
                                    <!-- Approve Button -->
                                    <form method="POST" action="{{ route('admin.partners.approve', $partner) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من تفعيل هذا الشريك؟')"
                                                class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                                            <i class="fas fa-check ml-1"></i>
                                            تفعيل
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <button onclick="openRejectModal('partner', {{ $partner->id }})"
                                            class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-700 transition-colors">
                                        <i class="fas fa-times ml-1"></i>
                                        رفض
                                    </button>
                                    
                                    <!-- View Details -->
                                    <a href="{{ route('admin.users.show', $partner) }}"
                                       class="bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-eye ml-1"></i>
                                        تفاصيل
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center mb-8">
            <i class="fas fa-handshake text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد طلبات تفعيل للشركاء</h3>
            <p class="text-gray-500">جميع الشركاء تم تفعيلهم أو لا توجد طلبات جديدة</p>
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">رفض طلب التفعيل</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="rejectForm" method="POST">
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
                              placeholder="اكتب سبب رفض طلب التفعيل..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-reverse space-x-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        رفض الطلب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(type, userId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    
    if (type === 'merchant') {
        form.action = `/admin/merchants/${userId}/reject`;
    } else {
        form.action = `/admin/partners/${userId}/reject`;
    }
    
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const textarea = document.getElementById('rejection_reason');
    
    modal.classList.add('hidden');
    textarea.value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection
