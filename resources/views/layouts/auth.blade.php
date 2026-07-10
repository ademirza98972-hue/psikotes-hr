<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Sistem Psikotes HR' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('tambahan_head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Sistem Psikotes HR" class="mx-auto h-40">
            </div>

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

            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>
</body>
</html>
