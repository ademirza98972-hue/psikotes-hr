<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Dashboard Admin' }} — Sistem Psikotes HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F7F8FA] text-slate-800 antialiased">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="hidden w-64 shrink-0 flex-col border-r border-white/10 bg-[#162A30] md:flex">
        <div class="flex h-16 items-center border-b border-white/10 px-6">
            <img src="{{ asset('images/logo.png') }}" alt="Psikotes HR Logo" class="h-10 w-auto object-contain brightness-200 contrast-150">
            <div class="ml-2">
                <p class="text-sm font-semibold text-white">Psikotes HR</p>
                <p class="text-xs text-slate-400">PT Jhonlin Group</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4">

            {{-- GROUP: standalone menu items --}}
            @auth
                @if(auth()->user()->hasIzin('dashboard.lihat'))
                    <a href="{{ route('admin.dashboard') }}"
                       @class([
                           'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                           'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.dashboard'),
                           'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                       ])>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2"
                             @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.dashboard'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.dashboard')])>
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endif

                {{-- GROUP: Manajemen Karyawan --}}
                @if(auth()->user()->hasIzin('pengguna.lihat') || auth()->user()->hasIzin('data_karyawan.kelola'))
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Manajemen Karyawan</p>

                        @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                            <a href="{{ route('admin.data-karyawan.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.data-karyawan.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.data-karyawan.*'),
                               ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.data-karyawan.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.data-karyawan.*')])>
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>Data Karyawan</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('pengguna.lihat'))
                            <a href="{{ route('admin.akun-karyawan.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.akun-karyawan.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.akun-karyawan.*'),
                               ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.akun-karyawan.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.akun-karyawan.*')])>
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <polyline points="17 11 19 13 23 9"/>
                                </svg>
                                <span>Akun Karyawan</span>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- GROUP: Manajemen Kandidat --}}
                @if(auth()->user()->hasIzin('pengguna.lihat'))
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Manajemen Kandidat</p>

                        <a href="{{ route('admin.data-kandidat.index') }}"
                           @class([
                               'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                               'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.data-kandidat.*'),
                               'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.data-kandidat.*'),
                           ])>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2"
                                 @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.data-kandidat.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.data-kandidat.*')])>
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <line x1="20" y1="8" x2="20" y2="14"/>
                                <line x1="23" y1="11" x2="17" y2="11"/>
                            </svg>
                            <span>Data Kandidat</span>
                        </a>
                    </div>
                @endif

                {{-- GROUP: Manajemen Tes --}}
                @if(auth()->user()->hasIzin('soal.lihat') || auth()->user()->hasIzin('kategori_tes.kelola') || auth()->user()->hasIzin('hasil_tes.lihat'))
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Manajemen Tes</p>

                        @if(auth()->user()->hasIzin('soal.lihat'))
                            <a href="#"
                               class="group flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-white/5 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-slate-200">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <path d="M9 13l2 2 4-4"/>
                                </svg>
                                <span>Kelola Soal</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('kategori_tes.kelola'))
                            <a href="#"
                               class="group flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-white/5 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-slate-200">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                                    <polyline points="2 17 12 22 22 17"/>
                                    <polyline points="2 12 12 17 22 12"/>
                                </svg>
                                <span>Kategori Tes</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('hasil_tes.lihat'))
                            <a href="#"
                               class="group flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-white/5 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-slate-200">
                                    <line x1="12" y1="20" x2="12" y2="10"/>
                                    <line x1="18" y1="20" x2="18" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="16"/>
                                    <line x1="3" y1="20" x2="21" y2="20"/>
                                </svg>
                                <span>Hasil Tes</span>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- GROUP: Data Master --}}
                @if(auth()->user()->hasIzin('master_data.kelola'))
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Data Master</p>

                        <a href="{{ route('admin.departemen.index') }}"
                           @class([
                               'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                               'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.departemen.*'),
                               'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.departemen.*'),
                           ])>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2"
                                 @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.departemen.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.departemen.*')])>
                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18z"/>
                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                                <path d="M10 6h4"/>
                                <path d="M10 10h4"/>
                                <path d="M10 14h4"/>
                                <path d="M10 18h4"/>
                            </svg>
                            <span>Departemen</span>
                        </a>
                        <a href="{{ route('admin.posisi.index') }}"
                           @class([
                               'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                               'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.posisi.*'),
                               'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.posisi.*'),
                           ])>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2"
                                 @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.posisi.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.posisi.*')])>
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <circle cx="9" cy="10" r="2"/>
                                <path d="M15 8h2"/>
                                <path d="M15 12h2"/>
                                <path d="M7 16h10"/>
                            </svg>
                            <span>Posisi</span>
                        </a>
                    </div>
                @endif

                {{-- GROUP: Pengaturan Sistem --}}
                @if(auth()->user()->hasIzin('peran.kelola') || auth()->user()->hasIzin('pengguna_admin.kelola'))
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pengaturan Sistem</p>

                        @if(auth()->user()->hasIzin('peran.kelola'))
                            <a href="{{ route('admin.peran.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.peran.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.peran.*'),
                               ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.peran.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.peran.*')])>
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <span>Kelola Peran</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('pengguna_admin.kelola'))
                            <a href="{{ route('admin.pengguna-admin.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.pengguna-admin.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.pengguna-admin.*'),
                               ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     @class(['w-5 h-5 shrink-0', 'text-teal-300' => request()->routeIs('admin.pengguna-admin.*'), 'text-slate-400 group-hover:text-slate-200' => ! request()->routeIs('admin.pengguna-admin.*')])>
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                    <path d="M12 11v2"/>
                                    <circle cx="12" cy="15" r="1"/>
                                </svg>
                                <span>Kelola Admin/Staff</span>
                            </a>
                        @endif
                    </div>
                @endif
            @endauth
        </nav>
    </aside>

    {{-- MAIN --}}
    <div class="flex flex-1 flex-col">

        {{-- HEADER --}}
        <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-6 shadow-sm">
            <div class="flex items-center gap-3">
                <h1 class="text-base font-semibold text-slate-900">{{ $judulHalaman ?? 'Dashboard' }}</h1>
            </div>

            @auth
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = ! open"
                            class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm transition hover:bg-slate-50">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ auth()->user()->foto_profil ? '' : 'bg-[#2C5F6F]/10 text-[#2C5F6F]' }}">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                     class="h-8 w-8 rounded-full object-cover" alt="Foto Profil">
                            @else
                                <span class="text-xs font-semibold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                            @endif
                        </div>
                        <span class="text-slate-700">{{ auth()->user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full z-50 mt-1 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-slate-200 focus:outline-none">

                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ auth()->user()->foto_profil ? '' : 'bg-[#2C5F6F]/10 text-[#2C5F6F]' }}">
                                    @if(auth()->user()->foto_profil)
                                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                             class="h-11 w-11 rounded-full object-cover" alt="Foto Profil">
                                    @else
                                        <span class="text-base font-semibold">{{ mb_substr(explode(' ', auth()->user()->name)[0], 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                    <p class="mt-0.5 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                        {{ auth()->user()->peran->nama_peran ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            <hr class="my-3 border-slate-200">

                            <a href="{{ route('profil.index') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Profil
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 px-6 py-4">
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

</body>
</html>