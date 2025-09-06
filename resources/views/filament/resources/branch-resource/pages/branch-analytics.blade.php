<x-filament-panels::page>
    <div class="space-y-6">
        <!-- معلومات الفرع -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">{{ $this->record->name }}</h2>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $this->record->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $this->record->is_active ? 'نشط' : 'معطل' }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                @if($this->record->address)
                    <div class="flex items-center text-gray-600">
                        <x-heroicon-o-map-pin class="w-4 h-4 mr-2" />
                        {{ $this->record->address }}
                    </div>
                @endif
                
                @if($this->record->phone)
                    <div class="flex items-center text-gray-600">
                        <x-heroicon-o-phone class="w-4 h-4 mr-2" />
                        {{ $this->record->phone }}
                    </div>
                @endif
                
                @if($this->record->capacity)
                    <div class="flex items-center text-gray-600">
                        <x-heroicon-o-users class="w-4 h-4 mr-2" />
                        سعة {{ number_format($this->record->capacity) }} شخص
                    </div>
                @endif
                
                @if($this->record->manager_name)
                    <div class="flex items-center text-gray-600">
                        <x-heroicon-o-user-circle class="w-4 h-4 mr-2" />
                        المدير: {{ $this->record->manager_name }}
                    </div>
                @endif
                
                @if($this->record->manager_phone)
                    <div class="flex items-center text-gray-600">
                        <x-heroicon-o-device-phone-mobile class="w-4 h-4 mr-2" />
                        {{ $this->record->manager_phone }}
                    </div>
                @endif
                
                @if($this->record->services)
                    <div class="flex items-center text-gray-600">
                        <x-heroicon-o-squares-2x2 class="w-4 h-4 mr-2" />
                        {{ is_array($this->record->services) ? implode(', ', $this->record->services) : $this->record->services }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ساعات العمل -->
        @if($this->record->opening_hours)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ساعات العمل</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($this->record->opening_hours as $schedule)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium">{{ $schedule['day'] ?? '' }}</span>
                            <span class="text-sm text-gray-600">
                                {{ $schedule['open_time'] ?? '' }} - {{ $schedule['close_time'] ?? '' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- المخطط البياني للإيرادات -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">الإيرادات الشهرية (آخر 12 شهر)</h3>
            <div class="h-64">
                @php
                    $chartData = $this->getMonthlyRevenueChart();
                @endphp
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- جدول أحدث الحجوزات -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">أحدث الحجوزات</h3>
                <a href="{{ url('/admin/paid-reservations?tableFilters[branch_id][value]=' . $this->record->id) }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    عرض الكل
                </a>
            </div>
            
            @php
                $recentBookings = App\Models\PaidReservation::where('branch_id', $this->record->id)
                    ->with(['user', 'offering'])
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp
            
            @if($recentBookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العميل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخدمة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->user->name ?? 'غير محدد' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->offering->title ?? 'غير محدد' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($booking->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                               ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">لا توجد حجوزات لهذا الفرع بعد</p>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const chartData = @json($chartData);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'الإيرادات ($)',
                        data: chartData.data,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
