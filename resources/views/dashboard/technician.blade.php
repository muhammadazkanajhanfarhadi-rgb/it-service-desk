<x-app-layout>
    <x-slot name="title">Technician Dashboard — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <span class="material-symbols-outlined" style="font-size: 15px; color: var(--color-text-tertiary);">home</span>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Dashboard</span>
    </x-slot>

    <x-page-header
        title="Technician Dashboard"
        subtitle="Selamat datang, {{ $user->name }}. Kelola antrian tiket yang di-assign kepada Anda." />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Assigned"       value="0" icon="assignment_ind"  color="status-info" />
        <x-stat-card label="In Progress"    value="0" icon="autorenew"        color="accent-electric" />
        <x-stat-card label="Overdue"        value="0" icon="timer_off"        color="status-danger" />
        <x-stat-card label="Resolved Today" value="0" icon="task_alt"         color="status-success" />
    </div>

    {{-- SLA Alerts + Assigned Queue --}}
    <div class="grid lg:grid-cols-2 gap-4">

        {{-- SLA Urgent Alerts --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: var(--font-size-headline-sm); font-weight: 600; color: var(--color-text-primary);">
                <span class="material-symbols-outlined" style="font-size: 17px; color: var(--color-status-danger); vertical-align: middle;">warning</span>
                SLA Urgent Alerts
            </h2>
            <div class="flex flex-col items-center justify-center py-8 gap-2">
                <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-status-success);">verified</span>
                <p style="font-size: var(--font-size-body-sm); color: var(--color-text-secondary);">Tidak ada tiket yang mendekati SLA breach</p>
            </div>
        </div>

        {{-- Assigned Tickets --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: var(--font-size-headline-sm); font-weight: 600; color: var(--color-text-primary);">
                Tiket Assigned
            </h2>
            <div class="flex flex-col items-center justify-center py-8 gap-2">
                <span class="material-symbols-outlined" style="font-size: 40px; color: var(--color-text-disabled);">inbox</span>
                <p style="font-size: var(--font-size-body-sm); color: var(--color-text-secondary);">Antrian kosong</p>
            </div>
        </div>

    </div>
</x-app-layout>
