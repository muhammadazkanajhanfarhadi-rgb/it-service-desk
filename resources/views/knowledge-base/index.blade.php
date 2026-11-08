<x-app-layout>
    <x-slot name="title">Knowledge Base — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Knowledge Base</span>
    </x-slot>

    <x-page-header title="How can we help?" subtitle="Cari artikel solusi dan panduan IT." />

    {{-- Search bar --}}
    <form method="GET" class="mb-5">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" style="font-size: 18px; color: var(--color-text-tertiary);">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="Cari artikel...">
            </div>
            <select name="category" class="form-select" style="width: 180px;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Cari</button>
        </div>
    </form>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Articles list --}}
        <div class="lg:col-span-2 flex flex-col gap-4">
            @if($articles->isEmpty())
            <div class="card flex flex-col items-center justify-center py-16 gap-3">
                <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">search_off</span>
                <p style="font-size: 14px; color: var(--color-text-secondary);">Tidak ada artikel ditemukan</p>
            </div>
            @else
            @foreach($articles as $article)
            <a href="{{ route('knowledge-base.show', $article) }}" class="card no-underline" style="text-decoration: none;">
                <div class="flex items-start gap-3">
                    @if($article->category)
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: rgba(79,140,255,0.12);">
                        <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-accent-electric);">
                            {{ $article->category->icon ?? 'article' }}
                        </span>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">{{ $article->title }}</div>
                        @if($article->excerpt)
                        <p class="mt-1" style="font-size: 13px; color: var(--color-text-secondary); line-height: 1.5;">{{ $article->excerpt }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-2" style="font-size: 11px; color: var(--color-text-tertiary);">
                            @if($article->category)
                            <span class="badge badge-assigned">{{ $article->category->name }}</span>
                            @endif
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size: 12px;">visibility</span>
                                {{ $article->views_count }}
                            </span>
                            @if($article->helpfulCount() > 0)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size: 12px; color: var(--color-status-success);">thumb_up</span>
                                {{ $article->helpfulCount() }}
                            </span>
                            @endif
                            <span>{{ $article->published_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
            @if($articles->hasPages())
            <div>{{ $articles->links() }}</div>
            @endif
            @endif
        </div>

        {{-- Sidebar: Popular articles --}}
        <div class="flex flex-col gap-4">
            <div class="card">
                <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">trending_up</span>
                    Popular Articles
                </h2>
                @if($popular->isEmpty())
                <p style="font-size: 13px; color: var(--color-text-tertiary);">Belum ada artikel populer</p>
                @else
                <div class="flex flex-col gap-2">
                    @foreach($popular as $p)
                    <a href="{{ route('knowledge-base.show', $p) }}" style="text-decoration: none;">
                        <div class="flex items-center gap-2 p-2 rounded-xl transition-colors"
                             onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                             onmouseout="this.style.backgroundColor='transparent'">
                            <span style="font-size: 11px; color: var(--color-text-tertiary);">{{ $p->views_count }}</span>
                            <span style="font-size: 13px; color: var(--color-text-secondary);">{{ Str::limit($p->title, 35) }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Categories filter --}}
            <div class="card">
                <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">category</span>
                    Categories
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $c)
                    <a href="{{ route('knowledge-base.index', ['category' => $c->id]) }}"
                       class="badge {{ request('category') == $c->id ? 'badge-in-progress' : 'badge-assigned' }}"
                       style="text-decoration: none;">
                        {{ $c->name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
