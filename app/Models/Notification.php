<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperNotification
 * @property int $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $title
 * @property string $message
 * @property array|null $data
 * @property string $priority
 * @property string $channel
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property bool $is_sent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $notifiable
 * @method static \Illuminate\Database\Eloquent\Builder|Notification byPriority($priority)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification read()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification unread()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @mixin \Eloquent
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
