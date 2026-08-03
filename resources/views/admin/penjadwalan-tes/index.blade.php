@extends('layouts.admin', ['judulHalaman' => 'Penjadwalan Tes'])

@section('content')
@php
    $warnaStatus = [
        'Draft'   => 'bg-slate-100 text-slate-600 border border-slate-200',
        'Aktif'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Selesai' => 'bg-slate-100 text-slate-600 border border-slate-200',
    ];
    $warnaAlatTes = [
        'DISC'   => 'bg-blue-50 text-blue-700 border border-blue-200',
        'IST'    => 'bg-violet-50 text-violet-700 border border-violet-200',
        'EPPS'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'MMPI-2' => 'bg-orange-50 text-orange-700 border border-orange-200',
    ];
    $formatLookup = collect([
        ['nama' => 'DISC',  'format' => 'Skala Likert'],
        ['nama' => 'IST',   'format' => 'Pilihan Ganda'],
        ['nama' => 'EPPS',  'format' => 'Forced Choice'],
        ['nama' => 'MMPI-2','format' => 'Skala Likert'],
    ])->keyBy('nama');
    $bulanId = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $tglId = function ($iso) use ($bulanId) {
        if (! $iso) return '-';
        [$y, $m, $d] = explode('-', substr($iso, 0, 10));
        return (int) $d . ' ' . $bulanId[(int) $m] . ' ' . $y;
    };
@endphp

<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-[#191c1e]">Daftar Penjadwalan Tes</h2>
            <p class="mt-0.5 text-sm text-[#41484b]">Sesi tes yang dijadwalkan untuk kandidat dan karyawan.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.tambah') }}"
           class="self-start inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Penjadwalan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($penjadwalan as $sesi)
            @php
                $persen = $sesi['jumlah_peserta'] > 0
                    ? round(($sesi['jumlah_selesai'] / $sesi['jumlah_peserta']) * 100)
                    : 0;
            @endphp
            <div class="rounded-xl border border-[#c1c7cb] bg-white p-6 transition-all duration-300 hover:-translate-y-0.5 hover:border-[#2C5F6F] hover:shadow-md flex flex-col justify-between">

                <div>
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-[#191c1e] text-base">{{ $sesi['nama_sesi'] }}</h3>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold border {{ $warnaStatus[$sesi['status']] ?? 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $sesi['status'] }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-[#41484b] text-sm">
                            <span class="material-symbols-outlined text-[18px]">business</span>
                            <span>Departemen: <strong class="text-[#191c1e]">{{ $sesi['departemen_terkait'] ?? '—' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2 text-[#41484b] text-sm">
                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            <span>Periode: <strong class="text-[#191c1e]">{{ $tglId($sesi['tanggal_mulai']) }} – {{ $tglId($sesi['tanggal_selesai']) }}</strong></span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#41484b] mb-2">ALAT TES:</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($sesi['daftar_alat_tes'] as $namaAlat)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $warnaAlatTes[$namaAlat] ?? 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                                    {{ $namaAlat }}
                                </span>
                            @empty
                                <span class="text-xs text-[#919eab]">— belum dipilih</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-auto">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-[#41484b]">Progres Peserta: <span class="font-bold text-[#191c1e]">{{ $sesi['jumlah_selesai'] }}/{{ $sesi['jumlah_peserta'] }}</span> selesai</span>
                        <span class="text-sm font-bold text-[#2C5F6F]">{{ $persen }}%</span>
                    </div>
                    <div class="w-full h-2 bg-[#e6e8ea] rounded-full overflow-hidden mb-6">
                        <div class="h-full rounded-full transition-all" style="width: {{ $persen }}%; background-color: #2C5F6F;"></div>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-[#c1c7cb] pt-4">
                        <button type="button" disabled title="Detail belum diimplementasikan"
                                class="cursor-not-allowed px-4 py-2 text-sm font-semibold text-slate-400 opacity-60">Detail</button>
                        <button type="button" disabled title="Ubah belum diimplementasikan"
                                class="cursor-not-allowed px-4 py-2 text-sm font-semibold text-slate-600 opacity-60">Ubah</button>
                        <button type="button" disabled title="Hapus belum diimplementasikan"
                                class="cursor-not-allowed px-4 py-2 text-sm font-semibold text-slate-600 opacity-60">Hapus</button>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-[#c1c7cb] bg-white px-6 py-10 text-center text-sm text-[#41484b]">
                Belum ada penjadwalan tes.
            </div>
        @endforelse
    </div>

</div>
@endsection
