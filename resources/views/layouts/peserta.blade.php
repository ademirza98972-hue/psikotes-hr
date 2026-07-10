<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Dashboard Peserta' }} — Sistem Psikotes HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F7F8FA] text-slate-800 antialiased">

    {{-- HEADER --}}
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-[#2C5F6F] text-sm font-semibold text-white">P</div>
                <p class="text-sm font-semibold text-slate-900">Psikotes HR</p>
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
                                        {{ ucfirst(auth()->user()->tipe_akun ?? '-') }}
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
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="mx-auto max-w-5xl px-6 py-10">
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

</body>
</html>