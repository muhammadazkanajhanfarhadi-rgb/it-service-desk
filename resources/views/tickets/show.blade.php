<x-app-layout>
    <x-slot name="title">{{ $ticket->ticket_number }} — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <a href="{{ route('tickets.my') }}" style="color: var(--color-text-tertiary);">Tickets</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">{{ $ticket->ticket_number }}</span>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-5">

        {{-- LEFT: Main content --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Ticket header --}}
            <div class="card">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span style="font-size: 12px; font-weight: 600; color: var(--color-accent-electric);">
                                {{ $ticket->ticket_number }}
                            </span>
                            <x-ticket-status-badge :status="$ticket->status" />
                            <x-priority-badge :priority="$ticket->priority" />
                        </div>
                        <h1 style="font-size: 18px; font-weight: 600; color: var(--color-text-primary);">
                            {{ $ticket->title }}
                        </h1>
                        <p class="mt-1" style="font-size: 12px; color: var(--color-text-tertiary);">
                            Dibuat oleh {{ $ticket->requester->name }} · {{ $ticket->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Description --}}
                <div class="rounded-xl p-4 mb-4"
                     style="background-color: var(--color-surface-container-low); border: 1px solid var(--color-border-subtle);">
                    <p style="font-size: 14px; color: var(--color-on-surface); white-space: pre-wrap; line-height: 1.6;">{{ $ticket->description }}</p>
                </div>

                {{-- Attachments --}}
                @if($ticket->attachments->isNotEmpty())
                <div>
                    <p class="mb-2" style="font-size: 12px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                        Lampiran ({{ $ticket->attachments->count() }})
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ticket->attachments as $att)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                             style="background-color: var(--color-card-elevated); border: 1px solid var(--color-border-subtle);">
                            <span class="material-symbols-outlined" style="font-size: 16px; color: var(--color-accent-electric);">
                                {{ $att->isImage() ? 'image' : 'attach_file' }}
                            </span>
                            <span style="font-size: 12px; color: var(--color-text-primary);">{{ $att->original_name }}</span>
                            <span style="font-size: 11px; color: var(--color-text-tertiary);">{{ $att->fileSizeHuman() }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Comments & Activity --}}
            <div class="card">
                <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                    Diskusi & Aktivitas
                </h2>

                {{-- Comment List --}}
                @forelse($ticket->comments as $comment)
                @php $isInternal = $comment->isInternal(); @endphp
                <div class="flex gap-3 mb-4 @if($isInternal) opacity-80 @endif">
                    <img src="{{ $comment->user->avatarUrl() }}" alt="{{ $comment->user->name }}"
                         class="w-8 h-8 rounded-full flex-shrink-0 mt-0.5">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary);">
                                {{ $comment->user->name }}
                            </span>
                            @if($isInternal)
                            <span class="badge" style="background-color: rgba(167,139,250,0.15); color: var(--color-status-pending); border: 1px solid rgba(167,139,250,0.3); font-size: 10px;">
                                Internal Note
                            </span>
                            @endif
                            <span style="font-size: 11px; color: var(--color-text-tertiary);">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="rounded-xl p-3"
                             style="background-color: {{ $isInternal ? 'rgba(167,139,250,0.06)' : 'var(--color-surface-container-low)' }}; border: 1px solid {{ $isInternal ? 'rgba(167,139,250,0.2)' : 'var(--color-border-subtle)' }};">
                            <p style="font-size: 14px; color: var(--color-text-primary); white-space: pre-wrap; line-height: 1.5;">{{ $comment->body }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="mb-4" style="font-size: 13px; color: var(--color-text-tertiary);">Belum ada komentar.</p>
                @endforelse

                {{-- Add Comment Form --}}
                @unless(in_array($ticket->status, ['closed']))
                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}"
                      class="mt-4 pt-4" style="border-top: 1px solid var(--color-border-subtle);">
                    @csrf
                    <textarea name="body" rows="3"
                              class="form-textarea mb-3"
                              placeholder="Tulis balasan atau catatan..."
                              required></textarea>
                    <div class="flex items-center justify-between">
                        @if(auth()->user()->isTechnician() || auth()->user()->canManage())
                        <div class="flex items-center gap-2">
                            <select name="type" class="form-select" style="width: 160px;">
                                <option value="public">Public Reply</option>
                                <option value="internal">Internal Note</option>
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="type" value="public">
                        <div></div>
                        @endif
                        <button type="submit" class="btn-primary btn-sm">
                            <span class="material-symbols-outlined" style="font-size: 15px;">send</span>
                            Kirim
                        </button>
                    </div>
                </form>
                @endunless
            </div>

            {{-- Timeline --}}
            <div class="card">
                <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                    Riwayat Aktivitas
                </h2>
                @forelse($ticket->histories as $h)
                <div class="flex gap-3 mb-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background-color: var(--color-surface-container-high); margin-top: 2px;">
                        <span class="material-symbols-outlined" style="font-size: 14px; color: var(--color-accent-electric);">
                            {{ $h->icon() }}
                        </span>
                    </div>
                    <div>
                        <p style="font-size: 13px; color: var(--color-text-primary);">{!! $h->description !!}</p>
                        <p style="font-size: 11px; color: var(--color-text-tertiary); margin-top: 2px;">
                            {{ $h->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <p style="font-size: 13px; color: var(--color-text-tertiary);">Belum ada aktivitas.</p>
                @endforelse
            </div>

        </div>

        {{-- RIGHT: Sidebar panel --}}
        <div class="flex flex-col gap-4">

            {{-- SLA Panel --}}
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    SLA Status
                </h2>
                @if($ticket->resolution_due_at)
                <div class="mb-3">
                    <div class="flex justify-between mb-1">
                        <span style="font-size: 12px; color: var(--color-text-secondary);">Resolution</span>
                        <span style="font-size: 12px; color: var(--color-{{ $ticket->slaStatusColor() }});">
                            {{ $ticket->resolution_due_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="sla-bar">
                        <div class="sla-bar-fill sla-bar-fill-{{ $ticket->isOverdue() ? 'breach' : 'ok' }}" style="width: 40%;"></div>
                    </div>
                </div>
                @else
                <p style="font-size: 13px; color: var(--color-text-tertiary);">SLA belum ditetapkan</p>
                @endif
            </div>

            {{-- Properties --}}
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Properties
                </h2>
                @foreach([
                    ['label' => 'Kategori',  'value' => $ticket->category?->name ?? '—'],
                    ['label' => 'Prioritas', 'value' => $ticket->priority?->name ?? '—'],
                    ['label' => 'Impact',    'value' => ucfirst($ticket->impact)],
                    ['label' => 'Urgency',   'value' => ucfirst($ticket->urgency)],
                    ['label' => 'Lokasi',    'value' => $ticket->location?->name ?? '—'],
                ] as $prop)
                <div class="flex justify-between py-2" style="border-bottom: 1px solid var(--color-border-subtle);">
                    <span style="font-size: 12px; color: var(--color-text-secondary);">{{ $prop['label'] }}</span>
                    <span style="font-size: 12px; color: var(--color-text-primary); font-weight: 500;">{{ $prop['value'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Assign Section (tech/admin/manager) --}}
            @if(auth()->user()->canManage() || auth()->user()->isTechnician())
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Assign Teknisi
                </h2>
                @if($ticket->assignee)
                <div class="flex items-center gap-2 mb-3">
                    <img src="{{ $ticket->assignee->avatarUrl() }}" class="w-7 h-7 rounded-full">
                    <span style="font-size: 13px; color: var(--color-text-primary);">{{ $ticket->assignee->name }}</span>
                </div>
                @endif
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                    @csrf @method('PATCH')
                    <select name="assignee_id" class="form-select mb-2" required>
                        <option value="">— Pilih Teknisi —</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" @selected($ticket->assignee_id == $tech->id)>
                            {{ $tech->name }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary btn-sm w-full justify-center">
                        Assign
                    </button>
                </form>
            </div>
            @endif

            {{-- Status Actions --}}
            @unless(in_array($ticket->status, ['closed']))
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Update Status
                </h2>
                @foreach(\App\Models\Ticket::ALLOWED_TRANSITIONS[$ticket->status] ?? [] as $nextStatus)
                <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="mb-2">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    @if($nextStatus === 'resolved')
                    <textarea name="resolution_notes" class="form-textarea mb-2" style="min-height: 80px;" placeholder="Catatan resolusi (opsional)..."></textarea>
                    @endif
                    <button type="submit"
                            class="{{ $nextStatus === 'resolved' ? 'btn-primary' : 'btn-secondary' }} btn-sm w-full justify-center">
                        <span class="material-symbols-outlined" style="font-size: 14px;">swap_horiz</span>
                        {{ ucfirst(str_replace('_', ' ', $nextStatus)) }}
                    </button>
                </form>
                @endforeach
            </div>
            @endunless

        </div>
    </div>
</x-app-layout>
