@extends('layouts.auth', ['judulHalaman' => 'Login Peserta'])

@section('content')
<div class="grid gap-6">
    <div class="grid gap-1.5 text-center">
        <h1 class="text-2xl font-semibold tracking-tight">Selamat datang</h1>
        <p class="text-sm text-muted-foreground">Masuk untuk mengerjakan tes psikologi Anda.</p>
    </div>

    @include('auth.partials.pilih-portal')

    {{-- Dari login admin dengan akun peserta (AutentikasiController::loginAdmin) --}}
    @error('email')<x-ui.alert variant="destructive" icon="error">{{ $message }}</x-ui.alert>@enderror

    <form method="POST" action="{{ route('login') }}" class="grid gap-4">
        @csrf

        <div class="grid gap-2">
            <x-ui.label for="identifier">Email / ID Karyawan</x-ui.label>
            <x-ui.input id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus autocomplete="username"
                        placeholder="nama@email.com atau 2026001" class="h-10!"
                        :aria-invalid="$errors->has('identifier') ? 'true' : null" />
            @error('identifier')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-2">
            <div class="flex items-center justify-between">
                <x-ui.label for="password">Kata sandi</x-ui.label>
                <a href="{{ route('password.request') }}" class="text-sm text-primary underline-offset-4 hover:underline">Lupa password?</a>
            </div>
            <x-ui.input-password id="password" name="password" required autocomplete="current-password" class="h-10!"
                                 :aria-invalid="$errors->has('password') ? 'true' : null" />
            @error('password')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
        </div>

        <x-ui.label for="ingat_saya" class="font-normal text-muted-foreground">
            <x-ui.checkbox id="ingat_saya" name="ingat_saya" value="1" :checked="old('ingat_saya')" />
            Ingat saya di perangkat ini
        </x-ui.label>

        <x-ui.button type="submit" size="lg" class="w-full">
            Masuk <span class="material-symbols-outlined">arrow_forward</span>
        </x-ui.button>
    </form>

    <p class="text-center text-sm text-muted-foreground">
        Kandidat baru?
        <a href="{{ route('register') }}" class="font-medium text-primary underline-offset-4 hover:underline">Daftar akun</a>
    </p>
</div>
@endsection
