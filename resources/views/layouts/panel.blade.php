{{-- Kerangka bersama panel admin & portal peserta (sidebar shadcn, topbar, notifikasi).
     Dipakai lewat @extends dari layouts.admin / layouts.peserta yang menyiapkan:
       $menu      ['Grup' => [[rute, polaAktif, ikon, label, badge?], ...]]
       $beranda   nama route halaman utama
       $subjudul  teks kecil di bawah "Psikotes HR"
       $modeFokus true = tanpa sidebar & navigasi (halaman pengerjaan tes) --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Dashboard' }} — Sistem Psikotes HR</title>
    <link rel="preload" href="/fonts/MaterialSymbolsOutlined.woff2" as="font" type="font/woff2" crossorigin>
    <style>html,body{background:#fafafa}</style>
    <script>document.documentElement.style.setProperty('--sb-ml', localStorage.getItem('sidebarOpen') === 'false' ? '3.5rem' : '16rem')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            /* token lama — masih dipakai halaman yang belum dimigrasi */
            --psikotes: #00843D;
            --psikotes-light: #00A651;
            --psikotes-subtle: #E9F6EF;
            --surface-container-low: #f2f4f6;
            --surface-container-high: #e6e8ea;
            --outline-variant: #c0c8cb;
            --on-surface: #191c1e;
            --on-surface-variant: #40484b;
        }
        .material-symbols-outlined { font-variation-settings: 'wght' 300; }

        .sb-item {
            display: flex; align-items: center; gap: .5rem;
            height: 2rem; padding: 0 .5rem; border-radius: .375rem;
            font-size: .875rem; color: var(--sidebar-foreground);
            white-space: nowrap; overflow: hidden; transition: background .12s, color .12s;
        }
        .sb-item .material-symbols-outlined { font-size: 18px; flex-shrink: 0; }
        .sb-item:hover { background: var(--sidebar-accent); color: var(--sidebar-accent-foreground); }
        .sb-item.active { background: var(--sidebar-accent); color: var(--sidebar-accent-foreground); font-weight: 500; }
        .sb-label { height: 2rem; display: flex; align-items: center; padding: 0 .5rem; font-size: .75rem; font-weight: 500; color: var(--muted-foreground); white-space: nowrap; transition: opacity .15s, margin .2s; }

        @media (min-width: 1024px) {
            aside.collapsed .sb-label { margin-top: -2rem; opacity: 0; }
            aside.collapsed .sb-item { justify-content: center; padding: 0; }
            aside.collapsed .sb-item > :not(.material-symbols-outlined) { display: none; }
            aside.collapsed .sb-user > :not(:first-child) { display: none; }
            aside.collapsed .sb-user { justify-content: center; padding: 0; }
        }
    </style>
</head>
<body class="min-h-screen bg-[#fafafa] font-inter text-foreground antialiased">

@php
    $u = auth()->user();
    $fotoProfil = $u?->foto_profil ? asset('storage/' . $u->foto_profil) : null;
    $modeFokus = $modeFokus ?? false;

    $flash = [];
    if (session('sukses'))  $flash[] = ['type' => 'success', 'title' => 'Berhasil', 'text' => session('sukses')];
    if (session('error'))   $flash[] = ['type' => 'error', 'title' => 'Gagal', 'text' => session('error'), 'duration' => 6000];
    if (session('alert'))   $flash[] = ['type' => 'info', 'title' => 'Informasi', 'text' => session('alert')];
    if (session('warning')) $flash[] = ['type' => 'warning', 'title' => 'Perhatian', 'text' => session('warning'), 'duration' => 6000];
    if (session('info')) {
        $nik = collect(session('duplikat_nik', []))->map(fn ($n) => '<code class="rounded bg-muted px-1 font-mono text-xs">' . e($n) . '</code>')->implode(' ');
        $flash[] = ['type' => 'warning', 'title' => 'Perhatian', 'html' => e(session('info')) . ($nik ? '<div class="mt-2 flex flex-wrap gap-1">' . $nik . '</div>' : ''), 'duration' => 10000];
    }
    if ($errors->any()) {
        $flash[] = ['type' => 'error', 'title' => 'Terdapat kesalahan', 'duration' => 8000,
            'html' => '<ul>' . collect($errors->all())->map(fn ($m) => '<li>' . e($m) . '</li>')->implode('') . '</ul>'];
    }
@endphp

<div class="flex h-screen overflow-hidden"
     x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', mobileOpen: false }"
     x-init="$watch('sidebarOpen', v => { localStorage.setItem('sidebarOpen', v); document.documentElement.style.setProperty('--sb-ml', v ? '16rem' : '3.5rem') })"
     @keydown.escape.window="mobileOpen = false">

    @unless ($modeFokus)
    {{-- overlay mobile --}}
    <div x-show="mobileOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="mobileOpen = false"></div>

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-sidebar-border bg-sidebar transition-[width,transform] duration-200 lg:w-[var(--sb-ml)] lg:translate-x-0"
           :class="{ 'translate-x-0': mobileOpen, collapsed: !sidebarOpen }">

        <div class="flex h-14 shrink-0 items-center px-2">
            <a href="{{ route($beranda) }}" class="sb-user flex min-w-0 flex-1 items-center gap-2 rounded-md p-1">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="size-8 shrink-0 rounded-lg object-contain">
                <div class="grid min-w-0 leading-tight">
                    <span class="truncate text-sm font-semibold">Psikotes HR</span>
                    <span class="truncate text-xs text-muted-foreground">{{ $subjudul }}</span>
                </div>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-2">
            @foreach ($menu as $grup => $items)
                @continue (empty($items))
                <div class="mb-2">
                    <p class="sb-label">{{ $grup }}</p>
                    <div class="grid gap-0.5">
                        @foreach ($items as $item)
                            @php [$rute, $pola, $ikon, $label] = $item; $badge = $item[4] ?? 0; @endphp
                            <a href="{{ route($rute) }}" title="{{ $label }}" class="sb-item {{ request()->routeIs($pola) ? 'active' : '' }}"
                               @if (request()->routeIs($pola)) aria-current="page" @endif>
                                <span class="material-symbols-outlined">{{ $ikon }}</span>
                                <span class="truncate">{{ $label }}</span>
                                @if ($badge > 0)
                                    <span class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-md bg-sidebar-accent px-1 text-xs font-medium tabular-nums">{{ $badge }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        @auth
        <div class="shrink-0 p-2">
            <x-ui.dropdown-menu side="top" align="start" width="w-60">
                <x-slot:trigger>
                    <button type="button" class="sb-user flex w-full cursor-pointer items-center gap-2 rounded-md p-2 text-left transition-colors hover:bg-sidebar-accent">
                        <x-ui.avatar :src="$fotoProfil" :name="$u->name" class="rounded-lg" />
                        <div class="grid min-w-0 flex-1 leading-tight">
                            <span class="truncate text-sm font-medium">{{ $u->name }}</span>
                            <span class="truncate text-xs text-muted-foreground">{{ $u->peran->nama_peran ?? $u->email }}</span>
                        </div>
                        <span class="material-symbols-outlined text-[18px] text-muted-foreground">unfold_more</span>
                    </button>
                </x-slot:trigger>

                <div class="flex items-center gap-2 px-2 py-1.5">
                    <x-ui.avatar :src="$fotoProfil" :name="$u->name" class="rounded-lg" />
                    <div class="grid min-w-0 leading-tight">
                        <span class="truncate text-sm font-medium">{{ $u->name }}</span>
                        <span class="truncate text-xs text-muted-foreground">{{ $u->email ?? $u->id_karyawan }}</span>
                    </div>
                </div>
                <x-ui.separator class="-mx-1 my-1 w-auto!" />
                <x-ui.dropdown-item :href="route('profil.index')" icon="person">Profil Saya</x-ui.dropdown-item>
                <x-ui.separator class="-mx-1 my-1 w-auto!" />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-ui.dropdown-item type="submit" icon="logout">Keluar</x-ui.dropdown-item>
                </form>
            </x-ui.dropdown-menu>
        </div>
        @endauth
    </aside>
    @endunless

    {{-- MAIN --}}
    <div class="flex h-screen min-w-0 flex-1 flex-col transition-[margin] duration-200 {{ $modeFokus ? '' : 'lg:ml-[var(--sb-ml)]' }}">
        <header class="z-30 flex h-14 shrink-0 items-center gap-2 border-b border-border bg-background px-4 lg:px-6">
            @if ($modeFokus)
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="size-7 object-contain">
                <span class="text-sm font-semibold">Psikotes HR</span>
                <x-ui.separator orientation="vertical" class="mx-1 h-4!" />
                <span class="truncate text-sm text-muted-foreground">{{ $judulHalaman ?? 'Pengerjaan Tes' }}</span>
            @else
                <x-ui.button variant="ghost" size="icon-sm" class="-ml-1" title="Buka/tutup sidebar"
                             @click="window.innerWidth >= 1024 ? sidebarOpen = !sidebarOpen : mobileOpen = true">
                    <span class="material-symbols-outlined">dock_to_right</span>
                    <span class="sr-only">Buka/tutup sidebar</span>
                </x-ui.button>
                <x-ui.separator orientation="vertical" class="mx-1 h-4!" />
                <nav aria-label="breadcrumb" class="flex min-w-0 items-center gap-1.5 text-sm text-muted-foreground">
                    @unless (request()->routeIs($beranda))
                        <a href="{{ route($beranda) }}" class="hidden transition-colors hover:text-foreground sm:inline">Dashboard</a>
                        <span class="material-symbols-outlined hidden text-[16px] sm:inline">chevron_right</span>
                    @endunless
                    <span class="truncate font-medium text-foreground">{{ $judulHalaman ?? 'Dashboard' }}</span>
                </nav>
            @endif
        </header>

        <main class="flex-1 overflow-y-auto px-4 py-5 lg:px-6 lg:py-6">
            @yield('content')
        </main>
    </div>
</div>

<x-ui.notifikasi :flash="$flash" />
@stack('scripts')
</body>
</html>
