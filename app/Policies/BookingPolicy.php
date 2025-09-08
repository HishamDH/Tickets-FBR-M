<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Customer can view their own bookings
        if ($booking->customer_id === $user->id) {
            return true;
        }

        // Merchant can view bookings for their services
        if ($booking->merchant_id === $user->id) {
            return true;
        }

        // Admin can view all bookings
        if ($user->user_type === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->user_type === 'customer' || $user->user_type === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Only merchant can update booking details
        if ($booking->merchant_id === $user->id) {
            return true;
        }

        // Admin can update any booking
        if ($user->user_type === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Only admin can delete bookings
        return $user->user_type === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->user_type === 'admin';
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Customer can cancel their own bookings
        if ($booking->customer_id === $user->id) {
            return $booking->status !== 'completed' && $booking->status !== 'cancelled';
        }

        // Merchant can cancel bookings for their services
        if ($booking->merchant_id === $user->id) {
            return $booking->status !== 'completed' && $booking->status !== 'cancelled';
        }

        // Admin can cancel any booking
        if ($user->user_type === 'admin') {
            return $booking->status !== 'completed' && $booking->status !== 'cancelled';
        }

        return false;
    }
}
