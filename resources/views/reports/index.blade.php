<x-app-layout>
    <x-slot name="title">Reports — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Reports</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="Reports"
            subtitle="Analisis performa layanan TI berdasarkan data aktual." />
    </div>

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" class="card mb-6" style="padding: 1rem 1.25rem;">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input" style="width: 150px;">
            </div>
            <div>
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input" style="width: 150px;">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select" style="width: 150px;">
                    <option value="">Semua</option>
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" @selected($statusFilter === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Prioritas</label>
                <select name="priority" class="form-select" style="width: 150px;">
                    <option value="">Semua</option>
                    @foreach($priorities as $p)
                    <option value="{{ $p->slug }}" @selected($priorityFilter === $p->slug)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary btn-sm">
                <span class="material-symbols-outlined" style="font-size: 16px;">filter_list</span>
                Filter
            </button>
            @if($dateFrom || $dateTo || $statusFilter || $priorityFilter)
            <a href="{{ route('reports.index') }}" class="btn-secondary btn-sm">
                <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- ── KPI Cards ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Tiket" value="{{ number_format($totalTickets) }}"
            icon="confirmation_number" color="accent-electric" />
        <x-stat-card label="Tiket Aktif" value="{{ number_format($openTickets) }}"
            icon="autorenew" color="status-warning" />
        <x-stat-card label="Resolved" value="{{ number_format($resolvedTickets) }}"
            icon="check_circle" color="status-success" />
        <x-stat-card label="Overdue" value="{{ number_format($overdueTickets) }}"
            icon="timer_off" color="status-danger" />
    </div>

    {{-- ── SLA & Performance Metrics ─────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(34,197,94,0.12);">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-status-success);">policy</span>
                </div>
                <div>
                    <div class="stat-value">{{ $slaCompliance }}%</div>
                    <div class="stat-label">SLA Compliance</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(56,189,248,0.12);">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-status-info);">flash_on</span>
                </div>
                <div>
                    <div class="stat-value">
                        @if($avgResponseSeconds > 0){{ gmdate('H\j i\m', $avgResponseSeconds) }}@else — @endif
                    </div>
                    <div class="stat-label">Avg Response Time</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(167,139,250,0.12);">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-status-pending);">timer</span>
                </div>
                <div>
                    <div class="stat-value">
                        @if($avgResolutionSeconds > 0){{ gmdate('H\j i\m', $avgResolutionSeconds) }}@else — @endif
                    </div>
                    <div class="stat-label">Avg Resolution Time</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SLA Breakdown ──────────────────────────────────────── --}}
    <div class="grid lg:grid-cols-2 gap-4 mb-6">
        {{-- By Status --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                Tiket berdasarkan Status
            </h2>
            @if(empty($byStatus))
            <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada data</p>
            @else
            <div class="flex flex-col gap-3">
                @php $maxStatus = max($byStatus); @endphp
                @foreach($byStatus as $status => $count)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <x-ticket-status-badge :status="$status" />
                        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ $count }}</span>
                    </div>
                    <div class="sla-bar">
                        <div class="sla-bar-fill sla-bar-fill-ok"
                             style="width: {{ $maxStatus > 0 ? ($count / $maxStatus) * 100 : 0 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- By Priority --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                Tiket berdasarkan Prioritas
            </h2>
            @if($byPriority->isEmpty())
            <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada data</p>
            @else
            <div class="flex flex-col gap-3">
                @php $maxPriority = $byPriority->max('total'); @endphp
                @foreach($byPriority as $item)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <x-priority-badge :priority="\App\Models\Priority::where('slug', $item->slug)->first()" />
                        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ $item->total }}</span>
                    </div>
                    <div class="sla-bar">
                        <div class="sla-bar-fill sla-bar-fill-{{ $item->slug === 'critical' ? 'breach' : ($item->slug === 'high' ? 'warning' : 'ok') }}"
                             style="width: {{ $maxPriority > 0 ? ($item->total / $maxPriority) * 100 : 0 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── By Category + Trend ────────────────────────────────── --}}
    <div class="grid lg:grid-cols-2 gap-4 mb-6">
        {{-- By Category --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                Tiket berdasarkan Kategori
            </h2>
            @if($byCategory->isEmpty())
            <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada data</p>
            @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Kategori</th><th class="text-right">Total</th></tr></thead>
                    <tbody>
                    @foreach($byCategory as $cat)
                    <tr>
                        <td>{{ $cat->name ?? 'Tidak Terkategorikan' }}</td>
                        <td style="text-align: right; font-weight: 600;">{{ $cat->total }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Trend --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                Tren Tiket {{ $dateFrom || $dateTo ? '(Filtered)' : '(30 Hari Terakhir)' }}
            </h2>
            @if(empty($trend))
            <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada data pada periode ini</p>
            @else
            @php $maxTrend = max($trend); @endphp
            <div class="flex items-end gap-1" style="height: 160px;">
                @foreach($trend as $date => $count)
                <div class="flex-1 flex flex-col items-center justify-end" style="min-width: 8px;">
                    <span style="font-size: 9px; color: var(--color-text-tertiary); margin-bottom: 2px;">{{ $count }}</span>
                    <div style="width: 100%; background-color: var(--color-accent-electric); border-radius: 3px 3px 0 0; opacity: 0.8;
                          height: {{ $maxTrend > 0 ? max(4, ($count / $maxTrend) * 140) : 4 }}px;"></div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2" style="font-size: 10px; color: var(--color-text-tertiary);">
                <span>{{ array_key_first($trend) ? \Carbon\Carbon::parse(array_key_first($trend))->format('d M') : '' }}</span>
                <span>{{ array_key_last($trend) ? \Carbon\Carbon::parse(array_key_last($trend))->format('d M') : '' }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── SLA Breach Summary ─────────────────────────────────── --}}
    <div class="card mb-6">
        <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
            SLA Breach Summary
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl p-4" style="background-color: var(--color-surface-container-low); border: 1px solid var(--color-border-subtle);">
                <div style="font-size: 24px; font-weight: 700; color: var(--color-status-success);">{{ $slaResolvedTotal }}</div>
                <div style="font-size: 13px; color: var(--color-text-secondary);">Resolved Tickets</div>
            </div>
            <div class="rounded-xl p-4" style="background-color: var(--color-surface-container-low); border: 1px solid var(--color-border-subtle);">
                <div style="font-size: 24px; font-weight: 700; color: var(--color-status-danger);">{{ $slaBreachedResolved }}</div>
                <div style="font-size: 13px; color: var(--color-text-secondary);">Breached (Resolved)</div>
            </div>
            <div class="rounded-xl p-4" style="background-color: var(--color-surface-container-low); border: 1px solid var(--color-border-subtle);">
                <div style="font-size: 24px; font-weight: 700; color: var(--color-status-warning);">{{ $activeBreaches }}</div>
                <div style="font-size: 13px; color: var(--color-text-secondary);">Active Breaches</div>
            </div>
        </div>
    </div>

    {{-- ── Technician Performance ──────────────────────────────── --}}
    <div class="card">
        <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
            Technician Performance Matrix
        </h2>
        @if($techPerformance->isEmpty())
        <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada data teknisi</p>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Teknisi</th>
                        <th class="text-right">Assigned</th>
                        <th class="text-right">Resolved</th>
                        <th class="text-right">Resolution Rate</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($techPerformance as $tech)
                @php $rate = $tech->assigned_count > 0 ? round(($tech->resolved_count / $tech->assigned_count) * 100, 1) : 0; @endphp
                <tr>
                    <td style="font-weight: 500;">{{ $tech->name }}</td>
                    <td style="text-align: right;">{{ $tech->assigned_count }}</td>
                    <td style="text-align: right; color: var(--color-status-success);">{{ $tech->resolved_count }}</td>
                    <td style="text-align: right;">
                        <span class="badge {{ $rate >= 80 ? 'badge-resolved' : ($rate >= 50 ? 'badge-high' : 'badge-critical') }}">
                            {{ $rate }}%
                        </span>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>
