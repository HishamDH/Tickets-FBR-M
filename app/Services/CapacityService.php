<?php

namespace App\Services;

use App\Models\Offering;
use App\Models\Booking;
use App\Models\PaidReservation;
use Carbon\Carbon;
use Exception;

class CapacityService
{
    /**
     * Check if booking can be accepted based on capacity
     */
    public function canAcceptBooking(Offering $offering, int $requestedQuantity, ?Carbon $bookingDate = null): array
    {
        $result = [
            'can_book' => false,
            'available_capacity' => 0,
            'total_capacity' => 0,
            'current_bookings' => 0,
            'message' => '',
            'suggestions' => []
        ];

        // Get effective capacity based on capacity type
        $totalCapacity = $this->getEffectiveCapacity($offering);
        
        // Get current bookings for the specific date/time
        $currentBookings = $this->getCurrentBookings($offering, $bookingDate);
        
        // Calculate available capacity
        $availableCapacity = $totalCapacity - $currentBookings;
        
        $result['total_capacity'] = $totalCapacity;
        $result['current_bookings'] = $currentBookings;
        $result['available_capacity'] = $availableCapacity;

        // Check if we can accommodate the request
        if ($requestedQuantity <= $availableCapacity) {
            $result['can_book'] = true;
            $result['message'] = "Available capacity: {$availableCapacity} spots";
        } else {
            // Check if overbooking is allowed
            if ($offering->allow_overbooking) {
                $overbookingLimit = $this->calculateOverbookingLimit($offering);
                $totalWithOverbooking = $totalCapacity + $overbookingLimit;
                
                if ($currentBookings + $requestedQuantity <= $totalWithOverbooking) {
                    $result['can_book'] = true;
                    $result['message'] = "Booking accepted with overbooking policy";
                } else {
                    $result['message'] = "Fully booked (including overbooking capacity)";
                    $result['suggestions'] = $this->getSuggestions($offering, $requestedQuantity, $bookingDate);
                }
            } else {
                $result['message'] = "Insufficient capacity. Available: {$availableCapacity}, Requested: {$requestedQuantity}";
                $result['suggestions'] = $this->getSuggestions($offering, $requestedQuantity, $bookingDate);
            }
        }

        return $result;
    }

    /**
     * Get effective capacity based on offering type
     */
    public function getEffectiveCapacity(Offering $offering): int
    {
        return match($offering->capacity_type) {
            'unlimited' => PHP_INT_MAX,
            'flexible' => $offering->max_capacity + $offering->buffer_capacity,
            'fixed' => $offering->max_capacity,
            default => $offering->chairs_count ?? $offering->max_capacity ?? 50,
        };
    }

    /**
     * Get current bookings for a specific offering and date
     */
    public function getCurrentBookings(Offering $offering, ?Carbon $bookingDate = null): int
    {
        $query = Booking::where('offering_id', $offering->id)
            ->whereIn('status', ['confirmed', 'pending', 'in_progress']);

        // If specific date is provided, filter by that date
        if ($bookingDate) {
            $query->whereDate('booking_date', $bookingDate->format('Y-m-d'));
            
            // If offering has specific time slots, filter by time as well
            if ($offering->start_time && $offering->end_time) {
                $query->whereTime('booking_time', '>=', $offering->start_time->format('H:i:s'))
                      ->whereTime('booking_time', '<=', $offering->end_time->format('H:i:s'));
            }
        } else {
            // For ongoing offerings, count current active bookings
            $query->where(function($q) {
                $q->where('booking_date', '>=', now()->format('Y-m-d'))
                  ->orWhereNull('booking_date');
            });
        }

        return $query->sum('quantity') ?? 0;
    }

    /**
     * Calculate overbooking limit
     */
    public function calculateOverbookingLimit(Offering $offering): int
    {
        if (!$offering->allow_overbooking) {
            return 0;
        }

        $baseCapacity = $offering->max_capacity;
        $percentage = $offering->overbooking_percentage / 100;
        
        return (int) ceil($baseCapacity * $percentage);
    }

