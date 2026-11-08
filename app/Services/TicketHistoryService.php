<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;

class TicketHistoryService
{
    public function record(
        Ticket  $ticket,
        User    $actor,
        string  $action,
        string  $description,
        ?string $fieldChanged = null,
        ?string $oldValue     = null,
        ?string $newValue     = null,
    ): TicketHistory {
        return TicketHistory::create([
            'ticket_id'     => $ticket->id,
            'user_id'       => $actor->id,
            'action'        => $action,
            'description'   => $description,
            'field_changed' => $fieldChanged,
            'old_value'     => $oldValue,
            'new_value'     => $newValue,
        ]);
    }

    public function recordCreated(Ticket $ticket, User $actor): TicketHistory
    {
        return $this->record(
            $ticket, $actor,
            TicketHistory::ACTION_CREATED,
            "Tiket dibuat oleh {$actor->name}",
        );
    }

    public function recordStatusChange(Ticket $ticket, User $actor, string $old, string $new): TicketHistory
    {
        return $this->record(
            $ticket, $actor,
            TicketHistory::ACTION_STATUS_CHANGED,
            "Status diubah dari <strong>{$old}</strong> menjadi <strong>{$new}</strong>",
            'status', $old, $new,
        );
    }

    public function recordAssigned(Ticket $ticket, User $actor, ?User $assignee): TicketHistory
    {
        $name = $assignee?->name ?? 'Unassigned';
        return $this->record(
            $ticket, $actor,
            TicketHistory::ACTION_ASSIGNED,
            "Tiket di-assign kepada <strong>{$name}</strong>",
            'assignee_id', null, (string) $assignee?->id,
        );
    }

    public function recordPriorityChange(Ticket $ticket, User $actor, string $old, string $new): TicketHistory
    {
        return $this->record(
            $ticket, $actor,
            TicketHistory::ACTION_PRIORITY_CHANGED,
            "Prioritas diubah dari <strong>{$old}</strong> menjadi <strong>{$new}</strong>",
            'priority_id', $old, $new,
        );
    }
}
