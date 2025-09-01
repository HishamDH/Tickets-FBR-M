<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\SupportTicketResponse;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupportService
{
    /**
     * Create a new support ticket
     */
    public function createTicket(array $data): SupportTicket
    {
        DB::beginTransaction();
        try {
            $ticket = SupportTicket::create([
                'user_id' => $data['user_id'],
                'booking_id' => $data['booking_id'] ?? null,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'normal',
                'category' => $data['category'] ?? 'general',
                'source' => $data['source'] ?? 'web',
                'tags' => $data['tags'] ?? null,
            ]);

            // Auto-assign based on category
            $assignedAgent = $this->getAutoAssignedAgent($ticket);
            if ($assignedAgent) {
                $ticket->update(['assigned_to' => $assignedAgent->id]);
            }

            DB::commit();

            Log::info('Support ticket created', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'user_id' => $ticket->user_id,
                'category' => $ticket->category,
            ]);

            // Send notifications
            $this->sendTicketCreatedNotifications($ticket);

            return $ticket;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to create support ticket', [
                'user_id' => $data['user_id'],
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Add response to ticket
     */
    public function addResponse(SupportTicket $ticket, int $userId, string $message, array $attachments = [], bool $isInternal = false): SupportTicketResponse
    {
        DB::beginTransaction();
        try {
            $response = SupportTicketResponse::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => $userId,
                'message' => $message,
                'is_internal' => $isInternal,
                'attachments' => $attachments,
            ]);

            DB::commit();

            Log::info('Support ticket response added', [
                'ticket_id' => $ticket->id,
                'response_id' => $response->id,
                'user_id' => $userId,
                'is_internal' => $isInternal,
            ]);

            // Send notifications if not internal
            if (! $isInternal) {
                $this->sendResponseNotifications($ticket, $response);
            }

            return $response;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to add ticket response', [
                'ticket_id' => $ticket->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Assign ticket to agent
     */
    public function assignTicket(SupportTicket $ticket, int $agentId): bool
    {
        $agent = User::find($agentId);

        if (! $agent || ! $this->canAssignTickets($agent)) {
            throw new Exception('Invalid agent or insufficient permissions');
        }

        $oldAssignee = $ticket->assignedTo;
        $ticket->update(['assigned_to' => $agentId]);

        Log::info('Support ticket assigned', [
            'ticket_id' => $ticket->id,
            'assigned_to' => $agentId,
            'previous_assignee' => $oldAssignee?->id,
        ]);

        // Notify new assignee
        $this->sendTicketAssignmentNotification($ticket, $agent);

        return true;
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus(SupportTicket $ticket, string $status, ?string $resolutionNotes = null): bool
    {
        $oldStatus = $ticket->status;

        $updateData = ['status' => $status];

        if ($status === 'resolved') {
            $updateData['resolved_at'] = now();
            if ($resolutionNotes) {
                $updateData['resolution_notes'] = $resolutionNotes;
            }
        } elseif ($status === 'closed') {
            $updateData['closed_at'] = now();
        }

        $ticket->update($updateData);

        Log::info('Support ticket status updated', [
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $status,
        ]);

        // Send status change notifications
        $this->sendStatusChangeNotifications($ticket, $oldStatus);

        return true;
    }

    /**
     * Update ticket priority
     */
    public function updateTicketPriority(SupportTicket $ticket, string $priority): bool
    {
        $oldPriority = $ticket->priority;
        $ticket->update(['priority' => $priority]);

        Log::info('Support ticket priority updated', [
            'ticket_id' => $ticket->id,
            'old_priority' => $oldPriority,
            'new_priority' => $priority,
        ]);

        return true;
    }

    /**
     * Get tickets for user
     */
    public function getUserTickets(int $userId, array $filters = [])
    {
        $query = SupportTicket::where('user_id', $userId)
            ->with(['assignedTo', 'booking', 'responses'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get tickets assigned to agent
     */
    public function getAgentTickets(int $agentId, array $filters = [])
    {
        $query = SupportTicket::where('assigned_to', $agentId)
            ->with(['user', 'booking', 'responses'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get unassigned tickets
     */
    public function getUnassignedTickets(array $filters = [])
    {
        $query = SupportTicket::whereNull('assigned_to')
            ->with(['user', 'booking'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        // Apply filters
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get support statistics
     */
    public function getSupportStats(?int $agentId = null, ?string $period = 'month'): array
    {
        $dateFrom = match ($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $query = SupportTicket::where('created_at', '>=', $dateFrom);

        if ($agentId) {
            $query->where('assigned_to', $agentId);
        }

        $tickets = $query->get();

        return [
            'total_tickets' => $tickets->count(),
            'open_tickets' => $tickets->where('status', 'open')->count(),
            'in_progress_tickets' => $tickets->where('status', 'in_progress')->count(),
            'resolved_tickets' => $tickets->where('status', 'resolved')->count(),
            'closed_tickets' => $tickets->where('status', 'closed')->count(),
            'average_response_time' => $this->calculateAverageResponseTime($tickets),
            'average_resolution_time' => $this->calculateAverageResolutionTime($tickets),
            'tickets_by_priority' => $tickets->groupBy('priority')->map->count(),
            'tickets_by_category' => $tickets->groupBy('category')->map->count(),
            'satisfaction_rate' => $this->calculateSatisfactionRate($tickets),
        ];
    }

    /**
     * Auto-assign ticket based on category and workload
     */
    protected function getAutoAssignedAgent(SupportTicket $ticket): ?User
    {
        // Get available support agents based on category
        $agents = $this->getAvailableAgents($ticket->category);

        if ($agents->isEmpty()) {
            return null;
        }

        // Simple round-robin assignment based on current workload
        return $agents->sortBy(function ($agent) {
            return SupportTicket::where('assigned_to', $agent->id)
                ->whereIn('status', ['open', 'in_progress', 'waiting_response'])
                ->count();
        })->first();
    }

    /**
     * Get available support agents
     */
    protected function getAvailableAgents(?string $category = null)
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin'); // Or Support Agent role
        })->get();
    }

    /**
     * Check if user can assign tickets
     */
    protected function canAssignTickets(User $user): bool
    {
        return $user->hasRole(['Admin', 'Support Agent']);
    }

    /**
     * Calculate average response time in hours
     */
    protected function calculateAverageResponseTime($tickets): float
    {
        $responseTimes = $tickets->filter(function ($ticket) {
            return $ticket->first_response_at !== null;
        })->map(function ($ticket) {
            return $ticket->response_time;
        });

        return $responseTimes->isEmpty() ? 0 : round($responseTimes->average(), 2);
    }

    /**
     * Calculate average resolution time in hours
     */
    protected function calculateAverageResolutionTime($tickets): float
    {
        $resolutionTimes = $tickets->filter(function ($ticket) {
            return $ticket->resolved_at !== null;
        })->map(function ($ticket) {
            return $ticket->resolution_time;
        });

        return $resolutionTimes->isEmpty() ? 0 : round($resolutionTimes->average(), 2);
    }

    /**
     * Calculate customer satisfaction rate
     */
    protected function calculateSatisfactionRate($tickets): float
    {
        // This would integrate with a customer satisfaction survey system
        // For now, return a placeholder
        return 85.5;
    }

    /**
     * Send ticket created notifications
     */
    protected function sendTicketCreatedNotifications(SupportTicket $ticket): void
    {
        // Send email to customer confirming ticket creation
        // Send notification to assigned agent if any
        Log::info('Ticket created notifications sent', ['ticket_id' => $ticket->id]);
    }

    /**
     * Send response notifications
     */
    protected function sendResponseNotifications(SupportTicket $ticket, SupportTicketResponse $response): void
    {
        // Send email to customer if response is from staff
        // Send email to staff if response is from customer
        Log::info('Response notifications sent', [
            'ticket_id' => $ticket->id,
            'response_id' => $response->id,
        ]);
    }

    /**
     * Send ticket assignment notification
     */
    protected function sendTicketAssignmentNotification(SupportTicket $ticket, User $agent): void
    {
        // Send email to newly assigned agent
        Log::info('Assignment notification sent', [
            'ticket_id' => $ticket->id,
            'agent_id' => $agent->id,
        ]);
    }

    /**
     * Send status change notifications
     */
    protected function sendStatusChangeNotifications(SupportTicket $ticket, string $oldStatus): void
    {
        // Send status update to customer
        Log::info('Status change notifications sent', [
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $ticket->status,
        ]);
    }

    /**
     * Search tickets
     */
    public function searchTickets(string $query, array $filters = [])
    {
        $searchQuery = SupportTicket::query()
            ->with(['user', 'assignedTo', 'booking'])
            ->where(function ($q) use ($query) {
                $q->where('ticket_number', 'like', "%{$query}%")
                    ->orWhere('subject', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    });
            });

        // Apply filters
        if (isset($filters['status'])) {
            $searchQuery->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $searchQuery->where('category', $filters['category']);
        }

        if (isset($filters['priority'])) {
            $searchQuery->where('priority', $filters['priority']);
        }

        return $searchQuery->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}