    /**
     * Get booking suggestions when capacity is full
     */
    public function getSuggestions(Offering $offering, int $requestedQuantity, ?Carbon $bookingDate = null): array
    {
        $suggestions = [];

        // Suggest alternative dates (next 7 days)
        if ($bookingDate) {
            for ($i = 1; $i <= 7; $i++) {
                $alternativeDate = $bookingDate->copy()->addDays($i);
                $alternativeBookings = $this->getCurrentBookings($offering, $alternativeDate);
                $availableCapacity = $this->getEffectiveCapacity($offering) - $alternativeBookings;
                
                if ($availableCapacity >= $requestedQuantity) {
                    $suggestions[] = [
                        'type' => 'alternative_date',
                        'date' => $alternativeDate->format('Y-m-d'),
                        'available_capacity' => $availableCapacity,
                        'message' => "Available on {$alternativeDate->format('M d, Y')} - {$availableCapacity} spots"
                    ];
                }
            }
        }

        // Suggest reducing quantity
        $totalCapacity = $this->getEffectiveCapacity($offering);
        $currentBookings = $this->getCurrentBookings($offering, $bookingDate);
        $maxPossible = $totalCapacity - $currentBookings;
        
        if ($maxPossible > 0 && $maxPossible < $requestedQuantity) {
            $suggestions[] = [
                'type' => 'reduce_quantity',
                'quantity' => $maxPossible,
                'message' => "Consider booking {$maxPossible} spots instead"
            ];
        }

        // Suggest similar offerings
        $similarOfferings = $this->getSimilarOfferings($offering, $requestedQuantity, $bookingDate);
        foreach ($similarOfferings as $similar) {
            $suggestions[] = [
                'type' => 'alternative_offering',
                'offering_id' => $similar->id,
                'offering_name' => $similar->name,
                'available_capacity' => $similar->available_capacity,
                'message' => "Try '{$similar->name}' - {$similar->available_capacity} spots available"
            ];
        }

        return $suggestions;
    }

    /**
     * Find similar offerings with availability
     */
    protected function getSimilarOfferings(Offering $offering, int $requestedQuantity, ?Carbon $bookingDate = null): array
    {
        $similar = Offering::where('id', '!=', $offering->id)
            ->where('category', $offering->category)
            ->where('status', 'active')
            ->limit(3)
            ->get();

        $available = [];
        
        foreach ($similar as $alt) {
            $capacity = $this->canAcceptBooking($alt, $requestedQuantity, $bookingDate);
            if ($capacity['can_book']) {
                $alt->available_capacity = $capacity['available_capacity'];
                $available[] = $alt;
            }
        }

        return $available;
    }

    /**
     * Get capacity statistics for an offering
     */
    public function getCapacityStats(Offering $offering, ?Carbon $fromDate = null, ?Carbon $toDate = null): array
    {
        $fromDate = $fromDate ?? now()->subDays(30);
        $toDate = $toDate ?? now();

        $bookings = Booking::where('offering_id', $offering->id)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereIn('status', ['confirmed', 'completed', 'in_progress'])
            ->get();

        $totalCapacity = $this->getEffectiveCapacity($offering);
        $totalBookings = $bookings->sum('quantity');
        $averageBookings = $bookings->count() > 0 ? $totalBookings / $bookings->count() : 0;
        
        $utilizationRate = $totalCapacity > 0 ? ($totalBookings / $totalCapacity) * 100 : 0;

        return [
            'total_capacity' => $totalCapacity,
            'total_bookings' => $totalBookings,
            'average_booking_size' => round($averageBookings, 2),
            'utilization_rate' => round($utilizationRate, 2),
            'busiest_days' => $this->getBusiestDays($bookings),
            'peak_hours' => $this->getPeakHours($bookings),
            'overbooking_incidents' => $this->getOverbookingIncidents($offering, $fromDate, $toDate),
        ];
    }

    /**
     * Get busiest days of the week
     */
    protected function getBusiestDays($bookings): array
    {
        $dayStats = $bookings->groupBy(function($booking) {
            return $booking->booking_date ? Carbon::parse($booking->booking_date)->format('l') : 'Unknown';
        })->map->count()->sortDesc();

        return $dayStats->toArray();
    }

    /**
     * Get peak booking hours
     */
    protected function getPeakHours($bookings): array
    {
        $hourStats = $bookings->groupBy(function($booking) {
            return $booking->booking_time ? Carbon::parse($booking->booking_time)->format('H:00') : 'Unknown';
        })->map->count()->sortDesc();

        return $hourStats->toArray();
    }

    /**
     * Get overbooking incidents
     */
    protected function getOverbookingIncidents(Offering $offering, Carbon $fromDate, Carbon $toDate): int
    {
        // This would track when bookings exceeded normal capacity
        // For now, return 0 as a placeholder
        return 0;
    }

    /**
     * Validate capacity settings
     */
    public function validateCapacitySettings(array $settings): array
    {
        $errors = [];

        if (isset($settings['min_capacity']) && isset($settings['max_capacity'])) {
            if ($settings['min_capacity'] > $settings['max_capacity']) {
                $errors[] = 'Minimum capacity cannot be greater than maximum capacity';
            }
        }

        if (isset($settings['overbooking_percentage']) && $settings['overbooking_percentage'] > 50) {
            $errors[] = 'Overbooking percentage should not exceed 50%';
        }

        if (isset($settings['buffer_capacity']) && $settings['buffer_capacity'] < 0) {
            $errors[] = 'Buffer capacity cannot be negative';
        }

        return $errors;
    }
}