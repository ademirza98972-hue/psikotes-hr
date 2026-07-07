@extends('layouts.admin', ['judulHalaman' => 'Dashboard'])

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">Selamat datang, {{ $pengguna->name }}!</h2>
            <p class="mt-2 text-sm text-slate-600">
                Anda login sebagai
                <span class="font-medium text-[#2C5F6F]">{{ $pengguna->peran->nama_peran ?? 'Tanpa Peran' }}</span>.
            </p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-md border border-slate-200 bg-[#F7F8FA] p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Total Pengguna</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">—</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-[#F7F8FA] p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Hasil Tes Menunggu Review</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">—</p>
                </div>
            </div>

            <p class="mt-6 text-xs text-slate-500">Dashboard ini masih placeholder. Modul-modul CRUD akan ditambahkan di fase berikutnya.</p>
        </div>
    </div>
@endsection