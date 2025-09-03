<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperOrderItem
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'item_type',
        'quantity',
        'price',
        'discount',
        'total',
        'item_data',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'item_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function getItemNameAttribute(): string
    {
        return $this->item_data['name'] ?? 'Unknown Item';
    }

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

    public function getAdditionalDataAttribute()
    {
        return $this->item_data['additional_data'] ?? null;
    }

    public function getBranchInfoAttribute(): ?array
    {
        $additionalData = $this->getAdditionalDataAttribute();

        return $additionalData['branch'] ?? null;
    }

    public function getTimeSlotAttribute(): ?array
    {
        $additionalData = $this->getAdditionalDataAttribute();

        return $additionalData['time_slot'] ?? null;
    }

    public function getSelectedOptionsAttribute(): array
    {
        $additionalData = $this->getAdditionalDataAttribute();

        return $additionalData['options'] ?? [];
    }
}
