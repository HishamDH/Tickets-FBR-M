@extends('layouts.app')

@section('title', 'Staff Management')

@push('styles')
<style>
/* Custom badge styles for consistent coloring */
.badge-manager { @apply bg-purple-100 text-purple-800; }
.badge-supervisor { @apply bg-blue-100 text-blue-800; }
.badge-cashier { @apply bg-green-100 text-green-800; }
.badge-staff { @apply bg-gray-100 text-gray-800; }

.badge-active { @apply bg-green-100 text-green-800; }
.badge-inactive { @apply bg-gray-100 text-gray-800; }
.badge-suspended { @apply bg-red-100 text-red-800; }

.shift-in-progress { @apply bg-green-100 text-green-800; }
.shift-scheduled { @apply bg-blue-100 text-blue-800; }
.shift-completed { @apply bg-gray-100 text-gray-800; }
.shift-cancelled { @apply bg-red-100 text-red-800; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100" dir="rtl">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full opacity-10 animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full opacity-15 animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        
        <div class="relative container mx-auto px-4 py-12">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h1 class="text-4xl font-bold mb-2">إدارة الموظفين 👥</h1>
                    <p class="text-blue-100 text-lg">إدارة فريق العمل والمناوبات والأداء</p>
                </div>
                <div class="hidden md:block">
                    <a href="{{ route('merchant.staff.create') }}" 
                       class="bg-white text-blue-600 px-6 py-3 rounded-xl font-bold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus ml-2"></i>إضافة موظف جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-xl mb-6 shadow-lg">
                <i class="fas fa-check-circle ml-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500 text-white p-4 rounded-xl mb-6 shadow-lg">
                <i class="fas fa-exclamation-triangle ml-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">إجمالي الموظفين</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_staff'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">الموظفين النشطين</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['active_staff'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-purple-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-600 text-sm font-medium">في المناوبة الآن</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['staff_on_duty'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-clock text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-yellow-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-600 text-sm font-medium">ساعات هذا الشهر</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['monthly_hours'], 1) }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100">
                        <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">إجراءات سريعة ⚡</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <a href="{{ route('merchant.staff.create') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border-2 border-blue-200 hover:border-blue-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-plus text-2xl"></i>
                        </div>
                        <span class="text-lg font-bold text-gray-900">إضافة موظف</span>
                        <span class="text-sm text-blue-600 font-medium">موظف جديد</span>
                    </a>

                    <a href="{{ route('merchant.staff.schedules') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl border-2 border-green-200 hover:border-green-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                        <span class="text-lg font-bold text-gray-900">إدارة المناوبات</span>
                        <span class="text-sm text-green-600 font-medium">جدولة العمل</span>
                    </a>

                    <a href="{{ route('merchant.staff.reports') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border-2 border-purple-200 hover:border-purple-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                        <span class="text-lg font-bold text-gray-900">تقارير الأداء</span>
                        <span class="text-sm text-purple-600 font-medium">إحصائيات مفصلة</span>
                    </a>

                    <div class="group flex flex-col items-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl border-2 border-yellow-200">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white mb-4">
                            <i class="fas fa-search text-2xl"></i>
                        </div>
                        <input type="text" placeholder="البحث عن موظف..." 
                               class="w-full text-center border-0 bg-transparent text-lg font-bold text-gray-900 placeholder-yellow-600"
                               id="staffSearch">
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List -->
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">قائمة الموظفين</h2>
            </div>
            
            @if($staff->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">الموظف</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">المنصب</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">كود الموظف</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">الحالة</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">المناوبة اليوم</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">تاريخ التوظيف</th>
                                <th class="px-6 py-4 text-right text-sm font-medium text-gray-500">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($staff as $employee)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($employee->employee->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $employee->employee->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $employee->employee->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $roleClass = match($employee->role) {
                                                'manager' => 'badge-manager',
                                                'supervisor' => 'badge-supervisor',
                                                'cashier' => 'badge-cashier',
                                                default => 'badge-staff'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleClass }}">
                                            {{ App\Models\MerchantEmployee::getRoles()[$employee->role] ?? $employee->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-mono">{{ $employee->employee_code }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusClass = match($employee->status) {
                                                'active' => 'badge-active',
                                                'inactive' => 'badge-inactive',
                                                default => 'badge-suspended'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            @if($employee->status == 'active') نشط
                                            @elseif($employee->status == 'inactive') غير نشط
                                            @else معلق @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $todayShift = $employee->shifts->first(); @endphp
                                        @if($todayShift)
                                            @php
                                                $shiftClass = match($todayShift->status) {
                                                    'in_progress' => 'shift-in-progress',
                                                    'scheduled' => 'shift-scheduled',
                                                    'completed' => 'shift-completed',
                                                    default => 'shift-cancelled'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $shiftClass }}">
                                                @if($todayShift->status == 'in_progress') في العمل
                                                @elseif($todayShift->status == 'scheduled') مجدولة
                                                @elseif($todayShift->status == 'completed') انتهت
                                                @else ملغية @endif
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">لا توجد مناوبة</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $employee->hire_date->format('Y/m/d') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2 space-x-reverse">
                                            <a href="{{ route('merchant.staff.show', $employee) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('merchant.staff.edit', $employee) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($todayShift && $todayShift->status == 'scheduled')
                                                <form action="{{ route('merchant.staff.clock-in', $employee) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 transition-colors">
                                                        <i class="fas fa-play" title="تسجيل دخول"></i>
                                                    </button>
                                                </form>
                                            @elseif($todayShift && $todayShift->status == 'in_progress')
                                                <form action="{{ route('merchant.staff.clock-out', $employee) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                                        <i class="fas fa-stop" title="تسجيل خروج"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $staff->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين بعد</h3>
                    <p class="text-gray-500 mb-6">ابدأ بإضافة أول موظف في فريقك</p>
                    <a href="{{ route('merchant.staff.create') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus ml-2"></i>إضافة موظف
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Staff search functionality
document.getElementById('staffSearch').addEventListener('input', function(e) {
    const query = e.target.value;
    
    if (query.length >= 2) {
        fetch(`{{ route('merchant.staff.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                // Implement search results display
                console.log('Search results:', data);
            })
            .catch(error => {
                console.error('Search error:', error);
            });
    }
});
</script>
@endsection
