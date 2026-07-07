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
    <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white md:flex">
        <div class="flex h-16 items-center border-b border-slate-200 px-6">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-[#2C5F6F] text-sm font-semibold text-white">P</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Psikotes HR</p>
                    <p class="text-xs text-slate-500">PT Jhonlin Group</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4">
            @auth
                @if(auth()->user()->hasIzin('dashboard.lihat'))
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-[#2C5F6F]/10 text-[#2C5F6F]' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="text-base">▦</span> Dashboard
                    </a>
                @endif

                @if(auth()->user()->hasIzin('pengguna.lihat'))
                    <a href="#"
                       class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <span class="text-base">◉</span> Kelola Pengguna
                    </a>
                @endif

                @if(auth()->user()->hasIzin('soal.lihat'))
                    <a href="#"
                       class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <span class="text-base">✎</span> Kelola Soal
                    </a>
                @endif

                @if(auth()->user()->hasIzin('kategori_tes.kelola'))
                    <a href="#"
                       class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <span class="text-base">▤</span> Kategori Tes
                    </a>
                @endif

                @if(auth()->user()->hasIzin('hasil_tes.lihat'))
                    <a href="#"
                       class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <span class="text-base">▣</span> Hasil Tes
                    </a>
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
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->peran->nama_peran ?? '-' }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#2C5F6F]/10 text-sm font-semibold text-[#2C5F6F]">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Keluar
                        </button>
                    </form>
                </div>
            @endauth
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 px-6 py-8">
            @if (session('sukses'))
                <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('sukses') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</div>

</body>
</html>