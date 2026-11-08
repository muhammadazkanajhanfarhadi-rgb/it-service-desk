<x-app-layout>
    <x-slot name="title">Admin Dashboard — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <span class="material-symbols-outlined" style="font-size: 15px; color: var(--color-text-tertiary);">home</span>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Dashboard</span>
    </x-slot>

    <x-page-header
        title="Admin Dashboard"
        subtitle="Ringkasan sistem dan akses cepat ke konfigurasi." />

    {{-- System Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Users"    value="{{ \App\Models\User::count() }}"     icon="group"         color="accent-electric" />
        <x-stat-card label="Active Tickets" value="0"                                   icon="confirmation_number" color="status-warning" />
        <x-stat-card label="Categories"     value="{{ \App\Models\Category::whereNull('parent_id')->count() }}" icon="category" color="status-info" />
        <x-stat-card label="SLA Policies"   value="{{ \App\Models\SlaPolicy::count() }}" icon="policy"       color="status-success" />
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            ['icon' => 'group',        'title' => 'Users & Roles',    'desc' => 'Kelola akun dan hak akses',  'color' => 'accent-electric'],
            ['icon' => 'category',     'title' => 'Categories',       'desc' => 'Taksonomi tiket & request',  'color' => 'status-info'],
            ['icon' => 'timer',        'title' => 'SLA Policies',     'desc' => 'Konfigurasi target SLA',     'color' => 'status-warning'],
            ['icon' => 'storefront',   'title' => 'Service Catalog',  'desc' => 'Kelola layanan tersedia',    'color' => 'status-pending'],
            ['icon' => 'receipt_long', 'title' => 'Audit Log',        'desc' => 'Rekam jejak aktivitas',      'color' => 'status-danger'],
            ['icon' => 'settings',     'title' => 'System Settings',  'desc' => 'Konfigurasi aplikasi',       'color' => 'text-secondary'],
        ] as $item)
        <a href="#" class="card flex items-start gap-4 no-underline"
           style="text-decoration: none;">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background-color: rgba(79,140,255,0.1);">
                <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-{{ $item['color'] }});">
                    {{ $item['icon'] }}
                </span>
            </div>
            <div>
                <div style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ $item['title'] }}</div>
                <div style="font-size: 12px; color: var(--color-text-secondary); margin-top: 2px;">{{ $item['desc'] }}</div>
            </div>
        </a>
        @endforeach
    </div>
</x-app-layout>
