<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use App\Notifications\ServiceRequestNotification;

class NotificationService
{
    /** Notify admins/managers when a new ticket is created. */
    public function notifyTicketCreated(Ticket $ticket): void
    {
        $recipients = User::whereHas('role', fn($q) => $q->whereIn('slug', ['admin', 'it-manager']))
                          ->where('is_active', true)
                          ->where('id', '!=', $ticket->requester_id)
                          ->get();

        $this->send($recipients, $ticket, TicketNotification::TICKET_CREATED,
            "Tiket baru {$ticket->ticket_number}: {$ticket->title}");
    }

    /** Notify assignee when a ticket is assigned to them. */
    public function notifyAssigned(Ticket $ticket, User $assignee): void
    {
        $assignee->notify(new TicketNotification(
            $ticket,
            TicketNotification::TICKET_ASSIGNED,
            "Tiket {$ticket->ticket_number} telah di-assign kepada Anda: {$ticket->title}",
        ));
    }

    /** Notify requester when status changes. */
    public function notifyStatusChanged(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        $ticket->requester->notify(new TicketNotification(
            $ticket,
            TicketNotification::STATUS_CHANGED,
            "Status tiket {$ticket->ticket_number} berubah dari {$oldStatus} menjadi {$newStatus}",
        ));

        // Also notify assignee (if different from requester)
        if ($ticket->assignee && $ticket->assignee_id !== $ticket->requester_id) {
            $ticket->assignee->notify(new TicketNotification(
                $ticket,
                TicketNotification::STATUS_CHANGED,
                "Status tiket {$ticket->ticket_number} berubah menjadi {$newStatus}",
            ));
        }
    }

    /** Notify relevant parties when a comment is added. */
    public function notifyCommentAdded(Ticket $ticket, User $commenter, string $commentType): void
    {
        $recipients = collect();

        // Notify requester if tech/admin commented publicly
        if ($commentType === 'public' && $commenter->id !== $ticket->requester_id) {
            $recipients->push($ticket->requester);
        }

        // Notify assignee if requester commented
        if ($ticket->assignee && $commenter->id !== $ticket->assignee_id) {
            $recipients->push($ticket->assignee);
        }

        $recipients->unique('id')->each(fn($u) =>
            $u->notify(new TicketNotification(
                $ticket,
                TicketNotification::COMMENT_ADDED,
                "{$commenter->name} menambahkan komentar pada tiket {$ticket->ticket_number}",
            ))
        );
    }

    /** Notify approvers/managers about a new service request */
    public function notifyServiceRequestCreated(\App\Models\ServiceRequest $request): void
    {
        $recipients = User::whereHas('role', fn($q) => $q->whereIn('slug', ['it-manager', 'approver']))
                          ->where('is_active', true)
                          ->where('id', '!=', $request->user_id)
                          ->get();

        foreach ($recipients as $user) {
            $user->notify(new ServiceRequestNotification(
                $request,
                ServiceRequestNotification::REQUEST_CREATED,
                "Permintaan layanan baru {$request->request_number}: {$request->service?->name}",
            ));
        }
    }

    /** Notify requester about approval decision */
    public function notifyServiceRequestDecision(\App\Models\ServiceRequest $request, User $approver, bool $approved): void
    {
        $type    = $approved
            ? ServiceRequestNotification::REQUEST_APPROVED
            : ServiceRequestNotification::REQUEST_REJECTED;
        $message = $approved
            ? "Permintaan {$request->request_number} telah disetujui oleh {$approver->name}"
            : "Permintaan {$request->request_number} ditolak oleh {$approver->name}";

        $request->user->notify(new ServiceRequestNotification(
            $request,
            $type,
            $message,
        ));
    }

    private function send($users, Ticket $ticket, string $type, string $message): void
    {
        foreach ($users as $user) {
            $user->notify(new TicketNotification($ticket, $type, $message));
        }
    }
}
