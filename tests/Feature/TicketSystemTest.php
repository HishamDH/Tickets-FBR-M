<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Offering;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test offerings
        $this->offering = Offering::factory()->create([
            'name' => 'Test Offering',
            'description' => 'Test offering description',
            'start_date' => now()->addDays(30),
            'location' => 'Test Location',
            'is_active' => true
        ]);

        $this->tickets = Ticket::factory()->count(3)->create([
            'offering_id' => $this->offering->id,
            'price' => 100,
            'quantity' => 50
        ]);
    }

    public function test_customer_can_view_available_offerings()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/offerings');

        $response->assertStatus(200);
        $response->assertSee($this->offering->name);
        $response->assertSee($this->offering->location);
    }

    public function test_customer_can_view_offering_details()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get("/offerings/{$this->offering->id}");

        $response->assertStatus(200);
        $response->assertSee($this->offering->name);
        $response->assertSee($this->offering->description);
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

    public function test_merchant_can_create_offering()
    {
        $merchant = User::factory()->create(['role' => 'merchant']);

        $offeringData = [
            'name' => 'New Offering',
            'description' => 'Offering description',
            'start_date' => now()->addDays(30)->format('Y-m-d H:i:s'),
            'location' => 'Offering Location',
            'is_active' => true
        ];

        $response = $this->actingAs($merchant)->post('/merchant/offerings', $offeringData);

        $response->assertRedirect();
        $this->assertDatabaseHas('offerings', [
            'name' => 'New Offering',
            'user_id' => $merchant->id
        ]);
    }

    public function test_merchant_can_create_tickets_for_their_offering()
    {
        $merchant = User::factory()->create(['role' => 'merchant']);
        $offering = Offering::factory()->create(['user_id' => $merchant->id]);

        $ticketData = [
            'offering_id' => $offering->id,
            'name' => 'VIP Ticket',
            'description' => 'VIP access',
            'price' => 500,
            'quantity' => 20
        ];

        $response = $this->actingAs($merchant)->post('/merchant/tickets', $ticketData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'offering_id' => $offering->id,
            'name' => 'VIP Ticket',
            'price' => 500
        ]);
    }

    public function test_merchant_cannot_modify_other_merchant_offerings()
    {
        $merchant1 = User::factory()->create(['role' => 'merchant']);
        $merchant2 = User::factory()->create(['role' => 'merchant']);
        
        $offering = Offering::factory()->create(['user_id' => $merchant1->id]);

        $response = $this->actingAs($merchant2)->patch("/merchant/offerings/{$offering->id}", [
            'name' => 'Modified Title'
        ]);

        $response->assertStatus(403);
    }

    public function test_offering_search_functionality()
    {
        $searchOffering = Offering::factory()->create([
            'name' => 'Concert in Dubai',
            'location' => 'Dubai Opera House'
        ]);

        $response = $this->get('/offerings?search=Dubai');

        $response->assertStatus(200);
        $response->assertSee('Concert in Dubai');
    }

    public function test_offering_filtering_by_category()
    {
        $category1 = Category::factory()->create(['name' => 'music']);
        $category2 = Category::factory()->create(['name' => 'sports']);
        $musicOffering = Offering::factory()->create(['category_id' => $category1->id]);
        $sportsOffering = Offering::factory()->create(['category_id' => $category2->id]);

        $response = $this->get('/offerings?category=music');

        $response->assertStatus(200);
        $response->assertSee($musicOffering->name);
        $response->assertDontSee($sportsOffering->name);
    }
}
