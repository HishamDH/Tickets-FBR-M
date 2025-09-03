<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatComponent extends Component
{
    use WithFileUploads;

    public $selectedConversation = null;

    public $conversations = [];

    public $messages = [];

    public $newMessage = '';

    public $attachments = [];

    public $searchTerm = '';

    public $onlineUsers = [];

    public function mount()
    {
        $this->loadConversations();
        $this->loadOnlineUsers();
    }

    public function loadConversations()
    {
        $query = Conversation::where(function ($q) {
            $q->where('customer_id', Auth::id())
                ->orWhere('merchant_id', Auth::id())
                ->orWhere('admin_id', Auth::id());
        })
            ->with(['customer', 'merchant', 'admin', 'lastMessage'])
            ->orderBy('updated_at', 'desc');

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->searchTerm.'%')
                    ->orWhereHas('messages', function ($messageQuery) {
                        $messageQuery->where('content', 'like', '%'.$this->searchTerm.'%');
                    });
            });
        }

        $this->conversations = $query->get();
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversation = Conversation::with(['customer', 'merchant', 'admin'])
            ->findOrFail($conversationId);

        $this->loadMessages();
        $this->markMessagesAsRead();
    }

    public function loadMessages()
    {
        if (! $this->selectedConversation) {
            return;
        }

        $this->messages = Message::where('conversation_id', $this->selectedConversation->id)
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        if (! $this->selectedConversation || (! $this->newMessage && empty($this->attachments))) {
            return;
        }

        $message = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => $this->getSenderType(),
            'content' => $this->newMessage,
            'type' => ! empty($this->attachments) ? 'file' : 'text',
            'attachments' => ! empty($this->attachments) ? $this->processAttachments() : null,
            'is_read' => false,
        ]);

        // Update conversation timestamp
        $this->selectedConversation->touch();

        // Reset form
        $this->newMessage = '';
        $this->attachments = [];

        // Reload messages
        $this->loadMessages();
        $this->loadConversations();

        // Notify other participants
        $this->notifyParticipants($message);

        // Emit event for real-time updates
        $this->dispatch('message-sent', [
            'conversationId' => $this->selectedConversation->id,
            'messageId' => $message->id,
        ]);
    }

    public function startNewConversation($participantId, $participantType)
    {
        $conversation = Conversation::create([
            'customer_id' => $participantType === 'customer' ? $participantId : Auth::id(),
            'merchant_id' => $participantType === 'merchant' ? $participantId : null,
            'admin_id' => $participantType === 'admin' ? $participantId : null,
            'type' => $this->getConversationType($participantType),
            'title' => $this->generateConversationTitle($participantId, $participantType),
            'status' => 'active',
        ]);

        $this->selectConversation($conversation->id);
        $this->loadConversations();
    }

    public function markMessagesAsRead()
    {
        if (! $this->selectedConversation) {
            return;
        }

        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        // Check if user can delete this message
        if ($message->sender_id !== Auth::id()) {
            return;
        }

        $message->delete();
        $this->loadMessages();
    }

    public function archiveConversation($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->update(['status' => 'archived']);

        $this->loadConversations();

        if ($this->selectedConversation && $this->selectedConversation->id === $conversationId) {
            $this->selectedConversation = null;
            $this->messages = [];
        }
    }

    #[On('echo:conversations.{conversationId},MessageSent')]
    public function handleNewMessage($data)
    {
        if ($this->selectedConversation && $this->selectedConversation->id == $data['conversationId']) {
            $this->loadMessages();
        }

        $this->loadConversations();
    }

    private function getSenderType()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return 'admin';
        } elseif ($user->hasRole('merchant')) {
            return 'merchant';
        } else {
            return 'customer';
        }
    }

    private function getConversationType($participantType)
    {
        $userType = $this->getSenderType();

        return match (true) {
            $userType === 'customer' && $participantType === 'merchant' => 'customer_merchant',
            $userType === 'merchant' && $participantType === 'customer' => 'customer_merchant',
            $userType === 'customer' && $participantType === 'admin' => 'customer_support',
            $userType === 'admin' && $participantType === 'customer' => 'customer_support',
            $userType === 'merchant' && $participantType === 'admin' => 'merchant_support',
            $userType === 'admin' && $participantType === 'merchant' => 'merchant_support',
            default => 'general',
        };
    }

    private function generateConversationTitle($participantId, $participantType)
    {
        $participant = User::find($participantId);
        $userName = Auth::user()->name;
        $participantName = $participant->name ?? 'Unknown User';

        return "Conversation between {$userName} and {$participantName}";
    }

    private function processAttachments()
    {
        $processedAttachments = [];

        foreach ($this->attachments as $attachment) {
            $path = $attachment->store('chat-attachments', 'public');
            $processedAttachments[] = [
                'name' => $attachment->getClientOriginalName(),
                'path' => $path,
                'size' => $attachment->getSize(),
                'type' => $attachment->getMimeType(),
            ];
        }

        return $processedAttachments;
    }

    private function notifyParticipants($message)
    {
        // Implementation for notifying other conversation participants
        // This could use Laravel notifications, websockets, etc.
        if (function_exists('notifcate')) {
            $participants = $this->getConversationParticipants();

            foreach ($participants as $participant) {
                if ($participant->id !== Auth::id()) {
                    notifcate(
                        $participant->id,
                        'New Message',
                        Auth::user()->name.' sent you a message: '.substr($message->content, 0, 50).'...'
                    );
                }
            }
        }
    }

    private function getConversationParticipants()
    {
        $participants = collect();

        if ($this->selectedConversation->customer) {
            $participants->push($this->selectedConversation->customer);
        }

        if ($this->selectedConversation->merchant) {
            $participants->push($this->selectedConversation->merchant);
        }

        if ($this->selectedConversation->admin) {
            $participants->push($this->selectedConversation->admin);
        }

        return $participants;
    }

    private function loadOnlineUsers()
    {
        // This would typically use Laravel Echo/Pusher to track online users
        // For now, we'll return an empty array
        $this->onlineUsers = [];
    }

    public function updatedSearchTerm()
    {
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.chat-component');
    }
}
