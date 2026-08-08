@extends('layouts.admin', ['judulHalaman' => 'Bank Soal'])

@section('content')
@php
    // Mapping kode alat tes → warna badge (konsisten dengan halaman Alat Tes)
    $warnaKode = [
        'EPPS'   => 'bg-emerald-100 text-emerald-700',
    ];
@endphp

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-[28px] leading-9 font-semibold text-[#00303c]">Bank Soal</h2>
            <p class="mt-0.5 text-[14px] text-[#40484b]">Kelola kumpulan instrumen tes psikologi.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasIzin('soal.tambah'))
            @if($alatTesTerpilih)
                <a href="{{ route('admin.bank-soal.tambah', $alatTesTerpilih->id) }}"
                   class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Soal
                </a>
            @else
                {{-- Tampilkan tombol Tambah Soal tanpa filter (ambil dari alat tes pertama) --}}
                @php $alatFirst = $alatTesSemua[0] ?? null; @endphp
                @if($alatFirst)
                <a href="{{ route('admin.bank-soal.tambah', $alatFirst->id) }}"
                   class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Soal
                </a>
                @endif
            @endif
            @endif
            {{-- TODO: implementasi import Excel saat backend siap --}}
            <button type="button" disabled
                    class="cursor-not-allowed rounded-xl bg-[#e6e8ea] px-4 py-2 text-sm font-semibold text-[#40484b] opacity-60 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                Import Excel
            </button>
        </div>
    </div>

    {{-- FILTER PILIH ALAT TES --}}
    <div class="bg-white border border-[#e0e3e5] rounded-xl p-4">
        <form method="GET" action="{{ route('admin.bank-soal.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                <div>
                    <label for="alat_tes_id" class="block text-[11px] uppercase tracking-wider font-semibold text-[#40484b] mb-1">Filter Instrumen</label>
                    <select id="alat_tes_id" name="alat_tes_id"
                            class="block w-64 rounded-xl border border-[#c0c8cb] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                        <option value="">Semua Instrumen</option>
                        @foreach ($alatTesSemua as $alat)
                            <option value="{{ $alat->id }}" @selected((int) request('alat_tes_id') === $alat->id)>
                                {{ $alat->nama }} ({{ $alat->format_dasar }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                    Tampilkan Soal
                </button>
                @if (request('alat_tes_id'))
                    <a href="{{ route('admin.bank-soal.index') }}"
                       class="rounded-xl bg-[#f2f4f6] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#e6e8ea] whitespace-nowrap transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TAMPILAN: MODE TERPILIH (satu alat tes) --}}
    @if($alatTesTerpilih)
        @php
            $kodeAlat = '';
            foreach (['EPPS'] as $k) {
                if (str_contains($alatTesTerpilih->nama, $k)) {
                    $kodeAlat = $k;
                    break;
                }
            }
        @endphp

        {{-- HEADER ALAT TES TERPILIH --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php
                    $namaTampil = ($alatTesTerpilih->nama === $kodeAlat)
                        ? 'Instrument ' . $kodeAlat
                        : $alatTesTerpilih->nama;
                @endphp
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $warnaKode[$kodeAlat] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $kodeAlat }}
                </span>
                <h2 class="text-base font-semibold text-[#191c1e]">{{ $namaTampil }}</h2>
                <span class="inline-block rounded-full border border-[#c0c8cb] px-2.5 py-0.5 text-[11px] font-medium text-[#40484b]">
                    {{ $alatTesTerpilih->format_dasar }}
                </span>
            </div>
            <span class="text-[12px] text-[#40484b]">{{ count($daftarSoal) }} soal terdaftar</span>
        </div>

        @if (empty($daftarSoal))
            <div class="bg-white border border-dashed border-[#c0c8cb] rounded-xl px-6 py-10 text-center text-sm text-[#40484b]">
                Belum ada soal untuk alat tes ini.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($daftarSoal as $idx => $soal)
                    @include('admin.bank-soal.partials.kartu-soal', [
                        'nomor'       => $idx + 1,
                        'soal'        => $soal,
                        'format'      => $alatTesTerpilih->format_dasar,
                        'kodeAlat'    => $kodeAlat,
                        'warnaKode'   => $warnaKode,
                    ])
                @endforeach
            </div>
        @endif

    {{-- TAMPILAN: MODE SEMUA (tanpa filter) --}}
    @else
        @if(count($kelompokSoalSemua) === 0)
            <div class="bg-white border border-dashed border-[#c0c8cb] rounded-xl px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[40px] text-[#c0c8cb] mb-3">quiz</span>
                <p class="text-sm text-[#40484b]">Belum ada soal tersedia.</p>
            </div>
        @else
            @foreach($kelompokSoalSemua as $alatId => $kelompok)
                @if(empty($kelompok['alat'])) @continue @endif
                @php
                    $kodeAlat = '';
                    foreach (['EPPS'] as $k) {
                        if (str_contains($kelompok['alat']->nama, $k)) {
                            $kodeAlat = $k;
                            break;
                        }
                    }
                @endphp

                {{-- Section Header --}}
                <div class="flex items-center justify-between mt-6 first:mt-0">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $warnaKode[$kodeAlat] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $kodeAlat }}
                        </span>
                        @php
                            $namaTampilAll = ($kelompok['alat']->nama === $kodeAlat)
                                ? 'Instrument ' . $kodeAlat
                                : $kelompok['alat']->nama;
                        @endphp
                        <h3 class="text-sm font-semibold text-[#191c1e]">{{ $namaTampilAll }}</h3>
                        <span class="inline-block rounded-full border border-[#c0c8cb] px-2.5 py-0.5 text-[11px] font-medium text-[#40484b]">
                            {{ $kelompok['alat']->format_dasar }}
                        </span>
                    </div>
                    <span class="text-[12px] text-[#40484b]">{{ $kelompok['jumlahSoal'] }} soal</span>
                </div>

                {{-- Soal Cards --}}
                <div class="space-y-3 mt-3">
                    @foreach($kelompok['soal'] as $idx => $soal)
                        @include('admin.bank-soal.partials.kartu-soal', [
                            'nomor'       => $idx + 1,
                            'soal'        => $soal,
                            'format'      => $kelompok['alat']->format_dasar,
                            'kodeAlat'    => $kodeAlat,
                            'warnaKode'   => $warnaKode,
                        ])
                    @endforeach
                </div>
            @endforeach
        @endif
    @endif

</div>
@endsection
