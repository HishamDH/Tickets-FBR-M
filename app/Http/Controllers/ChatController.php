<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\PaidReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Get all conversations for current user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $conversations = Conversation::forUser($user->id)
                ->with(['customer', 'merchant', 'supportAgent', 'latestMessage.sender'])
                ->active()
                ->orderBy('last_message_at', 'desc')
                ->paginate(20);

            $conversationsData = $conversations->map(function ($conversation) use ($user) {
                $otherParticipants = $conversation->getOtherParticipants($user->id);
                $latestMessage = $conversation->latestMessage->first();

                return [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?? $this->generateConversationTitle($conversation, $user),
                    'type' => $conversation->type,
                    'status' => $conversation->status,
                    'participants' => $otherParticipants->map(function ($participant) {
                        return [
                            'id' => $participant->id,
                            'name' => $participant->name,
                            'role' => $participant->role,
                            'avatar' => $participant->avatar_url ?? null,
                        ];
                    }),
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'latest_message' => $latestMessage ? [
                        'content' => $latestMessage->content,
                        'type' => $latestMessage->type,
                        'sender_name' => $latestMessage->sender->name,
                        'created_at' => $latestMessage->created_at->diffForHumans(),
                    ] : null,
                    'last_message_at' => $conversation->last_message_at?->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'conversations' => $conversationsData,
                'pagination' => [
                    'current_page' => $conversations->currentPage(),
                    'last_page' => $conversations->lastPage(),
                    'total' => $conversations->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Chat conversations error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load conversations'
            ], 500);
        }
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        try {
            $user = Auth::user();
            
            $conversation = Conversation::where('id', $conversationId)
                ->forUser($user->id)
                ->with(['customer', 'merchant', 'supportAgent'])
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found or access denied'
                ], 404);
            }

            // Mark messages as read
            $conversation->markAsReadForUser($user->id);

            $messages = $conversation->messages()
                ->with('sender')
                ->notDeleted()
                ->orderBy('created_at', 'asc')
                ->paginate(50);

            $messagesData = $messages->map(function ($message) {
                return $message->toDisplayArray();
            });

            return response()->json([
                'success' => true,
                'conversation' => [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?? $this->generateConversationTitle($conversation, $user),
                    'type' => $conversation->type,
                    'status' => $conversation->status,
                    'participants' => $conversation->getOtherParticipants($user->id)->map(function ($participant) {
                        return [
                            'id' => $participant->id,
                            'name' => $participant->name,
                            'role' => $participant->role,
                            'avatar' => $participant->avatar_url ?? null,
                        ];
                    }),
                ],
                'messages' => $messagesData,
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'total' => $messages->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get messages error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load messages'
            ], 500);
        }
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request, $conversationId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:5000',
                'type' => 'in:text,file,image',
                'attachments' => 'array|max:5',
                'attachments.*' => 'file|max:10240', // 10MB max per file
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            $conversation = Conversation::where('id', $conversationId)
                ->forUser($user->id)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found or access denied'
                ], 404);
            }

            // Check if conversation is active
            if ($conversation->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot send messages to closed conversation'
                ], 422);
            }

            // Handle file attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                $attachments = $this->handleAttachments($request->file('attachments'));
            }

            // Create the message
            $message = Message::createMessage(
                $conversationId,
                $user->id,
                $request->content,
                $request->type ?? 'text',
                $attachments
            );

            // Load sender relationship
            $message->load('sender');

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message->toDisplayArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Send message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Start a new conversation
     */
    public function startConversation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:customer_support,merchant_customer',
                'title' => 'nullable|string|max:255',
                'merchant_id' => 'nullable|exists:users,id',
                'booking_id' => 'nullable|exists:paid_reservations,id',
                'initial_message' => 'required|string|max:5000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Check if similar conversation already exists
            $existingConversation = Conversation::where('customer_id', $user->id)
                ->where('type', $request->type)
                ->when($request->merchant_id, function ($query) use ($request) {
                    $query->where('merchant_id', $request->merchant_id);
                })
                ->when($request->booking_id, function ($query) use ($request) {
                    $query->where('booking_id', $request->booking_id);
                })
                ->active()
                ->first();

            if ($existingConversation) {
                return response()->json([
                    'success' => true,
                    'message' => 'Using existing conversation',
                    'conversation_id' => $existingConversation->id
                ]);
            }

            // Assign support agent for customer support
            $supportAgentId = null;
            if ($request->type === 'customer_support') {
                $supportAgentId = $this->assignSupportAgent();
            }

            // Create new conversation
            $conversation = Conversation::createConversation([
                'title' => $request->title,
                'type' => $request->type,
                'customer_id' => $user->id,
                'merchant_id' => $request->merchant_id,
                'support_agent_id' => $supportAgentId,
                'booking_id' => $request->booking_id,
                'initial_message' => $request->initial_message,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation started successfully',
                'conversation_id' => $conversation->id
            ]);

        } catch (\Exception $e) {
            Log::error('Start conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to start conversation'
            ], 500);
        }
    }

    /**
     * Close a conversation
     */
    public function closeConversation(Request $request, $conversationId)
    {
        try {
            $user = Auth::user();
            
            $conversation = Conversation::where('id', $conversationId)
                ->forUser($user->id)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found or access denied'
                ], 404);
            }

            // Only merchant, support agent, or admin can close conversations
            if (!in_array($user->role, ['admin', 'merchant']) && $conversation->support_agent_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to close this conversation'
                ], 403);
            }

            $conversation->update(['status' => 'closed']);

            // Add a system message about closing
            Message::createMessage(
                $conversationId,
                $user->id,
                'Conversation has been closed.',
                'system'
            );

            return response()->json([
                'success' => true,
                'message' => 'Conversation closed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Close conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to close conversation'
            ], 500);
        }
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = Auth::user();
            
            $unreadCount = Conversation::forUser($user->id)
                ->active()
                ->get()
                ->sum(function ($conversation) use ($user) {
                    return $conversation->getUnreadCountForUser($user->id);
                });

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Get unread count error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count'
            ], 500);
        }
    }

    /**
     * Handle file attachments
     */
    private function handleAttachments($files)
    {
        $attachments = [];
        
        foreach ($files as $file) {
            if ($file->isValid()) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('chat-attachments', $fileName, 'public');
                
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        return $attachments;
    }

    /**
     * Assign support agent for customer support
     */
    private function assignSupportAgent()
    {
        // Simple round-robin assignment
        // You can implement more sophisticated assignment logic
        $supportAgent = User::where('role', 'admin')
            ->orWhere('role', 'support')
            ->inRandomOrder()
            ->first();

        return $supportAgent?->id;
    }

    /**
     * Generate conversation title
     */
    private function generateConversationTitle($conversation, $currentUser)
    {
        if ($conversation->type === 'customer_support') {
            return 'Support Request #' . $conversation->id;
        }

        if ($conversation->type === 'merchant_customer') {
            $otherParticipant = $conversation->getOtherParticipants($currentUser->id)->first();
            return $otherParticipant ? 'Chat with ' . $otherParticipant->name : 'Private Chat';
        }

        return 'Conversation #' . $conversation->id;
    }
}
