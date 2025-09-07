<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = 'offerings';

    protected $fillable = [
        'user_id',
        'name', 
        'description',
        'price',
        'category',
        'status',
        'max_capacity',
        'start_time',
        'end_time',
        'location',
        'image',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'offering_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function getTotalTicketsAvailableAttribute(): int
    {
        return $this->tickets()->sum('available_quantity') ?: $this->max_capacity ?: 0;
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->getTotalTicketsAvailableAttribute() <= 0;
    }

    public function getHasAvailableTicketsAttribute(): bool
    {
        return !$this->getIsSoldOutAttribute();
    }

    public function getMinimumPriceAttribute(): float
    {
        $ticketMinPrice = $this->tickets()->min('price');
        return min($this->price ?: 0, $ticketMinPrice ?: 0) ?: ($this->price ?: 0);
    }

    public function getMaximumPriceAttribute(): float
    {
        $ticketMaxPrice = $this->tickets()->max('price');
        return max($this->price ?: 0, $ticketMaxPrice ?: 0) ?: ($this->price ?: 0);
    }

    public function getCanBeBookedAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->is_active && 
               $this->start_time > now() && 
               $this->getHasAvailableTicketsAttribute();
    }

    public function getDaysUntilEventAttribute(): int
    {
        return $this->start_time ? now()->diffInDays($this->start_time, false) : 0;
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->start_time ? $this->start_time->isToday() : false;
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->start_time ? $this->start_time->format('M j, Y') : '';
    }
}