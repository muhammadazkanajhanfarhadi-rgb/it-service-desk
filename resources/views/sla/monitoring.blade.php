<x-app-layout>
    <x-slot name="title">SLA Monitoring — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">SLA Monitoring</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="SLA Monitoring"
            subtitle="Pantau kepatuhan SLA, tiket at-risk, dan pelanggaran." />

        {{-- Priority filter --}}
        <form method="GET" class="flex items-center gap-2">
            <select name="priority" class="form-select" style="width: 160px;" onchange="this.form.submit()">
                <option value="">Semua Prioritas</option>
                @foreach($priorities as $p)
                <option value="{{ $p->slug }}" @selected($priorityFilter === $p->slug)>{{ $p->name }}</option>
                @endforeach
            </select>
            @if($priorityFilter)
            <a href="{{ route('sla.monitoring') }}" class="btn-secondary btn-sm">
                <span class="material-symbols-outlined" style="font-size: 14px;">close</span>
            </a>
            @endif
        </form>
    </div>

    {{-- ── KPI Cards ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="SLA Compliance" value="{{ $slaCompliance }}%"
            icon="policy" color="status-success" />
        <x-stat-card label="Active Tickets" value="{{ $totalActive }}"
            icon="confirmation_number" color="accent-electric" />
        <x-stat-card label="SLA Breached" value="{{ $breachedCount }}"
            icon="warning" color="status-danger" />
        <x-stat-card label="At Risk (≤30m)" value="{{ $atRiskTickets->count() }}"
            icon="timer" color="status-warning" />
    </div>

    {{-- ── Avg Response & Resolution ──────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background-color: rgba(56,189,248,0.12);">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-status-info);">flash_on</span>
                </div>
                <div>
                    <div class="stat-value">
                        @if($avgResponseSeconds > 0)
                        {{ gmdate('H\j m\m', $avgResponseSeconds) }}
                        @else — @endif
                    </div>
                    <div class="stat-label">Average Response Time</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background-color: rgba(34,197,94,0.12);">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-status-success);">timer</span>
                </div>
                <div>
                    <div class="stat-value">
                        @if($avgResolutionSeconds > 0)
                        {{ gmdate('H\j m\m', $avgResolutionSeconds) }}
                        @else — @endif
                    </div>
                    <div class="stat-label">Average Resolution Time</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SLA Tier Policy Matrix ────────────────────────────── --}}
    <div class="card mb-6">
        <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
            SLA Tier Policy Matrix
        </h2>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Policy</th>
                        <th>Prioritas</th>
                        <th>Response Target</th>
                        <th>Resolution Target</th>
                        <th>Jam Kerja</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($policies as $policy)
                <tr>
                    <td style="font-weight: 600;">{{ $policy->name }}</td>
                    <td>
                        @if($policy->priority)
                        <x-priority-badge :priority="$policy->priority" />
                        @else — @endif
                    </td>
                    <td style="color: var(--color-status-info); font-weight: 500;">{{ $policy->responseLabel() }}</td>
                    <td style="color: var(--color-status-success); font-weight: 500;">{{ $policy->resolutionLabel() }}</td>
                    <td>
                        @if($policy->business_hours_only)
                        <span class="badge badge-pending">{{ $policy->business_start }}–{{ $policy->business_end }}</span>
                        @else
                        <span class="badge badge-resolved">24/7</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── At-Risk Tickets ────────────────────────────────────── --}}
    <div class="card mb-6">
        <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-status-warning);">
            <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">timer</span>
            Tiket Mendekati SLA Breach (≤30 menit)
        </h2>

        @if($atRiskTickets->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 gap-2">
            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-status-success);">verified</span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada tiket yang mendekati pelanggaran SLA</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Tiket</th><th>Judul</th><th>Prioritas</th><th>Assigned</th><th>Sisa Waktu</th><th>Progress</th></tr>
                </thead>
                <tbody>
                @foreach($atRiskTickets as $ticket)
                @php
                    $remaining = now()->diffInMinutes($ticket->resolution_due_at, false);
                    $totalSla = $ticket->created_at->diffInMinutes($ticket->resolution_due_at);
                    $elapsed = max(0, $totalSla - $remaining);
                    $percent = $totalSla > 0 ? round(($elapsed / $totalSla) * 100) : 0;
                    $percent = min(100, max(0, $percent));
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}"
                           style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">
                            {{ $ticket->ticket_number }}
                        </a>
                    </td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $ticket->title }}
                    </td>
                    <td><x-priority-badge :priority="$ticket->priority" /></td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">
                        {{ $ticket->assignee?->name ?? 'Unassigned' }}
                    </td>
                    <td style="font-size: 12px; font-weight: 600; color: var(--color-status-warning);">
                        {{ $remaining }} menit
                    </td>
                    <td style="min-width: 100px;">
                        <div class="sla-bar">
                            <div class="sla-bar-fill sla-bar-fill-warning" style="width: {{ $percent }}%;"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Breached Tickets ───────────────────────────────────── --}}
    <div class="card mb-6">
        <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-status-danger);">
            <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">warning</span>
            Tiket SLA Breached ({{ $breachedTickets->count() }})
        </h2>

        @if($breachedTickets->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 gap-2">
            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-status-success);">verified</span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada tiket yang breach SLA</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Tiket</th><th>Judul</th><th>Prioritas</th><th>Assigned</th><th>Due At</th><th>Breach</th></tr>
                </thead>
                <tbody>
                @foreach($breachedTickets as $ticket)
                <tr>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}"
                           style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">
                            {{ $ticket->ticket_number }}
                        </a>
                    </td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $ticket->title }}
                    </td>
                    <td><x-priority-badge :priority="$ticket->priority" /></td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">
                        {{ $ticket->assignee?->name ?? 'Unassigned' }}
                    </td>
                    <td style="font-size: 12px; color: var(--color-text-tertiary);">
                        {{ $ticket->resolution_due_at?->format('d M H:i') }}
                    </td>
                    <td>
                        @if($ticket->sla_resolution_breached)
                        <span class="badge badge-critical">Resolution Breached</span>
                        @endif
                        @if($ticket->sla_response_breached)
                        <span class="badge badge-high">Response Breached</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ── All Active Tickets with SLA ────────────────────────── --}}
    <div class="card">
        <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
            Semua Tiket Aktif dengan SLA
        </h2>

        @if($allTickets->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 gap-2">
            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-text-disabled);">inbox</span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada tiket aktif dengan SLA</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Tiket</th><th>Judul</th><th>Status</th><th>Prioritas</th><th>Response Deadline</th><th>Resolution Deadline</th><th>SLA Status</th></tr>
                </thead>
                <tbody>
                @foreach($allTickets as $ticket)
                @php
                    $respRemaining = $ticket->response_due_at ? now()->diffInMinutes($ticket->response_due_at, false) : null;
                    $resRemaining  = $ticket->resolution_due_at ? now()->diffInMinutes($ticket->resolution_due_at, false) : null;
                    $color = $ticket->slaStatusColor();
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}"
                           style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">
                            {{ $ticket->ticket_number }}
                        </a>
                    </td>
                    <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $ticket->title }}
                    </td>
                    <td><x-ticket-status-badge :status="$ticket->status" /></td>
                    <td><x-priority-badge :priority="$ticket->priority" /></td>
                    <td style="font-size: 12px;">
                        @if($ticket->first_response_at)
                        <span style="color: var(--color-status-success);">✓ Responded</span>
                        @elseif($ticket->response_due_at)
                            @if($ticket->sla_response_breached)
                            <span style="color: var(--color-status-danger);">Breached</span>
                            @elseif($respRemaining !== null && $respRemaining <= 0)
                            <span style="color: var(--color-status-danger);">Overdue</span>
                            @else
                            <span style="color: var(--color-text-tertiary);">{{ $respRemaining }}m left</span>
                            @endif
                        @else
                        <span style="color: var(--color-text-disabled);">—</span>
                        @endif
                    </td>
                    <td style="font-size: 12px;">
                        @if($ticket->resolution_due_at)
                            @if($resRemaining !== null && $resRemaining <= 0)
                            <span style="color: var(--color-status-danger); font-weight: 600;">Overdue</span>
                            @elseif($ticket->sla_resolution_breached)
                            <span style="color: var(--color-status-danger);">Breached</span>
                            @else
                            <span style="color: var(--color-{{ $color }});">
                                {{ $resRemaining }}m left
                            </span>
                            @endif
                        @else
                        <span style="color: var(--color-text-disabled);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->sla_resolution_breached || ($resRemaining !== null && $resRemaining <= 0))
                        <span class="badge badge-critical">Breached</span>
                        @elseif($resRemaining !== null && $resRemaining <= 30)
                        <span class="badge badge-high">At Risk</span>
                        @else
                        <span class="badge badge-resolved">On Track</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($allTickets->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">
            {{ $allTickets->links() }}
        </div>
        @endif
        @endif
    </div>
</x-app-layout>
