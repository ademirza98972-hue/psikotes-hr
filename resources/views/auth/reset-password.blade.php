@extends('layouts.auth', ['judulHalaman' => 'Reset Password', 'showVisual' => false])

@section('content')
<div class="grid gap-6">
    <div class="grid gap-1.5">
        <span class="mb-2 flex size-10 items-center justify-center rounded-full bg-primary/10 text-primary">
            <span class="material-symbols-outlined">key</span>
        </span>
        <h1 class="text-2xl font-semibold tracking-tight">Buat password baru</h1>
        <p class="text-sm text-muted-foreground">Gunakan minimal 8 karakter yang belum pernah Anda pakai.</p>
    </div>

    <form method="POST" action="{{ route('password.simpan') }}" class="grid gap-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="grid gap-2">
            <x-ui.label for="email">Email</x-ui.label>
            <x-ui.input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email"
                        class="h-10!" :aria-invalid="$errors->has('email') ? 'true' : null" />
            @error('email')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-2">
            <x-ui.label for="password">Password baru</x-ui.label>
            <x-ui.input-password id="password" name="password" required minlength="8" autocomplete="new-password" autofocus
                                 placeholder="Minimal 8 karakter" class="h-10!"
                                 :aria-invalid="$errors->has('password') ? 'true' : null" />
            @error('password')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-2">
            <x-ui.label for="password_confirmation">Ulangi password baru</x-ui.label>
            <x-ui.input-password id="password_confirmation" name="password_confirmation" required minlength="8"
                                 autocomplete="new-password" placeholder="Ketik ulang password" class="h-10!" />
        </div>

        <x-ui.button type="submit" size="lg" class="w-full">
            Simpan password baru <span class="material-symbols-outlined">check</span>
        </x-ui.button>
    </form>

    <x-ui.button variant="ghost" :href="route('login')" class="justify-self-center">
        <span class="material-symbols-outlined">arrow_back</span> Kembali ke halaman masuk
    </x-ui.button>
</div>
@endsection
