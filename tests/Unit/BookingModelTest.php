<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_belongs_to_customer()
    {
        $customer = User::factory()->create();
        $booking = Booking::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(User::class, $booking->customer);
        $this->assertEquals($customer->id, $booking->customer->id);
    }

    public function test_booking_can_be_associated_with_a_bookable_item()
    {
        $ticket = Ticket::factory()->create();
        $booking = Booking::factory()->create([
            'bookable_id' => $ticket->id,
            'bookable_type' => Ticket::class,
        ]);

        $this->assertInstanceOf(Ticket::class, $booking->bookable);
        $this->assertEquals($ticket->id, $booking->bookable->id);
    }

    public function test_booking_has_many_payments()
    {
        $booking = Booking::factory()->create();
        Payment::factory()->count(2)->create(['booking_id' => $booking->id]);

        $this->assertCount(2, $booking->payments);
        $this->assertInstanceOf(Payment::class, $booking->payments->first());
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

    public function test_booking_generates_unique_booking_number()
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();

        $this->assertNotEmpty($booking1->booking_number);
        $this->assertNotEmpty($booking2->booking_number);
        $this->assertNotEquals($booking1->booking_number, $booking2->booking_number);
    }
}