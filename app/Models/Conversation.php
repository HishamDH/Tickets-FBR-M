<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperConversation
 */
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'status',
        'customer_id',
        'merchant_id',
        'support_agent_id',
        'booking_id',
        'last_message_at',
        'metadata',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the customer that owns the conversation.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the merchant that owns the conversation.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Get the support agent assigned to the conversation.
     */
    public function supportAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'support_agent_id');
    }

    /**
     * Get the booking related to the conversation.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(PaidReservation::class, 'booking_id');
    }

    /**
     * Get all messages for the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message for the conversation.
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    /**
     * Get unread messages count for a specific user.
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark all messages as read for a specific user.
     */
    public function markAsReadForUser($userId)
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Check if user is participant in conversation.
     */
    public function hasParticipant($userId)
    {
        return $this->customer_id == $userId ||
               $this->merchant_id == $userId ||
               $this->support_agent_id == $userId;
    }

    /**
     * Get other participant(s) for the conversation.
     */
    public function getOtherParticipants($currentUserId)
    {
        $participants = collect([
            $this->customer_id,
            $this->merchant_id,
            $this->support_agent_id,
        ])->filter()->unique()->reject(function ($id) use ($currentUserId) {
            return $id == $currentUserId;
        });

        return User::whereIn('id', $participants)->get();
    }

    /**
     * Create a new conversation.
     */
    public static function createConversation($data)
    {
        $conversation = self::create([
            'title' => $data['title'] ?? null,
            'type' => $data['type'] ?? 'customer_support',
            'customer_id' => $data['customer_id'],
            'merchant_id' => $data['merchant_id'] ?? null,
            'support_agent_id' => $data['support_agent_id'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Send first message if provided
        if (isset($data['initial_message'])) {
            $conversation->messages()->create([
                'sender_id' => $data['customer_id'],
                'content' => $data['initial_message'],
                'type' => 'text',
            ]);

            $conversation->update(['last_message_at' => now()]);
        }

        return $conversation;
    }

    /**
     * Scope to get active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get conversations for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('customer_id', $userId)
                ->orWhere('merchant_id', $userId)
                ->orWhere('support_agent_id', $userId);
        });
    }

    /**
     * Scope to get support conversations.
     */
    public function scopeSupport($query)
    {
        return $query->where('type', 'customer_support');
    }

    /**
     * Scope to get merchant-customer conversations.
     */
    public function scopeMerchantCustomer($query)
    {
        return $query->where('type', 'merchant_customer');
    }
}
