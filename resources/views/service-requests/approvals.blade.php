<x-app-layout>
    <x-slot name="title">Pending Approvals — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Pending Approvals</span>
    </x-slot>

    <x-page-header title="Pending Approvals" subtitle="Permintaan layanan yang menunggu persetujuan." />

    @if($requests->isEmpty())
    <div class="card flex flex-col items-center justify-center py-16 gap-3">
        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-status-success);">check_circle</span>
        <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada permintaan yang menunggu persetujuan</p>
    </div>
    @else
    <div class="flex flex-col gap-4">
        @foreach($requests as $req)
        <div class="card">
            <div class="flex items-start justify-between mb-3 flex-wrap gap-2">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">{{ $req->request_number }}</span>
                        <x-service-request-status-badge :status="$req->status" />
                    </div>
                    <div style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">{{ $req->service?->name }}</div>
                    <div style="font-size: 12px; color: var(--color-text-tertiary); margin-top: 2px;">
                        Diajukan oleh {{ $req->user?->name }} · {{ $req->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            @if($req->description)
            <div class="rounded-xl p-3 mb-4" style="background-color: var(--color-surface-container-low); border: 1px solid var(--color-border-subtle);">
                <p style="font-size: 13px; color: var(--color-on-surface); white-space: pre-wrap;">{{ $req->description }}</p>
            </div>
            @endif

            {{-- Approve form --}}
            <form method="POST" action="{{ route('service-requests.approve', $req) }}" class="mb-2">
                @csrf @method('PATCH')
                <div class="flex gap-2">
                    <input type="text" name="approval_notes" class="form-input" style="flex: 1;" placeholder="Catatan persetujuan (opsional)">
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                        Setujui
                    </button>
                </div>
            </form>

            {{-- Reject form --}}
            <form method="POST" action="{{ route('service-requests.reject', $req) }}">
                @csrf @method('PATCH')
                <div class="flex gap-2">
                    <input type="text" name="approval_notes" class="form-input" style="flex: 1;" placeholder="Alasan penolakan (wajib)" required>
                    <button type="submit" class="btn-danger">
                        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                        Tolak
                    </button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
    @if($requests->hasPages())
    <div class="mt-4">{{ $requests->links() }}</div>
    @endif
    @endif
</x-app-layout>
