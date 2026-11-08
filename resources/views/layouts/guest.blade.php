<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'IT Service Desk') }}</title>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col justify-between" style="background-color: var(--color-app-bg); color: var(--color-text-primary);">

    <!-- Ambient glow backgrounds -->
    <div class="fixed pointer-events-none -z-10 inset-0 overflow-hidden">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2"
             style="width: 720px; height: 480px; background: rgba(37,99,235,0.07); border-radius: 9999px; filter: blur(130px);"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2"
             style="width: 420px; height: 280px; background: rgba(79,140,255,0.05); border-radius: 9999px; filter: blur(100px);"></div>
    </div>

    <!-- Header -->
    <header class="w-full px-6 py-4 flex items-center justify-between"
            style="border-bottom: 1px solid var(--color-border-subtle); background-color: rgba(16,24,42,0.6); backdrop-filter: blur(12px);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                 style="background-color: var(--color-primary-container); box-shadow: 0 0 20px rgba(37,99,235,0.3);">
                <span class="material-symbols-outlined" style="font-size: 18px; color: #ffffff;">terminal</span>
            </div>
            <div>
                <div class="font-semibold" style="font-size: 14px; color: var(--color-text-primary);">APEX SERVICE DESK</div>
                <div class="uppercase tracking-widest" style="font-size: 10px; color: var(--color-text-tertiary);">Tier 1-3 Operations Command</div>
            </div>
        </div>
        <div class="flex items-center gap-2 px-3 py-1 rounded-full"
             style="background-color: rgba(18,28,49,0.6); border: 1px solid var(--color-border-subtle);">
            <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--color-status-success);"></span>
            <span class="material-symbols-outlined" style="font-size: 14px; color: var(--color-text-secondary);">lock</span>
            <span style="font-size: 11px; color: var(--color-text-secondary); letter-spacing: 0.1em; text-transform: uppercase;">256-Bit TLS</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-10">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="w-full px-6 py-3 flex items-center justify-between"
            style="border-top: 1px solid var(--color-border-subtle);">
        <span style="font-size: 11px; color: var(--color-text-tertiary);">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </span>
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined" style="font-size: 13px; color: var(--color-status-success);">verified_user</span>
            <span style="font-size: 11px; color: var(--color-text-secondary);">Secure Connection</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
