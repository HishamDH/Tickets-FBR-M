<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperOrderItem
 * @property int $id
 * @property int $order_id
 * @property int $item_id
 * @property string $item_type
 * @property int $quantity
 * @property string $price
 * @property string $discount
 * @property string $total
 * @property array|null $item_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $additional_data
 * @property-read array|null $branch_info
 * @property-read string $item_name
 * @property-read float $original_total
 * @property-read array $selected_options
 * @property-read float $subtotal
 * @property-read array|null $time_slot
 * @property-read float $total_discount
 * @property-read Model|\Eloquent $item
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereItemData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereUpdatedAt($value)
 * @mixin \Eloquent
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
