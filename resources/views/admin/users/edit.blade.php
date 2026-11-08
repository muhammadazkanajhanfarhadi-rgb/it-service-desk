<x-app-layout>
    <x-slot name="title">Edit User — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('admin.users.index') }}" style="color: var(--color-text-tertiary);">Users</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Edit {{ $user->name }}</span>
    </x-slot>

    <x-page-header title="Edit User" subtitle="Perbarui informasi pengguna: {{ $user->name }}." />

    <div class="grid lg:grid-cols-2 gap-4 max-w-4xl">
        {{-- Edit Form --}}
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PATCH')
            <div class="card flex flex-col gap-4">
                <h2 style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">Informasi User</h2>
                <div>
                    <label class="form-label">Nama <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email <span style="color: var(--color-status-danger);">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select">
                            @foreach($roles as $r)
                            <option value="{{ $r->id }}" @selected(old('role_id', $user->role_id) == $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Departemen</label>
                        <select name="department_id" class="form-select">
                            <option value="">—</option>
                            @foreach($departments as $d)
                            <option value="{{ $d->id }}" @selected(old('department_id', $user->department_id) == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Lokasi</label>
                        <select name="location_id" class="form-select">
                            <option value="">—</option>
                            @foreach($locations as $l)
                            <option value="{{ $l->id }}" @selected(old('location_id', $user->location_id) == $l->id)>{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}" class="form-input">
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Simpan Perubahan</button>
            </div>
        </form>

        {{-- Reset Password --}}
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
            @csrf @method('PATCH')
            <div class="card flex flex-col gap-4">
                <h2 style="font-size: 15px; font-weight: 600; color: var(--color-status-warning);">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">key</span>
                    Reset Password
                </h2>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
                <button type="submit" class="btn-danger w-full justify-center">
                    <span class="material-symbols-outlined" style="font-size: 16px;">lock_reset</span>
                    Reset Password
                </button>
            </div>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">← Kembali ke Daftar</a>
    </div>
</x-app-layout>
