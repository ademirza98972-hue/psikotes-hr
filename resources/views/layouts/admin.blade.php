<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Dashboard Admin' }} — Sistem Psikotes HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --psikotes: #2C5F6F;
            --psikotes-light: #4A8A9B;
            --psikotes-subtle: #E8F0F2;
            --surface-container-low: #f2f4f6;
            --surface-container-high: #e6e8ea;
            --outline-variant: #c0c8cb;
            --on-surface: #191c1e;
            --on-surface-variant: #40484b;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: var(--on-surface-variant);
            font-size: 14px;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
        }
        .sidebar-link:hover {
            background: var(--surface-container-high);
            color: var(--on-surface);
        }
        .sidebar-link.active {
            background: color-mix(in srgb, var(--psikotes) 8%, white);
            color: var(--psikotes);
            border-left-color: var(--psikotes);
            font-weight: 600;
        }
        .sidebar-link.active .material-symbols-outlined {
            color: var(--psikotes);
        }
        .section-label {
            padding: 16px 24px 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #919eab;
        }
        .nav-divider {
            height: 1px;
            background: var(--outline-variant);
            margin: 8px 24px;
        }
    </style>
</head>
<body class="min-h-screen bg-[#f7f9fb] font-body antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-[280px] flex flex-col bg-white border-r border-[#e0e3e5] overflow-y-auto">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 h-16 border-b border-[#e0e3e5] shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Psikotes HR Logo" class="h-9 w-auto object-contain">
            <div>
                <p class="text-sm font-bold text-[#2C5F6F] leading-none">Psikotes HR</p>
                <p class="text-[11px] text-[#40484b] mt-0.5">Admin Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-2">
            @auth

            {{-- MENU UTAMA --}}
            <div class="section-label">Menu Utama</div>

            @if(auth()->user()->hasIzin('dashboard.lihat'))
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>Dashboard</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('data_karyawan.kelola') || auth()->user()->hasIzin('pengguna.lihat'))
                <a href="{{ route('admin.data-karyawan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.data-karyawan.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    <span>Data Karyawan</span>
                </a>
                
                <a href="{{ route('admin.akun-karyawan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.akun-karyawan.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                    <span>Akun Karyawan</span>
                </a>

                
            @endif

            @if(auth()->user()->hasIzin('pengguna.lihat'))
                <a href="{{ route('admin.data-kandidat.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.data-kandidat.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">person_search</span>
                    <span>Akun Kandidat</span>
                </a>
            @endif

            <div class="nav-divider"></div>

            {{-- TES & ASSESSMENT --}}
            <div class="section-label">Tes &amp; Assessment</div>

            @if(auth()->user()->hasIzin('soal.lihat') || auth()->user()->hasIzin('kategori_tes.kelola'))
                <a href="{{ route('admin.bank-soal.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.bank-soal.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">database</span>
                    <span>Bank Soal</span>
                </a>

                <a href="{{ route('admin.penjadwalan-tes.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.penjadwalan-tes.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                    <span>Penjadwalan Tes</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('hasil_tes.lihat'))
                <a href="{{ route('admin.hasil-tes.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.hasil-tes.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">analytics</span>
                    <span>Hasil Tes</span>
                </a>
            @endif

            <div class="nav-divider"></div>

            {{-- SYSTEM --}}
            <div class="section-label">System</div>

            @if(auth()->user()->hasIzin('peran.kelola'))
                <a href="{{ route('admin.peran.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.peran.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                    <span>Kelola Peran &amp; Izin</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('pengguna_admin.kelola'))
                <a href="{{ route('admin.pengguna-admin.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.pengguna-admin.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">settings_suggest</span>
                    <span>Kelola Admin/Staff</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('master_data.kelola'))
                <a href="{{ route('admin.departemen.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.departemen.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">apartment</span>
                    <span>Data Departemen</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('data_terhapus.kelola'))
                @php
                    $jumlahTrash = (int) \App\Models\User::where('tipe_akun','karyawan')->onlyTrashed()->count()
                        + (int) \App\Models\User::where('tipe_akun','kandidat')->onlyTrashed()->count()
                        + (int) \App\Models\User::where('tipe_akun','custom')->onlyTrashed()->count()
                        + (int) \App\Models\DataKaryawan::onlyTrashed()->count()
                        + (int) \App\Models\Departemen::onlyTrashed()->count()
                        + (int) \App\Models\Posisi::onlyTrashed()->count()
                        + (int) \App\Models\Peran::onlyTrashed()->count();
                @endphp
                <a href="{{ route('admin.data-terhapus.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.data-terhapus.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">delete_outline</span>
                    <span>Data Terhapus</span>
                    @if ($jumlahTrash > 0)
                        <span class="ml-auto rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $jumlahTrash }}</span>
                    @endif
                </a>
            @endif

            @endauth
        </nav>

        {{-- Bottom User Card --}}
        <div class="border-t border-[#e0e3e5] p-4 shrink-0">
            @auth
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ auth()->user()->foto_profil ? '' : 'bg-[#2C5F6F]/10 text-[#2C5F6F]' }}" id="avatar-sidebar-{{ auth()->id() }}">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                             class="h-9 w-9 rounded-full object-cover" alt="Foto Profil"
                             onerror="handleAvatarError(this, 'avatar-sidebar-{{ auth()->id() }}-fallback')">
                        <span id="avatar-sidebar-{{ auth()->id() }}-fallback" class="hidden text-xs font-bold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                    @else
                        <span>{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-[#191c1e] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-[#40484b] truncate">{{ auth()->user()->peran->nama_peran ?? '-' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[#40484b] hover:text-[#2C5F6F] transition-colors p-1" title="Keluar">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </button>
                </form>
            </div>
            @endauth
        </div>

    </aside>

    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col ml-[280px] min-h-screen overflow-hidden">

        {{-- HEADER --}}
        <header class="flex h-16 items-center justify-between border-b border-[#e0e3e5] bg-white px-6 shrink-0 sticky top-0 z-40">

            <div class="flex items-center gap-4">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-[13px] text-[#40484b]">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#2C5F6F] transition-colors">Home</a>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <span class="font-semibold text-[#2C5F6F]">{{ $judulHalaman ?? 'Dashboard' }}</span>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 border-r border-[#e0e3e5] pr-4">
                    <button class="text-[#40484b] hover:text-[#2C5F6F] transition-colors p-1.5 rounded-md hover:bg-[#f2f4f6]" title="Notifikasi">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                    </button>
                    <button class="text-[#40484b] hover:text-[#2C5F6F] transition-colors p-1.5 rounded-md hover:bg-[#f2f4f6]" title="Bantuan">
                        <span class="material-symbols-outlined text-[20px]">help_outline</span>
                    </button>
                </div>

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
            @if (session('sukses'))
                <div class="mb-4 rounded-md border border-emerald-600 bg-emerald-600 px-4 py-3 text-sm text-white">
                    {{ session('sukses') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</div>

    <script>
        function handleAvatarError(imgElement, fallbackId) {
            imgElement.style.display = 'none';
            const fallback = document.getElementById(fallbackId);
            if (fallback) fallback.style.display = 'flex';
        }
    </script>
</body>
</html>
