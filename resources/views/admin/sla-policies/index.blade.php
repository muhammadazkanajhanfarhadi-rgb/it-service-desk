<x-app-layout>
    <x-slot name="title">SLA Policies — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">SLA Policies</span>
    </x-slot>

    <x-page-header title="SLA Policy Management" subtitle="Atur target response dan resolution per prioritas." />

    <div class="flex flex-col gap-4">
        @foreach($policies as $policy)
        <form method="POST" action="{{ route('admin.sla-policies.update', $policy) }}">
            @csrf @method('PATCH')
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        @if($policy->priority)
                        <x-priority-badge :priority="$policy->priority" />
                        @endif
                        <input type="text" name="name" value="{{ old('name', $policy->name) }}" class="form-input" style="width: 200px; height: 32px; font-size: 13px;">
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" @checked($policy->is_active) class="w-4 h-4" style="accent-color: var(--color-primary-container);">
                        <span style="font-size: 13px; color: var(--color-text-secondary);">Aktif</span>
                    </label>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                    <div>
                        <label class="form-label">Response (menit)</label>
                        <input type="number" name="response_minutes" value="{{ old('response_minutes', $policy->response_minutes) }}" class="form-input" min="1" required>
                    </div>
                    <div>
                        <label class="form-label">Resolution (menit)</label>
                        <input type="number" name="resolution_minutes" value="{{ old('resolution_minutes', $policy->resolution_minutes) }}" class="form-input" min="1" required>
                    </div>
                    <div>
                        <label class="form-label">Business Hours</label>
                        <label class="flex items-center gap-2 h-10">
                            <input type="checkbox" name="business_hours_only" value="1" @checked($policy->business_hours_only) class="w-4 h-4" style="accent-color: var(--color-primary-container);">
                            <span style="font-size: 13px; color: var(--color-text-secondary);">Ya</span>
                        </label>
                    </div>
                    <div>
                        <label class="form-label">Mulai</label>
                        <input type="time" name="business_start" value="{{ old('business_start', substr($policy->business_start, 0, 5)) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Selesai</label>
                        <input type="time" name="business_end" value="{{ old('business_end', substr($policy->business_end, 0, 5)) }}" class="form-input">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <input type="text" name="description" value="{{ old('description', $policy->description) }}" class="form-input" style="flex: 1; height: 32px; font-size: 12px;" placeholder="Deskripsi (opsional)">
                    <button type="submit" class="btn-primary btn-sm ml-3">
                        <span class="material-symbols-outlined" style="font-size: 14px;">save</span>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
        @endforeach
    </div>
</x-app-layout>
