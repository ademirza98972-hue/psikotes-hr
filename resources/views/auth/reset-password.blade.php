@extends('layouts.auth', ['judulHalaman' => 'Reset Password', 'showVisual' => false])

@section('content')
<div x-data="{ showPwd: false, showPwdConfirm: false }" class="space-y-6">

    <div class="space-y-1">
        <h2 class="font-body font-semibold text-2xl text-on-surface">Reset Password</h2>
        <p class="font-body text-sm text-on-surface-variant">Buat password baru untuk akun Anda.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-sm border border-rose-600 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.simpan') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="email">Alamat Email</label>
            <div class="relative focused-input group">
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="email"
                    placeholder="nama@perusahaan.com"
                    class="w-full px-4 py-3 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('email') border-rose-500 @else border-surface-variant @enderror"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">mail</span>
            </div>
            @error('email')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="password">Password Baru</label>
            <div class="relative focused-input group">
                <input
                    id="password"
                    name="password"
                    :type="showPwd ? 'text' : 'password'"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full px-4 py-3 pr-10 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('password') border-rose-500 @else border-surface-variant @enderror"
                >
                <button type="button" @click="showPwd = !showPwd"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors flex items-center">
                    <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @else
                <p class="font-body text-xs text-on-surface-variant mt-1">Minimal 8 karakter.</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="password_confirmation">Konfirmasi Password Baru</label>
            <div class="relative focused-input group">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showPwdConfirm ? 'text' : 'password'"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                    class="w-full px-4 py-3 pr-10 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors"
                >
                <button type="button" @click="showPwdConfirm = !showPwdConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors flex items-center">
                    <span class="material-symbols-outlined text-lg" x-text="showPwdConfirm ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-psikotes text-white font-body font-semibold text-sm py-3.5 rounded-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm shadow-psikotes/20">
            <span>Simpan Password Baru</span>
            <span class="material-symbols-outlined text-[18px]">lock_reset</span>
        </button>
    </form>

    <div class="text-center text-sm text-on-surface-variant">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-psikotes font-semibold hover:underline">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke halaman login
        </a>
    </div>
</div>
@endsection
