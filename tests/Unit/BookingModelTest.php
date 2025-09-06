<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_belongs_to_user()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($user->id, $booking->user->id);
    }

    public function test_booking_belongs_to_ticket()
    {
        $ticket = Ticket::factory()->create();
        $booking = Booking::factory()->create(['ticket_id' => $ticket->id]);

        $this->assertInstanceOf(Ticket::class, $booking->ticket);
        $this->assertEquals($ticket->id, $booking->ticket->id);
    }

    public function test_booking_has_many_payments()
    {
        $booking = Booking::factory()->create();
        $payments = Payment::factory()->count(2)->create(['booking_id' => $booking->id]);

        $this->assertCount(2, $booking->payments);
        $this->assertInstanceOf(Payment::class, $booking->payments->first());
    }

    public function test_booking_calculates_total_amount()
    {
        $ticket = Ticket::factory()->create(['price' => 100]);
        $booking = Booking::factory()->create([
            'ticket_id' => $ticket->id,
            'quantity' => 3
        ]);

        $this->assertEquals(300, $booking->calculateTotalAmount());
    }

    public function test_booking_can_be_cancelled()
    {
        $booking = Booking::factory()->create(['status' => 'confirmed']);

        $this->assertTrue($booking->canBeCancelled());
    }

    public function test_cancelled_booking_cannot_be_cancelled_again()
    {
        $booking = Booking::factory()->create(['status' => 'cancelled']);

        $this->assertFalse($booking->canBeCancelled());
    }

    public function test_booking_is_paid()
    {
        $booking = Booking::factory()->create();
        Payment::factory()->create([
            'booking_id' => $booking->id,
            'status' => 'completed',
            'amount' => $booking->total_amount
        ]);

        $this->assertTrue($booking->isPaid());
    }

    public function test_booking_is_not_paid()
    {
        $booking = Booking::factory()->create();
        Payment::factory()->create([
            'booking_id' => $booking->id,
            'status' => 'pending',
            'amount' => $booking->total_amount
        ]);

        $this->assertFalse($booking->isPaid());
    }

    public function test_booking_is_refundable()
    {
        $futureEvent = Event::factory()->create(['date' => now()->addDays(10)]);
        $ticket = Ticket::factory()->create(['event_id' => $futureEvent->id]);
        $booking = Booking::factory()->create([
            'ticket_id' => $ticket->id,
            'status' => 'confirmed',
            'created_at' => now()->subDays(1)
        ]);

        $this->assertTrue($booking->isRefundable());
    }

    public function test_booking_is_not_refundable_for_past_events()
    {
        $pastEvent = Event::factory()->create(['date' => now()->subDays(10)]);
        $ticket = Ticket::factory()->create(['event_id' => $pastEvent->id]);
        $booking = Booking::factory()->create([
            'ticket_id' => $ticket->id,
            'status' => 'confirmed'
        ]);

        $this->assertFalse($booking->isRefundable());
    }

    public function test_booking_generates_unique_reference_number()
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();

        $this->assertNotEmpty($booking1->reference_number);
        $this->assertNotEmpty($booking2->reference_number);
        $this->assertNotEquals($booking1->reference_number, $booking2->reference_number);
    }

    public function test_booking_scope_confirmed()
    {
        Booking::factory()->create(['status' => 'confirmed']);
        Booking::factory()->create(['status' => 'pending']);
        Booking::factory()->create(['status' => 'cancelled']);

        $confirmedBookings = Booking::confirmed()->get();
        
        $this->assertCount(1, $confirmedBookings);
        $this->assertEquals('confirmed', $confirmedBookings->first()->status);
    }

    public function test_booking_scope_pending()
    {
        Booking::factory()->create(['status' => 'confirmed']);
        Booking::factory()->create(['status' => 'pending']);
        Booking::factory()->create(['status' => 'cancelled']);

        $pendingBookings = Booking::pending()->get();
        
        $this->assertCount(1, $pendingBookings);
        $this->assertEquals('pending', $pendingBookings->first()->status);
    }

    public function test_booking_format_reference_number()
    {
        $booking = Booking::factory()->create(['id' => 123]);

        $formattedRef = $booking->formatReferenceNumber();
        $this->assertStringContainsString('TKT', $formattedRef);
        $this->assertStringContainsString('123', $formattedRef);
    }

    public function test_booking_get_qr_code_data()
    {
        $booking = Booking::factory()->create();

        $qrData = $booking->getQRCodeData();
        $this->assertIsArray($qrData);
        $this->assertArrayHasKey('booking_id', $qrData);
        $this->assertArrayHasKey('reference_number', $qrData);
    }
}
