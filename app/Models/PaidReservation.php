<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', // offering_id
        'user_id', // customer_id
        'booking_date',
        'booking_time',
        'guest_count',
        'total_amount',
        'payment_status',
        'reservation_status',
        'special_requests',
        'qr_code',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Offering relationship
     */
    public function offering()
    {
        return $this->belongsTo(Offering::class, 'item_id');
    }

    /**
     * Customer relationship
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for offering relationship to match reference naming
     */
    public function item()
    {
        return $this->offering();
    }
}
