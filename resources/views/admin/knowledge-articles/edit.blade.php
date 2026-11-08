<x-app-layout>
    <x-slot name="title">Edit Article — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('admin.knowledge-articles.index') }}" style="color: var(--color-text-tertiary);">Articles</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Edit</span>
    </x-slot>

    <x-page-header title="Edit Article" subtitle="{{ $article->title }}" />

    <form method="POST" action="{{ route('admin.knowledge-articles.update', $article) }}" class="max-w-3xl">
        @csrf @method('PATCH')
        <div class="card flex flex-col gap-4">
            <div>
                <label class="form-label">Judul <span style="color: var(--color-status-danger);">*</span></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" class="form-input" required>
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Ringkasan</label>
                <input type="text" name="excerpt" value="{{ old('excerpt', $article->excerpt) }}" class="form-input" maxlength="500">
                @error('excerpt')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">— Tanpa Kategori —</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $article->category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Konten <span style="color: var(--color-status-danger);">*</span></label>
                <p class="form-hint mb-2">Konten disanitasi otomatis — tag HTML di luar allowlist akan dihapus.</p>
                <textarea name="content" rows="15" class="form-textarea" style="font-family: monospace; font-size: 13px;" required>{{ old('content', $article->content) }}</textarea>
                @error('content')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Status <span style="color: var(--color-status-danger);">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft" @selected(old('status', $article->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $article->status) === 'published')>Published</option>
                    <option value="archived" @selected(old('status', $article->status) === 'archived')>Archived</option>
                </select>
            </div>
            <div class="flex items-center gap-4 p-3 rounded-xl" style="background-color: var(--color-surface-container-low); border: 1px solid var(--color-border-subtle);">
                <span style="font-size: 12px; color: var(--color-text-tertiary);">Author: {{ $article->author?->name }}</span>
                <span style="font-size: 12px; color: var(--color-text-tertiary);">Published: {{ $article->published_at?->format('d M Y') ?? '—' }}</span>
                <span style="font-size: 12px; color: var(--color-text-tertiary);">Views: {{ $article->views_count }}</span>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                Simpan Perubahan
            </button>
            <a href="{{ route('knowledge-base.show', $article) }}" class="btn-secondary" target="_blank">Preview</a>
            <a href="{{ route('admin.knowledge-articles.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
