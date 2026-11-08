<x-app-layout>
    <x-slot name="title">My Tickets — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">My Tickets</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <x-page-header title="My Tickets" subtitle="Tiket yang Anda buat dan pantau." />
        <a href="{{ route('tickets.create') }}" class="btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
            Buat Tiket Baru
        </a>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex gap-2 mb-5 flex-wrap">
        @foreach(['' => 'Semua', 'new' => 'New', 'in_progress' => 'In Progress', 'pending' => 'Pending', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $val => $label)
        <a href="{{ route('tickets.my', $val ? ['status' => $val] : []) }}"
           class="px-3 py-1.5 rounded-xl text-sm transition-colors"
           style="{{ request('status') === $val || (!request('status') && $val === '')
               ? 'background-color: var(--color-interactive-active-bg); color: var(--color-accent-electric); font-weight: 600;'
               : 'color: var(--color-text-secondary);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Ticket Cards --}}
    @if($tickets->isEmpty())
    <div class="card flex flex-col items-center justify-center py-16 gap-3">
        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">confirmation_number</span>
        <p style="font-size: 14px; color: var(--color-text-secondary);">Belum ada tiket</p>
        <a href="{{ route('tickets.create') }}" class="btn-primary btn-sm">Buat Tiket Pertama Anda</a>
    </div>
    @else
    <div class="flex flex-col gap-3">
        @foreach($tickets as $ticket)
        <a href="{{ route('tickets.show', $ticket) }}"
           class="card flex items-start gap-4 no-underline"
           style="text-decoration: none; padding: 1rem 1.25rem;">

            {{-- Priority color strip --}}
            <div class="w-1 self-stretch rounded-full flex-shrink-0"
                 style="background-color: var(--color-{{ $ticket->slaStatusColor() }});"></div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">
                        {{ $ticket->ticket_number }}
                    </span>
                    <x-ticket-status-badge :status="$ticket->status" />
                    <x-priority-badge :priority="$ticket->priority" />
                </div>
                <div class="font-medium mb-1" style="font-size: 14px; color: var(--color-text-primary);">
                    {{ $ticket->title }}
                </div>
                <div style="font-size: 12px; color: var(--color-text-tertiary);">
                    {{ $ticket->created_at->diffForHumans() }}
                    @if($ticket->category)
                    · {{ $ticket->category->name }}
                    @endif
                </div>
            </div>

            <span class="material-symbols-outlined flex-shrink-0"
                  style="font-size: 18px; color: var(--color-text-tertiary);">chevron_right</span>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="mt-4">{{ $tickets->links() }}</div>
    @endif
    @endif
</x-app-layout>
