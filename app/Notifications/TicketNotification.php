<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
{
    use Queueable;

    // Type constants
    const TICKET_CREATED   = 'ticket_created';
    const TICKET_ASSIGNED  = 'ticket_assigned';
    const STATUS_CHANGED   = 'status_changed';
    const COMMENT_ADDED    = 'comment_added';
    const SLA_WARNING      = 'sla_warning';
    const SLA_BREACH       = 'sla_breach';

    public function __construct(
        private Ticket  $ticket,
        private string  $type,
        private string  $message,
    ) {}

    public function via(): array
    {
        return ['database'];
    }

    public function toDatabase(): array
    {
        return [
            'type'          => $this->type,
            'ticket_id'     => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'ticket_title'  => $this->ticket->title,
            'message'       => $this->message,
            'icon'          => $this->iconForType(),
            'color'         => $this->colorForType(),
            'url'           => route('tickets.show', $this->ticket),
        ];
    }

    private function iconForType(): string
    {
        return match($this->type) {
            self::TICKET_CREATED  => 'add_circle',
            self::TICKET_ASSIGNED => 'assignment_ind',
            self::STATUS_CHANGED  => 'swap_horiz',
            self::COMMENT_ADDED   => 'chat',
            self::SLA_WARNING     => 'timer',
            self::SLA_BREACH      => 'warning',
            default               => 'notifications',
        };
    }

    private function colorForType(): string
    {
        return match($this->type) {
            self::SLA_BREACH      => 'status-danger',
            self::SLA_WARNING     => 'status-warning',
            self::TICKET_ASSIGNED => 'status-info',
            self::STATUS_CHANGED  => 'accent-electric',
            self::COMMENT_ADDED   => 'status-pending',
            default               => 'accent-light',
        };
    }
}
