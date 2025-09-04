<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VenueLayout;
use App\Models\Seat;
use App\Models\SeatReservation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SeatBookingController extends Controller
{
    /**
     * Get venue layout with seat availability
     */
    public function getVenueLayout(Request $request, int $offeringId): JsonResponse
    {
        $venueLayout = VenueLayout::where('offering_id', $offeringId)
            ->where('is_active', true)
            ->with(['seats' => function ($query) {
                $query->orderBy('row_number')->orderBy('column_number');
            }])
            ->first();

        if (!$venueLayout) {
            return response()->json([
                'success' => false,
                'message' => 'Venue layout not found'
            ], 404);
        }

        // Get reserved seats for today (or specific date if provided)
        $date = $request->input('date', now()->toDateString());
        $reservedSeats = SeatReservation::whereHas('booking', function ($query) use ($date) {
            $query->whereDate('event_date', $date)
                  ->whereIn('status', ['confirmed', 'paid']);
        })->pluck('seat_id')->toArray();

        // Mark seats as available/reserved
        $seats = $venueLayout->seats->map(function ($seat) use ($reservedSeats) {
            return [
                'id' => $seat->id,
                'row_number' => $seat->row_number,
                'column_number' => $seat->column_number,
                'seat_number' => $seat->seat_number,
                'category' => $seat->category,
                'price' => $seat->price,
                'is_available' => !in_array($seat->id, $reservedSeats),
                'x_position' => $seat->x_position,
                'y_position' => $seat->y_position,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'venue_layout' => [
                    'id' => $venueLayout->id,
                    'name' => $venueLayout->name,
                    'rows' => $venueLayout->rows,
                    'columns' => $venueLayout->columns,
                    'layout_data' => $venueLayout->layout_data,
                ],
                'seats' => $seats,
                'total_seats' => $venueLayout->total_seats,
                'available_seats' => $seats->where('is_available', true)->count(),
            ]
        ]);
    }

    /**
     * Reserve selected seats
     */
    public function reserveSeats(Request $request): JsonResponse
    {
        $request->validate([
            'offering_id' => 'required|exists:offerings,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'customer_details' => 'required|array',
            'customer_details.name' => 'required|string',
            'customer_details.email' => 'required|email',
            'customer_details.phone' => 'required|string',
            'event_date' => 'required|date|after_or_equal:today',
        ]);

        try {
            DB::beginTransaction();

            // Check if seats are still available
            $reservedSeats = SeatReservation::whereHas('booking', function ($query) use ($request) {
                $query->whereDate('event_date', $request->event_date)
                      ->whereIn('status', ['confirmed', 'paid', 'pending']);
            })->whereIn('seat_id', $request->seat_ids)->exists();

            if ($reservedSeats) {
                throw ValidationException::withMessages([
                    'seat_ids' => 'Some of the selected seats are no longer available.'
                ]);
            }

            // Get seats and calculate total price
            $seats = Seat::whereIn('id', $request->seat_ids)->get();
            $totalPrice = $seats->sum('price');

            // Create booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'offering_id' => $request->offering_id,
                'total_amount' => $totalPrice,
                'commission_amount' => $totalPrice * 0.05, // 5% commission
                'status' => 'pending',
                'payment_status' => 'pending',
                'event_date' => $request->event_date,
                'customer_details' => $request->customer_details,
                'booking_type' => 'seat_based',
                'additional_data' => [
                    'seat_count' => count($request->seat_ids),
                    'seat_categories' => $seats->pluck('category')->unique()->values(),
                ]
            ]);

            // Create seat reservations
            foreach ($seats as $seat) {
                SeatReservation::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id,
                    'price' => $seat->price,
                    'status' => 'reserved',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Seats reserved successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'total_price' => $totalPrice,
                    'seat_count' => count($request->seat_ids),
                    'seats' => $seats->map(function ($seat) {
                        return [
                            'id' => $seat->id,
                            'seat_number' => $seat->seat_number,
                            'category' => $seat->category,
                            'price' => $seat->price,
                        ];
                    }),
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reserve seats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel seat reservation
     */
    public function cancelReservation(Request $request, int $bookingId): JsonResponse
    {
        try {
            $booking = Booking::where('id', $bookingId)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or cannot be cancelled'
                ], 404);
            }

            DB::beginTransaction();

            // Update seat reservations
            SeatReservation::where('booking_id', $booking->id)
                ->update(['status' => 'cancelled']);

            // Update booking
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking details with seat information
     */
    public function getBookingDetails(int $bookingId): JsonResponse
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->with(['seatReservations.seat', 'offering'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'booking' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'total_amount' => $booking->total_amount,
                    'event_date' => $booking->event_date,
                    'created_at' => $booking->created_at,
                ],
                'offering' => [
                    'id' => $booking->offering->id,
                    'title' => $booking->offering->title,
                    'description' => $booking->offering->description,
                ],
                'seats' => $booking->seatReservations->map(function ($reservation) {
                    return [
                        'id' => $reservation->seat->id,
                        'seat_number' => $reservation->seat->seat_number,
                        'category' => $reservation->seat->category,
                        'price' => $reservation->price,
                        'row_number' => $reservation->seat->row_number,
                        'column_number' => $reservation->seat->column_number,
                    ];
                }),
                'customer_details' => $booking->customer_details,
            ]
        ]);
    }
}
