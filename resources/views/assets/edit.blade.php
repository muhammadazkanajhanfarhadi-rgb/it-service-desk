<x-app-layout>
    <x-slot name="title">Edit Aset — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('assets.index') }}" style="color: var(--color-text-tertiary);">Assets</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <a href="{{ route('assets.show', $asset) }}" style="color: var(--color-text-tertiary);">{{ $asset->asset_number }}</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Edit</span>
    </x-slot>

    <x-page-header title="Edit Aset" subtitle="{{ $asset->asset_number }} — {{ $asset->name }}" />

    <form method="POST" action="{{ route('assets.update', $asset) }}" class="max-w-2xl">
        @csrf @method('PATCH')
        <div class="card flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Aset <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $asset->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Serial Number <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="form-input" required>
                    @error('serial_number')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Merek</label>
                    <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="{{ old('model', $asset->model) }}" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $asset->category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Garansi Sampai</label>
                    <input type="date" name="warranty_end" value="{{ old('warranty_end', $asset->warranty_end?->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['available','assigned','maintenance','retired'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $asset->status) === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Spesifikasi</label>
                <textarea name="specifications" class="form-textarea">{{ old('specifications', $asset->specifications) }}</textarea>
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $asset->is_active)) class="w-4 h-4" style="accent-color: var(--color-primary-container);">
                <span style="font-size: 13px; color: var(--color-text-secondary);">Aset aktif</span>
            </label>
        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('assets.show', $asset) }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
