<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperMessage
 * @property int $id
 * @property int $conversation_id
 * @property int $sender_id
 * @property string $content
 * @property string $type
 * @property array|null $attachments
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property bool $is_deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message notDeleted()
 * @method static \Illuminate\Database\Eloquent\Builder|Message notFrom($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message unread()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',
        'attachments',
        'read_at',
        'is_deleted',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that sent the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Check if message is read.
     */
    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        if (! $this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if message has attachments.
     */
    public function hasAttachments(): bool
    {
        return ! empty($this->attachments);
    }

    /**
     * Get attachment URLs.
     */
    public function getAttachmentUrls()
    {
        if (! $this->hasAttachments()) {
            return [];
        }

        return collect($this->attachments)->map(function ($attachment) {
            return [
                'name' => $attachment['name'],
                'url' => asset('storage/'.$attachment['path']),
                'type' => $attachment['type'] ?? 'file',
                'size' => $attachment['size'] ?? null,
            ];
        });
    }

    /**
     * Create a new message.
     */
    public static function createMessage($conversationId, $senderId, $content, $type = 'text', $attachments = [])
    {
        $message = self::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'content' => $content,
            'type' => $type,
            'attachments' => $attachments,
        ]);

        // Update conversation's last message timestamp
        $message->conversation->update(['last_message_at' => now()]);

        return $message;
    }

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get messages not from specific user.
     */
    public function scopeNotFrom($query, $userId)
    {
        return $query->where('sender_id', '!=', $userId);
    }

    /**
     * Scope to get non-deleted messages.
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Soft delete message.
     */
    public function softDelete()
    {
        $this->update(['is_deleted' => true]);
    }

    /**
     * Format message for display.
     */
    public function toDisplayArray()
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'type' => $this->type,
            'attachments' => $this->getAttachmentUrls(),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar_url ?? null,
            ],
            'is_read' => $this->isRead(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'),
        ];
    }
}
