<?php

namespace App\Services;

use App\Models\SlaPolicy;
use App\Models\Ticket;
use Carbon\Carbon;

class SlaService
{
    /**
     * Calculate and assign SLA deadlines when a ticket is created or priority changes.
     */
    public function applyToTicket(Ticket $ticket): void
    {
        $policy = SlaPolicy::where('priority_id', $ticket->priority_id)
                           ->where('is_active', true)
                           ->first();

        if (!$policy) return;

        $start = $ticket->created_at ?? now();

        $ticket->sla_policy_id       = $policy->id;
        $ticket->response_due_at     = $this->addMinutes($start, $policy->response_minutes, $policy);
        $ticket->resolution_due_at   = $this->addMinutes($start, $policy->resolution_minutes, $policy);
    }

    /**
     * Check and mark SLA breaches on a ticket.
     * Called periodically or on ticket update.
     */
    public function checkBreaches(Ticket $ticket): bool
    {
        $changed = false;
        $now = now();

        // Response breach
        if (!$ticket->sla_response_breached
            && $ticket->response_due_at
            && $now->isAfter($ticket->response_due_at)
            && !$ticket->first_response_at)
        {
            $ticket->sla_response_breached = true;
            $changed = true;
        }

        // Resolution breach
        if (!$ticket->sla_resolution_breached
            && $ticket->resolution_due_at
            && $now->isAfter($ticket->resolution_due_at)
            && !in_array($ticket->status, ['resolved', 'closed']))
        {
            $ticket->sla_resolution_breached = true;
            $changed = true;
        }

        return $changed;
    }

    /**
     * Mark first response time when technician first replies.
     */
    public function recordFirstResponse(Ticket $ticket): void
    {
        if (!$ticket->first_response_at) {
            $ticket->first_response_at = now();
        }
    }

    /**
     * Add minutes to a start time.
     * If business_hours_only, skip non-business hours (simplified: add calendar minutes for now).
     * TODO: implement true business-hours calculator for production.
     */
    private function addMinutes(Carbon $start, int $minutes, SlaPolicy $policy): Carbon
    {
        // Simplified: always add calendar minutes
        // For business-hours-aware SLA, integrate a proper business calendar
        return $start->copy()->addMinutes($minutes);
    }
}
