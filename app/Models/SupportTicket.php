<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'booking_id',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'source',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'resolution_notes',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (! $ticket->ticket_number) {
                $ticket->ticket_number = 'TKT-'.str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SupportTicketResponse::class)->orderBy('created_at', 'asc');
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'secondary',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'waiting_response' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
            default => 'Unknown',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'waiting_response' => 'Waiting Response',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => 'Unknown',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'general' => 'General',
            'booking' => 'Booking',
            'payment' => 'Payment',
            'refund' => 'Refund',
            'technical' => 'Technical',
            'complaint' => 'Complaint',
            'feature_request' => 'Feature Request',
            default => 'Unknown',
        };
    }

    public function getResponseTimeAttribute(): ?int
    {
        if (! $this->first_response_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->first_response_at);
    }

    public function getResolutionTimeAttribute(): ?int
    {
        if (! $this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->resolved_at);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'waiting_response']);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function canBeReopened(): bool
    {
        return $this->status === 'closed' && $this->closed_at && $this->closed_at->diffInDays(now()) <= 30;
    }

    public function markAsResolved(?string $resolutionNotes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes,
        ]);
    }

    public function markAsClosed(): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }
}
