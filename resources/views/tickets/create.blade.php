<x-app-layout>
    <x-slot name="title">Create Ticket — {{ config('app.name') }}</x-slot>

    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <a href="{{ route('tickets.my') }}" style="color: var(--color-text-tertiary);">My Tickets</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">Create Ticket</span>
    </x-slot>

    <x-page-header title="Create New Ticket" subtitle="Laporkan insiden atau ajukan permintaan layanan TI." />

    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid lg:grid-cols-3 gap-5">

            {{-- Left: Main details --}}
            <div class="lg:col-span-2 flex flex-col gap-4">

                {{-- Title --}}
                <div class="card">
                    <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                        Detail Insiden
                    </h2>

                    <div class="mb-4">
                        <label class="form-label" for="title">Judul Tiket <span style="color: var(--color-status-danger);">*</span></label>
                        <input id="title" type="text" name="title"
                               value="{{ old('title') }}"
                               class="form-input @error('title') border-red-500 @enderror"
                               placeholder="Deskripsi singkat masalah..."
                               required maxlength="255">
                        @error('title')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="description">Deskripsi Detail <span style="color: var(--color-status-danger);">*</span></label>
                        <textarea id="description" name="description"
                                  class="form-textarea @error('description') border-red-500 @enderror"
                                  style="min-height: 140px;"
                                  placeholder="Jelaskan masalah secara detail: langkah-langkah reproduksi, pesan error, dampak, dll..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Attachments --}}
                <div class="card">
                    <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                        Lampiran
                    </h2>
                    <input type="file" id="attachments" name="attachments[]"
                           multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                           class="form-input" style="height: auto; padding: 0.5rem;">
                    <p class="form-hint">Maks 5 file, masing-masing maks 10MB. Format: gambar, PDF, dokumen, ZIP.</p>
                    @error('attachments.*')<p class="form-error">{{ $message }}</p>@enderror
                </div>

            </div>

            {{-- Right: Classification --}}
            <div class="flex flex-col gap-4">
                <div class="card">
                    <h2 class="mb-4" style="font-size: 15px; font-weight: 600; color: var(--color-text-primary);">
                        Klasifikasi
                    </h2>

                    {{-- Category --}}
                    <div class="mb-4">
                        <label class="form-label" for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" class="form-select">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                            <optgroup label="{{ $cat->name }}">
                                @foreach($cat->children as $sub)
                                <option value="{{ $sub->id }}" @selected(old('category_id') == $sub->id)>
                                    {{ $sub->name }}
                                </option>
                                @endforeach
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                    {{ $cat->name }} (Umum)
                                </option>
                            </optgroup>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div class="mb-4">
                        <label class="form-label" for="priority_id">Prioritas</label>
                        <select id="priority_id" name="priority_id" class="form-select">
                            <option value="">— Pilih Prioritas —</option>
                            @foreach($priorities as $p)
                            <option value="{{ $p->id }}" @selected(old('priority_id') == $p->id)>
                                {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Impact --}}
                    <div class="mb-4">
                        <label class="form-label" for="impact">Impact <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="impact" name="impact" class="form-select" required>
                            <option value="low"      @selected(old('impact','medium') === 'low')>Low — Satu pengguna</option>
                            <option value="medium"   @selected(old('impact','medium') === 'medium')>Medium — Beberapa pengguna</option>
                            <option value="high"     @selected(old('impact','medium') === 'high')>High — Satu departemen</option>
                            <option value="critical" @selected(old('impact','medium') === 'critical')>Critical — Seluruh organisasi</option>
                        </select>
                    </div>

                    {{-- Urgency --}}
                    <div class="mb-4">
                        <label class="form-label" for="urgency">Urgensi <span style="color: var(--color-status-danger);">*</span></label>
                        <select id="urgency" name="urgency" class="form-select" required>
                            <option value="low"      @selected(old('urgency','medium') === 'low')>Low — Dapat ditunda</option>
                            <option value="medium"   @selected(old('urgency','medium') === 'medium')>Medium — Dalam hari ini</option>
                            <option value="high"     @selected(old('urgency','medium') === 'high')>High — Dalam beberapa jam</option>
                            <option value="critical" @selected(old('urgency','medium') === 'critical')>Critical — Sekarang juga</option>
                        </select>
                    </div>

                    {{-- Location --}}
                    <div class="mb-0">
                        <label class="form-label" for="location_id">Lokasi</label>
                        <select id="location_id" name="location_id" class="form-select">
                            <option value="">— Pilih Lokasi —</option>
                            @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @selected(old('location_id') == $loc->id)>
                                {{ $loc->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary w-full justify-center" style="height: 2.75rem;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">send</span>
                    Kirim Tiket
                </button>
                <a href="{{ route('tickets.my') }}" class="btn-secondary w-full justify-center">
                    Batal
                </a>

            </div>
        </div>
    </form>
</x-app-layout>
