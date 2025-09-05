<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display merchant's payments index
     */
    public function merchantIndex(Request $request)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $query = Payment::whereHas('booking', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->with(['booking.service', 'booking.customer', 'paymentGateway']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->whereHas('paymentGateway', function ($q) use ($request) {
                $q->where('code', $request->payment_method);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by amount range
        if ($request->filled('amount_from')) {
            $query->where('amount', '>=', $request->amount_from);
        }

        if ($request->filled('amount_to')) {
            $query->where('amount', '<=', $request->amount_to);
        }

        $payments = $query->latest()->paginate(15);

        // Calculate summary statistics
        $stats = $this->getPaymentStats($merchantId, $request);

        return view('merchant.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $payment = Payment::whereHas('booking', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->with(['booking.service', 'booking.customer', 'paymentGateway', 'refunds'])
        ->findOrFail($id);

        return view('merchant.payments.show', compact('payment'));
    }

    /**
     * Process refund
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $merchantId = $user->id;

        $payment = Payment::whereHas('booking', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->findOrFail($id);

        if ($payment->status !== 'completed') {
            return redirect()->back()->with('error', 'لا يمكن استرداد دفعة غير مكتملة');
        }

        $totalRefunded = $payment->refunds()->sum('amount');
        $availableForRefund = $payment->amount - $totalRefunded;

        if ($request->refund_amount > $availableForRefund) {
            return redirect()->back()->with('error', 'مبلغ الاسترداد أكبر من المبلغ المتاح للاسترداد');
        }

        DB::beginTransaction();
        try {
            // Create refund record
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'amount' => $request->refund_amount,
                'reason' => $request->refund_reason,
                'status' => 'pending',
                'requested_by' => $user->id,
                'requested_at' => now(),
            ]);

            // Here you would integrate with payment gateway to process actual refund
            // For now, we'll mark it as completed
            $refund->update([
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_refund_id' => 'mock_refund_' . uniqid(),
            ]);

            // Update booking status if full refund
            if ($request->refund_amount == $payment->amount) {
                $payment->booking->update(['payment_status' => 'refunded']);
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم معالجة الاسترداد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الاسترداد');
        }
    }

    /**
     * Generate payment report
     */
    public function report(Request $request)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $payments = Payment::whereHas('booking', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->whereBetween('created_at', [$startDate, $endDate])
        ->with(['booking.service', 'paymentGateway'])
        ->get();

        $reportData = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            'total_refunds' => $payments->flatMap->refunds->sum('amount'),
            'net_amount' => $payments->where('status', 'completed')->sum('amount') - $payments->flatMap->refunds->sum('amount'),
            'by_status' => $payments->groupBy('status')->map->count(),
            'by_gateway' => $payments->groupBy('paymentGateway.name')->map->count(),
            'by_service' => $payments->groupBy('booking.service.name')->map->count(),
            'daily_totals' => $payments->groupBy(function ($payment) {
                return $payment->created_at->format('Y-m-d');
            })->map(function ($dailyPayments) {
                return $dailyPayments->where('status', 'completed')->sum('amount');
            })->sortKeys(),
        ];

        return view('merchant.payments.report', compact('reportData', 'startDate', 'endDate'));
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $query = Payment::whereHas('booking', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->with(['booking.service', 'booking.customer', 'paymentGateway']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        $filename = 'payments_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // CSV headers
            fputcsv($file, [
                'رقم الدفع',
                'رقم الحجز',
                'اسم العميل',
                'الخدمة',
                'المبلغ',
                'بوابة الدفع',
                'الحالة',
                'تاريخ الدفع',
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->booking->id,
                    $payment->booking->customer->name ?? 'غير محدد',
                    $payment->booking->service->name,
                    $payment->amount,
                    $payment->paymentGateway->display_name_ar ?? 'غير محدد',
                    $this->getStatusLabel($payment->status),
                    $payment->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStats($merchantId, $request)
    {
        $query = Payment::whereHas('booking', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        });

        // Apply date filters if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        return [
            'total_count' => $payments->count(),
            'completed_count' => $payments->where('status', 'completed')->count(),
            'pending_count' => $payments->where('status', 'pending')->count(),
            'failed_count' => $payments->where('status', 'failed')->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
            'average_amount' => $payments->where('status', 'completed')->avg('amount') ?: 0,
            'today_count' => $payments->where('created_at', '>=', Carbon::today())->count(),
            'today_amount' => $payments->where('created_at', '>=', Carbon::today())
                                    ->where('status', 'completed')->sum('amount'),
        ];
    }

    /**
     * Get status label in Arabic
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'في الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
        ];

        return $labels[$status] ?? $status;
    }
}
