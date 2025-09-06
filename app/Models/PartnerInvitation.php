<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PartnerInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'email',
        'business_name',
        'contact_person',
        'phone',
        'invitation_token',
        'status',
        'message',
        'sent_at',
        'expires_at',
        'accepted_at',
        'merchant_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // حالات الدعوة
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_ACCEPTED => 'مقبولة',
            self::STATUS_EXPIRED => 'منتهية الصلاحية',
            self::STATUS_CANCELLED => 'ملغية',
        ];
    }

    // دوال فحص الحالة
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING && !$this->isExpired();
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now() || $this->status === self::STATUS_EXPIRED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // دوال العمليات
    public function accept(Merchant $merchant): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
            'merchant_id' => $merchant->id,
        ]);

        // ربط التاجر بالشريك
        $merchant->update(['partner_id' => $this->partner_id]);

        // إضافة عمولة ترحيب للشريك
        $this->addWelcomeCommission();

        return true;
    }

    public function cancel(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->update(['status' => self::STATUS_CANCELLED]);
        return true;
    }

    public function markAsExpired(): bool
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
        return true;
    }

    // دوال مساعدة
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function getInvitationUrl(): string
    {
        return url('/register/merchant?invitation=' . $this->invitation_token);
    }

    public function getDaysUntilExpiry(): int
    {
        return max(0, $this->expires_at->diffInDays(now()));
    }

    private function addWelcomeCommission(): void
    {
        $wallet = $this->partner->getOrCreateWallet();
        
        // مكافأة ترحيب ثابتة (يمكن تخصيصها)
        $welcomeBonus = 50.00;
        
        $wallet->addCommission(
            $welcomeBonus,
            "مكافأة ترحيب لدعوة التاجر: {$this->merchant->business_name}",
            [
                'invitation_id' => $this->id,
                'merchant_id' => $this->merchant_id,
                'type' => 'welcome_bonus'
            ]
        );
    }

    // Scope للاستعلامات
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere('expires_at', '<=', now());
        });
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    protected static function boot()
    {
        parent::boot();

        // تعيين قيم افتراضية عند الإنشاء
        static::creating(function ($invitation) {
            if (empty($invitation->invitation_token)) {
                $invitation->invitation_token = self::generateToken();
            }
            
            if (empty($invitation->expires_at)) {
                $invitation->expires_at = now()->addDays(30); // صالحة لمدة 30 يوم
            }
        });
    }
}
