@extends('layouts.auth', ['judulHalaman' => 'Login Admin'])

@section('content')
<div x-data="{ showPwd: false }" class="space-y-6">
    <div class="space-y-1">
        <h2 class="font-body font-semibold text-2xl text-on-surface">Masuk ke Dashboard</h2>
        <p class="font-body text-sm text-on-surface-variant">Silakan masukkan kredensial admin Anda untuk melanjutkan.</p>
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

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="email">Alamat Email</label>
            <div class="relative focused-input group">
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="nama@perusahaan.com"
                    class="w-full px-4 py-3 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('email') border-rose-500 @enderror"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">mail</span>
            </div>
            @error('email')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <div class="flex justify-between items-center">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="password">Kata Sandi</label>
            </div>
            <div class="relative focused-input group">
                <input
                    id="password"
                    name="password"
                    :type="showPwd ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 pr-10 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors"
                >
                <button
                    type="button"
                    @click="showPwd = !showPwd"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors flex items-center"
                >
                    <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2 py-1">
            <input
                id="ingat_saya"
                name="ingat_saya"
                type="checkbox"
                value="1"
                {{ old('ingat_saya') ? 'checked' : '' }}
                class="w-4 h-4 rounded-sm border-outline-variant text-psikotes focus:ring-psikotes cursor-pointer"
            >
            <label for="ingat_saya" class="font-body text-sm text-on-surface-variant cursor-pointer select-none">Ingat saya di perangkat ini</label>
        </div>

        <button type="submit"
            class="w-full bg-psikotes text-white font-body font-semibold text-sm py-3.5 rounded-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm shadow-psikotes/20">
            <span>Masuk</span>
            <span class="material-symbols-outlined text-[18px]">login</span>
        </button>
    </form>

    <div class="text-center mt-4 text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-[#2C5F6F] font-semibold hover:underline">Daftar di sini</a>
    </div>

    <div class="pt-6 border-t border-surface-variant">
        <p class="font-body text-sm text-on-surface-variant text-center">
            Membutuhkan bantuan akses? <a href="#" class="text-psikotes font-medium hover:underline">Hubungi IT Support</a>
        </p>
    </div>
</div>
@endsection
