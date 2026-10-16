<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Masuk' }} — Sistem Psikotes HR</title>
    <link rel="preload" href="/fonts/MaterialSymbolsOutlined.woff2" as="font" type="font/woff2" crossorigin>
    <style>html,body{background:#fafafa}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>.material-symbols-outlined { font-variation-settings: 'wght' 300; }</style>
</head>
<body class="min-h-svh bg-[#fafafa] font-inter text-foreground antialiased">
@php
    $denganVisual = $showVisual ?? true;
    $alatTesAktif = $denganVisual ? \App\Models\AlatTes::where('is_aktif', true)->orderBy('nama')->pluck('nama') : collect();
    $flash = array_filter([
        'success'     => session('sukses'),
        'destructive' => session('error'),
    ]);
@endphp

@if ($denganVisual)
    <main class="grid min-h-svh lg:grid-cols-2">
        <section class="flex flex-col gap-6 bg-background p-6 md:p-10">
            <div class="flex flex-1 items-center justify-center">
                <div class="w-full max-w-sm">
                    <div class="mb-8">@include('layouts.partials.brand-auth')</div>
                    @foreach ($flash as $varian => $pesan)
                        <x-ui.alert :variant="$varian" :icon="$varian === 'success' ? 'check_circle' : 'error'" class="mb-6">{{ $pesan }}</x-ui.alert>
                    @endforeach
                    @yield('content')
                </div>
            </div>

            <p class="text-center text-xs text-muted-foreground">&copy; {{ date('Y') }} Jhonlin Group · Sistem Evaluasi Psikometri Internal</p>
        </section>

        <section class="relative hidden overflow-hidden bg-brand-navy lg:block">
            <img src="{{ asset('images/kegiatan-perusahaan.jpg') }}" alt="" class="absolute inset-0 size-full object-cover">
            <div class="absolute inset-0 bg-linear-to-t from-brand-navy/95 via-primary/70 to-primary/20"></div>

            <div class="relative flex h-full flex-col justify-end gap-8 p-10 text-white xl:p-14">
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-sm">
                    <span class="size-1.5 rounded-full bg-brand-green"></span>
                    Sistem Psikotes HR Online · Departemen HR
                </span>
                <div class="grid max-w-lg gap-3">
                    <h2 class="text-3xl font-semibold leading-tight tracking-tight xl:text-4xl">Psikotes online untuk kandidat dan karyawan Jhonlin Group.</h2>
                    <p class="text-sm leading-relaxed text-white/80">Kerjakan tes yang dijadwalkan HR langsung dari browser. Jawaban dinilai otomatis oleh sistem dan hasilnya tersimpan untuk ditinjau tim HR.</p>
                </div>
                @if ($alatTesAktif->isNotEmpty())
                    <div class="grid max-w-lg gap-3 border-t border-white/20 pt-6">
                        <p class="text-sm text-white/70">Alat tes yang tersedia</p>
                        <ul class="flex flex-wrap gap-2">
                            @foreach ($alatTesAktif as $nama)
                                <li class="rounded-md border border-white/20 bg-white/10 px-2.5 py-1 text-xs font-medium backdrop-blur-sm">{{ $nama }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
    </main>
@else
    <main class="flex min-h-svh flex-col items-center justify-center gap-6 p-4 sm:p-6 md:p-10">
        @include('layouts.partials.brand-auth')

        <x-ui.card class="w-full overflow-hidden {{ $lebarKartu ?? 'max-w-md' }}">
            <div class="h-1 bg-linear-to-r from-brand-navy via-primary to-brand-green"></div>
            <div class="p-6 sm:p-8">
                @foreach ($flash as $varian => $pesan)
                    <x-ui.alert :variant="$varian" :icon="$varian === 'success' ? 'check_circle' : 'error'" class="mb-6">{{ $pesan }}</x-ui.alert>
                @endforeach
                @yield('content')
            </div>
        </x-ui.card>

        <p class="text-center text-xs text-muted-foreground">&copy; {{ date('Y') }} Jhonlin Group · Sistem Evaluasi Psikometri Internal</p>
    </main>
@endif
</body>
</html>
