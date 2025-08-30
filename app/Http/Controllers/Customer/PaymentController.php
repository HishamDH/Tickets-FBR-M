<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show payment page for a booking
     */
    public function show(Booking $booking)
    {
        $this->authorize('pay', $booking);

        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('info', 'تم دفع هذا الحجز بالفعل');
        }

        $booking->load(['service', 'merchant']);

        // Get active payment gateways
        $paymentGateways = PaymentGateway::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('customer.payments.show', compact('booking', 'paymentGateways'));
    }

    /**
     * Process payment for a booking
     */
    public function process(Booking $booking, Request $request)
    {
        $this->authorize('pay', $booking);

        $request->validate([
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'payment_method' => 'required|string',
        ]);

        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'تم دفع هذا الحجز بالفعل'
            ]);
        }

        try {
            DB::beginTransaction();

            $paymentGateway = PaymentGateway::findOrFail($request->payment_gateway_id);

            $paymentData = [
                'amount' => $booking->total_amount,
                'currency' => 'SAR',
                'payment_method' => $request->payment_method,
                'description' => "دفع حجز #{$booking->id} - {$booking->service->name}",
                'customer' => [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->phone,
                ],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'customer_id' => Auth::id(),
                    'service_id' => $booking->service_id,
                    'merchant_id' => $booking->merchant_id,
                ]
            ];

            $result = $this->paymentService->processPayment(
                $paymentGateway,
                $booking,
                $paymentData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'] ?? null,
                'payment_id' => $result['payment_id'] ?? null,
                'message' => 'تم إنشاء رابط الدفع بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle payment success callback
     */
    public function success(Booking $booking, Request $request)
    {
        $this->authorize('view', $booking);

        $payment = $booking->payments()
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('warning', 'لم يتم العثور على تفاصيل الدفع');
        }

        return view('customer.payments.success', compact('booking', 'payment'));
    }

    /**
     * Handle payment failure callback
     */
    public function failed(Booking $booking, Request $request)
    {
        $this->authorize('view', $booking);

        $payment = $booking->payments()
            ->where('status', 'failed')
            ->latest()
            ->first();

        return view('customer.payments.failed', compact('booking', 'payment'));
    }

    /**
     * Show customer's payment history
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Payment::whereHas('booking', function ($q) use ($user) {
            $q->where('customer_id', $user->id);
        })->with(['booking.service', 'paymentGateway']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15);

        $statuses = [
            'pending' => 'في الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد'
        ];

        return view('customer.payments.history', compact('payments', 'statuses'));
    }

    /**
     * Download payment receipt
     */
    public function downloadReceipt(Payment $payment)
    {
        $booking = $payment->booking;
        $this->authorize('view', $booking);

        if ($payment->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'لا يمكن تحميل إيصال الدفع لدفعة غير مكتملة');
        }

        // Generate and return receipt PDF
        // This would integrate with a PDF generation service
        return response()->download(
            storage_path('app/receipts/payment-' . $payment->id . '.pdf')
        );
    }

    /**
     * Request refund for a payment
     */
    public function requestRefund(Payment $payment, Request $request)
    {
        $booking = $payment->booking;
        $this->authorize('view', $booking);

        $request->validate([
            'reason' => 'required|string|max:500',
            'refund_amount' => 'nullable|numeric|min:1|max:' . $payment->amount,
        ]);

        if ($payment->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'لا يمكن طلب استرداد لدفعة غير مكتملة');
        }

        try {
            $refundAmount = $request->refund_amount ?? $payment->amount;

            $result = $this->paymentService->processRefund(
                $payment,
                $refundAmount,
                $request->reason
            );

            return redirect()->back()
                ->with('success', 'تم تقديم طلب الاسترداد بنجاح، سيتم مراجعته خلال 3-5 أيام عمل');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء معالجة طلب الاسترداد: ' . $e->getMessage());
        }
    }
}
