<x-app-layout>
    <x-slot name="title">Tambah User — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('admin.users.index') }}" style="color: var(--color-text-tertiary);">Users</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Tambah</span>
    </x-slot>

    <x-page-header title="Tambah User Baru" subtitle="Buat akun pengguna baru dengan role dan departemen." />

    <form method="POST" action="{{ route('admin.users.store') }}" class="max-w-2xl">
        @csrf
        <div class="card flex flex-col gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Email <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Password <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Role <span style="color: var(--color-status-danger);">*</span></label>
                    <select name="role_id" class="form-select" required>
                        <option value="">— Pilih —</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Departemen</label>
                    <select name="department_id" class="form-select">
                        <option value="">— Pilih —</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">— Pilih —</option>
                        @foreach($locations as $l)
                        <option value="{{ $l->id }}" @selected(old('location_id') == $l->id)>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}" class="form-input">
                </div>
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4" style="accent-color: var(--color-primary-container);">
                <span style="font-size: 13px; color: var(--color-text-secondary);">User aktif</span>
            </label>
        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn-primary">Simpan User</button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
