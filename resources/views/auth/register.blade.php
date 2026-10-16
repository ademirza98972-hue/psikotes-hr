@extends('layouts.auth', ['judulHalaman' => 'Daftar Akun', 'showVisual' => false, 'lebarKartu' => 'max-w-2xl'])

@php
    $field = fn (string $nama) => $errors->has($nama) ? 'true' : null;
    $posisiPerDepartemen = $posisi->groupBy(fn ($p) => $p->departemen?->nama_departemen ?? 'Lainnya');
@endphp

@section('content')
<div class="grid gap-6">
    <div class="grid gap-1.5">
        <h1 class="text-2xl font-semibold tracking-tight">Daftar sebagai kandidat</h1>
        <p class="text-sm text-muted-foreground">Lengkapi data berikut. Akun aktif setelah diverifikasi oleh HR.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="grid gap-6">
        @csrf
        <input type="hidden" name="tipe_akun" value="kandidat">

        <fieldset class="grid gap-4">
            <legend class="mb-4 flex items-center gap-2 text-sm font-medium">
                <span class="flex size-6 items-center justify-center rounded-full bg-primary/10 text-xs text-primary">1</span>
                Data diri
            </legend>
            <div class="grid items-start gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <x-ui.label for="name">Nama lengkap</x-ui.label>
                    <x-ui.input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                placeholder="Sesuai KTP" class="h-10!" :aria-invalid="$field('name')" />
                    @error('name')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="nik_kandidat">NIK KTP</x-ui.label>
                    <x-ui.input id="nik_kandidat" name="nik_kandidat" value="{{ old('nik_kandidat') }}" required
                                maxlength="16" inputmode="numeric" pattern="[0-9]{16}" title="16 digit angka"
                                placeholder="16 digit NIK" class="h-10!" :aria-invalid="$field('nik_kandidat')" />
                    @error('nik_kandidat')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="tanggal_lahir">Tanggal lahir <span class="font-normal text-muted-foreground">(opsional)</span></x-ui.label>
                    <x-ui.input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir') }}"
                                class="h-10!" :aria-invalid="$field('tanggal_lahir')" />
                    @error('tanggal_lahir')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="jenis_kelamin">Jenis kelamin</x-ui.label>
                    <x-ui.select id="jenis_kelamin" name="jenis_kelamin" required class="h-10!" :aria-invalid="$field('jenis_kelamin')">
                        <option value="">Pilih</option>
                        <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                    </x-ui.select>
                    @error('jenis_kelamin')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
            </div>
        </fieldset>

        <x-ui.separator />

        <fieldset class="grid gap-4">
            <legend class="mb-4 flex items-center gap-2 text-sm font-medium">
                <span class="flex size-6 items-center justify-center rounded-full bg-primary/10 text-xs text-primary">2</span>
                Kontak &amp; lamaran
            </legend>
            <div class="grid items-start gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <x-ui.label for="email">Email</x-ui.label>
                    <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                                placeholder="nama@email.com" class="h-10!" :aria-invalid="$field('email')" />
                    @error('email')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="no_hp">No. HP</x-ui.label>
                    <x-ui.input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" required autocomplete="tel"
                                placeholder="08xxxxxxxxxx" class="h-10!" :aria-invalid="$field('no_hp')" />
                    @error('no_hp')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="posisi_dilamar">Posisi yang dilamar</x-ui.label>
                    <x-ui.select id="posisi_dilamar" name="posisi_dilamar" required class="h-10!" :aria-invalid="$field('posisi_dilamar')">
                        <option value="">Pilih posisi</option>
                        @foreach ($posisiPerDepartemen as $departemen => $daftar)
                            <optgroup label="{{ $departemen }}">
                                @foreach ($daftar as $p)
                                    <option value="{{ $p->id }}" @selected(old('posisi_dilamar') == $p->id)>{{ $p->nama_posisi }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-ui.select>
                    @error('posisi_dilamar')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="pendidikan_terakhir">Pendidikan terakhir</x-ui.label>
                    <x-ui.input id="pendidikan_terakhir" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir') }}" required
                                placeholder="Contoh: S1 Teknik Informatika" class="h-10!" :aria-invalid="$field('pendidikan_terakhir')" />
                    @error('pendidikan_terakhir')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
            </div>
        </fieldset>

        <x-ui.separator />

        <fieldset class="grid gap-4">
            <legend class="mb-4 flex items-center gap-2 text-sm font-medium">
                <span class="flex size-6 items-center justify-center rounded-full bg-primary/10 text-xs text-primary">3</span>
                Keamanan akun
            </legend>
            <div class="grid items-start gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <x-ui.label for="password">Password</x-ui.label>
                    <x-ui.input-password id="password" name="password" required minlength="8" autocomplete="new-password"
                                         placeholder="Minimal 8 karakter" class="h-10!" :aria-invalid="$field('password')" />
                    @error('password')<p class="text-sm text-destructive">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="password_confirmation">Ulangi password</x-ui.label>
                    <x-ui.input-password id="password_confirmation" name="password_confirmation" required minlength="8"
                                         autocomplete="new-password" placeholder="Ketik ulang password" class="h-10!" />
                </div>
            </div>
        </fieldset>

        <x-ui.button type="submit" size="lg" class="w-full">
            Buat akun <span class="material-symbols-outlined">arrow_forward</span>
        </x-ui.button>
    </form>

    <p class="text-center text-sm text-muted-foreground">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-primary underline-offset-4 hover:underline">Masuk</a>
    </p>
</div>
@endsection
