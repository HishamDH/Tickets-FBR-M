<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $payment_number
 * @property int $booking_id
 * @property int $merchant_id
 * @property int $payment_gateway_id
 * @property int|null $customer_id
 * @property string $amount
 * @property string $gateway_fee
 * @property string $platform_fee
 * @property string $total_amount
 * @property string $currency
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $gateway_transaction_id
 * @property string|null $gateway_reference
 * @property array|null $gateway_response
 * @property array|null $gateway_metadata
 * @property \Illuminate\Support\Carbon|null $initiated_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $failed_at
 * @property string|null $failure_reason
 * @property string|null $customer_ip
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\User|null $customer
 * @property-read string $status_arabic
 * @property-read \App\Models\Merchant $merchant
 * @property-read \App\Models\PaymentGateway $paymentGateway
 * @method static \Illuminate\Database\Eloquent\Builder|Payment completed()
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Payment failed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCustomerIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInitiatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePlatformFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperPayment
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'booking_id',
        'merchant_id',
        'payment_gateway_id',
        'customer_id',
        'amount',
        'gateway_fee',
        'platform_fee',
        'total_amount',
        'currency',
        'status',
        'payment_method',
        'gateway_transaction_id',
        'gateway_reference',
        'gateway_response',
        'gateway_metadata',
        'initiated_at',
        'completed_at',
        'failed_at',
        'failure_reason',
        'customer_ip',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'gateway_metadata' => 'array',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * العلاقة مع الحجز
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * العلاقة مع التاجر
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * العلاقة مع بوابة الدفع
     */
    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    /**
     * العلاقة مع العميل
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * النطاق - المدفوعات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * النطاق - المدفوعات الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * النطاق - المدفوعات في الانتظار
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * توليد رقم دفع فريد
     */
    public static function generatePaymentNumber(): string
    {
        do {
            $number = 'PAY-'.strtoupper(uniqid());
        } while (self::where('payment_number', $number)->exists());

        return $number;
    }

    /**
     * تحديث حالة الدفع
     */
    public function updateStatus(string $status, array $additionalData = []): void
    {
        $updateData = ['status' => $status];

        switch ($status) {
            case 'completed':
                $updateData['completed_at'] = Carbon::now();
                break;
            case 'failed':
                $updateData['failed_at'] = Carbon::now();
                if (isset($additionalData['failure_reason'])) {
                    $updateData['failure_reason'] = $additionalData['failure_reason'];
                }
                break;
        }

        if (isset($additionalData['gateway_response'])) {
            $updateData['gateway_response'] = $additionalData['gateway_response'];
        }

        if (isset($additionalData['gateway_transaction_id'])) {
            $updateData['gateway_transaction_id'] = $additionalData['gateway_transaction_id'];
        }

        $this->update($updateData);
    }

    /**
     * التحقق من إمكانية الاسترداد
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' &&
               $this->paymentGateway->supports_refund &&
               $this->completed_at &&
               $this->completed_at->diffInDays(now()) <= 30; // 30 يوم للاسترداد
    }

    /**
     * الحصول على حالة الدفع بالعربية
     */
    public function getStatusArabicAttribute(): string
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
