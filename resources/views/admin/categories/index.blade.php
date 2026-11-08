<x-app-layout>
    <x-slot name="title">Categories — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Categories</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="Categories & Taxonomies" subtitle="Kelola kategori dan subkategori tiket." />
    </div>

    {{-- Add new category --}}
    <div class="card mb-5">
        <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            @csrf
            <input type="text" name="name" class="form-input" placeholder="Nama kategori" required>
            <select name="parent_id" class="form-select">
                <option value="">Kategori Utama</option>
                @foreach($parents as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <input type="text" name="icon" class="form-input" placeholder="Icon (opsional)">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
                Tambah
            </button>
        </form>
    </div>

    {{-- Category list --}}
    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 220px;" placeholder="Cari kategori...">
        <button type="submit" class="btn-secondary btn-sm ml-2">Cari</button>
    </form>

    <div class="card" style="padding: 0;">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr><th>Nama</th><th>Parent</th><th>Icon</th><th>Subkategori</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                @foreach($categories as $cat)
                <tr>
                    <form method="POST" action="{{ route('admin.categories.update', $cat) }}">
                        @csrf @method('PATCH')
                    <td>
                        <div class="flex items-center gap-2">
                            @if($cat->color)
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }};"></div>
                            @endif
                            <input type="text" name="name" value="{{ $cat->name }}" class="form-input" style="height: 32px; font-size: 12px; width: 120px;">
                        </div>
                    </td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">{{ $cat->parent?->name ?? '—' }}</td>
                    <td>
                        @if($cat->icon)
                        <span class="material-symbols-outlined" style="font-size: 16px; color: var(--color-accent-electric);">{{ $cat->icon }}</span>
                        @else — @endif
                    </td>
                    <td><span class="badge badge-assigned">{{ $cat->children_count }}</span></td>
                    <td>
                        <label class="flex items-center gap-1">
                            <input type="checkbox" name="is_active" value="1" @checked($cat->is_active) class="w-3.5 h-3.5" style="accent-color: var(--color-primary-container);" onchange="this.form.submit()">
                            <span style="font-size: 11px; color: var(--color-text-secondary);">Aktif</span>
                        </label>
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-status-success);" title="Simpan"
                                    onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                <span class="material-symbols-outlined" style="font-size: 16px;">save</span>
                            </button>
                            @if($cat->children_count === 0)
                            <button type="submit" formaction="{{ route('admin.categories.destroy', $cat) }}" formmethod="POST" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-status-danger);" title="Hapus"
                                    onclick="return confirm('Hapus kategori {{ $cat->name }}?')"
                                    onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                @csrf @method('DELETE')
                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                            </button>
                            @endif
                        </div>
                    </td>
                    </form>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $categories->links() }}</div>
        @endif
    </div>
</x-app-layout>
