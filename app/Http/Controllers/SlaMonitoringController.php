<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Services\SlaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlaMonitoringController extends Controller
{
    public function __construct(
        private SlaService $slaService,
    ) {}

    public function index(Request $request): View
    {
        // ── Refresh breach flags on all active tickets ──────────
        $activeTickets = Ticket::whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('resolution_due_at')
            ->get();

        foreach ($activeTickets as $ticket) {
            if ($this->slaService->checkBreaches($ticket)) {
                $ticket->save();
            }
        }

        // ── Filter by priority ─────────────────────────────────
        $priorityFilter = $request->get('priority');

        // ── KPI Stats ───────────────────────────────────────────
        $totalActive  = Ticket::whereNotIn('status', ['resolved', 'closed'])->count();
        $breachedCount = Ticket::whereNotIn('status', ['resolved', 'closed'])
            ->where(function ($q) {
                $q->where('sla_response_breached', true)
                  ->orWhere('sla_resolution_breached', true);
            })->count();

        $resolvedTotal = Ticket::whereIn('status', ['resolved', 'closed'])->count();
        $resolvedBreached = Ticket::whereIn('status', ['resolved', 'closed'])
            ->where(function ($q) {
                $q->where('sla_response_breached', true)
                  ->orWhere('sla_resolution_breached', true);
            })->count();

        $slaCompliance = $resolvedTotal > 0
            ? round((($resolvedTotal - $resolvedBreached) / $resolvedTotal) * 100, 1)
            : 100;

        // Avg response & resolution time (from resolved tickets)
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('first_response_at')
            ->whereNotNull('resolved_at')
            ->get();

        $avgResponseSeconds = 0;
        $avgResolutionSeconds = 0;

        if ($resolvedTickets->isNotEmpty()) {
            $avgResponseSeconds = $resolvedTickets->avg(
                fn($t) => $t->first_response_at->diffInSeconds($t->created_at)
            );
            $avgResolutionSeconds = $resolvedTickets->avg(
                fn($t) => $t->resolved_at->diffInSeconds($t->created_at)
            );
        }

        // ── At-risk tickets (resolution due within 30 min) ──────
        $atRiskQuery = Ticket::with(['priority', 'assignee', 'requester'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('resolution_due_at')
            ->where('sla_resolution_breached', false)
            ->where('resolution_due_at', '<=', now()->addMinutes(30))
            ->where('resolution_due_at', '>', now())
            ->orderBy('resolution_due_at');

        if ($priorityFilter) {
            $atRiskQuery->whereHas('priority', fn($q) => $q->where('slug', $priorityFilter));
        }
        $atRiskTickets = $atRiskQuery->get();

        // ── Breached tickets ────────────────────────────────────
        $breachedQuery = Ticket::with(['priority', 'assignee', 'requester'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->where(function ($q) {
                $q->where('sla_response_breached', true)
                  ->orWhere('sla_resolution_breached', true)
                  ->orWhere(function ($sq) {
                      $sq->whereNotNull('resolution_due_at')
                         ->where('resolution_due_at', '<', now());
                  });
            })
            ->orderByDesc('priority_id')
            ->orderBy('resolution_due_at');

        if ($priorityFilter) {
            $breachedQuery->whereHas('priority', fn($q) => $q->where('slug', $priorityFilter));
        }
        $breachedTickets = $breachedQuery->get();

        // ── All active tickets with SLA (paginated) ─────────────
        $allQuery = Ticket::with(['priority', 'assignee', 'requester'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('resolution_due_at')
            ->orderBy('resolution_due_at');

        if ($priorityFilter) {
            $allQuery->whereHas('priority', fn($q) => $q->where('slug', $priorityFilter));
        }
        $allTickets = $allQuery->paginate(15)->withQueryString();

        // ── SLA Policy Matrix ──────────────────────────────────
        $policies = SlaPolicy::with('priority')
            ->where('is_active', true)
            ->orderBy('priority_id')
            ->get();

        $priorities = Priority::active()->get();

        return view('sla.monitoring', compact(
            'totalActive',
            'breachedCount',
            'slaCompliance',
            'avgResponseSeconds',
            'avgResolutionSeconds',
            'atRiskTickets',
            'breachedTickets',
            'allTickets',
            'policies',
            'priorities',
            'priorityFilter',
        ));
    }
}
