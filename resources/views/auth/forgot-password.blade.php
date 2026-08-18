@extends('layouts.auth', ['judulHalaman' => 'Lupa Password', 'showVisual' => false])

@section('content')
<div class="space-y-6">

    <div class="space-y-1">
        <h2 class="font-body font-semibold text-2xl text-on-surface">Lupa Password</h2>
        <p class="font-body text-sm text-on-surface-variant">Masukkan email Anda dan kami akan mengirimkan link reset password.</p>
    </div>

    @if (session('sukses'))
        <div class="flex items-start gap-3 rounded-sm border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">check_circle</span>
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-sm border border-rose-600 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.kirim') }}" class="space-y-5">
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
                    class="w-full px-4 py-3 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('email') border-rose-500 @else border-surface-variant @enderror"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">mail</span>
            </div>
            @error('email')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-psikotes text-white font-body font-semibold text-sm py-3.5 rounded-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm shadow-psikotes/20">
            <span>Kirim Link Reset</span>
            <span class="material-symbols-outlined text-[18px]">send</span>
        </button>
    </form>

    <div class="text-center text-sm text-on-surface-variant">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-psikotes font-semibold hover:underline">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke halaman login
        </a>
    </div>

    <div class="pt-6 border-t border-surface-variant">
        <p class="font-body text-sm text-on-surface-variant text-center">
            Membutuhkan bantuan akses? <a href="#" class="text-psikotes font-medium hover:underline">Hubungi IT Support</a>
        </p>
    </div>
</div>
@endsection
