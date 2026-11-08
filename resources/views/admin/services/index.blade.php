<x-app-layout>
    <x-slot name="title">Service Management — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Services</span>
    </x-slot>

    <x-page-header title="Service Catalog Management" subtitle="Kelola layanan yang tersedia untuk pengguna." />

    {{-- Add new service --}}
    <div class="card mb-5">
        <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">Tambah Layanan Baru</h2>
        <form method="POST" action="{{ route('admin.services.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            @csrf
            <input type="text" name="name" class="form-input" placeholder="Nama layanan" required>
            <select name="category_id" class="form-select">
                <option value="">Tanpa Kategori</option>
                @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="sla_policy_id" class="form-select">
                <option value="">Tanpa SLA</option>
                @foreach($slaPolicies as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
            <input type="text" name="icon" class="form-input" placeholder="Icon (opsional)">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
                Tambah
            </button>
        </form>
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 220px;" placeholder="Cari layanan...">
        <button type="submit" class="btn-secondary btn-sm ml-2">Cari</button>
    </form>

    <div class="card" style="padding: 0;">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Nama</th><th>Kategori</th><th>SLA</th><th>Icon</th><th>Status</th><th>Permintaan</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($services as $service)
                <tr>
                    <form method="POST" action="{{ route('admin.services.update', $service) }}">
                        @csrf @method('PATCH')
                    <td><input type="text" name="name" value="{{ $service->name }}" class="form-input" style="height: 32px; font-size: 12px; width: 140px;"></td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">{{ $service->category?->name ?? '—' }}</td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">{{ $service->slaPolicy?->name ?? '—' }}</td>
                    <td>
                        @if($service->icon)
                        <span class="material-symbols-outlined" style="font-size: 16px; color: var(--color-accent-electric);">{{ $service->icon }}</span>
                        @else — @endif
                    </td>
                    <td>
                        <label class="flex items-center gap-1">
                            <input type="checkbox" name="is_active" value="1" @checked($service->is_active) class="w-3.5 h-3.5" style="accent-color: var(--color-primary-container);" onchange="this.form.submit()">
                            <span style="font-size: 11px; color: var(--color-text-secondary);">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </label>
                    </td>
                    <td><span class="badge badge-assigned">{{ $service->requests_count ?? 0 }}</span></td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-status-success);" title="Simpan"
                                    onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                <span class="material-symbols-outlined" style="font-size: 16px;">save</span>
                            </button>
                        </div>
                    </td>
                    </form>
                    <td>
                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan {{ $service->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-status-danger);" title="Hapus"
                                    onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $services->links() }}</div>
        @endif
    </div>
</x-app-layout>
