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
<body class="min-h-screen flex" style="background-color: var(--color-app-bg); color: var(--color-text-primary);">

    <!-- ── Sidebar ──────────────────────────────────────────── -->
    <aside id="sidebar"
           class="fixed top-0 left-0 h-full z-40 flex flex-col transition-transform duration-300"
           style="width: 260px; background-color: var(--color-sidebar-surface); border-right: 1px solid var(--color-border-subtle);">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom: 1px solid var(--color-border-subtle);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background-color: var(--color-primary-container); box-shadow: 0 0 20px rgba(37,99,235,0.3);">
                <span class="material-symbols-outlined" style="font-size: 18px; color: #ffffff;">terminal</span>
            </div>
            <div>
                <div class="font-semibold tracking-tight leading-none" style="font-size: 14px; color: var(--color-text-primary);">
                    IT Service Desk
                </div>
                <div class="uppercase tracking-widest" style="font-size: 10px; color: var(--color-text-tertiary); margin-top: 2px;">
                    Apex Operations
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-3 px-3" style="scrollbar-width: none;">
            @php $user = auth()->user(); @endphp

            {{-- Main Navigation --}}
            <div class="mb-1">
                <x-nav-item href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-nav-item>

                @unless($user->isAdmin())
                <x-nav-item href="{{ route('tickets.my') }}" icon="confirmation_number" :active="request()->routeIs('tickets.my*')">
                    My Tickets
                </x-nav-item>
                @endunless

                @if($user->isAdmin() || $user->isManager() || $user->isTechnician())
                <x-nav-item href="{{ route('tickets.index') }}" icon="list_alt" :active="request()->routeIs('tickets.index')">
                    All Tickets
                </x-nav-item>
                @endif

                @unless($user->isAdmin())
                <x-nav-item href="{{ route('tickets.create') }}" icon="add_circle" :active="request()->routeIs('tickets.create')">
                    Create Ticket
                </x-nav-item>
                @endunless

                <x-nav-item href="{{ route('notifications.index') }}" icon="notifications" :active="request()->routeIs('notifications*')">
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    Notifications
                    @if($unread > 0)
                    <span class="ml-auto badge badge-critical" style="font-size: 10px; padding: 0 6px; height: 16px;">{{ $unread }}</span>
                    @endif
                </x-nav-item>
            </div>

            {{-- Divider with label --}}
            <x-nav-section-label>Services</x-nav-section-label>

            <div class="mb-1">
                <x-nav-item href="{{ route('service-catalog.index') }}" icon="storefront" :active="request()->routeIs('service-catalog*')">
                    Service Catalog
                </x-nav-item>
                <x-nav-item href="{{ route('service-requests.my') }}" icon="assignment" :active="request()->routeIs('service-requests.my*')">
                    My Requests
                </x-nav-item>
                <x-nav-item href="{{ route('assets.index') }}" icon="inventory_2" :active="request()->routeIs('assets*')">
                    Assets
                </x-nav-item>
                <x-nav-item href="{{ route('knowledge-base.index') }}" icon="menu_book" :active="request()->routeIs('knowledge-base*')">
                    Knowledge Base
                </x-nav-item>
            </div>

            {{-- Manager / Admin only --}}
            @if($user->canManage())
            <x-nav-section-label>Reports</x-nav-section-label>
            <div class="mb-1">
                <x-nav-item href="{{ route('reports.index') }}" icon="bar_chart" :active="request()->routeIs('reports*')">
                    Reports
                </x-nav-item>
                <x-nav-item href="{{ route('sla.monitoring') }}" icon="policy" :active="request()->routeIs('sla*')">
                    SLA Monitoring
                </x-nav-item>
                @if($user->isApprover() || $user->isManager())
                <x-nav-item href="{{ route('service-requests.approvals') }}" icon="approval" :active="request()->routeIs('service-requests.approvals*')">
                    Pending Approvals
                </x-nav-item>
                @endif
                <x-nav-item href="{{ route('admin.knowledge-articles.index') }}" icon="edit_note" :active="request()->routeIs('admin.knowledge-articles*')">
                    Knowledge Articles
                </x-nav-item>
            </div>
            @endif

            {{-- Admin only --}}
            @if($user->isAdmin())
            <x-nav-section-label>Administration</x-nav-section-label>
            <div class="mb-1">
                <x-nav-item href="{{ route('admin.users.index') }}" icon="group" :active="request()->routeIs('admin.users*')">
                    Users & Roles
                </x-nav-item>
                <x-nav-item href="{{ route('admin.categories.index') }}" icon="category" :active="request()->routeIs('admin.categories*')">
                    Categories
                </x-nav-item>
                <x-nav-item href="{{ route('admin.sla-policies.index') }}" icon="timer" :active="request()->routeIs('admin.sla-policies*')">
                    SLA Policies
                </x-nav-item>
                <x-nav-item href="{{ route('admin.services.index') }}" icon="article" :active="request()->routeIs('admin.services*')">
                    Services
                </x-nav-item>
                <x-nav-item href="{{ route('admin.audit-log.index') }}" icon="receipt_long" :active="request()->routeIs('admin.audit-log*')">
                    Audit Log
                </x-nav-item>
            </div>
            @endif
        </nav>

        <!-- User Profile at Bottom -->
        <div class="px-3 py-3" style="border-top: 1px solid var(--color-border-subtle);">
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer transition-colors"
                 style="hover:background-color: var(--color-interactive-active-bg);">
                <img src="{{ auth()->user()->avatarUrl() }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="truncate font-medium" style="font-size: 13px; color: var(--color-text-primary);">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="truncate" style="font-size: 11px; color: var(--color-text-tertiary);">
                        {{ auth()->user()->role?->name ?? 'User' }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            style="color: var(--color-text-tertiary);"
                            onmouseover="this.style.color='var(--color-status-danger)'"
                            onmouseout="this.style.color='var(--color-text-tertiary)'">
                        <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ── Overlay (mobile) ──────────────────────────────────── -->
    <div id="sidebar-overlay"
         class="fixed inset-0 z-30 hidden"
         style="background-color: var(--color-overlay-backdrop);"
         onclick="toggleSidebar()"></div>

    <!-- ── Main Content Area ─────────────────────────────────── -->
    <div class="flex-1 flex flex-col min-h-screen" style="margin-left: 260px;">

        <!-- Topbar -->
        <header class="sticky top-0 z-20 flex items-center justify-between px-6 h-14"
                style="background-color: rgba(16, 24, 42, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid var(--color-border-subtle);">

            <!-- Left: Hamburger + Breadcrumb -->
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()"
                        class="lg:hidden"
                        style="color: var(--color-text-secondary);">
                    <span class="material-symbols-outlined" style="font-size: 22px;">menu</span>
                </button>

                {{-- Breadcrumb slot --}}
                @isset($breadcrumb)
                <nav class="hidden md:flex items-center gap-1.5" style="font-size: 13px;">
                    {{ $breadcrumb }}
                </nav>
                @endisset
            </div>

            <!-- Right: Notification + Profile -->
            <div class="flex items-center gap-2">
                <!-- Notification Bell + Dropdown -->
                <div class="relative" id="notif-wrapper">
                    <button id="notif-btn"
                            onclick="toggleNotifDropdown()"
                            class="relative w-9 h-9 flex items-center justify-center rounded-xl transition-colors"
                            style="color: var(--color-text-secondary);"
                            onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined" style="font-size: 20px;">notifications</span>
                        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                        @if($unreadCount > 0)
                        <span id="notif-badge"
                              class="absolute top-1 right-1 min-w-[16px] h-4 rounded-full flex items-center justify-center text-white"
                              style="font-size: 10px; font-weight: 700; background-color: var(--color-status-danger); padding: 0 3px;">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                        @else
                        <span id="notif-badge" class="hidden absolute top-1 right-1 w-4 h-4 rounded-full flex items-center justify-center text-white"
                              style="font-size: 10px; font-weight: 700; background-color: var(--color-status-danger);"></span>
                        @endif
                    </button>

                    <!-- Dropdown panel -->
                    <div id="notif-dropdown"
                         class="hidden absolute right-0 mt-2 z-50 rounded-2xl overflow-hidden"
                         style="width: 360px; background-color: var(--color-card-elevated); border: 1px solid var(--color-border-subtle); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-3"
                             style="border-bottom: 1px solid var(--color-border-subtle);">
                            <span style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">Notifikasi</span>
                            <div class="flex items-center gap-2">
                                @if($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            style="font-size: 12px; color: var(--color-accent-light);"
                                            onmouseover="this.style.color='var(--color-accent-electric)'"
                                            onmouseout="this.style.color='var(--color-accent-light)'">
                                        Mark all read
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('notifications.index') }}"
                                   style="font-size: 12px; color: var(--color-text-tertiary);"
                                   onmouseover="this.style.color='var(--color-text-primary)'"
                                   onmouseout="this.style.color='var(--color-text-tertiary)'">
                                    Lihat semua
                                </a>
                            </div>
                        </div>

                        <!-- Items -->
                        <div style="max-height: 380px; overflow-y: auto;">
                            @forelse(auth()->user()->notifications()->latest()->take(8)->get() as $notif)
                            @php $data = $notif->data; @endphp
                            <a href="{{ route('notifications.read', $notif->id) }}"
                               class="flex items-start gap-3 px-4 py-3 transition-colors"
                               style="text-decoration: none; {{ $notif->unread() ? 'background-color: rgba(37,99,235,0.06);' : '' }}"
                               onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                               onmouseout="this.style.backgroundColor='{{ $notif->unread() ? 'rgba(37,99,235,0.06)' : 'transparent' }}'">

                                <!-- Icon -->
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                                     style="background-color: rgba(79,140,255,0.12);">
                                    <span class="material-symbols-outlined"
                                          style="font-size: 16px; color: var(--color-{{ $data['color'] ?? 'accent-electric' }});">
                                        {{ $data['icon'] ?? 'notifications' }}
                                    </span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p style="font-size: 13px; color: var(--color-text-primary); line-height: 1.4;" class="leading-snug">
                                        {{ Str::limit($data['message'] ?? '', 70) }}
                                    </p>
                                    <p class="mt-0.5" style="font-size: 11px; color: var(--color-text-tertiary);">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                @if($notif->unread())
                                <span class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5"
                                      style="background-color: var(--color-accent-electric);"></span>
                                @endif
                            </a>
                            @empty
                            <div class="flex flex-col items-center justify-center py-10 gap-2">
                                <span class="material-symbols-outlined" style="font-size: 36px; color: var(--color-text-disabled);">notifications_off</span>
                                <p style="font-size: 13px; color: var(--color-text-tertiary);">Tidak ada notifikasi</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Profile link -->
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-3 py-1.5 rounded-xl transition-colors"
                   style="color: var(--color-text-secondary);"
                   onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    <img src="{{ auth()->user()->avatarUrl() }}"
                         alt="{{ auth()->user()->name }}"
                         class="w-6 h-6 rounded-full object-cover">
                    <span class="hidden md:block" style="font-size: 13px; color: var(--color-text-primary);">
                        {{ auth()->user()->name }}
                    </span>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success mb-4" role="alert">
                <span class="material-symbols-outlined" style="font-size: 18px; flex-shrink: 0;">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger mb-4" role="alert">
                <span class="material-symbols-outlined" style="font-size: 18px; flex-shrink: 0;">error</span>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebar-overlay');
            const isHidden = sidebar.style.transform === 'translateX(-100%)';

            sidebar.style.transform = isHidden ? 'translateX(0)' : 'translateX(-100%)';
            overlay.classList.toggle('hidden', !isHidden);
        }

        // On desktop, always show sidebar
        function handleResize() {
            const sidebar  = document.getElementById('sidebar');
            const overlay  = document.getElementById('sidebar-overlay');
            if (window.innerWidth >= 1024) {
                sidebar.style.transform = 'translateX(0)';
                overlay.classList.add('hidden');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();

        // Notification dropdown
        function toggleNotifDropdown() {
            const dd = document.getElementById('notif-dropdown');
            dd.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notif-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('notif-dropdown')?.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
