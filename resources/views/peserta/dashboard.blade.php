@extends('layouts.peserta', ['judulHalaman' => 'Dashboard'])

@php
    \Carbon\Carbon::setLocale('id');
    $jam = (int) now()->hour;
    $sapaan = match(true) {
        $jam >= 4 && $jam < 11  => 'Selamat pagi',
        $jam >= 11 && $jam < 15 => 'Selamat siang',
        $jam >= 15 && $jam < 18 => 'Selamat sore',
        default                  => 'Selamat malam',
    };
    $tanggalHariIni = \Carbon\Carbon::now()->translatedFormat('l, j F Y');

    $today   = \Carbon\Carbon::today();
    $allSesi = collect($sesiTes);

    $sesiAktif   = $allSesi->filter(fn($s) => $s['status_pengerjaan'] !== 'Selesai' && $s['status_sesi'] !== 'Kedaluwarsa');
    $sesiRiwayat = $allSesi->filter(fn($s) => $s['status_pengerjaan'] === 'Selesai'  || $s['status_sesi'] === 'Kedaluwarsa');

    $sesiMendesak = $sesiAktif->filter(function ($s) use ($today) {
        if ($s['status_sesi'] !== 'Aktif') return false;
        $sisa = $today->diffInDays(\Carbon\Carbon::parse($s['tanggal_selesai']), false);
        return $sisa >= 0 && $sisa <= 2;
    });

    $totalSesi      = count($sesiTes);
    $perluDikerjakan = $sesiAktif->where('status_sesi', 'Aktif')->count();
    $selesai        = $allSesi->where('status_pengerjaan', 'Selesai')->count();
