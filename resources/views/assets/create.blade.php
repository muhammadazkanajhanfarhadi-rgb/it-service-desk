<x-app-layout>
    <x-slot name="title">Tambah Aset — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('assets.index') }}" style="color: var(--color-text-tertiary);">Assets</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Tambah</span>
    </x-slot>

    <x-page-header title="Tambah Aset Baru" subtitle="Buat inventaris aset perangkat TI." />

    <form method="POST" action="{{ route('assets.store') }}" class="max-w-2xl">
        @csrf
        <div class="card flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Aset <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Serial Number <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="form-input" required>
                    @error('serial_number')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Merek</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="{{ old('model') }}" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Garansi Sampai</label>
                    <input type="date" name="warranty_end" value="{{ old('warranty_end') }}" class="form-input">
                    @error('warranty_end')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="form-label">Spesifikasi</label>
                <textarea name="specifications" class="form-textarea" placeholder="CPU, RAM, Storage, OS, dll...">{{ old('specifications') }}</textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn-primary">Simpan Aset</button>
            <a href="{{ route('assets.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
