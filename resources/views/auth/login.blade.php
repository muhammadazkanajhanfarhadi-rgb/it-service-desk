<x-guest-layout>
    <x-slot name="title">Sign In — {{ config('app.name') }}</x-slot>

    <div class="w-full max-w-md mx-auto flex flex-col gap-4">

        {{-- Status bar --}}
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-2 px-3 py-1 rounded-full"
                 style="background-color: rgba(18,28,49,0.6); border: 1px solid var(--color-border-subtle);">
                <span class="w-2 h-2 rounded-full animate-pulse"
                      style="background-color: var(--color-status-success); box-shadow: 0 0 8px rgba(34,197,94,0.6);"></span>
                <span style="font-size: 12px; color: var(--color-text-secondary);">
                    System: <span style="color: var(--color-text-primary); font-weight: 600;">OPERATIONAL</span>
                </span>
            </div>
            <span style="font-size: 12px; color: var(--color-text-tertiary);">{{ config('app.name') }}</span>
        </div>

        {{-- Main auth card --}}
        <div class="relative overflow-hidden rounded-3xl p-8 flex flex-col gap-6"
             style="background-color: var(--color-card-surface); border: 1px solid var(--color-border-subtle); box-shadow: 0 24px 64px rgba(0,0,0,0.45);">

            {{-- Top shimmer line --}}
            <div class="absolute top-0 left-0 right-0 h-px"
                 style="background: linear-gradient(to right, transparent, rgba(79,140,255,0.5), transparent);"></div>

            {{-- Card header --}}
            <div class="flex flex-col items-center text-center gap-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center relative"
                     style="background-color: var(--color-card-elevated); box-shadow: 0 0 35px rgba(37,99,235,0.3);">
                    <span class="material-symbols-outlined"
                          style="font-size: 30px; color: var(--color-accent-electric);">shield_person</span>
                </div>
                <div>
                    <h1 class="font-bold" style="font-size: 22px; color: var(--color-text-primary);">
                        Sign in to Console
                    </h1>
                    <p class="mt-1" style="font-size: 13px; color: var(--color-text-secondary);">
                        Masukkan kredensial untuk mengakses IT Service Desk
                    </p>
                </div>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
            <div class="alert alert-success">
                <span class="material-symbols-outlined" style="font-size: 16px; flex-shrink: 0;">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="form-label">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined pointer-events-none"
                              style="font-size: 17px; color: var(--color-text-tertiary);">alternate_email</span>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}"
                               class="form-input"
                               style="padding-left: 2.5rem;"
                               placeholder="nama@perusahaan.com"
                               required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="font-size: 12px; color: var(--color-accent-light);"
                           onmouseover="this.style.color='var(--color-accent-electric)'"
                           onmouseout="this.style.color='var(--color-accent-light)'">
                            Lupa password?
                        </a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined pointer-events-none"
                              style="font-size: 17px; color: var(--color-text-tertiary);">lock</span>
                        <input id="password" type="password" name="password"
                               class="form-input"
                               style="padding-left: 2.5rem; padding-right: 2.5rem;"
                               placeholder="••••••••••••"
                               required autocomplete="current-password">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                                style="color: var(--color-text-secondary);"
                                onmouseover="this.style.backgroundColor='var(--color-card-elevated)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                            <span class="material-symbols-outlined" id="pwd-icon" style="font-size: 17px;">visibility</span>
                        </button>
                    </div>
                    @error('password')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="w-4 h-4 rounded cursor-pointer"
                           style="accent-color: var(--color-primary-container);">
                    <label for="remember_me" style="font-size: 13px; color: var(--color-text-secondary); cursor: pointer;">
                        Ingat perangkat ini selama 30 hari
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full h-11 rounded-xl font-semibold flex items-center justify-center gap-2 transition-all duration-200"
                        style="background: linear-gradient(135deg, var(--color-primary-container), var(--color-accent-electric)); color: #ffffff; font-size: 14px; box-shadow: 0 0 30px rgba(37,99,235,0.35);"
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'">
                    <span class="material-symbols-outlined" style="font-size: 18px;">login</span>
                    Sign In
                </button>
            </form>

            {{-- Compliance footnote --}}
            <div class="flex flex-wrap items-center justify-center gap-4"
                 style="font-size: 11px; color: var(--color-text-tertiary); padding-top: 4px;">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 13px; color: var(--color-accent-electric);">enhanced_encryption</span>
                    256-Bit TLS
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 13px; color: var(--color-status-success);">verified</span>
                    Session Secure
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 13px; color: var(--color-status-pending);">policy</span>
                    RBAC Enforced
                </span>
            </div>

        </div>{{-- /card --}}

    </div>

    @push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('pwd-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
    @endpush
</x-guest-layout>
