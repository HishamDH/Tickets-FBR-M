<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Admin Reports
    public function adminRevenueReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $format = $request->get('format', 'view'); // view, pdf, excel

        $data = $this->getAdminRevenueData($startDate, $endDate);

        if ($format === 'pdf') {
            return $this->generateRevenuePDF($data, $startDate, $endDate);
        } elseif ($format === 'excel') {
            return $this->generateRevenueExcel($data, $startDate, $endDate);
        }

        return view('reports.admin.revenue', compact('data', 'startDate', 'endDate'));
    }

    public function merchantPerformanceReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $format = $request->get('format', 'view');

        $data = $this->getMerchantPerformanceData($startDate, $endDate);

        if ($format === 'pdf') {
            return $this->generateMerchantPerformancePDF($data, $startDate, $endDate);
        } elseif ($format === 'excel') {
            return $this->generateMerchantPerformanceExcel($data, $startDate, $endDate);
        }

        return view('reports.admin.merchant-performance', compact('data', 'startDate', 'endDate'));
    }

    // Merchant Reports
    public function merchantBookingsReport(Request $request)
    {
        $merchant = auth()->user()->merchant;
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $format = $request->get('format', 'view');

        $data = $this->getMerchantBookingsData($merchant, $startDate, $endDate);

        if ($format === 'pdf') {
            return $this->generateMerchantBookingsPDF($data, $startDate, $endDate, $merchant);
        } elseif ($format === 'excel') {
            return $this->generateMerchantBookingsExcel($data, $startDate, $endDate, $merchant);
        }

        return view('reports.merchant.bookings', compact('data', 'startDate', 'endDate', 'merchant'));
    }

    // Partner Reports
    public function partnerCommissionReport(Request $request)
    {
        $partner = auth()->user()->partner;
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $format = $request->get('format', 'view');

        $data = $this->getPartnerCommissionData($partner, $startDate, $endDate);

        if ($format === 'pdf') {
            return $this->generatePartnerCommissionPDF($data, $startDate, $endDate, $partner);
        } elseif ($format === 'excel') {
            return $this->generatePartnerCommissionExcel($data, $startDate, $endDate, $partner);
        }

        return view('reports.partner.commission', compact('data', 'startDate', 'endDate', 'partner'));
    }

    // Data Collection Methods
    private function getAdminRevenueData($startDate, $endDate)
    {
        return [
            'total_revenue' => Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('booking_status', 'completed')
                ->sum('total_amount'),

            'total_bookings' => Booking::whereBetween('booking_date', [$startDate, $endDate])->count(),

            'commission_earned' => Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('booking_status', 'completed')
                ->sum('commission_amount'),

            'monthly_breakdown' => Booking::selectRaw('
                    MONTH(booking_date) as month,
                    YEAR(booking_date) as year,
                    SUM(total_amount) as revenue,
                    COUNT(*) as bookings,
                    SUM(commission_amount) as commission
                ')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->where('booking_status', 'completed')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),

            'top_services' => Service::withCount(['bookings' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('booking_date', [$startDate, $endDate])
                    ->where('booking_status', 'completed');
            }])
                ->with('merchant')
                ->having('bookings_count', '>', 0)
                ->orderBy('bookings_count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    private function getMerchantPerformanceData($startDate, $endDate)
    {
        return User::whereHas('merchant')
            ->with(['merchant.services.bookings' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('booking_date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($user) {
                $bookings = $user->merchant->services->flatMap->bookings;

                // Get average rating from reviews instead of bookings
                $serviceIds = $user->merchant->services->pluck('id');
                $averageRating = \App\Models\Review::whereIn('service_id', $serviceIds)
                    ->where('is_approved', true)
                    ->avg('rating') ?? 0;

                return [
                    'merchant' => $user->merchant,
                    'total_bookings' => $bookings->count(),
                    'completed_bookings' => $bookings->where('booking_status', 'completed')->count(),
                    'total_revenue' => $bookings->where('booking_status', 'completed')->sum('total_amount'),
                    'average_rating' => round($averageRating, 2),
                ];
            });
    }

    private function getMerchantBookingsData($merchant, $startDate, $endDate)
    {
        return [
            'bookings' => Booking::whereHas('service', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->with(['service', 'customer'])
                ->orderBy('booking_date', 'desc')
                ->get(),

            'summary' => [
            'total_bookings' => Booking::whereHas('service', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->count(),

            'total_revenue' => Booking::whereHas('service', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->where('booking_status', 'completed')
                ->sum('total_amount'),

            'completion_rate' => $this->calculateCompletionRate($merchant, $startDate, $endDate),
            ],
        ];
    }

    private function getPartnerCommissionData($partner, $startDate, $endDate)
    {
        return [
            'merchants' => User::whereHas('merchant', function ($query) use ($partner) {
                $query->where('partner_id', $partner->id);
            })
                ->with(['merchant.services.bookings' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('booking_date', [$startDate, $endDate])
                        ->where('booking_status', 'completed');
                }])
                ->get()
                ->map(function ($user) use ($partner) {
                    $bookings = $user->merchant->services->flatMap->bookings;
                    $revenue = $bookings->sum('total_amount');
                    $commission = $revenue * ($partner->commission_rate / 100);

                    return [
                        'merchant' => $user->merchant,
                        'bookings_count' => $bookings->count(),
                        'total_revenue' => $revenue,
                        'commission_earned' => $commission,
                    ];
                }),

            'summary' => [
                'total_commission' => $this->calculateTotalPartnerCommission($partner, $startDate, $endDate),
                'total_merchants' => User::whereHas('merchant', function ($query) use ($partner) {
                    $query->where('partner_id', $partner->id);
                })->count(),
            ],
        ];
    }

    // PDF Generation Methods
    private function generateRevenuePDF($data, $startDate, $endDate)
    {
        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('reports.pdf.admin-revenue', compact('data', 'startDate', 'endDate'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('revenue-report-'.Carbon::now()->format('Y-m-d').'.pdf');
    }

    // Excel Generation Methods
    private function generateRevenueExcel($data, $startDate, $endDate)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'تقرير الإيرادات');
        $sheet->setCellValue('A2', 'من تاريخ: '.$startDate.' إلى: '.$endDate);

        // Data
        $sheet->setCellValue('A4', 'إجمالي الإيرادات');
        $sheet->setCellValue('B4', $data['total_revenue']);

        $sheet->setCellValue('A5', 'إجمالي الحجوزات');
        $sheet->setCellValue('B5', $data['total_bookings']);

        $sheet->setCellValue('A6', 'إجمالي العمولات');
        $sheet->setCellValue('B6', $data['commission_earned']);

        $writer = new Xlsx($spreadsheet);

        $filename = 'revenue-report-'.Carbon::now()->format('Y-m-d').'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);

        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    // Helper Methods
    private function calculateCompletionRate($merchant, $startDate, $endDate)
    {
        $total = Booking::whereHas('service', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->count();

        $completed = Booking::whereHas('service', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('booking_status', 'completed')
            ->count();

        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function calculateTotalPartnerCommission($partner, $startDate, $endDate)
    {
        $total_revenue = Booking::whereHas('service.merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('booking_status', 'completed')
            ->sum('total_amount');

        return $total_revenue * ($partner->commission_rate / 100);
    }
}
