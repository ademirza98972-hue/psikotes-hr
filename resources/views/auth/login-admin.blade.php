@extends('layouts.auth', ['judulHalaman' => 'Login Admin'])

@section('content')
<div class="grid gap-6">
    <div class="grid gap-1.5 text-center">
        <h1 class="text-2xl font-semibold tracking-tight">Panel Admin</h1>
        <p class="text-sm text-muted-foreground">Masuk dengan akun Admin atau HR untuk mengelola tes.</p>
    </div>

    @include('auth.partials.pilih-portal')

    <form method="POST" action="{{ route('login.admin') }}" class="grid gap-4">
        @csrf

        <div class="grid gap-2">
            <x-ui.label for="email">Email</x-ui.label>
            <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        placeholder="nama@perusahaan.com" class="h-10!"
                        :aria-invalid="$errors->has('email') ? 'true' : null" />
            @error('email')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
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
            Masuk ke Dashboard <span class="material-symbols-outlined">arrow_forward</span>
        </x-ui.button>
    </form>
</div>
@endsection
