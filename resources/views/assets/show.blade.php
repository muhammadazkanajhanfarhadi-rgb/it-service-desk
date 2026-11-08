<x-app-layout>
    <x-slot name="title">{{ $asset->asset_number }} — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <a href="{{ route('assets.index') }}" style="color: var(--color-text-tertiary);">Assets</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">{{ $asset->asset_number }}</span>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-5">

        {{-- LEFT: Asset info --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Header --}}
            <div class="card">
                <div class="flex items-start justify-between mb-4 flex-wrap gap-2">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">{{ $asset->asset_number }}</span>
                            <x-asset-status-badge :status="$asset->status" />
                        </div>
                        <h1 style="font-size: 18px; font-weight: 600; color: var(--color-text-primary);">{{ $asset->name }}</h1>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div class="flex gap-2">
                        <a href="{{ route('assets.edit', $asset) }}" class="btn-secondary btn-sm">
                            <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger btn-sm">
                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                {{-- Specs --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['Merek', $asset->brand ?? '—'],
                        ['Model', $asset->model ?? '—'],
                        ['Serial Number', $asset->serial_number ?? '—'],
                        ['Kategori', $asset->category?->name ?? '—'],
                        ['Tanggal Beli', $asset->purchase_date?->format('d M Y') ?? '—'],
                        ['Garansi Sampai', $asset->warranty_end ? ($asset->warranty_end->format('d M Y') . ($asset->warranty_end->isPast() ? ' (Expired)' : '')) : '—'],
                    ] as $prop)
                    <div class="flex justify-between py-2" style="border-bottom: 1px solid var(--color-border-subtle);">
                        <span style="font-size: 12px; color: var(--color-text-secondary);">{{ $prop[0] }}</span>
                        <span style="font-size: 12px; color: var(--color-text-primary); font-weight: 500;">{{ $prop[1] }}</span>
                    </div>
                    @endforeach
                </div>

                @if($asset->specifications)
                <div class="mt-4">
                    <p style="font-size: 12px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Spesifikasi</p>
                    <p class="mt-1" style="font-size: 13px; color: var(--color-on-surface); white-space: pre-wrap;">{{ $asset->specifications }}</p>
                </div>
                @endif
            </div>

            {{-- Assignment History --}}
            <div class="card">
                <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">history</span>
                    Riwayat Assignment
                </h2>

                @if($asset->assignments->isEmpty())
                <p style="font-size: 13px; color: var(--color-text-tertiary);">Belum ada riwayat assignment</p>
                @else
                <div class="flex flex-col gap-3">
                    @foreach($asset->assignments as $assignment)
                    <div class="flex items-start gap-3 pb-3" style="border-bottom: 1px solid var(--color-border-subtle);">
                        <img src="{{ $assignment->user?->avatarUrl() ?? '' }}" class="w-8 h-8 rounded-full flex-shrink-0">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">{{ $assignment->user?->name ?? '—' }}</span>
                                @if($assignment->isActive())
                                <span class="badge badge-assigned">Active</span>
                                @else
                                <span class="badge badge-closed">Returned</span>
                                @endif
                            </div>
                            <p style="font-size: 11px; color: var(--color-text-tertiary); margin-top: 2px;">
                                Diserahkan: {{ $assignment->assigned_at?->format('d M Y H:i') }}
                                @if($assignment->returned_at)
                                · Dikembalikan: {{ $assignment->returned_at?->format('d M Y H:i') }}
                                @endif
                                · Oleh: {{ $assignment->assigner?->name ?? '—' }}
                            </p>
                            @if($assignment->notes)
                            <p style="font-size: 12px; color: var(--color-text-secondary); margin-top: 4px;">{{ $assignment->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Maintenance History --}}
            <div class="card">
                <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">build</span>
                    Riwayat Maintenance
                </h2>

                @if($asset->maintenanceRecords->isEmpty())
                <p style="font-size: 13px; color: var(--color-text-tertiary);">Belum ada riwayat maintenance</p>
                @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Deskripsi</th><th>Teknisi</th><th>Biaya</th></tr></thead>
                        <tbody>
                        @foreach($asset->maintenanceRecords as $m)
                        <tr>
                            <td style="font-size: 12px;">{{ $m->maintenance_date?->format('d M Y') }}</td>
                            <td><span class="badge badge-high">{{ ucfirst($m->type) }}</span></td>
                            <td style="font-size: 12px; color: var(--color-text-secondary);">{{ $m->description ?? '—' }}</td>
                            <td style="font-size: 12px;">{{ $m->technician ?? '—' }}</td>
                            <td style="font-size: 12px; color: var(--color-text-secondary);">Rp {{ number_format($m->cost, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Admin actions --}}
        @if(auth()->user()->isAdmin())
        <div class="flex flex-col gap-4">

            {{-- Assign --}}
            @if(!$asset->hasActiveAssignment() && in_array($asset->status, ['available', 'maintenance']))
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Assign Aset ke User
                </h2>
                <form method="POST" action="{{ route('assets.assign', $asset) }}">
                    @csrf @method('PATCH')
                    <select name="user_id" class="form-select mb-2" required>
                        <option value="">— Pilih User —</option>
                        @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role?->name }})</option>
                        @endforeach
                    </select>
                    <textarea name="notes" class="form-textarea mb-2" style="min-height: 60px;" placeholder="Catatan (opsional)"></textarea>
                    <button type="submit" class="btn-primary w-full justify-center btn-sm">
                        <span class="material-symbols-outlined" style="font-size: 16px;">assignment_ind</span>
                        Assign
                    </button>
                </form>
            </div>
            @elseif($asset->hasActiveAssignment())
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Kembalikan Aset
                </h2>
                <p class="mb-3" style="font-size: 13px; color: var(--color-text-secondary);">
                    Sedang dipegang oleh <strong style="color: var(--color-text-primary);">{{ $asset->currentAssignment->user?->name }}</strong>
                </p>
                <form method="POST" action="{{ route('assets.return', $asset) }}">
                    @csrf @method('PATCH')
                    <textarea name="notes" class="form-textarea mb-2" style="min-height: 60px;" placeholder="Catatan pengembalian (opsional)"></textarea>
                    <button type="submit" class="btn-secondary w-full justify-center btn-sm">
                        <span class="material-symbols-outlined" style="font-size: 16px;">undo</span>
                        Kembalikan
                    </button>
                </form>
            </div>
            @endif

            {{-- Maintenance form --}}
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Catat Maintenance
                </h2>
                <form method="POST" action="{{ route('assets.maintenance', $asset) }}">
                    @csrf
                    <div class="mb-2">
                        <select name="type" class="form-select" required>
                            <option value="preventive">Preventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="upgrade">Upgrade</option>
                            <option value="inspection">Inspection</option>
                        </select>
                    </div>
                    <input type="text" name="technician" class="form-input mb-2" placeholder="Nama teknisi">
                    <input type="date" name="maintenance_date" class="form-input mb-2" value="{{ date('Y-m-d') }}" required>
                    <input type="number" name="cost" class="form-input mb-2" placeholder="Biaya (Rp)" step="0.01" min="0">
                    <textarea name="description" class="form-textarea mb-2" style="min-height: 60px;" placeholder="Deskripsi perawatan"></textarea>
                    <button type="submit" class="btn-primary w-full justify-center btn-sm">
                        <span class="material-symbols-outlined" style="font-size: 16px;">build</span>
                        Catat
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
