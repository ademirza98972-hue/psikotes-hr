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
            @auth
                @if(auth()->user()->hasIzin('dashboard.lihat'))
                    <a href="{{ route('admin.dashboard') }}"
                       @class([
                           'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                           'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.dashboard'),
                           'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                       ])>
                        <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.dashboard') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">▦</span> Dashboard
                    </a>
                @endif

                @if(auth()->user()->hasIzin('pengguna.lihat') || auth()->user()->hasIzin('data_karyawan.kelola'))
                    <div class="mt-6 border-t border-white/10 pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Manajemen Karyawan</p>

                        @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                            <a href="{{ route('admin.data-karyawan.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.data-karyawan.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.data-karyawan.*'),
                               ])>
                                <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.data-karyawan.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">▤</span> Data Karyawan
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('pengguna.lihat'))
                            <a href="{{ route('admin.akun-karyawan.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.akun-karyawan.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.akun-karyawan.*'),
                               ])>
                                <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.akun-karyawan.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">◉</span> Akun Karyawan
                            </a>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->hasIzin('pengguna.lihat'))
                    <div class="mt-6 border-t border-white/10 pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Manajemen Kandidat</p>

                        <a href="{{ route('admin.data-kandidat.index') }}"
                           @class([
                               'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                               'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.data-kandidat.*'),
                               'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.data-kandidat.*'),
                           ])>
                            <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.data-kandidat.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">◐</span> Data Kandidat
                        </a>
                    </div>
                @endif

                @if(auth()->user()->hasIzin('soal.lihat') || auth()->user()->hasIzin('kategori_tes.kelola') || auth()->user()->hasIzin('hasil_tes.lihat'))
                    <div class="mt-6 border-t border-white/10 pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Manajemen Tes</p>

                        @if(auth()->user()->hasIzin('soal.lihat'))
                            <a href="#"
                               class="group flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-white/5 hover:text-white">
                                <span class="flex w-5 h-5 items-center justify-center text-lg text-slate-400 group-hover:text-slate-200">✎</span> Kelola Soal
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('kategori_tes.kelola'))
                            <a href="#"
                               class="group flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-white/5 hover:text-white">
                                <span class="flex w-5 h-5 items-center justify-center text-lg text-slate-400 group-hover:text-slate-200">▤</span> Kategori Tes
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('hasil_tes.lihat'))
                            <a href="#"
                               class="group flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm font-medium text-slate-300 transition-colors duration-150 hover:bg-white/5 hover:text-white">
                                <span class="flex w-5 h-5 items-center justify-center text-lg text-slate-400 group-hover:text-slate-200">▣</span> Hasil Tes
                            </a>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->hasIzin('master_data.kelola'))
                    <div class="mt-6 border-t border-white/10 pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Data Master</p>

                        <a href="{{ route('admin.departemen.index') }}"
                           @class([
                               'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                               'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.departemen.*'),
                               'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.departemen.*'),
                           ])>
                            <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.departemen.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">▦</span> Departemen
                        </a>
                        <a href="{{ route('admin.posisi.index') }}"
                           @class([
                               'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                               'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.posisi.*'),
                               'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.posisi.*'),
                           ])>
                            <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.posisi.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">☷</span> Posisi
                        </a>
                    </div>
                @endif

                @if(auth()->user()->hasIzin('peran.kelola') || auth()->user()->hasIzin('pengguna_admin.kelola'))
                    <div class="mt-6 border-t border-white/10 pt-4">
                        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Pengaturan Sistem</p>

                        @if(auth()->user()->hasIzin('peran.kelola'))
                            <a href="{{ route('admin.peran.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.peran.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.peran.*'),
                               ])>
                                <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.peran.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">⚙</span> Kelola Peran
                            </a>
                        @endif

                        @if(auth()->user()->hasIzin('pengguna_admin.kelola'))
                            <a href="{{ route('admin.pengguna-admin.index') }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm font-medium transition-colors duration-150',
                                   'border-teal-400 bg-white/10 text-white' => request()->routeIs('admin.pengguna-admin.*'),
                                   'border-transparent text-slate-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs('admin.pengguna-admin.*'),
                               ])>
                                <span class="flex w-5 h-5 items-center justify-center text-lg {{ request()->routeIs('admin.pengguna-admin.*') ? 'text-teal-300' : 'text-slate-400 group-hover:text-slate-200' }}">▣</span> Kelola Admin/Staff
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