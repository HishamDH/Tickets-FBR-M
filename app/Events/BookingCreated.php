<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('merchant.'.$this->booking->service->merchant_id),
            new PrivateChannel('customer.'.$this->booking->customer_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'booking.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'service_name' => $this->booking->service->name,
            'customer_name' => $this->booking->customer_name,
            'booking_date' => $this->booking->booking_date->format('Y-m-d H:i'),
            'status' => $this->booking->status,
            'total_amount' => $this->booking->total_amount,
            'message' => 'حجز جديد تم إنشاؤه',
            'type' => 'booking_created',
            'timestamp' => now()->toISOString(),
        ];
    }
}
