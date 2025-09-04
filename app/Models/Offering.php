<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $location
 * @property string|null $description
 * @property string|null $image
 * @property string|null $price
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property string|null $status
 * @property string|null $type
 * @property string|null $category
 * @property array|null $additional_data
 * @property array|null $translations
 * @property bool|null $has_chairs
 * @property int|null $chairs_count
 * @property int $max_capacity
 * @property int $min_capacity
 * @property bool $allow_overbooking
 * @property string $overbooking_percentage
 * @property string $capacity_type
 * @property int $buffer_capacity
 * @property int $user_id
 * @property array|null $features
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read mixed $rating
 * @property-read int|null $reviews_count
 * @property-read mixed $title
 * @property-read \App\Models\Merchant $merchant
 * @property-read \App\Models\Merchant|null $merchantModel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaidReservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerRating> $reviews
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OfferingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Offering newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offering newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offering query()
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereAdditionalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereAllowOverbooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereBufferCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereCapacityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereChairsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereHasChairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereMaxCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereMinCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereOverbookingPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereTranslations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offering whereUserId($value)
 * @mixin \Eloquent
 * @mixin IdeHelperOffering
 */
class Offering extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'image',
        'price',
        'start_time',
        'end_time',
        'status',
        'type',
        'category',
        'additional_data',
        'translations',
        'has_chairs',
        'chairs_count',
        'user_id',
        'features',
        'max_capacity',
        'min_capacity',
        'allow_overbooking',
        'overbooking_percentage',
        'capacity_type',
        'buffer_capacity',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'translations' => 'array',
        'features' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'has_chairs' => 'boolean',
        'allow_overbooking' => 'boolean',
        'price' => 'decimal:2',
        'overbooking_percentage' => 'decimal:2',
    ];

    /**
     * User relationship - the merchant who owns this offering
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reservations relationship - bookings for this offering
     */
    public function reservations()
    {
        return $this->hasMany(PaidReservation::class, 'item_id');
    }

    /**
     * Reviews relationship - customer ratings for this offering
     */
    public function reviews()
    {
        return $this->hasMany(CustomerRating::class, 'service_id');
    }

    /**
     * Check if offering is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if offering is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get merchant/owner of this offering through the user relationship
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'user_id', 'user_id');
    }

    /**
     * Get the merchant model directly
     */
    public function merchantModel()
    {
        return $this->hasOneThrough(Merchant::class, User::class, 'id', 'user_id', 'user_id');
    }

    /**
     * Title accessor (alias for name)
     */
    public function getTitleAttribute()
    {
        return $this->name;
    }

    /**
     * Image URL accessor
     */
    public function getImageUrlAttribute()
    {
        return $this->image;
    }

    /**
     * Rating accessor (calculated from reviews)
     */
    public function getRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 5.0;
    }

    /**
     * Reviews count accessor
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}
