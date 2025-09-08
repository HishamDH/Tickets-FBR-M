<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'customer_id',
        'bookable_id', // Polymorphic
        'bookable_type', // Polymorphic
        'service_id', // For backward compatibility
        'merchant_id',
        'booking_date',
        'booking_time',
        'guest_count',
        'total_amount',
        'commission_amount',
        'commission_rate',
        'payment_status',
        'payment_method',
        'status',
        'reservation_status',
        'booking_source',
        'special_requests',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'qr_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'number_of_people',
        'number_of_tables',
        'duration_hours',
        'notes',
        'discount',
        'code',
        'pos_terminal_id',
        'pos_data',
        'printed_at',
        'print_count',
        'is_offline_transaction',
        'offline_transaction_id',
        'synced_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'discount' => 'decimal:2',
        'pos_data' => 'array',
        'printed_at' => 'datetime',
        'synced_at' => 'datetime',
        'is_offline_transaction' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'TKT-'.date('Y').'-'.str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            }
            if (empty($booking->qr_code)) {
                $booking->qr_code = Str::uuid()->toString();
            }
            
            // Auto-populate service_id when bookable is a Service
            if ($booking->bookable_type === Service::class && !$booking->service_id) {
                $booking->service_id = $booking->bookable_id;
            }
        });

        static::updating(function ($booking) {
            // Auto-populate service_id when bookable is a Service
            if ($booking->bookable_type === Service::class && !$booking->service_id) {
                $booking->service_id = $booking->bookable_id;
            }
        });
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latest();
    }

    public function seatReservations(): HasMany
    {
        return $this->hasMany(SeatReservation::class);
    }

    /**
     * Service relationship (direct via service_id)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

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

    public function generateQrCode(): string
    {
        $data = json_encode([
            'booking_number' => $this->booking_number,
            'customer_name' => $this->customer_name ?? $this->customer?->name,
            'service_name' => $this->bookable->name, // Changed from service to bookable
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking_time,
            'verification_code' => $this->qr_code,
        ]);

        return base64_encode('QR Code Data: '.$data);
    }

    public function getMerchantAmountAttribute(): float
    {
        return $this->total_amount - $this->commission_amount;
    }
}
