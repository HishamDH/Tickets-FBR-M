<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, $oldStatus, $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('merchant.' . $this->booking->service->merchant_id),
            new PrivateChannel('customer.' . $this->booking->customer_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'booking.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $messages = [
            'confirmed' => 'تم تأكيد حجزك',
            'cancelled' => 'تم إلغاء الحجز',
            'completed' => 'تم إكمال الخدمة',
            'in_progress' => 'جاري تنفيذ الخدمة',
        ];

        return [
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'service_name' => $this->booking->service->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status_text' => $messages[$this->newStatus] ?? 'تم تحديث حالة الحجز',
            'booking_date' => $this->booking->booking_date->format('Y-m-d H:i'),
            'message' => $messages[$this->newStatus] ?? 'تم تحديث حالة الحجز',
            'type' => 'booking_status_changed',
            'timestamp' => now()->toISOString(),
        ];
    }
}
