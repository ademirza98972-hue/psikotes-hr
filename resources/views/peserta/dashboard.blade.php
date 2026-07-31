@extends('layouts.peserta', ['judulHalaman' => 'Dashboard'])

@php
// Mapping warna alat tes literal untuk Tailwind JIT detection
$warnaAlatTes = [
    'IST'    => 'bg-teal-600 text-[11px] font-medium px-2 py-0.5 rounded',
    'DISC'   => 'bg-cyan-600 text-[11px] font-medium px-2 py-0.5 rounded',
    'EPPS'   => 'bg-purple-600 text-[11px] font-medium px-2 py-0.5 rounded',
    'MMPI-2' => 'bg-pink-600 text-[11px] font-medium px-2 py-0.5 rounded',
];
@endphp

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900">Selamat datang, {{ $pengguna->name }}!</h2>
        <p class="mt-2 text-sm text-slate-600">
            Anda terdaftar sebagai
            <span class="font-medium text-[#2C5F6F]">{{ ucfirst($pengguna->tipe_akun ?? '') }}</span>.
            @if ($pengguna->status === 'menunggu_verifikasi')
                <span class="mt-2 inline-block rounded-md bg-amber-500 px-2 py-0.5 text-xs font-medium text-white">Akun menunggu verifikasi HR</span>
            @endif
        </p>

        <!-- Daftar Sesi Tes -->
        @if (count($sesiTes) > 0)
            <div class="mt-8 space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 border-b border-slate-200 pb-2">Sesi Tes yang Ditugaskan</h3>

                @foreach ($sesiTes as $sesi)
                    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <!-- Header Sesi -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900">{{ $sesi['nama_sesi'] }}</h4>
                                <p class="text-sm text-slate-600 mt-1">Departemen: {{ $sesi['departemen_terkait'] }}</p>
                            </div>

                            <!-- Badge Status -->
                            @if ($sesi['status_pengerjaan'] == 'Belum Mengerjakan')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-sm font-medium text-amber-800">
                                    Belum Mengerjakan
                                </span>
                            @elseif ($sesi['status_pengerjaan'] == 'Sedang Berjalan')
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-sm font-medium text-blue-800">
                                    Sedang Berjalan
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-sm font-medium text-emerald-800">
                                    Selesai
                                </span>
                            @endif
                        </div>

                        <!-- Alat Tes Ditugaskan -->
                        <div class="mb-4">
                            <p class="text-sm text-slate-600 mb-2">Alat Tes yang Ditugaskan:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sesi['daftar_alat_tes_ditugaskan'] as $alat)
                                    <span class="{{ $warnaAlatTes[$alat] ?? 'bg-slate-500' }}">
                                        {{ $alat }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Periode / Deadline -->
                        <div class="mb-4 text-sm text-slate-600">
                            <p>Tanggal: {{ Carbon\Carbon::parse($sesi['tanggal_mulai'])->translatedFormat('d F Y') }}
                               &ndash; {{ Carbon\Carbon::parse($sesi['tanggal_selesai'])->translatedFormat('d F Y') }}</p>
                        </div>

                        <!-- Tombol Aksi -->
                        @if ($sesi['status_pengerjaan'] == 'Belum Mengerjakan')
                            <a href="{{ route('peserta.tes.instruksi', $sesi['id'] ?? $loop->iteration) }}"
                               class="inline-flex items-center rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-medium text-white hover:bg-[#1e4450] transition">
                                Mulai Tes
                            </a>
                        @elseif ($sesi['status_pengerjaan'] == 'Sedang Berjalan')
                            <a href="{{ route('peserta.tes.kerjakan', $sesi['id'] ?? $loop->iteration) }}"
                               class="inline-flex items-center rounded-md bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-700 transition">
                                Lanjutkan Tes
                            </a>
                        @else
                            <button disabled
                                    class="inline-flex items-center rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-500 cursor-not-allowed">
                                Selesai Dikerjakan
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-6 text-sm text-slate-600">Belum ada sesi tes yang ditugaskan untuk Anda.</p>
        @endif
    </div>
@endsection