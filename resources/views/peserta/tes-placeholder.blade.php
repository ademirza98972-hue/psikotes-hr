@extends('layouts.peserta', ['judulHalaman' => 'Pengerjaan Tes'])

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm text-center">
        <h2 class="text-xl font-semibold text-slate-900">Halaman Pengerjaan Soal</h2>
        <p class="mt-4 text-sm text-slate-600">Halaman Pengerjaan Soal akan dibangun di tahap berikutnya.</p>
        <a href="{{ route('peserta.dashboard') }}"
           class="mt-6 inline-flex items-center rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-medium text-white hover:bg-[#1e4450] transition">
            Kembali ke Dashboard
        </a>
    </div>
@endsection