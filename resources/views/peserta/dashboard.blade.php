@extends('layouts.peserta', ['judulHalaman' => 'Dashboard'])

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900">Selamat datang, {{ $pengguna->name }}!</h2>
        <p class="mt-2 text-sm text-slate-600">
            Anda terdaftar sebagai
            <span class="font-medium text-[#2C5F6F]">{{ ucfirst($pengguna->tipe_akun ?? '') }}</span>.
            @if ($pengguna->status === 'menunggu_verifikasi')
                <span class="mt-2 inline-block rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Akun menunggu verifikasi HR</span>
            @endif
        </p>

        <div class="mt-6">
            <p class="text-sm text-slate-600">Tes psikotes yang tersedia akan muncul di sini setelah HR memverifikasi akun Anda.</p>
        </div>

        <p class="mt-6 text-xs text-slate-500">Dashboard ini masih placeholder. Modul pengerjaan tes akan ditambahkan di fase berikutnya.</p>
    </div>
@endsection