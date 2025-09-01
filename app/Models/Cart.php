<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'item_id',
        'item_type',
        'quantity',
        'price',
        'discount',
        'additional_data',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Polymorphic relationship - can be Offering, Service, etc.
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Legacy relationship for backward compatibility
    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class, 'item_id')->where('item_type', Offering::class);
    }

    // Calculated attributes
    public function getSubtotalAttribute(): float
    {
        return ($this->price - $this->discount) * $this->quantity;
    }

    public function getTotalDiscountAttribute(): float
    {
        return $this->discount * $this->quantity;
    }

    public function getOriginalTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeForGuest($query, $sessionId)
    {
        return $query->whereNull('user_id')->where('session_id', $sessionId);
    }

    // Helper methods
    public function isAvailable(): bool
    {
        if ($this->item && method_exists($this->item, 'isAvailable')) {
            return $this->item->isAvailable($this->quantity);
        }
        
        return true;
    }

    public function getItemName(): string
    {
        return $this->item->title ?? $this->item->name ?? 'Unknown Item';
    }

    public function getItemImage(): ?string
    {
        return $this->item->image ?? $this->item->featured_image ?? null;
    }

    public function getBranchInfo(): ?array
    {
        return $this->additional_data['branch'] ?? null;
    }

    public function getTimeSlot(): ?array
    {
        return $this->additional_data['time_slot'] ?? null;
    }

    public function getSelectedOptions(): array
    {
        return $this->additional_data['options'] ?? [];
    }

    // Static methods
    public static function addItem($userId, $sessionId, $itemId, $itemType, $quantity = 1, $price = 0, $additionalData = [])
    {
        $existingItem = static::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem;
        }

        return static::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'item_id' => $itemId,
            'item_type' => $itemType,
            'quantity' => $quantity,
            'price' => $price,
            'discount' => 0,
            'additional_data' => $additionalData,
        ]);
    }

    public static function getCartTotal($userId, $sessionId): array
    {
        $items = static::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId)->whereNull('user_id');
            }
        })->with('item')->get();

        $subtotal = $items->sum('subtotal');
        $discount = $items->sum('total_discount');
        $total = $subtotal;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'count' => $items->sum('quantity'),
        ];
    }

    public static function clearCart($userId, $sessionId): void
    {
        static::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId)->whereNull('user_id');
            }
        })->delete();
    }

    public static function mergeGuestCart($sessionId, $userId): void
    {
        static::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $userId]);
    }
}
