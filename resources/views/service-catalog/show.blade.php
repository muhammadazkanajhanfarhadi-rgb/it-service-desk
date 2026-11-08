<x-app-layout>
    <x-slot name="title">{{ $service->name }} — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <a href="{{ route('service-catalog.index') }}" style="color: var(--color-text-tertiary);">Catalog</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">{{ $service->name }}</span>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Left: service info --}}
        <div class="lg:col-span-2">
            <div class="card mb-4">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                         style="background-color: rgba(79,140,255,0.12);">
                        <span class="material-symbols-outlined" style="font-size: 28px; color: var(--color-accent-electric);">
                            {{ $service->icon ?? 'miscellaneous_services' }}
                        </span>
                    </div>
                    <div>
                        <h1 style="font-size: 18px; font-weight: 600; color: var(--color-text-primary);">{{ $service->name }}</h1>
                        @if($service->category)
                        <span class="badge badge-assigned mt-1">{{ $service->category->name }}</span>
                        @endif
                    </div>
                </div>
                @if($service->description)
                <p style="font-size: 14px; color: var(--color-on-surface); line-height: 1.6;">{{ $service->description }}</p>
                @endif
            </div>

            {{-- Request form --}}
            <div class="card">
                <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                    Ajukan Permintaan Layanan
                </h2>
                <form method="POST" action="{{ route('service-requests.store', $service) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Deskripsi Permintaan <span style="color: var(--color-status-danger);">*</span></label>
                        <textarea name="description" rows="4" class="form-textarea" placeholder="Jelaskan kebutuhan Anda..." required>{{ old('description') }}</textarea>
                        @error('description')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined" style="font-size: 18px;">send</span>
                        Kirim Permintaan
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: sidebar --}}
        <div class="flex flex-col gap-4">
            <div class="card">
                <h2 class="mb-3" style="font-size: 13px; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">SLA Target</h2>
                @if($service->slaPolicy)
                <div class="flex justify-between py-2" style="border-bottom: 1px solid var(--color-border-subtle);">
                    <span style="font-size: 12px; color: var(--color-text-secondary);">Response</span>
                    <span style="font-size: 12px; color: var(--color-status-info); font-weight: 500;">{{ $service->slaPolicy->responseLabel() }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span style="font-size: 12px; color: var(--color-text-secondary);">Resolution</span>
                    <span style="font-size: 12px; color: var(--color-status-success); font-weight: 500;">{{ $service->slaPolicy->resolutionLabel() }}</span>
                </div>
                @else
                <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada SLA khusus</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
