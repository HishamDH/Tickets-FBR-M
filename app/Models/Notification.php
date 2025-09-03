<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperNotification
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'title',
        'message',
        'data',
        'priority',
        'channel',
        'read_at',
        'sent_at',
        'is_sent',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    // Relationships
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsSent()
    {
        $this->update(['sent_at' => now(), 'is_sent' => true]);
    }

    public function isRead()
    {
        return ! is_null($this->read_at);
    }

    public function isSent()
    {
        return $this->is_sent;
    }

    public function isUrgent()
    {
        return $this->priority === 'urgent';
    }

    public function isHigh()
    {
        return $this->priority === 'high';
    }

    // Static methods for creating notifications
    public static function createForUser($user, $type, $title, $message, $data = [], $priority = 'normal')
    {
        return self::create([
            'type' => $type,
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
        ]);
    }

    public static function createBookingNotification($booking, $type, $title, $message)
    {
        // Notify customer
        if ($booking->customer) {
            self::createForUser($booking->customer, $type, $title, $message, [
                'booking_id' => $booking->id,
                'service_name' => $booking->service->name,
                'booking_date' => $booking->booking_date,
            ]);
        }

        // Notify merchant
        if ($booking->service->merchant) {
            self::createForUser($booking->service->merchant, $type, $title, $message, [
                'booking_id' => $booking->id,
                'customer_name' => $booking->customer_name,
                'service_name' => $booking->service->name,
            ]);
        }
    }
}
