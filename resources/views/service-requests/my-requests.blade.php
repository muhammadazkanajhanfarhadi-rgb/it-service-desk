<x-app-layout>
    <x-slot name="title">My Requests — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">My Requests</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <x-page-header title="My Service Requests" subtitle="Permintaan layanan yang Anda ajukan." />
        <a href="{{ route('service-catalog.index') }}" class="btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
            Ajukan Baru
        </a>
    </div>

    {{-- Status filter --}}
    <div class="flex gap-2 mb-5 flex-wrap">
        @foreach(['' => 'Semua', 'pending_approval' => 'Pending', 'approved' => 'Approved', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
        <a href="{{ route('service-requests.my', $val ? ['status' => $val] : []) }}"
           class="px-3 py-1.5 rounded-xl text-sm transition-colors"
           style="{{ request('status') === $val || (!request('status') && $val === '')
               ? 'background-color: var(--color-interactive-active-bg); color: var(--color-accent-electric); font-weight: 600;'
               : 'color: var(--color-text-secondary);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @if($requests->isEmpty())
    <div class="card flex flex-col items-center justify-center py-16 gap-3">
        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">assignment</span>
        <p style="font-size: 14px; color: var(--color-text-secondary);">Belum ada permintaan</p>
        <a href="{{ route('service-catalog.index') }}" class="btn-primary btn-sm">Ajukan Permintaan</a>
    </div>
    @else
    <div class="card" style="padding: 0;">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>No. Permintaan</th><th>Layanan</th><th>Status</th><th>Disetujui Oleh</th><th>Diajukan</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($requests as $req)
                <tr>
                    <td style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">{{ $req->request_number }}</td>
                    <td style="font-size: 13px;">{{ $req->service?->name ?? '—' }}</td>
                    <td><x-service-request-status-badge :status="$req->status" /></td>
                    <td style="font-size: 13px; color: var(--color-text-secondary);">{{ $req->approver?->name ?? '—' }}</td>
                    <td style="font-size: 12px; color: var(--color-text-tertiary);">{{ $req->created_at->diffForHumans() }}</td>
                    <td>
                        @if($req->status === 'pending_approval')
                        <form method="POST" action="{{ route('service-requests.cancel', $req) }}" onsubmit="return confirm('Batalkan permintaan ini?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-danger btn-sm">
                                <span class="material-symbols-outlined" style="font-size: 14px;">cancel</span>
                                Batalkan
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $requests->links() }}</div>
        @endif
    </div>
    @endif
</x-app-layout>
