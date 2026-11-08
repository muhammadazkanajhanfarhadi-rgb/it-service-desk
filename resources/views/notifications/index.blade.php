<x-app-layout>
    <x-slot name="title">Notifikasi — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Notifikasi</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6">
        <x-page-header title="Notification Center"
            subtitle="{{ auth()->user()->unreadNotifications()->count() }} notifikasi belum dibaca." />

        @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn-secondary">
                <span class="material-symbols-outlined" style="font-size: 16px;">done_all</span>
                Mark All as Read
            </button>
        </form>
        @endif
    </div>

    <div class="card" style="padding: 0;">
        @forelse($notifications as $notif)
        @php $data = $notif->data; @endphp
        <a href="{{ route('notifications.read', $notif->id) }}"
           style="text-decoration: none; display: flex; align-items: flex-start; gap: 1rem;
                  padding: 1rem 1.25rem;
                  border-bottom: 1px solid var(--color-border-subtle);
                  {{ $notif->unread() ? 'background-color: rgba(37,99,235,0.05);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
           onmouseout="this.style.backgroundColor='{{ $notif->unread() ? 'rgba(37,99,235,0.05)' : 'transparent' }}'">

            <!-- Icon -->
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background-color: rgba(79,140,255,0.10);">
                <span class="material-symbols-outlined"
                      style="font-size: 18px; color: var(--color-{{ $data['color'] ?? 'accent-electric' }});">
                    {{ $data['icon'] ?? 'notifications' }}
                </span>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p style="font-size: 14px; color: var(--color-text-primary); line-height: 1.5;">
                    {{ $data['message'] ?? '' }}
                </p>
                @if(!empty($data['ticket_number']))
                <span class="badge badge-assigned mt-1" style="display: inline-flex;">
                    {{ $data['ticket_number'] }}
                </span>
                @endif
                <p class="mt-1" style="font-size: 12px; color: var(--color-text-tertiary);">
                    {{ $notif->created_at->diffForHumans() }}
                    · {{ $notif->created_at->format('d M Y, H:i') }}
                </p>
            </div>

            <!-- Unread dot -->
            @if($notif->unread())
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-1.5"
                  style="background-color: var(--color-accent-electric);"></span>
            @else
            <span class="w-2.5 h-2.5 flex-shrink-0"></span>
            @endif
        </a>
        @empty
        <div class="flex flex-col items-center justify-center py-16 gap-3">
            <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">
                notifications_off
            </span>
            <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada notifikasi</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</x-app-layout>
