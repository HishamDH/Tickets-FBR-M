<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSupportTicket
 * @property int $id
 * @property string $ticket_number
 * @property int $user_id
 * @property int|null $assigned_to
 * @property int|null $booking_id
 * @property string $subject
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property string $category
 * @property string $source
 * @property \Illuminate\Support\Carbon|null $first_response_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property string|null $resolution_notes
 * @property array|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\Booking|null $booking
 * @property-read string $category_label
 * @property-read string $priority_color
 * @property-read string $priority_label
 * @property-read int|null $resolution_time
 * @property-read int|null $response_time
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupportTicketResponse> $responses
 * @property-read int|null $responses_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereFirstResponseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereTicketNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportTicket whereUserId($value)
 * @mixin \Eloquent
 */
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
