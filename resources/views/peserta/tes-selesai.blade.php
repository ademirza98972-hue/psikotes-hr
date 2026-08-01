@extends('layouts.peserta', ['judulHalaman' => 'Tes Selesai'])

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm text-center">
        <div class="mb-6">
            <svg class="h-16 w-16 mx-auto text-[#2C5F6F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-3">Terima Kasih!</h2>

        @if($namaSesi)
            <p class="text-slate-700 mb-2 font-medium">{{ $namaSesi }}</p>
        @endif

        <p class="text-slate-600 mb-6">
            Anda telah menyelesaikan semua soal untuk sesi tes ini.<br>
            Jawaban Anda akan diproses lebih lanjut oleh tim HR.
        </p>

        {{-- Tombol Kembali ke Dashboard --}}
        <a href="{{ route('peserta.dashboard') }}"
           class="inline-flex items-center gap-2 px-6 py-2 rounded-md bg-[#2C5F6F] text-sm font-medium text-white hover:bg-[#1e4450] transition">
            Kembali ke Dashboard
        </a>
    </div>
@endsection