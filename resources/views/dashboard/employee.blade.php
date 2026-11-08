<x-app-layout>
    <x-slot name="title">Dashboard — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <span class="material-symbols-outlined" style="font-size: 15px; color: var(--color-text-tertiary);">home</span>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Dashboard</span>
    </x-slot>

    <x-page-header
        title="Dashboard"
        subtitle="Selamat datang, {{ $user->name }}. Pantau tiket dan permintaan Anda di sini." />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Tiket Open"     value="0" icon="confirmation_number" color="status-info" />
        <x-stat-card label="In Progress"    value="0" icon="autorenew"           color="accent-electric" />
        <x-stat-card label="Resolved"       value="0" icon="check_circle"        color="status-success" />
        <x-stat-card label="Closed"         value="0" icon="archive"             color="text-secondary" />
    </div>

    {{-- Recent Tickets placeholder --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 style="font-size: var(--font-size-headline-sm); font-weight: 600; color: var(--color-text-primary);">
                Tiket Terbaru
            </h2>
            <a href="#" class="btn-secondary btn-sm">Lihat Semua</a>
        </div>

        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-12 gap-3">
            <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">
                confirmation_number
            </span>
            <p style="font-size: var(--font-size-body-md); color: var(--color-text-secondary);">
                Belum ada tiket
            </p>
            <a href="#" class="btn-primary btn-sm">
                <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
                Buat Tiket Baru
            </a>
        </div>
    </div>
</x-app-layout>