@endphp

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="rounded-xl p-6 text-white shadow-sm" style="background: linear-gradient(to right, #2C5F6F, #3d7a8a)">
        <div class="flex items-start justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-xl font-semibold">{{ $sapaan }}, {{ $pengguna->name }}!</h2>
                <p class="text-sm mt-1 text-white/70">{{ $tanggalHariIni }}</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1.5 text-xs font-medium">
                {{ ucfirst($pengguna->tipe_akun ?? 'peserta') }}
            </span>
        </div>
        @if ($pengguna->status === 'menunggu_verifikasi')
            <div class="mt-3 flex items-center gap-2 rounded-lg bg-amber-400/20 px-3 py-2 text-sm">
                <span class="material-symbols-outlined text-base text-amber-300">warning</span>
                <span class="text-amber-100">Akun Anda menunggu verifikasi HR sebelum dapat mengerjakan tes.</span>
            </div>
        @endif
    </div>

    {{-- Urgency Alert --}}
    @if ($sesiMendesak->isNotEmpty())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 flex items-start gap-3">
            <span class="material-symbols-outlined text-rose-600 shrink-0 mt-0.5">notifications_active</span>
            <div>
                <p class="text-sm font-semibold text-rose-800">Segera selesaikan!</p>
                <p class="text-sm text-rose-700 mt-0.5">
                    {{ $sesiMendesak->count() }} sesi tes akan berakhir dalam 1–2 hari ke depan. Jangan sampai terlewat.
                </p>
            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#2C5F6F]/10">
                    <span class="material-symbols-outlined text-[22px] text-[#2C5F6F]">assignment</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $totalSesi }}</p>
                    <p class="text-xs text-slate-500">Total Ditugaskan</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-50">
                    <span class="material-symbols-outlined text-[22px] text-amber-500">pending_actions</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $perluDikerjakan }}</p>
                    <p class="text-xs text-slate-500">Perlu Dikerjakan</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-50">
                    <span class="material-symbols-outlined text-[22px] text-emerald-600">task_alt</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $selesai }}</p>
                    <p class="text-xs text-slate-500">Selesai Dikerjakan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sesi Perlu Dikerjakan --}}
    <div id="sesi-aktif" class="scroll-mt-20">
        <h3 class="text-base font-semibold text-slate-900 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">pending_actions</span>
            Perlu Dikerjakan
            @if ($sesiAktif->isNotEmpty())
                <span class="inline-flex items-center justify-center rounded-full bg-[#2C5F6F] min-w-[1.25rem] px-1.5 py-0.5 text-xs font-bold text-white">{{ $sesiAktif->count() }}</span>
            @endif
        </h3>

        @if ($sesiAktif->isNotEmpty())
            <div class="space-y-4">
                @foreach ($sesiAktif as $sesi)
                    @php
                        $endDate = \Carbon\Carbon::parse($sesi['tanggal_selesai']);
                        $sisaHari = $today->diffInDays($endDate, false);
                    @endphp
                    <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm {{ $sisaHari >= 0 && $sisaHari <= 2 && $sesi['status_sesi'] === 'Aktif' ? 'border-l-4 border-l-rose-400' : '' }}">

                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="min-w-0">
                                <h4 class="text-base font-semibold text-slate-900 truncate">{{ $sesi['nama_sesi'] }}</h4>
                                @if($sesi['departemen_terkait'])
                                    <div class="flex items-center gap-1 mt-1 text-xs text-slate-500">
                                        <span class="material-symbols-outlined text-[14px]">business</span>
                                        {{ $sesi['departemen_terkait'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                {{-- Deadline chip --}}
                                @if ($sesi['status_sesi'] === 'Aktif' && $sisaHari >= 0 && $sisaHari <= 2)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700 border border-rose-200">
                                        <span class="material-symbols-outlined text-[12px]">timer</span>
                                        {{ $sisaHari == 0 ? 'Berakhir hari ini' : $sisaHari . ' hari lagi' }}
                                    </span>
                                @endif

                                {{-- Status badge --}}
                                @if ($sesi['status_sesi'] === 'Belum Dimulai')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 border border-slate-200">
                                        <span class="material-symbols-outlined text-[13px]">schedule</span>
                                        Belum Dimulai
                                    </span>
                                @elseif ($sesi['status_pengerjaan'] === 'Sedang Berjalan')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Sedang Berjalan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Mengerjakan
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Meta info --}}
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500 mb-4">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">calendar_today</span>
                                {{ \Carbon\Carbon::parse($sesi['tanggal_mulai'])->translatedFormat('d M Y') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($sesi['tanggal_selesai'])->translatedFormat('d M Y') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">quiz</span>
                                {{ count($sesi['daftar_alat_tes_ditugaskan'] ?? []) }} alat tes
                            </span>
                        </div>

                        {{-- Alat Tes Chips --}}
                        <div class="flex flex-wrap gap-2 mb-5">
                            @foreach ($sesi['daftar_alat_tes_ditugaskan'] as $alat)
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 border border-slate-200">
                                    {{ $alat }}
                                </span>
                            @endforeach
                        </div>

                        {{-- CTA --}}
                        @if ($sesi['status_sesi'] === 'Belum Dimulai')
                            <button disabled
                                    class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-medium text-slate-400 cursor-not-allowed">
                                <span class="material-symbols-outlined text-base">schedule</span>
                                Belum Dimulai
                            </button>
                        @elseif ($sesi['status_pengerjaan'] === 'Sedang Berjalan')
                            <a href="{{ route('peserta.tes.kerjakan', $sesi['id']) }}"
                               class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#1e4450] transition">
                                <span class="material-symbols-outlined text-base">play_arrow</span>
                                Lanjutkan Tes
                            </a>
                        @else
                            <a href="{{ route('peserta.tes.instruksi', $sesi['id']) }}"
                               class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#1e4450] transition">
                                <span class="material-symbols-outlined text-base">play_arrow</span>
                                Mulai Tes
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-[#e0e3e5] bg-white p-8 text-center">
                <span class="material-symbols-outlined text-3xl text-slate-200 block mb-2">check_circle</span>
                <p class="text-sm text-slate-400">Tidak ada sesi yang perlu dikerjakan saat ini.</p>
            </div>
        @endif
    </div>

    {{-- Riwayat Tes --}}
    <div id="riwayat" class="scroll-mt-20">
        <h3 class="text-base font-semibold text-slate-500 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">history</span>
            Riwayat Tes
        </h3>

        @if ($sesiRiwayat->isNotEmpty())
            <div class="space-y-3">
                @foreach ($sesiRiwayat as $sesi)
                    <div class="rounded-xl border border-[#e0e3e5] bg-white/60 p-5 shadow-sm opacity-80">

                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="text-sm font-semibold text-slate-700 truncate">{{ $sesi['nama_sesi'] }}</h4>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-slate-400">
                                    @if($sesi['departemen_terkait'])
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">business</span>
                                            {{ $sesi['departemen_terkait'] }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                                        {{ \Carbon\Carbon::parse($sesi['tanggal_mulai'])->translatedFormat('d M Y') }}
                                        &ndash;
                                        {{ \Carbon\Carbon::parse($sesi['tanggal_selesai'])->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">quiz</span>
                                        {{ implode(', ', $sesi['daftar_alat_tes_ditugaskan'] ?? []) }}
                                    </span>
                                </div>
                            </div>

                            @if ($sesi['status_pengerjaan'] === 'Selesai')
                                <span class="inline-flex items-center gap-1 shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 border border-emerald-200">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 shrink-0 rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-600 border border-rose-200">
                                    <span class="material-symbols-outlined text-[13px]">hourglass_disabled</span>
                                    Kedaluwarsa
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-[#e0e3e5] bg-white p-8 text-center">
                <span class="material-symbols-outlined text-3xl text-slate-200 block mb-2">history</span>
                <p class="text-sm text-slate-400">Belum ada riwayat tes.</p>
            </div>
        @endif
    </div>

</div>
@endsection
