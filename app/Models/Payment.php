<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
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
