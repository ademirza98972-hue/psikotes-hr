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

            /* Dark sidebar tokens */
            --sb-bg: #0d1629;
            --sb-border: rgba(255,255,255,0.07);
            --sb-text: #8fa8b8;
            --sb-text-hover: #c5dde6;
            --sb-text-active: #7fd8ea;
            --sb-active-bg: rgba(95,207,223,0.22);
            --sb-hover-bg: rgba(255,255,255,0.055);
            --sb-label: #344d5c;
            --sb-divider: rgba(255,255,255,0.06);
        }

        /* Sidebar base */
        aside { background: var(--sb-bg) !important; border-right: 1px solid var(--sb-border) !important; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            margin: 1px 10px;
            color: var(--sb-text);
            font-size: 13.5px;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.12s ease, color 0.12s ease;
        }
        .sidebar-link:hover {
            background: var(--sb-hover-bg);
            color: var(--sb-text-hover);
        }
        .sidebar-link.active {
            background: var(--sb-active-bg);
            color: var(--sb-text-active);
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--sb-text-active);
        }
        .sidebar-link.active .material-symbols-outlined {
            color: var(--sb-text-active);
        }
        .section-label {
            padding: 14px 22px 5px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: var(--sb-label);
        }
        .nav-divider {
            height: 1px;
            background: var(--sb-divider);
            margin: 6px 22px;
        }

        /* Collapsed sidebar */
        aside.collapsed .sidebar-link {
            justify-content: center;
            padding: 9px 0;
            margin: 1px 0;
            border-radius: 0;
            gap: 0;
        }
        aside.collapsed .sidebar-link:hover {
            background: var(--sb-hover-bg);
            border-radius: 0;
        }
        /* Active item in collapsed: full-width "tab" pointing right */
        aside.collapsed .sidebar-link.active {
            background: var(--sb-active-bg);
            border-radius: 10px 0 0 10px;
            margin: 1px 0 1px 8px;
            position: relative;
            overflow: visible;
        }
        /* Curved notch above active — sb-bg colored square, bottom-left corner rounded */
        aside.collapsed .sidebar-link.active::before {
            content: '';
            position: absolute;
            top: -12px; right: 0;
            width: 12px; height: 12px;
            background: var(--sb-bg);
            border-bottom-left-radius: 12px;
            pointer-events: none;
            z-index: 2;
        }
        /* Curved notch below active — sb-bg colored square, top-left corner rounded */
        aside.collapsed .sidebar-link.active::after {
            content: '';
            position: absolute;
            bottom: -12px; right: 0;
            width: 12px; height: 12px;
            background: var(--sb-bg);
            border-top-left-radius: 12px;
            pointer-events: none;
            z-index: 2;
        }
        aside.collapsed .sidebar-link > span:not(.material-symbols-outlined) { display: none; }
        aside.collapsed .sidebar-link .ml-auto { display: none; }
        aside.collapsed .section-label { display: none; }
        aside.collapsed .nav-divider { margin: 6px 8px; }
        aside.collapsed .link-text { display: none; }
        aside.collapsed .sb-logo-text { display: none; }
        aside.collapsed .user-card-actions { display: none; }
        aside.collapsed .sb-header-expanded { display: none !important; }
        aside:not(.collapsed) .sb-header-collapsed { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-[#f7f9fb] font-body antialiased">


<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" x-init="$watch('sidebarOpen', v => localStorage.setItem('sidebarOpen', v))">

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col overflow-y-auto overflow-x-hidden"
           style="transition: width 0.2s ease;"
           :style="{ width: sidebarOpen ? '280px' : '64px' }"
           :class="{ collapsed: !sidebarOpen }">

        {{-- Logo: Expanded --}}
        <div class="sb-header-expanded flex items-center justify-between gap-2 px-4 shrink-0" style="height:64px; border-bottom: 1px solid var(--sb-border);">
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain shrink-0">
                <div class="whitespace-nowrap min-w-0">
                    <p class="text-sm font-bold leading-none" style="color: #e2e8f0;">Psikotes HR</p>
                    <p class="text-[11px] mt-0.5" style="color: var(--sb-text);">Admin Panel</p>
                </div>
            </div>
            <button @click="sidebarOpen = false"
                    class="shrink-0 flex items-center justify-center w-7 h-7 rounded-md"
                    style="color: var(--sb-text);"
                    onmouseover="this.style.color='var(--sb-text-hover)';this.style.background='var(--sb-hover-bg)'"
                    onmouseout="this.style.color='var(--sb-text)';this.style.background=''">
                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
            </button>
        </div>

        {{-- Logo: Collapsed --}}
        <div class="sb-header-collapsed flex flex-col items-center justify-center gap-2 shrink-0" style="height:64px; border-bottom: 1px solid var(--sb-border);">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain">
            <button @click="sidebarOpen = true"
                    class="flex items-center justify-center w-6 h-4 rounded"
                    style="color: var(--sb-text);"
                    onmouseover="this.style.color='var(--sb-text-hover)'"
                    onmouseout="this.style.color='var(--sb-text)'">
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-2">
            @auth

            {{-- MENU UTAMA --}}
            @if(auth()->user()->hasIzin('dashboard.lihat') || auth()->user()->hasIzin('data_karyawan.kelola') || auth()->user()->hasIzin('pengguna.lihat'))
            <div class="section-label">Menu Utama</div>
            @endif

            @if(auth()->user()->hasIzin('dashboard.lihat'))
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>Dashboard</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                <a href="{{ route('admin.data-karyawan.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.data-karyawan.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    <span>Data Karyawan</span>
                </a>
            @endif

            @if(auth()->user()->hasIzin('pengguna.lihat'))
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

            @if(auth()->user()->hasIzin('soal.lihat') || auth()->user()->hasIzin('kategori_tes.kelola') || auth()->user()->hasIzin('hasil_tes.lihat'))
            <div class="nav-divider"></div>

            {{-- TES & ASSESSMENT --}}
            <div class="section-label">Tes &amp; Assessment</div>

            @if(auth()->user()->hasIzin('soal.lihat') || auth()->user()->hasIzin('kategori_tes.kelola'))
                <a href="{{ route('admin.alat-tes.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.alat-tes.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">quiz</span>
                    <span>Alat Tes</span>
                </a>

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
            @endif

            @if(auth()->user()->hasIzin('peran.kelola') || auth()->user()->hasIzin('pengguna_admin.kelola') || auth()->user()->hasIzin('master_data.kelola') || auth()->user()->hasIzin('data_terhapus.kelola'))
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

            @endif

            @endauth
        </nav>

        {{-- Bottom User Card --}}
        <div class="p-4 shrink-0" style="border-top: 1px solid var(--sb-border);">
            @auth
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                     style="background: rgba(44,95,111,0.4); color: var(--sb-text-active);"
                     id="avatar-sidebar-{{ auth()->id() }}">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                             class="h-9 w-9 rounded-full object-cover" alt="Foto Profil"
                             onerror="handleAvatarError(this, 'avatar-sidebar-{{ auth()->id() }}-fallback')">
                        <span id="avatar-sidebar-{{ auth()->id() }}-fallback" class="hidden text-xs font-bold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                    @else
                        {{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}
                    @endif
                </div>
                <div class="user-card-actions min-w-0 flex-1 flex items-center gap-2">
                    <div class="min-w-0 flex-1 link-text">
                        <p class="text-sm font-semibold truncate" style="color: #e2e8f0;">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] truncate" style="color: var(--sb-text);">{{ auth()->user()->peran->nama_peran ?? '-' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="transition-colors p-1" style="color: var(--sb-text);"
                                onmouseover="this.style.color='var(--sb-text-active)'" onmouseout="this.style.color='var(--sb-text)'"
                                title="Keluar">
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
        <header class="flex h-16 items-center justify-between bg-white px-6 shrink-0 sticky top-0 z-40" style="border-bottom: 1px solid #e5e7eb; box-shadow: 0 2px 12px rgba(0,0,0,0.10);">

            <div class="flex items-center gap-3">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-1.5 text-[13px] text-[#40484b]">
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
        <main class="flex-1 overflow-y-auto px-4 py-5">
            @yield('content')
        </main>

    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function handleAvatarError(imgElement, fallbackId) {
            imgElement.style.display = 'none';
            const fallback = document.getElementById(fallbackId);
            if (fallback) fallback.style.display = 'flex';
        }

        @if(session('sukses'))
        Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('sukses')), timer: 2500, timerProgressBar: true, showConfirmButton: false });
        @endif
        @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Perhatian', text: @json(session('error')), showConfirmButton: true, confirmButtonColor: '#2C5F6F' });
        @endif

        // Global interceptor: ganti semua onsubmit="return confirm(...)" dengan SweetAlert2
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[onsubmit]').forEach(form => {
                const attr = form.getAttribute('onsubmit') || '';
                const match = attr.match(/confirm\(['"`]([\s\S]*?)['"`]\)/);
                if (!match) return;
                const pesan = match[1].replace(/\\'/g, "'").replace(/\\"/g, '"');
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Konfirmasi',
                        text: pesan,
                        showCancelButton: true,
                        confirmButtonColor: '#2C5F6F',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                    }).then(r => { if (r.isConfirmed) form.submit(); });
                });
            });
        });
        @if(session('alert'))
        Swal.fire({ icon: 'info', title: 'Informasi', text: @json(session('alert')), timer: 3000, timerProgressBar: true, showConfirmButton: false });
        @endif
        @if(session('info'))
        @php
            $nikList = session('duplikat_nik', []);
            $nikHtml = !empty($nikList)
                ? '<div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:4px;justify-content:center">'
                  . collect($nikList)->map(fn($n) => '<span style="background:#fef3c7;border:1px solid #fde68a;border-radius:4px;padding:2px 8px;font-family:monospace;font-size:12px;font-weight:600;color:#92400e">'.$n.'</span>')->implode('')
                  . '</div>'
                : '';
        @endphp
        Swal.fire({ icon: 'warning', title: 'Perhatian', html: @json(session('info') . $nikHtml), showConfirmButton: true, confirmButtonColor: '#2C5F6F' });
        @endif
        @if(session('warning'))
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: @json(session('warning')), showConfirmButton: true, confirmButtonColor: '#2C5F6F' });
        @endif
        @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Terdapat Kesalahan', html: '<ul style="text-align:left;list-style:disc;padding-left:1.2rem">' + @json($errors->all()).map(e => `<li>${e}</li>`).join('') + '</ul>', confirmButtonColor: '#2C5F6F' });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
