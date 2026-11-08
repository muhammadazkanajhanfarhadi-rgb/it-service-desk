<x-app-layout>
    <x-slot name="title">Service Catalog — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Service Catalog</span>
    </x-slot>

    <x-page-header title="Service Catalog" subtitle="Pilih layanan yang Anda butuhkan." />

    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="width: 200px;" placeholder="Cari layanan...">
        <select name="category" class="form-select" style="width: 180px;" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
    </form>

    @if($services->isEmpty())
    <div class="card flex flex-col items-center justify-center py-16 gap-3">
        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--color-text-disabled);">storefront</span>
        <p style="font-size: 14px; color: var(--color-text-secondary);">Belum ada layanan tersedia</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($services as $service)
        <a href="{{ route('service-catalog.show', $service) }}" class="card no-underline" style="text-decoration: none;">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background-color: rgba(79,140,255,0.12);">
                    <span class="material-symbols-outlined" style="font-size: 20px; color: var(--color-accent-electric);">
                        {{ $service->icon ?? 'miscellaneous_services' }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <div style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">{{ $service->name }}</div>
                    <div style="font-size: 12px; color: var(--color-text-secondary); margin-top: 4px;">
                        {{ Str::limit($service->description ?? '—', 60) }}
                    </div>
                    @if($service->category)
                    <span class="badge badge-assigned mt-2" style="font-size: 10px;">{{ $service->category->name }}</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @if($services->hasPages())
    <div class="mt-4">{{ $services->links() }}</div>
    @endif
    @endif
</x-app-layout>
