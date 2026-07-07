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
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ ucfirst(auth()->user()->tipe_akun ?? '') }}</p>
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
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="mx-auto max-w-5xl px-6 py-10">
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

</body>
</html>