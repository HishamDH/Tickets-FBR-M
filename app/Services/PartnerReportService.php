<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerWallet;
use App\Models\PartnerWalletTransaction;
use App\Models\Merchant;
use App\Models\Booking;
use App\Models\ScheduledReport;
use App\Mail\PartnerReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class PartnerReportService
{
    protected PartnerAnalyticsService $analyticsService;

    public function __construct(PartnerAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * إنشاء تقرير شامل للشريك
     */
    public function generateComprehensiveReport(Partner $partner, array $options = []): array
    {
        $reportData = [
            'partner' => $partner,
            'generated_at' => now(),
            'period' => [
                'start' => $options['start_date'] ?? Carbon::now()->startOfMonth(),
                'end' => $options['end_date'] ?? Carbon::now()->endOfMonth(),
            ],
        ];

        // بيانات الأداء
        $reportData['performance'] = $this->analyticsService->getPartnerPerformanceReport(
            $partner,
            $reportData['period']['start'],
            $reportData['period']['end']
        );

        // إحصائيات مقارنة مع الفترة السابقة
        $previousPeriod = $this->calculatePreviousPeriod(
            $reportData['period']['start'],
            $reportData['period']['end']
        );

        $reportData['comparison'] = $this->generateComparisonData($partner, $reportData['period'], $previousPeriod);

        // توقعات الأداء
        $reportData['forecasts'] = $this->generatePerformanceForecasts($partner);

        // تحليل التجار
        $reportData['merchants_analysis'] = $this->generateMerchantsAnalysis($partner, $reportData['period']);

        // توصيات التحسين
        $reportData['recommendations'] = $this->generatePerformanceRecommendations($partner, $reportData);

        return $reportData;
    }

    /**
     * إنشاء تقرير Excel
     */
    public function generateExcelReport(Partner $partner, array $options = []): string
    {
        $reportData = $this->generateComprehensiveReport($partner, $options);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // إعداد الرأس
        $sheet->setTitle('Partner Report - ' . $partner->name);
        $sheet->setCellValue('A1', 'تقرير أداء الشريك');
        $sheet->setCellValue('A2', 'الشريك: ' . $partner->name);
        $sheet->setCellValue('A3', 'الفترة: ' . $reportData['period']['start']->format('Y-m-d') . ' إلى ' . $reportData['period']['end']->format('Y-m-d'));
        $sheet->setCellValue('A4', 'تاريخ الإنشاء: ' . $reportData['generated_at']->format('Y-m-d H:i:s'));

        // الملخص المالي
        $row = 6;
        $sheet->setCellValue("A{$row}", 'الملخص المالي');
        $row++;
        $sheet->setCellValue("A{$row}", 'الرصيد الحالي');
        $sheet->setCellValue("B{$row}", $reportData['performance']['financial']['current_balance']);
        $row++;
        $sheet->setCellValue("A{$row}", 'إجمالي المكتسب');
        $sheet->setCellValue("B{$row}", $reportData['performance']['financial']['total_earned']);
        $row++;
        $sheet->setCellValue("A{$row}", 'أرباح الفترة');
        $sheet->setCellValue("B{$row}", $reportData['performance']['financial']['period_earnings']);
        $row++;
        $sheet->setCellValue("A{$row}", 'مسحوبات الفترة');
        $sheet->setCellValue("B{$row}", $reportData['performance']['financial']['period_withdrawals']);

        // بيانات التجار
        $row += 3;
        $sheet->setCellValue("A{$row}", 'بيانات التجار');
        $row++;
        $sheet->setCellValue("A{$row}", 'اسم التاجر');
        $sheet->setCellValue("B{$row}", 'عدد الحجوزات');
        $sheet->setCellValue("C{$row}", 'إجمالي الإيرادات');
        $sheet->setCellValue("D{$row}", 'العمولة المكتسبة');

        $row++;
        foreach ($reportData['merchants_analysis']['merchants'] as $merchant) {
            $sheet->setCellValue("A{$row}", $merchant['business_name']);
            $sheet->setCellValue("B{$row}", $merchant['bookings_count']);
            $sheet->setCellValue("C{$row}", $merchant['total_revenue']);
            $sheet->setCellValue("D{$row}", $merchant['commission_earned']);
            $row++;
        }

        // حفظ الملف
        $fileName = 'partner_report_' . $partner->id . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'reports/partners/' . $fileName;
        
        $writer = new Xlsx($spreadsheet);
        $fullPath = storage_path('app/' . $filePath);
        
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        $writer->save($fullPath);

        return $filePath;
    }

    /**
     * إنشاء تقرير PDF
     */
    public function generatePDFReport(Partner $partner, array $options = []): string
    {
        $reportData = $this->generateComprehensiveReport($partner, $options);
        
        $pdf = PDF::loadView('reports.partner.comprehensive', $reportData);
        
        $fileName = 'partner_report_' . $partner->id . '_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        $filePath = 'reports/partners/' . $fileName;
        
        Storage::put($filePath, $pdf->output());
        
        return $filePath;
    }

    /**
     * إرسال التقرير بالبريد الإلكتروني
     */
    public function emailReport(Partner $partner, array $options = []): bool
    {
        try {
            $reportData = $this->generateComprehensiveReport($partner, $options);
            
            // إنشاء ملفات التقرير
            $excelPath = $this->generateExcelReport($partner, $options);
            $pdfPath = $this->generatePDFReport($partner, $options);
            
            // إرسال البريد الإلكتروني
            Mail::to($partner->user->email)->send(
                new PartnerReportMail($partner, $reportData, [
                    'excel' => storage_path('app/' . $excelPath),
                    'pdf' => storage_path('app/' . $pdfPath),
                ])
            );

            Log::info('Partner report emailed successfully', [
                'partner_id' => $partner->id,
                'email' => $partner->user->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to email partner report', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * جدولة التقرير
     */
    public function scheduleReport(Partner $partner, string $frequency, array $options = []): ScheduledReport
    {
        return ScheduledReport::create([
            'partner_id' => $partner->id,
            'report_type' => 'comprehensive',
            'frequency' => $frequency, // daily, weekly, monthly
            'options' => $options,
            'next_run_at' => $this->calculateNextRun($frequency),
            'is_active' => true,
        ]);
    }

    /**
     * تشغيل التقارير المجدولة
     */
    public function runScheduledReports(): int
    {
        $reports = ScheduledReport::where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->get();

        $processed = 0;

        foreach ($reports as $report) {
            try {
                $this->emailReport($report->partner, $report->options);
                
                $report->update([
                    'last_run_at' => now(),
                    'next_run_at' => $this->calculateNextRun($report->frequency),
                    'run_count' => $report->run_count + 1,
                ]);

                $processed++;
            } catch (\Exception $e) {
                Log::error('Failed to run scheduled report', [
                    'report_id' => $report->id,
                    'partner_id' => $report->partner_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $processed;
    }

    /**
     * تحليل أداء الشريك مقابل الشركاء الآخرين
     */
    public function generateBenchmarkReport(Partner $partner): array
    {
        $allPartners = Partner::with('wallet')->get();
        $partnerMetrics = $this->calculatePartnerMetrics($partner);

        $benchmark = [
            'partner_metrics' => $partnerMetrics,
            'industry_average' => $this->calculateIndustryAverages($allPartners),
            'rankings' => $this->calculatePartnerRankings($partner, $allPartners),
            'performance_percentile' => $this->calculatePerformancePercentile($partner, $allPartners),
        ];

        return $benchmark;
    }

    /**
     * تحليل الاتجاهات الشهرية
     */
    public function generateTrendAnalysis(Partner $partner, int $months = 12): array
    {
        $trends = [];
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->copy()->addMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            $monthData = $this->analyticsService->getPartnerPerformanceReport(
                $partner,
                $monthStart,
                $monthEnd
            );

            $trends[] = [
                'month' => $monthStart->format('Y-m'),
                'earnings' => $monthData['financial']['period_earnings'] ?? 0,
                'new_merchants' => $monthData['referrals']['period_new_merchants'] ?? 0,
                'bookings' => $monthData['performance']['period_bookings'] ?? 0,
                'revenue_generated' => $monthData['performance']['period_revenue_generated'] ?? 0,
            ];
        }

        return [
            'monthly_trends' => $trends,
            'growth_rates' => $this->calculateGrowthRates($trends),
            'seasonal_patterns' => $this->identifySeasonalPatterns($trends),
            'forecasts' => $this->generateTrendForecasts($trends),
        ];
    }

    // Helper Methods

    private function calculatePreviousPeriod(Carbon $start, Carbon $end): array
    {
        $duration = $start->diffInDays($end);
        
        return [
            'start' => $start->copy()->subDays($duration + 1),
            'end' => $start->copy()->subDay(),
        ];
    }

    private function generateComparisonData(Partner $partner, array $currentPeriod, array $previousPeriod): array
    {
        $current = $this->analyticsService->getPartnerPerformanceReport(
            $partner,
            $currentPeriod['start'],
            $currentPeriod['end']
        );

        $previous = $this->analyticsService->getPartnerPerformanceReport(
            $partner,
            $previousPeriod['start'],
            $previousPeriod['end']
        );

        return [
            'earnings_change' => $this->calculatePercentageChange(
                $previous['financial']['period_earnings'] ?? 0,
                $current['financial']['period_earnings'] ?? 0
            ),
            'merchants_change' => $this->calculatePercentageChange(
                $previous['referrals']['period_new_merchants'] ?? 0,
                $current['referrals']['period_new_merchants'] ?? 0
            ),
            'bookings_change' => $this->calculatePercentageChange(
                $previous['performance']['period_bookings'] ?? 0,
                $current['performance']['period_bookings'] ?? 0
            ),
            'revenue_change' => $this->calculatePercentageChange(
                $previous['performance']['period_revenue_generated'] ?? 0,
                $current['performance']['period_revenue_generated'] ?? 0
            ),
        ];
    }

    private function generatePerformanceForecasts(Partner $partner): array
    {
        $trendAnalysis = $this->generateTrendAnalysis($partner, 6);
        $trends = $trendAnalysis['monthly_trends'];

        if (count($trends) < 3) {
            return [
                'next_month_earnings' => 0,
                'confidence' => 'low',
                'trend_direction' => 'unknown',
            ];
        }

        $recentTrends = array_slice($trends, -3);
        $avgGrowthRate = array_sum(array_column($recentTrends, 'earnings')) / 3;
        $lastMonthEarnings = end($trends)['earnings'];

        return [
            'next_month_earnings' => max(0, $lastMonthEarnings * 1.1), // تفاؤل بنمو 10%
            'confidence' => $avgGrowthRate > 0 ? 'high' : 'medium',
            'trend_direction' => $avgGrowthRate > 0 ? 'up' : ($avgGrowthRate < 0 ? 'down' : 'stable'),
        ];
    }

    private function generateMerchantsAnalysis(Partner $partner, array $period): array
    {
        $merchants = $partner->merchants()->with(['bookings' => function($q) use ($period) {
            $q->whereBetween('created_at', [$period['start'], $period['end']])
              ->where('payment_status', 'paid');
        }])->get();

        $analysis = [
            'total_merchants' => $merchants->count(),
            'active_merchants' => $merchants->filter(fn($m) => $m->bookings->count() > 0)->count(),
            'merchants' => $merchants->map(function($merchant) use ($partner) {
                $bookings = $merchant->bookings;
                $totalRevenue = $bookings->sum('total_amount');
                
                return [
                    'id' => $merchant->id,
                    'business_name' => $merchant->business_name,
                    'bookings_count' => $bookings->count(),
                    'total_revenue' => $totalRevenue,
                    'commission_earned' => $totalRevenue * ($partner->commission_rate / 100),
                    'performance_rating' => $this->calculateMerchantPerformanceRating($merchant),
                ];
            })->sortByDesc('total_revenue')->values()->toArray(),
        ];

        return $analysis;
    }

    private function generatePerformanceRecommendations(Partner $partner, array $reportData): array
    {
        $recommendations = [];

        // توصيات بناءً على الأداء المالي
        $earnings = $reportData['performance']['financial']['period_earnings'] ?? 0;
        $previousEarnings = $reportData['comparison']['earnings_change'] ?? 0;

        if ($previousEarnings < 0) {
            $recommendations[] = [
                'type' => 'financial',
                'priority' => 'high',
                'title' => 'تحسين الأداء المالي',
                'description' => 'انخفضت أرباحك بنسبة ' . abs($previousEarnings) . '%. ننصح بزيادة نشاط التسويق والتركيز على الخدمات عالية القيمة.',
                'actions' => [
                    'مراجعة استراتيجية التسويق',
                    'تحليل أداء التجار المرتبطين',
                    'تحسين جودة الخدمات المقدمة',
                ]
            ];
        }

        // توصيات بناءً على عدد التجار
        $activeMerchants = $reportData['merchants_analysis']['active_merchants'] ?? 0;
        $totalMerchants = $reportData['merchants_analysis']['total_merchants'] ?? 0;

        if ($totalMerchants > 0 && ($activeMerchants / $totalMerchants) < 0.7) {
            $recommendations[] = [
                'type' => 'merchants',
                'priority' => 'medium',
                'title' => 'تحسين نشاط التجار',
                'description' => 'نسبة التجار النشطين منخفضة. يمكن تحسين هذا من خلال الدعم والمتابعة.',
                'actions' => [
                    'متابعة دورية مع التجار غير النشطين',
                    'تقديم تدريب على استخدام المنصة',
                    'تحفيز التجار بعروض خاصة',
                ]
            ];
        }

        return $recommendations;
    }

    private function calculateNextRun(string $frequency): Carbon
    {
        return match($frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            default => now()->addMonth(),
        };
    }

    private function calculatePartnerMetrics(Partner $partner): array
    {
        $wallet = $partner->wallet;
        
        return [
            'total_earned' => $wallet ? $wallet->total_earned : 0,
            'current_balance' => $wallet ? $wallet->balance : 0,
            'merchants_count' => $partner->merchants()->count(),
            'active_merchants' => $partner->merchants()->whereHas('bookings')->count(),
            'monthly_earnings' => $wallet ? $wallet->transactions()
                ->where('type', 'commission')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount') : 0,
        ];
    }

    private function calculateIndustryAverages(Collection $partners): array
    {
        $totalPartners = $partners->count();
        
        if ($totalPartners === 0) return [];

        return [
            'avg_total_earned' => $partners->sum('wallet.total_earned') / $totalPartners,
            'avg_current_balance' => $partners->sum('wallet.balance') / $totalPartners,
            'avg_merchants_count' => $partners->sum(fn($p) => $p->merchants()->count()) / $totalPartners,
        ];
    }

    private function calculatePartnerRankings(Partner $partner, Collection $partners): array
    {
        $sorted = $partners->sortByDesc('wallet.total_earned');
        $rank = $sorted->search(fn($p) => $p->id === $partner->id) + 1;
        
        return [
            'overall_rank' => $rank,
            'total_partners' => $partners->count(),
            'percentile' => round((($partners->count() - $rank + 1) / $partners->count()) * 100, 1),
        ];
    }

    private function calculatePerformancePercentile(Partner $partner, Collection $partners): float
    {
        $partnerEarnings = $partner->wallet ? $partner->wallet->total_earned : 0;
        $lowerPerformers = $partners->filter(fn($p) => ($p->wallet ? $p->wallet->total_earned : 0) < $partnerEarnings)->count();
        
        return $partners->count() > 0 ? round(($lowerPerformers / $partners->count()) * 100, 1) : 0;
    }

    private function calculateGrowthRates(array $trends): array
    {
        $growthRates = [];
        
        for ($i = 1; $i < count($trends); $i++) {
            $current = $trends[$i]['earnings'];
            $previous = $trends[$i - 1]['earnings'];
            
            $growthRate = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
            $growthRates[] = round($growthRate, 2);
        }
        
        return $growthRates;
    }

    private function identifySeasonalPatterns(array $trends): array
    {
        // تحليل بسيط للأنماط الموسمية
        $monthlyAvg = [];
        
        foreach ($trends as $trend) {
            $month = (int) date('m', strtotime($trend['month'] . '-01'));
            $monthlyAvg[$month][] = $trend['earnings'];
        }
        
        $patterns = [];
        foreach ($monthlyAvg as $month => $earnings) {
            $patterns[$month] = array_sum($earnings) / count($earnings);
        }
        
        return $patterns;
    }

    private function generateTrendForecasts(array $trends): array
    {
        if (count($trends) < 3) return [];
        
        $recent = array_slice($trends, -3);
        $avgGrowth = array_sum(array_column($recent, 'earnings')) / 3;
        
        return [
            'next_month' => max(0, end($trends)['earnings'] * 1.05),
            'next_quarter' => max(0, $avgGrowth * 3),
            'confidence' => $avgGrowth > 0 ? 'high' : 'medium',
        ];
    }

    private function calculateMerchantPerformanceRating(Merchant $merchant): string
    {
        $bookingsCount = $merchant->bookings()->where('payment_status', 'paid')->count();
        // Get average rating from reviews instead of bookings
        $avgRating = \App\Models\Review::whereHas('service', function($q) use ($merchant) {
            $q->where('merchant_id', $merchant->user_id);
        })->where('is_approved', true)->avg('rating') ?? 0;
        
        if ($bookingsCount >= 20 && $avgRating >= 4.5) return 'excellent';
        if ($bookingsCount >= 10 && $avgRating >= 4.0) return 'good';
        if ($bookingsCount >= 5 && $avgRating >= 3.5) return 'average';
        if ($bookingsCount > 0) return 'poor';
        
        return 'inactive';
    }

    private function calculatePercentageChange(float $old, float $new): float
    {
        if ($old == 0) return $new > 0 ? 100 : 0;
        
        return round((($new - $old) / $old) * 100, 2);
    }
}
