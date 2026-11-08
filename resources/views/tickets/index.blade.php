<x-app-layout>
    <x-slot name="title">All Tickets — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">All Tickets</span>
    </x-slot>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <x-page-header title="All Tickets" subtitle="Seluruh tiket dalam sistem." />
        <a href="{{ route('tickets.create') }}" class="btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
            Buat Tiket
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-input" style="width: 220px;"
               placeholder="Cari tiket atau ID...">

        <select name="status" class="form-select" style="width: 160px;">
            <option value="">Semua Status</option>
            @foreach(['new','assigned','in_progress','pending','resolved','closed','reopened'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>
                {{ ucfirst(str_replace('_', ' ', $s)) }}
            </option>
            @endforeach
        </select>

        <select name="priority" class="form-select" style="width: 160px;">
            <option value="">Semua Prioritas</option>
            @foreach(\App\Models\Priority::active()->get() as $p)
            <option value="{{ $p->slug }}" @selected(request('priority') === $p->slug)>
                {{ $p->name }}
            </option>
            @endforeach
        </select>

        <button type="submit" class="btn-secondary">
            <span class="material-symbols-outlined" style="font-size: 16px;">filter_list</span>
            Filter
        </button>

        @if(request()->hasAny(['search','status','priority']))
        <a href="{{ route('tickets.index') }}" class="btn-secondary">
            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
            Reset
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="card" style="padding: 0;">
        @if($tickets->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">inbox</span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada tiket ditemukan</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Assigned To</th>
                        <th>SLA</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}"
                           style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);"
                           onmouseover="this.style.color='var(--color-accent-light)'"
                           onmouseout="this.style.color='var(--color-accent-electric)'">
                            {{ $ticket->ticket_number }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}"
                           style="color: var(--color-text-primary); font-size: 13px;"
                           onmouseover="this.style.color='var(--color-accent-light)'"
                           onmouseout="this.style.color='var(--color-text-primary)'">
                            {{ Str::limit($ticket->title, 55) }}
                        </a>
                    </td>
                    <td><x-ticket-status-badge :status="$ticket->status" /></td>
                    <td><x-priority-badge :priority="$ticket->priority" /></td>
                    <td>
                        @if($ticket->assignee)
                        <div class="flex items-center gap-2">
                            <img src="{{ $ticket->assignee->avatarUrl() }}"
                                 alt="{{ $ticket->assignee->name }}"
                                 class="w-6 h-6 rounded-full">
                            <span style="font-size: 13px; color: var(--color-text-secondary);">
                                {{ $ticket->assignee->name }}
                            </span>
                        </div>
                        @else
                        <span style="font-size: 12px; color: var(--color-text-disabled);">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->resolution_due_at)
                        @php $color = $ticket->slaStatusColor(); @endphp
                        <span style="font-size: 12px; color: var(--color-{{ $color }});">
                            {{ $ticket->resolution_due_at->diffForHumans() }}
                        </span>
                        @else
                        <span style="font-size: 12px; color: var(--color-text-disabled);">—</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size: 12px; color: var(--color-text-tertiary);">
                            {{ $ticket->created_at->diffForHumans() }}
                        </span>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($tickets->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">
            {{ $tickets->links() }}
        </div>
        @endif
        @endif
    </div>
</x-app-layout>
