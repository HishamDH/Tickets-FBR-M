<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display merchant's bookings
     */
    public function merchantIndex(Request $request)
    {
        $user = Auth::user();
        
        // Get merchant ID - assuming user has merchant relationship
        $merchantId = $user->id; // adjust this based on your user-merchant relationship
        
        $query = Booking::where('merchant_id', $merchantId)
            ->with(['customer', 'service', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $bookings = $query->latest()->paginate(15);

        // Get merchant's services for filter dropdown
        $services = Service::where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->pluck('name', 'id');

        return view('merchant.bookings.index', compact('bookings', 'services'));
    }

    /**
     * Show booking details
     */
    public function show($id)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $booking = Booking::where('merchant_id', $merchantId)
            ->with(['customer', 'service', 'payments'])
            ->findOrFail($id);

        return view('merchant.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $merchantId = $user->id;

        $booking = Booking::where('merchant_id', $merchantId)->findOrFail($id);

        DB::beginTransaction();
        try {
            $oldStatus = $booking->status;
            
            $booking->update([
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Log status change if you have logging
            \Log::info('Booking status updated', [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'merchant_id' => $merchantId,
                'updated_by' => $user->id,
            ]);

            // Send notification to customer if needed
            if ($booking->customer) {
                // Add notification logic here
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم تحديث حالة الحجز بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة الحجز');
        }
    }

    /**
     * Confirm booking
     */
    public function confirm($id)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $booking = Booking::where('merchant_id', $merchantId)->findOrFail($id);

        if ($booking->status === 'confirmed') {
            return redirect()->back()->with('info', 'هذا الحجز مؤكد بالفعل');
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => $user->id,
            ]);

            // Send confirmation notification to customer
            if ($booking->customer) {
                // Add notification logic here
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم تأكيد الحجز بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء تأكيد الحجز');
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $merchantId = $user->id;

        $booking = Booking::where('merchant_id', $merchantId)->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('info', 'هذا الحجز ملغي بالفعل');
        }

        if ($booking->status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء حجز مكتمل');
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Handle refund if payment was made
            if ($booking->payment_status === 'paid') {
                // Add refund logic here
            }

            // Send cancellation notification to customer
            if ($booking->customer) {
                // Add notification logic here
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الحجز');
        }
    }

    /**
     * Bulk update bookings
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id',
            'action' => 'required|in:confirm,cancel',
        ]);

        $user = Auth::user();
        $merchantId = $user->id;

        $bookings = Booking::where('merchant_id', $merchantId)
            ->whereIn('id', $request->booking_ids)
            ->get();

        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($bookings as $booking) {
                if ($request->action === 'confirm' && $booking->status === 'pending') {
                    $booking->update([
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                        'confirmed_by' => $user->id,
                    ]);
                    $updated++;
                } elseif ($request->action === 'cancel' && in_array($booking->status, ['pending', 'confirmed'])) {
                    $booking->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancelled_by' => $user->id,
                    ]);
                    $updated++;
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "تم تحديث {$updated} حجز بنجاح");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث الجماعي');
        }
    }
}
