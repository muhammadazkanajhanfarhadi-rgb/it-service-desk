<x-app-layout>
    <x-slot name="title">Account Settings — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Profile</span>
    </x-slot>

    <x-page-header title="Account Settings & Profile" subtitle="Kelola informasi akun dan keamanan." />

    <div class="grid lg:grid-cols-2 gap-4">
        {{-- Profile Information --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle; color: var(--color-accent-electric);">person</span>
                Profile Information
            </h2>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="form-label" for="name">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Telepon</label>
                            <input type="text" value="{{ $user->phone ?? '—' }}" class="form-input" disabled style="opacity: 0.6;">
                        </div>
                        <div>
                            <label class="form-label">Jabatan</label>
                            <input type="text" value="{{ $user->job_title ?? '—' }}" class="form-input" disabled style="opacity: 0.6;">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Role</label>
                            <input type="text" value="{{ $user->role?->name ?? '—' }}" class="form-input" disabled style="opacity: 0.6;">
                        </div>
                        <div>
                            <label class="form-label">Departemen</label>
                            <input type="text" value="{{ $user->department?->name ?? '—' }}" class="form-input" disabled style="opacity: 0.6;">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card">
            <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-status-warning);">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">key</span>
                Change Password
            </h2>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('PUT')
                <div class="flex flex-col gap-4">
                    <div>
                        <label class="form-label" for="current_password">Password Saat Ini</label>
                        <input id="current_password" type="password" name="current_password" class="form-input" required>
                        @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="password">Password Baru</label>
                        <input id="password" type="password" name="password" class="form-input" required>
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Account --}}
    <div class="card mt-4" style="border-color: rgba(239,68,68,0.2);">
        <h2 class="mb-3" style="font-size: 15px; font-weight: 600; color: var(--color-status-danger);">
            <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">dangerous</span>
            Danger Zone
        </h2>
        <p class="mb-4" style="font-size: 13px; color: var(--color-text-secondary);">
            Menghapus akun bersifat permanen. Data tiket yang Anda buat akan tetap tersimpan.
        </p>
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-danger">
                <span class="material-symbols-outlined" style="font-size: 16px;">delete_forever</span>
                Hapus Akun Saya
            </button>
        </form>
    </div>
</x-app-layout>
