<x-app-layout>
    <x-slot name="title">Audit Log — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Audit Log</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="System Audit Log" subtitle="Rekam jejak aktivitas tiket dan sistem." />
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 220px;" placeholder="Cari aktivitas...">
        <select name="action" class="form-select" style="width: 180px;">
            <option value="">Semua Aksi</option>
            @foreach($actions as $a)
            <option value="{{ $a }}" @selected(request('action') === $a)>{{ ucfirst(str_replace('_', ' ', $a)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    <div class="card" style="padding: 0;">
        @if($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 gap-2">
            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-text-disabled);">receipt_long</span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada log aktivitas</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Waktu</th><th>Actor</th><th>Aksi</th><th>Deskripsi</th><th>Tiket</th></tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                <tr>
                    <td style="font-size: 12px; color: var(--color-text-tertiary); white-space: nowrap;">
                        {{ $log->created_at->format('d M H:i') }}
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            @if($log->user)
                            <img src="{{ $log->user->avatarUrl() }}" class="w-6 h-6 rounded-full">
                            <span style="font-size: 13px;">{{ $log->user->name }}</span>
                            @else
                            <span style="font-size: 13px; color: var(--color-text-disabled);">System</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-assigned" style="font-size: 10px;">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td style="font-size: 13px; color: var(--color-text-primary);">{!! $log->description !!}</td>
                    <td>
                        @if($log->ticket)
                        <a href="{{ route('tickets.show', $log->ticket) }}" style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">
                            {{ $log->ticket->ticket_number }}
                        </a>
                        @else — @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $logs->links() }}</div>
        @endif
        @endif
    </div>
</x-app-layout>
