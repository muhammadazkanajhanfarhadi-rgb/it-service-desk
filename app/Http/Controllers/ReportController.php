<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        // ── Filters ──────────────────────────────────────────────
        $dateFrom    = $request->get('date_from');
        $dateTo      = $request->get('date_to');
        $statusFilter = $request->get('status');
        $priorityFilter = $request->get('priority');

        // Base query with optional filters
        $baseQuery = Ticket::query();
        if ($dateFrom)    $baseQuery->where('created_at', '>=', $dateFrom . ' 00:00:00');
        if ($dateTo)      $baseQuery->where('created_at', '<=', $dateTo . ' 23:59:59');
        if ($statusFilter)  $baseQuery->where('status', $statusFilter);
        if ($priorityFilter) $baseQuery->whereHas('priority', fn($q) => $q->where('slug', $priorityFilter));

        // ── Summary counts ───────────────────────────────────────
        $totalTickets   = (clone $baseQuery)->count();
        $openTickets    = (clone $baseQuery)->whereNotIn('status', ['closed'])->count();
        $resolvedTickets= (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count();
        $overdueTickets = (clone $baseQuery)->overdue()->count();

        // ── By Status ────────────────────────────────────────────
        $byStatus = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── By Priority ──────────────────────────────────────────
        $byPriority = (clone $baseQuery)
            ->join('priorities', 'tickets.priority_id', '=', 'priorities.id')
            ->select('priorities.name', 'priorities.slug', 'priorities.color', DB::raw('count(*) as total'))
            ->groupBy('priorities.id', 'priorities.name', 'priorities.slug', 'priorities.color')
            ->orderBy('priorities.level')
            ->get();

        // ── By Category ───────────────────────────────────────────
        $byCategory = (clone $baseQuery)
            ->leftJoin('categories', 'tickets.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.id', DB::raw('count(*) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        // ── Trend by day (last 30 days or filtered range) ─────────
        $trendQuery = clone $baseQuery;
        if (!$dateFrom && !$dateTo) {
            $trendQuery->where('created_at', '>=', now()->subDays(30));
        }
        $trend = $trendQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // ── Avg Response & Resolution Time ────────────────────────
        $resolvedWithTimes = (clone $baseQuery)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('first_response_at')
            ->whereNotNull('resolved_at')
            ->get();

        $avgResponseSeconds = 0;
        $avgResolutionSeconds = 0;

        if ($resolvedWithTimes->isNotEmpty()) {
            $avgResponseSeconds = (int) $resolvedWithTimes->avg(
                fn($t) => $t->first_response_at->diffInSeconds($t->created_at)
            );
            $avgResolutionSeconds = (int) $resolvedWithTimes->avg(
                fn($t) => $t->resolved_at->diffInSeconds($t->created_at)
            );
        }

        // ── SLA Compliance ────────────────────────────────────────
        $slaResolvedTotal   = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count();
        $slaBreachedResolved = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->where(function ($q) {
                $q->where('sla_response_breached', true)
                  ->orWhere('sla_resolution_breached', true);
            })->count();

        $slaCompliance = $slaResolvedTotal > 0
            ? round((($slaResolvedTotal - $slaBreachedResolved) / $slaResolvedTotal) * 100, 1)
            : 100;

        // Active (not resolved/closed) breach count
        $activeBreaches = (clone $baseQuery)->whereNotIn('status', ['resolved', 'closed'])
            ->where(function ($q) {
                $q->where('sla_response_breached', true)
                  ->orWhere('sla_resolution_breached', true);
            })->count();

        // ── Technician Performance ────────────────────────────────
        $techPerformance = (clone $baseQuery)
            ->whereNotNull('assignee_id')
            ->whereNotIn('status', ['new'])
            ->join('users', 'tickets.assignee_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('count(*) as assigned_count'),
                DB::raw('sum(case when tickets.status in ("resolved","closed") then 1 else 0 end) as resolved_count'),
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('assigned_count')
            ->get();

        // ── Options for filter dropdowns ────────────────────────
        $statuses = ['new','assigned','in_progress','pending','resolved','closed','reopened'];
        $priorities = Priority::active()->get();

        return view('reports.index', compact(
            'totalTickets', 'openTickets', 'resolvedTickets', 'overdueTickets',
            'byStatus', 'byPriority', 'byCategory', 'trend',
            'avgResponseSeconds', 'avgResolutionSeconds',
            'slaCompliance', 'slaResolvedTotal', 'slaBreachedResolved', 'activeBreaches',
            'techPerformance',
            'statuses', 'priorities',
            'dateFrom', 'dateTo', 'statusFilter', 'priorityFilter',
        ));
    }
}
