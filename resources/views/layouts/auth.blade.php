<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $judulHalaman ?? 'Sistem Psikotes HR' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('tambahan_head')
    <style>
        .auth-canvas {
            min-height: 100vh;
            display: flex;
        }
        .login-panel {
            width: 100%;
            max-width: 520px;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: white;
            z-index: 10;
        }
        .visual-panel {
            flex: 1;
            position: relative;
            background-color: #f7f9fb;
            overflow: hidden;
        }
        @media (max-width: 768px) {
            .visual-panel { display: none; }
            .login-panel { max-width: 100%; padding: 24px; }
        }
        .focused-input:focus-within {
            border-color: var(--color-psikotes);
            box-shadow: 0 0 0 2px var(--color-psikotes-glow);
        }
        /* hide visual panel on register page */
        .no-visual-panel { justify-content: center; }
        .no-visual-panel .visual-panel { display: none; }
        .no-visual-panel .login-panel { border-right: none; }
    </style>
</head>
<body class="min-h-screen bg-background text-on-surface font-body antialiased">
    <main class="auth-canvas @if(isset($showVisual) && !$showVisual) no-visual-panel @endif">
        <!-- Form Section -->
        <section class="login-panel border-r border-surface-variant">
            <div class="mb-10">
                <div class="flex items-center gap-4 mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Jhonlin Group" class="h-12 w-auto object-contain">
                    <h1 class="font-body font-bold text-xl text-psikotes uppercase tracking-widest">Psikotes HR</h1>
                </div>
                <p class="font-body text-sm text-on-surface-variant">Sistem Evaluasi Psikometri Internal</p>
            </div>

            @if (session('sukses'))
                <div class="mb-5 rounded-sm border border-emerald-600 bg-emerald-600 px-4 py-3 text-sm text-white">
                    {{ session('sukses') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-sm border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </section>

        <!-- Visual Background Section (login only) -->
        @if(!isset($showVisual) || $showVisual !== false)
        <section class="visual-panel rounded-r-xl overflow-hidden">
            <img src="{{ asset('images/kegiatan-perusahaan.jpg') }}" alt="Kegiatan Jhonlin Group" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            <div class="absolute bottom-12 left-12 right-12">
                <div class="max-w-md bg-white/10 backdrop-blur-md p-6 rounded-lg border border-white/20">
                    <h3 class="font-body font-semibold text-base text-white mb-2">Presisi Psikometrik</h3>
                    <p class="font-body text-sm text-white/80">Analisis data karyawan dengan standar institusi tinggi, memastikan objektivitas dalam setiap keputusan sumber daya manusia.</p>
                </div>
            </div>
        </section>
        @endif
    </main>
</body>
</html>
