<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class APITest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->merchant = User::factory()->create(['role' => 'merchant']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_api_authentication_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->customer->email,
            'password' => 'password'
        ]);

        if ($response->status() !== 200) {
            dd([
                'status' => $response->status(),
                'response' => $response->json(),
                'content' => $response->getContent()
            ]);
        }

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'role'
            ]
        ]);
    }

    public function test_api_authentication_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->customer->email,
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials'
        ]);
    }

    public function test_api_user_registration()
    {
        $userData = [
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+966501234567',
            'role' => 'customer'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'user' => [
                'id',
                'name',
                'email'
            ]
        ]);
    }

    public function test_api_get_events_list()
    {
        $events = Event::factory()->count(5)->create();

        $response = $this->getJson('/api/events');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'date',
                    'location',
                    'category'
                ]
            ],
            'meta' => [
                'current_page',
                'per_page',
                'total'
            ]
        ]);
    }

    public function test_api_get_single_event()
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description
            ]
        ]);
    }

    public function test_api_search_events()
    {
        $event1 = Event::factory()->create(['title' => 'Concert in Dubai']);
        $event2 = Event::factory()->create(['title' => 'Football Match']);

        $response = $this->getJson('/api/events?search=Dubai');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Concert in Dubai']);
        $response->assertJsonMissing(['title' => 'Football Match']);
    }

    public function test_api_filter_events_by_category()
    {
        $musicEvent = Event::factory()->create(['category' => 'music']);
        $sportsEvent = Event::factory()->create(['category' => 'sports']);

        $response = $this->getJson('/api/events?category=music');

        $response->assertStatus(200);
        $response->assertJsonFragment(['category' => 'music']);
        $response->assertJsonMissing(['category' => 'sports']);
    }

    public function test_authenticated_user_can_create_booking()
    {
        Sanctum::actingAs($this->customer);

        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id,
            'price' => 100
        ]);

        $bookingData = [
            'ticket_id' => $ticket->id,
            'quantity' => 2
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'ticket_id',
                'quantity',
                'total_amount',
                'status'
            ]
        ]);
    }

    public function test_unauthenticated_user_cannot_create_booking()
    {
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $bookingData = [
            'ticket_id' => $ticket->id,
            'quantity' => 2
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(401);
    }

    public function test_api_get_user_bookings()
    {
        Sanctum::actingAs($this->customer);

        $bookings = Booking::factory()->count(3)->create([
            'user_id' => $this->customer->id
        ]);

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_api_cancel_booking()
    {
        Sanctum::actingAs($this->customer);

        $booking = Booking::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'confirmed'
        ]);

        $response = $this->patchJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'status' => 'cancelled'
            ]
        ]);
    }

    public function test_merchant_can_create_event_via_api()
    {
        Sanctum::actingAs($this->merchant);

        $eventData = [
            'title' => 'New Event via API',
            'description' => 'Event created through API',
            'date' => now()->addDays(30)->format('Y-m-d H:i:s'),
            'location' => 'API Location',
            'category' => 'technology'
        ];

        $response = $this->postJson('/api/merchant/events', $eventData);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'title' => 'New Event via API',
                'merchant_id' => $this->merchant->id
            ]
        ]);
    }

    public function test_merchant_can_view_their_events_via_api()
    {
        Sanctum::actingAs($this->merchant);

        $events = Event::factory()->count(3)->create([
            'merchant_id' => $this->merchant->id
        ]);

        $response = $this->getJson('/api/merchant/events');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_api_rate_limiting()
    {
        // Test rate limiting by making multiple requests
        for ($i = 0; $i < 61; $i++) {
            $response = $this->getJson('/api/events');
        }

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_api_validation_errors()
    {
        Sanctum::actingAs($this->customer);

        $invalidBookingData = [
            'ticket_id' => 'invalid',
            'quantity' => -1
        ];

        $response = $this->postJson('/api/bookings', $invalidBookingData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ticket_id', 'quantity']);
    }

    public function test_api_error_handling()
    {
        $response = $this->getJson('/api/events/999999');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Event not found'
        ]);
    }

    public function test_api_logout()
    {
        Sanctum::actingAs($this->customer);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Successfully logged out'
        ]);
    }

    public function test_api_user_profile()
    {
        Sanctum::actingAs($this->customer);

        $response = $this->getJson('/api/user/profile');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email
            ]
        ]);
    }

    public function test_api_update_user_profile()
    {
        Sanctum::actingAs($this->customer);

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '+966501234567'
        ];

        $response = $this->patchJson('/api/user/profile', $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => 'Updated Name'
            ]
        ]);
    }
}
