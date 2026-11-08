<x-app-layout>
    <x-slot name="title">IT Manager Dashboard — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <span class="material-symbols-outlined" style="font-size: 15px; color: var(--color-text-tertiary);">home</span>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">IT Manager Dashboard</span>
    </x-slot>

    <x-page-header
        title="IT Manager Dashboard"
        subtitle="Ringkasan performa layanan TI, SLA compliance, dan workload tim." />

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Tickets"  value="0"    icon="confirmation_number" color="accent-electric" />
        <x-stat-card label="SLA Compliance" value="–%"   icon="policy"              color="status-success" />
        <x-stat-card label="Avg Resolution" value="–"    icon="timer"               color="status-info" />
        <x-stat-card label="CSAT Score"     value="–"    icon="sentiment_satisfied" color="status-warning" />
    </div>

    {{-- Charts & Tables --}}
    <div class="grid lg:grid-cols-2 gap-4 mb-4">
        <div class="card">
            <h2 class="mb-4" style="font-size: var(--font-size-headline-sm); font-weight: 600; color: var(--color-text-primary);">
                Ticket Volume & Resolution
            </h2>
            <div class="flex items-center justify-center py-12">
                <p style="font-size: var(--font-size-body-sm); color: var(--color-text-tertiary);">Chart tersedia setelah data masuk</p>
            </div>
        </div>
        <div class="card">
            <h2 class="mb-4" style="font-size: var(--font-size-headline-sm); font-weight: 600; color: var(--color-text-primary);">
                Tickets by Category
            </h2>
            <div class="flex items-center justify-center py-12">
                <p style="font-size: var(--font-size-body-sm); color: var(--color-text-tertiary);">Chart tersedia setelah data masuk</p>
            </div>
        </div>
    </div>

    {{-- SLA Breached Tickets --}}
    <div class="card">
        <h2 class="mb-4" style="font-size: var(--font-size-headline-sm); font-weight: 600; color: var(--color-text-primary);">
            <span class="material-symbols-outlined" style="font-size: 17px; color: var(--color-status-danger); vertical-align: middle;">warning</span>
            SLA Breached Tickets
        </h2>
        <div class="flex flex-col items-center justify-center py-8 gap-2">
            <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-status-success);">verified</span>
            <p style="font-size: var(--font-size-body-sm); color: var(--color-text-secondary);">Tidak ada tiket yang breach SLA</p>
        </div>
    </div>
</x-app-layout>
