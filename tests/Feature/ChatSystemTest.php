<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->merchant = User::factory()->create(['role' => 'merchant']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_customer_can_access_chat_page()
    {
        $response = $this->actingAs($this->customer)->get('/chat');

        $response->assertStatus(200);
        $response->assertSee('محادثات');
    }

    public function test_customer_can_start_conversation_with_merchant()
    {
        $conversationData = [
            'recipient_id' => $this->merchant->id,
            'subject' => 'سؤال عن الفعالية',
            'message' => 'مرحباً، لدي سؤال عن الفعالية'
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/chat/conversations', $conversationData);

        $response->assertRedirect();
        $this->assertDatabaseHas('conversations', [
            'subject' => 'سؤال عن الفعالية'
        ]);
        
        $this->assertDatabaseHas('messages', [
            'content' => 'مرحباً، لدي سؤال عن الفعالية',
            'sender_id' => $this->customer->id
        ]);
    }

    public function test_user_can_send_message_in_existing_conversation()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $messageData = [
            'content' => 'شكراً لك على الرد السريع',
            'type' => 'text'
        ];

        $response = $this->actingAs($this->customer)
                        ->post("/chat/conversations/{$conversation->id}/messages", $messageData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'content' => 'شكراً لك على الرد السريع',
            'sender_id' => $this->customer->id
        ]);
    }

    public function test_user_can_send_image_message()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $imageFile = \Illuminate\Http\UploadedFile::fake()->image('ticket.jpg');

        $messageData = [
            'content' => 'هنا صورة التذكرة',
            'type' => 'image',
            'attachment' => $imageFile
        ];

        $response = $this->actingAs($this->customer)
                        ->post("/chat/conversations/{$conversation->id}/messages", $messageData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'type' => 'image',
            'sender_id' => $this->customer->id
        ]);
    }

    public function test_user_can_send_file_attachment()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 500);

        $messageData = [
            'content' => 'المستندات المطلوبة',
            'type' => 'file',
            'attachment' => $file
        ];

        $response = $this->actingAs($this->customer)
                        ->post("/chat/conversations/{$conversation->id}/messages", $messageData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'type' => 'file',
            'sender_id' => $this->customer->id
        ]);
    }

    public function test_user_can_view_conversation_messages()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $messages = Message::factory()->count(5)->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->customer->id
        ]);

        $response = $this->actingAs($this->customer)
                        ->get("/chat/conversations/{$conversation->id}");

        $response->assertStatus(200);
        
        foreach($messages as $message) {
            $response->assertSee($message->content);
        }
    }

    public function test_user_cannot_access_other_users_conversations()
    {
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        
        $conversation = Conversation::factory()->create([
            'customer_id' => $otherCustomer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $response = $this->actingAs($this->customer)
                        ->get("/chat/conversations/{$conversation->id}");

        $response->assertStatus(403);
    }

    public function test_merchant_can_view_all_their_conversations()
    {
        $conversations = Conversation::factory()->count(3)->create([
            'merchant_id' => $this->merchant->id
        ]);

        $response = $this->actingAs($this->merchant)->get('/chat');

        $response->assertStatus(200);
        
        foreach($conversations as $conversation) {
            $response->assertSee($conversation->subject);
        }
    }

    public function test_conversation_marks_as_read_when_viewed()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->merchant->id,
            'read_at' => null
        ]);

        $this->actingAs($this->customer)
             ->get("/chat/conversations/{$conversation->id}");

        $message->refresh();
        $this->assertNotNull($message->read_at);
    }

    public function test_user_can_search_conversations()
    {
        $conversation1 = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id,
            'subject' => 'سؤال عن التذاكر'
        ]);

        $conversation2 = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id,
            'subject' => 'مشكلة في الدفع'
        ]);

        $response = $this->actingAs($this->customer)
                        ->get('/chat?search=تذاكر');

        $response->assertStatus(200);
        $response->assertSee('سؤال عن التذاكر');
        $response->assertDontSee('مشكلة في الدفع');
    }

    public function test_customer_can_start_support_conversation()
    {
        $supportData = [
            'subject' => 'مشكلة تقنية',
            'priority' => 'high',
            'category' => 'technical',
            'description' => 'لدي مشكلة تقنية في الموقع'
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/chat/support', $supportData);

        $response->assertRedirect();
        $this->assertDatabaseHas('conversations', [
            'subject' => 'مشكلة تقنية',
            'type' => 'support',
            'priority' => 'high'
        ]);
    }

    public function test_admin_can_view_support_conversations()
    {
        $supportConversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'support',
            'priority' => 'high'
        ]);

        $response = $this->actingAs($this->admin)
                        ->get('/admin/support');

        $response->assertStatus(200);
        $response->assertSee($supportConversation->subject);
    }

    public function test_typing_indicator_works()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $response = $this->actingAs($this->customer)
                        ->post("/chat/conversations/{$conversation->id}/typing", [
                            'is_typing' => true
                        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_message_delivery_status_updates()
    {
        $conversation = Conversation::factory()->create([
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id
        ]);

        $message = Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->customer->id,
            'delivered_at' => null
        ]);

        $response = $this->actingAs($this->merchant)
                        ->patch("/chat/messages/{$message->id}/delivered");

        $response->assertStatus(200);
        
        $message->refresh();
        $this->assertNotNull($message->delivered_at);
    }
}
