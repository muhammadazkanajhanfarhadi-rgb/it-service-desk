<x-app-layout>
    <x-slot name="title">Knowledge Articles — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Knowledge Articles</span>
    </x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <x-page-header title="Knowledge Article Management" subtitle="Buat dan kelola artikel Knowledge Base." />
        <a href="{{ route('admin.knowledge-articles.create') }}" class="btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
            Tambah Artikel
        </a>
    </div>

    {{-- Analytics --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
        <x-stat-card label="Total" value="{{ $stats['total'] }}" icon="article" color="accent-electric" />
        <x-stat-card label="Published" value="{{ $stats['published'] }}" icon="publish" color="status-success" />
        <x-stat-card label="Draft" value="{{ $stats['draft'] }}" icon="edit_note" color="status-warning" />
        <x-stat-card label="Archived" value="{{ $stats['archived'] }}" icon="archive" color="text-tertiary" />
        <x-stat-card label="Total Views" value="{{ number_format($stats['totalViews']) }}" icon="visibility" color="status-info" />
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 200px;" placeholder="Cari artikel...">
        <select name="status" class="form-select" style="width: 150px;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach(['draft','published','archived'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="category" class="form-select" style="width: 180px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    {{-- Article list --}}
    <div class="card" style="padding: 0;">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Judul</th><th>Author</th><th>Status</th><th>Views</th><th>Helpful</th><th>Published</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($articles as $article)
                <tr>
                    <td>
                        <a href="{{ route('knowledge-base.show', $article) }}" style="font-size: 13px; font-weight: 500; color: var(--color-text-primary);">
                            {{ Str::limit($article->title, 50) }}
                        </a>
                    </td>
                    <td style="font-size: 12px; color: var(--color-text-secondary);">{{ $article->author?->name ?? '—' }}</td>
                    <td>
                        @if($article->isPublished()) <span class="badge badge-resolved">Published</span>
                        @elseif($article->isDraft()) <span class="badge badge-high">Draft</span>
                        @else <span class="badge badge-closed">Archived</span> @endif
                    </td>
                    <td style="font-size: 12px;">{{ $article->views_count }}</td>
                    <td style="font-size: 12px;">
                        @if($article->helpfulCount() > 0)
                        <span style="color: var(--color-status-success);">{{ $article->helpfulCount() }} 👍</span>
                        @else — @endif
                    </td>
                    <td style="font-size: 12px; color: var(--color-text-tertiary);">{{ $article->published_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.knowledge-articles.edit', $article) }}" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-text-secondary);" title="Edit"
                               onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                               onmouseout="this.style.backgroundColor='transparent'">
                                <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.knowledge-articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg" style="color: var(--color-status-danger);" title="Hapus"
                                        onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
        <div class="px-5 py-3" style="border-top: 1px solid var(--color-border-subtle);">{{ $articles->links() }}</div>
        @endif
    </div>

    {{-- Most viewed & helpful --}}
    <div class="grid lg:grid-cols-2 gap-4 mt-4">
        <div class="card">
            <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">trending_up</span>
                Most Viewed
            </h2>
            @foreach($mostViewed as $a)
            <div class="flex justify-between py-2" style="border-bottom: 1px solid var(--color-border-subtle);">
                <span style="font-size: 13px; color: var(--color-text-secondary);">{{ Str::limit($a->title, 40) }}</span>
                <span style="font-size: 12px; color: var(--color-text-tertiary);">{{ $a->views_count }}</span>
            </div>
            @endforeach
        </div>
        <div class="card">
            <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">thumb_up</span>
                Most Helpful
            </h2>
            @forelse($mostHelpful as $a)
            <div class="flex justify-between py-2" style="border-bottom: 1px solid var(--color-border-subtle);">
                <span style="font-size: 13px; color: var(--color-text-secondary);">{{ Str::limit($a->title, 40) }}</span>
                <span style="font-size: 12px; color: var(--color-status-success);">{{ $a->helpful_count ?? 0 }} 👍</span>
            </div>
            @empty
            <p style="font-size: 13px; color: var(--color-text-tertiary);">Belum ada feedback</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
