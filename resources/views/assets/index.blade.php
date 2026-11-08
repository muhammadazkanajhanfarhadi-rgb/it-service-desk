<x-app-layout>
    <x-slot name="title">IT Assets — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Assets</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="IT Assets" subtitle="Inventaris aset dan perangkat TI." />
        @if(auth()->user()->isAdmin())
        <a href="{{ route('assets.create') }}" class="btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
            Tambah Aset
        </a>
        @endif
    </div>

    {{-- Dashboard Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        <x-stat-card label="Total" value="{{ $stats['total'] }}" icon="inventory_2" color="accent-electric" />
        <x-stat-card label="Available" value="{{ $stats['available'] }}" icon="check_circle" color="status-success" />
        <x-stat-card label="Assigned" value="{{ $stats['assigned'] }}" icon="assignment_ind" color="status-info" />
        <x-stat-card label="Maintenance" value="{{ $stats['maintenance'] }}" icon="build" color="status-warning" />
        <x-stat-card label="Retired" value="{{ $stats['retired'] }}" icon="block" color="text-tertiary" />
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 200px;" placeholder="Cari nomor, nama, serial...">
        <select name="status" class="form-select" style="width: 150px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach(['available','assigned','maintenance','retired'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="category" class="form-select" style="width: 180px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    {{-- Asset Table --}}
    <div class="card" style="padding: 0;">
        @if($assets->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">inventory_2</span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada aset ditemukan</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>No. Aset</th><th>Nama</th><th>Merek/Model</th><th>Serial</th><th>Status</th><th>Pemegang</th><th>Garansi</th></tr></thead>
                <tbody>
                @foreach($assets as $asset)
                <tr>
                    <td>
                        <a href="{{ route('assets.show', $asset) }}" style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">{{ $asset->asset_number }}</a>
                    </td>
                    <td style="font-size: 13px;">{{ $asset->name }}</td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">{{ trim(($asset->brand ?? '') . ' ' . ($asset->model ?? '')) ?: '—' }}</td>
                    <td style="font-size: 12px; color: var(--color-text-tertiary);">{{ $asset->serial_number }}</td>
                    <td><x-asset-status-badge :status="$asset->status" /></td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">
                        @if($asset->currentAssignment)
                        {{ $asset->currentAssignment->user?->name ?? '—' }}
                        @else — @endif
                    </td>
                    <td style="font-size: 12px;">
                        @if($asset->warranty_end)
                            @if($asset->warranty_end->isPast())
                            <span style="color: var(--color-status-danger);">Expired</span>
                            @else
                            <span style="color: var(--color-text-tertiary);">{{ $asset->warranty_end->format('d M Y') }}</span>
                            @endif
                        @else — @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($assets->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $assets->links() }}</div>
        @endif
        @endif
    </div>
</x-app-layout>
