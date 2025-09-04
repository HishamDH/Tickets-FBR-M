<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperBooking
 * @property int $id
 * @property string $booking_number
 * @property int|null $customer_id
 * @property int $service_id
 * @property int $merchant_id
 * @property \Illuminate\Support\Carbon $booking_date
 * @property string|null $booking_time
 * @property int|null $guest_count
 * @property string $total_amount
 * @property string $commission_amount
 * @property string $commission_rate
 * @property string $payment_status
 * @property string|null $status
 * @property string $booking_source
 * @property string|null $special_requests
 * @property string|null $cancellation_reason
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $cancelled_by
 * @property string|null $qr_code
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_email
 * @property int|null $number_of_people
 * @property int|null $number_of_tables
 * @property int|null $duration_hours
 * @property string|null $notes
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $customer
 * @property-read float $merchant_amount
 * @property-read string $payment_status_arabic
 * @property-read string $status_arabic
 * @property-read \App\Models\Payment|null $latestPayment
 * @property-read \App\Models\User $merchant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Service $service
 * @method static \Database\Factories\BookingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCancellationReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCancelledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereDurationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereGuestCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereNumberOfPeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereNumberOfTables($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'customer_id',
        'service_id',
        'merchant_id',
        'booking_date',
        'booking_time',
        'guest_count',
        'total_amount',
        'commission_amount',
        'commission_rate',
        'payment_status',
        'status',
        'booking_source',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'qr_code',
        // Additional fields for non-registered customers
        'customer_name',
        'customer_phone',
        'customer_email',
        'number_of_people',
        'number_of_tables',
        'duration_hours',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'string', // سنحفظه كـ string بصيغة H:i
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_number = 'TKT-'.date('Y').'-'.str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            $booking->qr_code = Str::uuid()->toString();
        });
    }

    /**
     * Customer relationship
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Service relationship
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Merchant relationship
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Cancelled by user relationship
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Payments relationship
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Latest payment relationship
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latest();
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get status in Arabic
     */
    public function getStatusArabicAttribute(): string
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغى',
            'no_show' => 'لم يحضر',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get payment status in Arabic
     */
    public function getPaymentStatusArabicAttribute(): string
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'paid' => 'مدفوع',
            'failed' => 'فشل الدفع',
            'refunded' => 'مسترد',
        ];

        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Generate QR code for booking verification
     */
    public function generateQrCode(): string
    {
        // Create a simple base64 encoded QR code data
        // In production, you would use a QR code library like SimpleSoftwareIO/simple-qrcode
        $data = json_encode([
            'booking_number' => $this->booking_number,
            'customer_name' => $this->customer_name ?? $this->customer?->name,
            'service_name' => $this->service->name,
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking_time, // احفظه كما هو (string)
            'verification_code' => $this->qr_code,
        ]);

        // For demonstration purposes, return a placeholder QR code
        // In production, replace this with actual QR code generation
        $qrCodeContent = base64_encode('QR Code Data: '.$data);

        return $qrCodeContent;
    }

    /**
     * Get merchant amount after commission
     */
    public function getMerchantAmountAttribute(): float
    {
        return $this->total_amount - $this->commission_amount;
    }
}
