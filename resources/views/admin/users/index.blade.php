<x-app-layout>
    <x-slot name="title">Users & Roles — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Users & Roles</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="Users & Access Control" subtitle="Kelola akun pengguna dan hak akses." />
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">person_add</span>
            Tambah User
        </a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 220px;" placeholder="Cari nama atau email...">
        <select name="role" class="form-select" style="width: 160px;">
            <option value="">Semua Role</option>
            @foreach($roles as $r)
            <option value="{{ $r->slug }}" @selected(request('role') === $r->slug)>{{ $r->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    <div class="card" style="padding: 0;">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th><th>Email</th><th>Role</th><th>Departemen</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <img src="{{ $user->avatarUrl() }}" class="w-7 h-7 rounded-full">
                            <span style="font-weight: 500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="font-size: 13px; color: var(--color-text-secondary);">{{ $user->email }}</td>
                    <td><span class="badge badge-assigned">{{ $user->role?->name ?? '—' }}</span></td>
                    <td style="font-size: 13px; color: var(--color-text-secondary);">{{ $user->department?->name ?? '—' }}</td>
                    <td>
                        @if($user->is_active)
                        <span class="badge badge-resolved">Active</span>
                        @else
                        <span class="badge badge-closed">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-text-secondary);" title="Edit"
                               onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                               onmouseout="this.style.backgroundColor='transparent'">
                                <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-text-secondary);" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">{{ $user->is_active ? 'person_off' : 'person_check' }}</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $users->links() }}</div>
        @endif
    </div>
</x-app-layout>
