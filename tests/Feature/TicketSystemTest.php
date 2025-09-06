<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test events
        $this->event = Event::factory()->create([
            'title' => 'Test Event',
            'description' => 'Test event description',
            'date' => now()->addDays(30),
            'location' => 'Test Location',
            'status' => 'active'
        ]);

        $this->tickets = Ticket::factory()->count(3)->create([
            'event_id' => $this->event->id,
            'price' => 100,
            'quantity' => 50
        ]);
    }

    public function test_customer_can_view_available_events()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/events');

        $response->assertStatus(200);
        $response->assertSee($this->event->title);
        $response->assertSee($this->event->location);
    }

    public function test_customer_can_view_event_details()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get("/events/{$this->event->id}");

        $response->assertStatus(200);
        $response->assertSee($this->event->title);
        $response->assertSee($this->event->description);
    }

    public function test_customer_can_book_available_tickets()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = $this->tickets->first();

        $bookingData = [
            'ticket_id' => $ticket->id,
            'quantity' => 2,
            'total_amount' => $ticket->price * 2
        ];

        $response = $this->actingAs($customer)->post('/bookings', $bookingData);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 2
        ]);
    }

    public function test_customer_cannot_book_more_tickets_than_available()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = $this->tickets->first();

        $bookingData = [
            'ticket_id' => $ticket->id,
            'quantity' => 100, // More than available
            'total_amount' => $ticket->price * 100
        ];

        $response = $this->actingAs($customer)->post('/bookings', $bookingData);

        $response->assertSessionHasErrors(['quantity']);
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity' => 100
        ]);
    }

    public function test_booking_reduces_available_ticket_quantity()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = $this->tickets->first();
        $originalQuantity = $ticket->quantity;

        $bookingData = [
            'ticket_id' => $ticket->id,
            'quantity' => 5,
            'total_amount' => $ticket->price * 5
        ];

        $this->actingAs($customer)->post('/bookings', $bookingData);

        $ticket->refresh();
        $this->assertEquals($originalQuantity - 5, $ticket->quantity);
    }

    public function test_customer_can_view_their_bookings()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $this->tickets->first()->id,
            'quantity' => 2,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($customer)->get('/my-bookings');

        $response->assertStatus(200);
        $response->assertSee($booking->id);
    }

    public function test_customer_can_cancel_booking()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'ticket_id' => $this->tickets->first()->id,
            'quantity' => 2,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($customer)->patch("/bookings/{$booking->id}/cancel");

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_merchant_can_create_event()
    {
        $merchant = User::factory()->create(['role' => 'merchant']);

        $eventData = [
            'title' => 'New Event',
            'description' => 'Event description',
            'date' => now()->addDays(30)->format('Y-m-d H:i:s'),
            'location' => 'Event Location',
            'category' => 'music',
            'status' => 'active'
        ];

        $response = $this->actingAs($merchant)->post('/merchant/events', $eventData);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'title' => 'New Event',
            'merchant_id' => $merchant->id
        ]);
    }

    public function test_merchant_can_create_tickets_for_their_event()
    {
        $merchant = User::factory()->create(['role' => 'merchant']);
        $event = Event::factory()->create(['merchant_id' => $merchant->id]);

        $ticketData = [
            'event_id' => $event->id,
            'name' => 'VIP Ticket',
            'description' => 'VIP access',
            'price' => 500,
            'quantity' => 20
        ];

        $response = $this->actingAs($merchant)->post('/merchant/tickets', $ticketData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'event_id' => $event->id,
            'name' => 'VIP Ticket',
            'price' => 500
        ]);
    }

    public function test_merchant_cannot_modify_other_merchant_events()
    {
        $merchant1 = User::factory()->create(['role' => 'merchant']);
        $merchant2 = User::factory()->create(['role' => 'merchant']);
        
        $event = Event::factory()->create(['merchant_id' => $merchant1->id]);

        $response = $this->actingAs($merchant2)->patch("/merchant/events/{$event->id}", [
            'title' => 'Modified Title'
        ]);

        $response->assertStatus(403);
    }

    public function test_event_search_functionality()
    {
        $searchEvent = Event::factory()->create([
            'title' => 'Concert in Dubai',
            'location' => 'Dubai Opera House'
        ]);

        $response = $this->get('/events?search=Dubai');

        $response->assertStatus(200);
        $response->assertSee('Concert in Dubai');
    }

    public function test_event_filtering_by_category()
    {
        $musicEvent = Event::factory()->create(['category' => 'music']);
        $sportsEvent = Event::factory()->create(['category' => 'sports']);

        $response = $this->get('/events?category=music');

        $response->assertStatus(200);
        $response->assertSee($musicEvent->title);
        $response->assertDontSee($sportsEvent->title);
    }
}
