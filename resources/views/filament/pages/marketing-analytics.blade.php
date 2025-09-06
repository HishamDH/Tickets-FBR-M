<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Date Range Selector -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">فترة التحليل</h3>
                <div class="flex space-x-2 rtl:space-x-reverse">
                    <select wire:model.live="dateRange" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="7">آخر 7 أيام</option>
                        <option value="14">آخر 14 يوم</option>
                        <option value="30">آخر 30 يوم</option>
                        <option value="60">آخر 60 يوم</option>
                        <option value="90">آخر 90 يوم</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Marketing Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $stats = $this->getMarketingStats();
            @endphp

            <!-- Coupons Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold">نظام الكوبونات</h4>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm opacity-90">الكوبونات النشطة: {{ $stats['coupons']['active'] }}</p>
                            <p class="text-sm opacity-90">الاستخدامات: {{ number_format($stats['coupons']['usage_count']) }}</p>
                            <p class="text-sm opacity-90">الوفورات: {{ number_format($stats['coupons']['total_savings'], 2) }} ر.س</p>
                        </div>
                    </div>
                    <div class="text-4xl opacity-80">
                        🎫
                    </div>
                </div>
            </div>

            <!-- Loyalty Card -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold">برامج الولاء</h4>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm opacity-90">البرامج النشطة: {{ $stats['loyalty']['active_programs'] }}</p>
                            <p class="text-sm opacity-90">النقاط الممنوحة: {{ number_format($stats['loyalty']['points_awarded']) }}</p>
                            <p class="text-sm opacity-90">المستخدمون النشطون: {{ number_format($stats['loyalty']['active_users']) }}</p>
                        </div>
                    </div>
                    <div class="text-4xl opacity-80">
                        ❤️
                    </div>
                </div>
            </div>

            <!-- Referrals Card -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-semibold">برامج الإحالة</h4>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm opacity-90">البرامج النشطة: {{ $stats['referrals']['active_programs'] }}</p>
                            <p class="text-sm opacity-90">الإحالات الناجحة: {{ number_format($stats['referrals']['successful_referrals']) }}</p>
                            <p class="text-sm opacity-90">معدل النجاح: {{ $stats['referrals']['conversion_rate'] }}%</p>
                        </div>
                    </div>
                    <div class="text-4xl opacity-80">
                        🤝
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">اتجاهات الاستخدام</h3>
            <div class="h-64">
                @php
                    $trends = $this->getTrendData();
                @endphp
                <canvas id="trendsChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Top Performing Items -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Coupons -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">أفضل الكوبونات أداءً</h3>
                <div class="space-y-3">
                    @foreach($stats['coupons']['top_coupons'] as $coupon)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $coupon->name }}</p>
                                <p class="text-sm text-gray-600">{{ $coupon->code }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600">{{ $coupon->usages_count }}</p>
                                <p class="text-xs text-gray-500">استخدام</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">مؤشرات الأداء الرئيسية</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">متوسط قيمة الكوبون</span>
                        <span class="font-bold text-gray-900">
                            {{ $stats['coupons']['usage_count'] > 0 ? number_format($stats['coupons']['total_savings'] / $stats['coupons']['usage_count'], 2) : 0 }} ر.س
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">متوسط النقاط المستردة</span>
                        <span class="font-bold text-gray-900">
                            {{ $stats['loyalty']['active_users'] > 0 ? number_format($stats['loyalty']['points_redeemed'] / max($stats['loyalty']['active_users'], 1)) : 0 }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">متوسط مكافأة الإحالة</span>
                        <span class="font-bold text-gray-900">
                            {{ $stats['referrals']['successful_referrals'] > 0 ? number_format($stats['referrals']['total_rewards'] / $stats['referrals']['successful_referrals'], 2) : 0 }} ر.س
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">معدل التحويل الإجمالي</span>
                        <span class="font-bold text-green-600">{{ $stats['referrals']['conversion_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">التحليلات التفصيلية</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['coupons']['total_savings'], 0) }}</div>
                    <div class="text-sm text-gray-600 mt-1">إجمالي الوفورات (ر.س)</div>
                    <div class="text-xs text-gray-500 mt-1">آخر {{ $dateRange }} يوم</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['loyalty']['points_awarded']) }}</div>
                    <div class="text-sm text-gray-600 mt-1">النقاط الممنوحة</div>
                    <div class="text-xs text-gray-500 mt-1">آخر {{ $dateRange }} يوم</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['referrals']['total_rewards'], 0) }}</div>
                    <div class="text-sm text-gray-600 mt-1">مكافآت الإحالة (ر.س)</div>
                    <div class="text-xs text-gray-500 mt-1">آخر {{ $dateRange }} يوم</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('trendsChart').getContext('2d');
            const trends = @json($this->getTrendData());
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trends.map(item => item.date),
                    datasets: [
                        {
                            label: 'الكوبونات',
                            data: trends.map(item => item.coupons),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'برامج الولاء',
                            data: trends.map(item => item.loyalty),
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'الإحالات',
                            data: trends.map(item => item.referrals),
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'التاريخ'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'عدد الاستخدامات'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'اتجاهات استخدام أنظمة التسويق'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
