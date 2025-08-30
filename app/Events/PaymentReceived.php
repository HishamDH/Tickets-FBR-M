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

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $amount;
    public $paymentMethod;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, $amount, $paymentMethod = 'online')
    {
        $this->booking = $booking;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
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
        return 'payment.received';
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
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'customer_name' => $this->booking->customer_name,
            'message' => 'تم استلام الدفعة بنجاح',
            'type' => 'payment_received',
            'timestamp' => now()->toISOString(),
        ];
    }
}
