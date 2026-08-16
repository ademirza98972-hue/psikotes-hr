@extends('layouts.peserta', ['judulHalaman' => 'Dashboard'])

@php
    $totalSesi = count($sesiTes);
    $sedangBerjalan = collect($sesiTes)->where('status_pengerjaan', 'Sedang Berjalan')->count();
    $selesai = collect($sesiTes)->where('status_pengerjaan', 'Selesai')->count();
@endphp

@section('content')
    <div class="space-y-6">

        {{-- Header Sapaan --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">Selamat datang, {{ $pengguna->name }}!</h2>
            <p class="mt-2 text-sm text-slate-600">
                Anda terdaftar sebagai
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 ml-1">
                    {{ ucfirst($pengguna->tipe_akun ?? '-') }}
                </span>
                @if ($pengguna->status === 'menunggu_verifikasi')
                    <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                        <span class="material-symbols-outlined text-base">warning</span>
                        Akun menunggu verifikasi HR
                    </span>
                @endif
            </p>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#2C5F6F]/10">
                        <span class="material-symbols-outlined text-[24px] text-[#2C5F6F]">assignment</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $totalSesi }}</p>
                        <p class="text-xs text-slate-500">Total Sesi Ditugaskan</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50">
                        <span class="material-symbols-outlined text-[24px] text-blue-600">hourglass_top</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $sedangBerjalan }}</p>
                        <p class="text-xs text-slate-500">Sedang Berjalan</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50">
                        <span class="material-symbols-outlined text-[24px] text-emerald-600">check_circle</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-900">{{ $selesai }}</p>
                        <p class="text-xs text-slate-500">Sesi Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Sesi Tes --}}
        @if (count($sesiTes) > 0)
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">assignment</span>
                    Sesi Tes yang Ditugaskan
                </h3>

                <div class="space-y-4">
                    @foreach ($sesiTes as $sesi)
                        <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
                            <!-- Header Sesi -->
                            <div class="flex items-start justify-between mb-5">
                                <div>
                                    <h4 class="text-base font-semibold text-slate-900">{{ $sesi['nama_sesi'] }}</h4>
                                    <div class="flex items-center gap-1 mt-1 text-sm text-slate-500">
                                        <span class="material-symbols-outlined text-base">business</span>
                                        {{ $sesi['departemen_terkait'] }}
                                    </div>
                                </div>

                                <!-- Badge Status -->
                                @php
                                    $today = \Illuminate\Support\Carbon::today();
                                    $startDate = \Illuminate\Support\Carbon::parse($sesi['tanggal_mulai']);
                                    $endDate = \Illuminate\Support\Carbon::parse($sesi['tanggal_selesai']);
                                    if ($today > $endDate) { $statusSesi = 'Kedaluwarsa'; }
                                    elseif ($today >= $startDate) { $statusSesi = 'Aktif'; }
                                    else { $statusSesi = 'Belum Dimulai'; }
                                @endphp
                                @if ($sesi['status_pengerjaan'] == 'Selesai')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 border border-emerald-200">
                                        <span class="material-symbols-outlined text-base">check_circle</span>
                                        Selesai
                                    </span>
                                @elseif ($statusSesi == 'Kedaluwarsa')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700 border border-rose-200">
                                        <span class="material-symbols-outlined text-base">hourglass_disabled</span>
                                        Kedaluwarsa
                                    </span>
                                @elseif ($statusSesi == 'Belum Dimulai')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Dimulai
                                    </span>
                                @elseif ($sesi['status_pengerjaan'] == 'Sedang Berjalan')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Sedang Berjalan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Mengerjakan
                                    </span>
                                @endif
                            </div>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                                <!-- Tanggal -->
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <span class="material-symbols-outlined text-base text-slate-400">calendar_today</span>
                                    <span>
                                        <span class="text-slate-400">Mulai:</span>
                                        {{ Carbon\Carbon::parse($sesi['tanggal_mulai'])->translatedFormat('d M Y') }}
                                        &ndash;
                                        <span class="text-slate-400">Selesai:</span>
                                        {{ Carbon\Carbon::parse($sesi['tanggal_selesai'])->translatedFormat('d M Y') }}
                                    </span>
                                </div>

                                <!-- Jumlah Alat Tes -->
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <span class="material-symbols-outlined text-base text-slate-400">app_registration</span>
                                    <span>{{ count($sesi['daftar_alat_tes_ditugaskan'] ?? []) }} alat tes</span>
                                </div>
                            </div>

                            <!-- Alat Tes Chips -->
                            <div class="mb-5">
                                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Alat Tes</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($sesi['daftar_alat_tes_ditugaskan'] as $alat)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 border border-slate-200">
                                            {{ $alat }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            @if ($sesi['status_pengerjaan'] == 'Selesai')
                                <button disabled
                                        class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-medium text-slate-400 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-base">check_circle</span>
                                    Selesai Dikerjakan
                                </button>
                            @elseif ($statusSesi == 'Kedaluwarsa')
                                <button disabled
                                        class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-medium text-slate-400 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-base">hourglass_disabled</span>
                                    Periode tes telah berakhir
                                </button>
                            @elseif ($statusSesi == 'Belum Dimulai')
                                <button disabled
                                        class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-medium text-slate-400 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-base">schedule</span>
                                    Belum Dimulai
                                </button>
                            @elseif ($sesi['status_pengerjaan'] == 'Sedang Berjalan')
                                <a href="{{ route('peserta.tes.kerjakan', $sesi['id'] ?? $loop->iteration) }}"
                                   class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#1e4450] transition">
                                    <span class="material-symbols-outlined text-base">play_arrow</span>
                                    Lanjutkan Tes
                                </a>
                            @else
                                <a href="{{ route('peserta.tes.instruksi', $sesi['id'] ?? $loop->iteration) }}"
                                   class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#1e4450] transition">
                                    <span class="material-symbols-outlined text-base">play_arrow</span>
                                    Mulai Tes
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-xl border border-[#e0e3e5] bg-white p-8 text-center shadow-sm">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-3 block">inbox</span>
                <p class="text-sm text-slate-500">Belum ada sesi tes yang ditugaskan untuk Anda.</p>
            </div>
        @endif
    </div>
@endsection
