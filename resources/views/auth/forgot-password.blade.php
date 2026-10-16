@extends('layouts.auth', ['judulHalaman' => 'Lupa Password', 'showVisual' => false])

@section('content')
<div class="grid gap-6">
    <div class="grid gap-1.5">
        <span class="mb-2 flex size-10 items-center justify-center rounded-full bg-primary/10 text-primary">
            <span class="material-symbols-outlined">lock_reset</span>
        </span>
        <h1 class="text-2xl font-semibold tracking-tight">Lupa password?</h1>
        <p class="text-sm text-muted-foreground">Masukkan email akun Anda, kami kirimkan tautan untuk membuat password baru.</p>
    </div>

    <form method="POST" action="{{ route('password.kirim') }}" class="grid gap-4">
        @csrf

        <div class="grid gap-2">
            <x-ui.label for="email">Email</x-ui.label>
            <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        placeholder="nama@email.com" class="h-10!"
                        :aria-invalid="$errors->has('email') ? 'true' : null" />
            @error('email')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
        </div>

        <x-ui.button type="submit" size="lg" class="w-full">
            Kirim tautan reset <span class="material-symbols-outlined">send</span>
        </x-ui.button>
    </form>

    <x-ui.button variant="ghost" :href="route('login')" class="justify-self-center">
        <span class="material-symbols-outlined">arrow_back</span> Kembali ke halaman masuk
    </x-ui.button>
</div>
@endsection
