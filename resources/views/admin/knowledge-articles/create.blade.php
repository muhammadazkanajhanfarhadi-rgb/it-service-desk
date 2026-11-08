<x-app-layout>
    <x-slot name="title">Create Article — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('admin.knowledge-articles.index') }}" style="color: var(--color-text-tertiary);">Articles</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Create</span>
    </x-slot>

    <x-page-header title="Create New Article" subtitle="Tulis artikel solusi untuk Knowledge Base." />

    <form method="POST" action="{{ route('admin.knowledge-articles.store') }}" class="max-w-3xl">
        @csrf
        <div class="card flex flex-col gap-4">
            <div>
                <label class="form-label">Judul <span style="color: var(--color-status-danger);">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input" required placeholder="Judul artikel...">
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Ringkasan (excerpt)</label>
                <input type="text" name="excerpt" value="{{ old('excerpt') }}" class="form-input" maxlength="500" placeholder="Ringkasan singkat artikel...">
                @error('excerpt')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">— Tanpa Kategori —</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Konten <span style="color: var(--color-status-danger);">*</span></label>
                <p class="form-hint mb-2">Mendukung HTML: &lt;p&gt;, &lt;h1&gt;-&lt;h6&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;a&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;img&gt;, &lt;blockquote&gt;, &lt;code&gt;, &lt;pre&gt;</p>
                <textarea name="content" rows="15" class="form-textarea" style="font-family: monospace; font-size: 13px;" required placeholder="Tulis konten artikel dengan HTML...">{{ old('content') }}</textarea>
                @error('content')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Status <span style="color: var(--color-status-danger);">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="draft" @selected(old('status','draft') === 'draft')>Draft (tidak terlihat oleh Employee)</option>
                    <option value="published" @selected(old('status','draft') === 'published')>Published (terlihat oleh semua user)</option>
                    <option value="archived" @selected(old('status','draft') === 'archived')>Archived (tidak muncul di katalog)</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                Simpan Artikel
            </button>
            <a href="{{ route('admin.knowledge-articles.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
