<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Dashboard Peserta' }} — Sistem Psikotes HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sb-bg: #0d2028;
            --sb-border: rgba(255,255,255,0.08);
            --sb-text: #7db8c2;
            --sb-text-hover: #b0d8df;
            --sb-text-active: #5fcfdf;
            --sb-active-bg: rgba(44,95,111,0.35);
            --sb-hover-bg: rgba(255,255,255,0.05);
            --sb-label: #3d7a88;
            --sb-divider: rgba(255,255,255,0.07);
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: var(--sb-text);
            font-size: 14px;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
        }
        .sidebar-link:hover {
            background: var(--sb-hover-bg);
            color: var(--sb-text-hover);
        }
        .sidebar-link.active {
            background: var(--sb-active-bg);
            color: var(--sb-text-active);
            border-left-color: var(--sb-text-active);
            font-weight: 600;
        }
        .sidebar-link.active .material-symbols-outlined {
            color: var(--sb-text-active);
        }
        .section-label {
            padding: 16px 24px 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sb-label);
        }
        .nav-divider {
            height: 1px;
            background: var(--sb-divider);
            margin: 8px 24px;
        }
        aside { background: var(--sb-bg) !important; border-right-color: var(--sb-border) !important; }
        aside .logo-area { border-bottom-color: var(--sb-border) !important; }
        aside .user-bottom { border-top-color: var(--sb-border) !important; }
        aside .logo-title { color: #5fcfdf !important; }
        aside .logo-sub { color: var(--sb-text) !important; }
        aside .user-name { color: #e0f4f7 !important; }
        aside .user-email { color: var(--sb-text) !important; }
        aside .logout-btn { color: var(--sb-text) !important; }
        aside .logout-btn:hover { color: #5fcfdf !important; }

        /* Collapsed sidebar */
        aside.collapsed .sidebar-link {
            justify-content: center;
            padding: 10px 0;
            gap: 0;
        }
        aside.collapsed .sidebar-link > span:not(.material-symbols-outlined) { display: none; }
        aside.collapsed .sidebar-link .ml-auto { display: none; }
        aside.collapsed .section-label { display: none; }
        aside.collapsed .nav-divider { margin: 8px 4px; }
        aside.collapsed .link-text { display: none; }
        aside.collapsed .logo-area { justify-content: center; }
        aside.collapsed .user-card-actions { display: none; }
    </style>
</head>
<body class="min-h-screen bg-[#f7f9fb] font-body antialiased">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpenPeserta') !== 'false' }" x-init="$watch('sidebarOpen', v => localStorage.setItem('sidebarOpenPeserta', v))">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-[#e0e3e5] overflow-y-auto overflow-x-hidden"
           style="transition: width 0.2s ease;"
           :style="{ width: sidebarOpen ? '280px' : '64px' }"
           :class="{ collapsed: !sidebarOpen }">

        {{-- Logo --}}
        <div class="logo-area flex items-center gap-3 px-3 h-16 border-b border-[#e0e3e5] shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Psikotes HR Logo" class="h-9 w-auto object-contain shrink-0">
            <div class="link-text whitespace-nowrap">
                <p class="logo-title text-sm font-bold text-[#2C5F6F] leading-none">Psikotes HR</p>
                <p class="logo-sub text-[11px] text-[#40484b] mt-0.5">Portal Peserta</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-2">
            @auth

            <div class="section-label">Menu</div>

            <a href="{{ route('peserta.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('peserta.panduan') }}"
               class="sidebar-link {{ request()->routeIs('peserta.panduan') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-[20px]">menu_book</span>
                <span>Panduan Tes</span>
            </a>

            <div class="section-label">Akun</div>

            <a href="{{ route('profil.index') }}"
               class="sidebar-link {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                <span>Profil Saya</span>
            </a>

            <div class="nav-divider"></div>

            @endauth
        </nav>

        {{-- Bottom User Card --}}
        <div class="user-bottom border-t border-[#e0e3e5] p-4 shrink-0">
            @auth
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#2C5F6F]/30 text-[#5fcfdf]" id="avatar-sidebar-{{ auth()->id() }}">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                             class="h-9 w-9 rounded-full object-cover" alt="Foto Profil"
                             onerror="handleAvatarError(this, 'avatar-sidebar-{{ auth()->id() }}-fallback')">
                        <span id="avatar-sidebar-{{ auth()->id() }}-fallback" class="hidden text-xs font-bold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                    @else
                        <span class="text-xs font-bold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                    @endif
                </div>
                <div class="user-card-actions min-w-0 flex-1 flex items-center gap-2">
                    <div class="min-w-0 flex-1 link-text">
                        <p class="user-name text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="user-email text-[11px] truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn transition-colors p-1" title="Keluar">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>

    </aside>

    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden"
         style="transition: margin-left 0.2s ease;"
         :style="{ marginLeft: sidebarOpen ? '280px' : '64px' }">

        {{-- HEADER --}}
        <header class="flex h-16 items-center justify-between border-b border-[#e0e3e5] bg-white px-6 shrink-0 sticky top-0 z-40">

            <div class="flex items-center gap-4">
                {{-- Hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="text-[#40484b] hover:text-[#2C5F6F] transition-colors p-1.5 rounded-md hover:bg-[#f2f4f6]"
                        title="Toggle Sidebar">
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>

                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-[13px] text-[#40484b]">
                    <a href="{{ route('peserta.dashboard') }}" class="hover:text-[#2C5F6F] transition-colors">Home</a>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <span class="font-semibold text-[#2C5F6F]">{{ $judulHalaman ?? 'Dashboard' }}</span>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                @auth
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = ! open"
                            class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm transition hover:bg-[#f2f4f6]">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ auth()->user()->foto_profil ? '' : 'bg-[#2C5F6F]/10 text-[#2C5F6F]' }}" id="avatar-header-{{ auth()->id() }}">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                     class="h-8 w-8 rounded-full object-cover" alt="Foto Profil"
                                     onerror="handleAvatarError(this, 'avatar-header-{{ auth()->id() }}-fallback')">
                                <span id="avatar-header-{{ auth()->id() }}-fallback" class="hidden text-xs font-semibold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                            @else
                                <span class="text-xs font-semibold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                            @endif
                        </div>
                        <span class="text-[#191c1e] hidden sm:inline">{{ auth()->user()->name }}</span>
                        <span class="material-symbols-outlined text-[16px] text-[#40484b]">expand_more</span>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full z-50 mt-1 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-[#e0e3e5] focus:outline-none">

                        <div class="p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ auth()->user()->foto_profil ? '' : 'bg-[#2C5F6F]/10 text-[#2C5F6F]' }}" id="avatar-dropdown-{{ auth()->id() }}">
                                    @if(auth()->user()->foto_profil)
                                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                             class="h-11 w-11 rounded-full object-cover" alt="Foto Profil"
                                             onerror="handleAvatarError(this, 'avatar-dropdown-{{ auth()->id() }}-fallback')">
                                        <span id="avatar-dropdown-{{ auth()->id() }}-fallback" class="hidden text-base font-semibold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                                    @else
                                        <span class="text-base font-semibold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[#191c1e] truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-[#40484b] truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <hr class="border-[#e0e3e5] mb-3">

                            <a href="{{ route('profil.index') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                                Edit Profil
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 transition">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>

        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>

    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Perhatian', text: @json(session('error')), confirmButtonColor: '#2C5F6F' });
        @endif
        @if(session('sukses'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('sukses')), timer: 3000, timerProgressBar: true, showConfirmButton: false, toast: true, position: 'top-end' });
        @endif
    </script>
    <script>
        function handleAvatarError(imgElement, fallbackId) {
            imgElement.style.display = 'none';
            const fallback = document.getElementById(fallbackId);
            if (fallback) fallback.style.display = 'flex';
        }
    </script>
    @stack('scripts')
</body>
</html>
