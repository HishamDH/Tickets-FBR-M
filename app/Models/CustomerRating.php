<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCustomerRating
 * @property int $id
 * @property int $service_id
 * @property int $user_id
 * @property int $rating Rating from 1 to 5
 * @property string|null $review
 * @property bool $is_approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $customer
 * @property-read \App\Models\Offering $offering
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerRating whereUserId($value)
 * @mixin \Eloquent
 */
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
