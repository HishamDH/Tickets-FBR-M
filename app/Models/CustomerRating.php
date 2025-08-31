<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', // offering_id
        'user_id', // customer_id
        'rating',
        'review',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    /**
     * Offering relationship
     */
    public function offering()
    {
        return $this->belongsTo(Offering::class, 'service_id');
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
    public function service()
    {
        return $this->offering();
    }
}
