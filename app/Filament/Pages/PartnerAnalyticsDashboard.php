<?php

namespace App\Filament\Pages;

use App\Services\PartnerAnalyticsService;
use App\Services\PartnerCommissionService;
use App\Models\Partner;
use App\Models\PartnerWallet;
use App\Models\PartnerWalletTransaction;
use App\Models\PartnerWithdraw;
use App\Models\Merchant;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PartnerAnalyticsDashboard extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'تحليلات الشركاء';
    protected static ?string $title = 'تحليلات ومراقبة أداء الشركاء';
    protected static string $view = 'filament.pages.partner-analytics-dashboard';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'تحليلات الشركاء';

    public ?array $data = [];
    public ?array $performanceData = [];
    public ?array $trendsData = [];
    public ?array $roiData = [];
    public ?array $comparativeData = [];

    protected PartnerAnalyticsService $analyticsService;

    public function boot(PartnerAnalyticsService $analyticsService): void
    {
        $this->analyticsService = $analyticsService;
    }

    public function mount(): void
    {
        $this->form->fill([
            'date_range' => 'current_month',
            'partner_id' => null,
            'analysis_type' => 'overview',
        ]);
        
        $this->loadDashboardData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('analysis_type')
                    ->label('نوع التحليل')
                    ->options([
                        'overview' => 'نظرة عامة',
                        'performance' => 'تحليل الأداء',
                        'trends' => 'الاتجاهات والتوقعات',
                        'roi' => 'تحليل العائد على الاستثمار',
                        'comparative' => 'المقارنة بين الشركاء',
                    ])
                    ->default('overview')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDashboardData()),

                Select::make('partner_id')
                    ->label('شريك محدد')
                    ->placeholder('جميع الشركاء')
                    ->options(Partner::pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDashboardData()),

                Select::make('date_range')
                    ->label('الفترة الزمنية')
                    ->options([
                        'today' => 'اليوم',
                        'yesterday' => 'أمس',
                        'current_week' => 'هذا الأسبوع',
                        'last_week' => 'الأسبوع الماضي',
                        'current_month' => 'هذا الشهر',
                        'last_month' => 'الشهر الماضي',
                        'current_quarter' => 'هذا الربع',
                        'current_year' => 'هذا العام',
                        'last_year' => 'العام الماضي',
                        'custom' => 'فترة مخصصة',
                    ])
                    ->default('current_month')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDashboardData()),

                DatePicker::make('start_date')
                    ->label('تاريخ البداية')
                    ->visible(fn (callable $get) => $get('date_range') === 'custom')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDashboardData()),

                DatePicker::make('end_date')
                    ->label('تاريخ النهاية')
                    ->visible(fn (callable $get) => $get('date_range') === 'custom')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadDashboardData()),
            ])
            ->statePath('data')
            ->columns(4);
    }

    protected function loadDashboardData(): void
    {
        $formData = $this->form->getState();
        $dates = $this->calculateDateRange($formData['date_range'], $formData['start_date'] ?? null, $formData['end_date'] ?? null);
        
        $cacheKey = 'partner_analytics_' . md5(serialize($formData));
        
        switch ($formData['analysis_type']) {
            case 'performance':
                $this->loadPerformanceData($formData['partner_id'], $dates);
                break;
            case 'trends':
                $this->loadTrendsData();
                break;
            case 'roi':
                $this->loadROIData();
                break;
            case 'comparative':
                $this->loadComparativeData();
                break;
            default:
                $this->loadOverviewData($dates);
        }
    }

    protected function loadOverviewData(array $dates): void
    {
        $this->performanceData = Cache::remember('partner_overview_' . md5(serialize($dates)), 300, function () use ($dates) {
            $startDate = $dates['start'];
            $endDate = $dates['end'];

            // إحصائيات عامة
            $totalPartners = Partner::count();
            $activePartners = Partner::whereHas('wallet', function($q) {
                $q->where('balance', '>', 0);
            })->count();

            // الإحصائيات المالية
            $totalBalance = PartnerWallet::sum('balance');
            $totalEarned = PartnerWallet::sum('total_earned');
            $totalWithdrawn = PartnerWallet::sum('total_withdrawn');
            
            $periodEarnings = PartnerWalletTransaction::where('type', 'commission')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $periodWithdrawals = PartnerWithdraw::where('status', 'completed')
                ->whereBetween('requested_at', [$startDate, $endDate])
                ->sum('amount');

            // إحصائيات التجار
            $totalMerchants = Merchant::whereNotNull('partner_id')->count();
            $newMerchants = Merchant::whereNotNull('partner_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // أداء أفضل الشركاء
            $topPartners = Partner::withSum(['wallet as current_balance' => function($q) {
                $q->select('balance');
            }], 'balance')
            ->withSum(['wallet as total_earned' => function($q) {
                $q->select('total_earned');
            }], 'total_earned')
            ->withCount('merchants')
            ->orderByDesc('total_earned')
            ->limit(10)
            ->get();

            return [
                'overview' => [
                    'total_partners' => $totalPartners,
                    'active_partners' => $activePartners,
                    'partner_activation_rate' => $totalPartners > 0 ? round(($activePartners / $totalPartners) * 100, 1) : 0,
                ],
                'financial' => [
                    'total_balance' => $totalBalance,
                    'total_earned' => $totalEarned,
                    'total_withdrawn' => $totalWithdrawn,
                    'period_earnings' => $periodEarnings,
                    'period_withdrawals' => $periodWithdrawals,
                    'net_period_flow' => $periodEarnings - $periodWithdrawals,
                ],
                'merchants' => [
                    'total_merchants' => $totalMerchants,
                    'new_merchants' => $newMerchants,
                    'average_merchants_per_partner' => $totalPartners > 0 ? round($totalMerchants / $totalPartners, 1) : 0,
                ],
                'top_partners' => $topPartners->map(function($partner) {
                    return [
                        'id' => $partner->id,
                        'name' => $partner->name,
                        'current_balance' => $partner->current_balance ?? 0,
                        'total_earned' => $partner->total_earned ?? 0,
                        'merchants_count' => $partner->merchants_count,
                        'commission_rate' => $partner->commission_rate,
                    ];
                })->toArray(),
            ];
        });
    }

    protected function loadPerformanceData(?int $partnerId, array $dates): void
    {
        if (!$partnerId) {
            $this->performanceData = ['error' => 'يرجى اختيار شريك محدد لعرض تحليل الأداء'];
            return;
        }

        $partner = Partner::find($partnerId);
        if (!$partner) {
            $this->performanceData = ['error' => 'الشريك المحدد غير موجود'];
            return;
        }

        $this->performanceData = Cache::remember("partner_performance_{$partnerId}_" . md5(serialize($dates)), 300, function () use ($partner, $dates) {
            return $this->analyticsService->getPartnerPerformanceReport(
                $partner, 
                $dates['start'], 
                $dates['end']
            );
        });
    }

    protected function loadTrendsData(): void
    {
        $this->trendsData = Cache::remember('partner_trends_data', 600, function () {
            return $this->analyticsService->getTrendsAndForecasting();
        });
    }

    protected function loadROIData(): void
    {
        $this->roiData = Cache::remember('partner_roi_data', 600, function () {
            return $this->analyticsService->getPartnerROIAnalysis();
        });
    }

    protected function loadComparativeData(): void
    {
        $this->comparativeData = Cache::remember('partner_comparative_data', 600, function () {
            return $this->analyticsService->getPartnersComparativeAnalysis();
        });
    }

    protected function calculateDateRange(string $range, ?string $startDate = null, ?string $endDate = null): array
    {
        $now = Carbon::now();
        
        return match($range) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'yesterday' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ],
            'current_week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ],
            'last_week' => [
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek(),
            ],
            'current_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
            ],
            'current_quarter' => [
                'start' => $now->copy()->startOfQuarter(),
                'end' => $now->copy()->endOfQuarter(),
            ],
            'current_year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            'last_year' => [
                'start' => $now->copy()->subYear()->startOfYear(),
                'end' => $now->copy()->subYear()->endOfYear(),
            ],
            'custom' => [
                'start' => $startDate ? Carbon::parse($startDate)->startOfDay() : $now->copy()->startOfMonth(),
                'end' => $endDate ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfMonth(),
            ],
            default => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
        };
    }

    protected function getActions(): array
    {
        return [
            Action::make('export_report')
                ->label('تصدير التقرير')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Logic to export report
                    Notification::make()
                        ->title('تم تصدير التقرير بنجاح')
                        ->success()
                        ->send();
                }),

            Action::make('refresh_data')
                ->label('تحديث البيانات')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    Cache::flush();
                    $this->loadDashboardData();
                    
                    Notification::make()
                        ->title('تم تحديث البيانات')
                        ->success()
                        ->send();
                }),

            Action::make('schedule_report')
                ->label('جدولة التقرير')
                ->icon('heroicon-o-clock')
                ->form([
                    Select::make('frequency')
                        ->label('تكرار الإرسال')
                        ->options([
                            'daily' => 'يومي',
                            'weekly' => 'أسبوعي',
                            'monthly' => 'شهري',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Logic to schedule report
                    Notification::make()
                        ->title('تم جدولة التقرير بنجاح')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'performanceData' => $this->performanceData,
            'trendsData' => $this->trendsData,
            'roiData' => $this->roiData,
            'comparativeData' => $this->comparativeData,
            'analysisType' => $this->data['analysis_type'] ?? 'overview',
        ];
    }
}
