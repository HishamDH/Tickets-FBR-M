<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSupportTicketResponse
 */
class SupportTicketResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'is_internal',
        'attachments',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($response) {
            $ticket = $response->supportTicket;

            // Update first response time if this is the first response
            if (! $ticket->first_response_at && ! $response->is_internal && $response->user_id !== $ticket->user_id) {
                $ticket->update(['first_response_at' => now()]);
            }

            // Update ticket status based on who responded
            if ($response->user_id === $ticket->user_id) {
                // Customer responded, set to waiting response if staff was involved
                if ($ticket->assigned_to) {
                    $ticket->update(['status' => 'waiting_response']);
                }
            } else {
                // Staff responded, set to in progress
                if ($ticket->status === 'open') {
                    $ticket->update(['status' => 'in_progress']);
                }
            }
        });
    }

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFromCustomer(): bool
    {
        return $this->user_id === $this->supportTicket->user_id;
    }

    public function isFromStaff(): bool
    {
        return ! $this->isFromCustomer();
    }

    public function markAsRead(): void
    {
        if (! $this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function hasAttachments(): bool
    {
        return ! empty($this->attachments);
    }

    public function getAttachmentCount(): int
    {
        return count($this->attachments ?? []);
    }
}
