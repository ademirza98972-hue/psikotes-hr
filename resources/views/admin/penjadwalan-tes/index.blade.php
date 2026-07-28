@extends('layouts.admin', ['judulHalaman' => 'Penjadwalan Tes'])

@section('content')
@php
    $warnaStatus = [
        'Draft'   => 'bg-slate-500',
        'Aktif'   => 'bg-emerald-600',
        'Selesai' => 'bg-blue-600',
    ];
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
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

<div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Daftar Penjadwalan Tes</h2>
            <p class="mt-0.5 text-xs text-slate-500">Sesi tes yang dijadwalkan untuk kandidat dan karyawan.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.tambah') }}"
           class="self-start rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] whitespace-nowrap">
            + Tambah Penjadwalan
        </a>
    </div>

    <div class="space-y-3">
        @forelse ($penjadwalan as $sesi)
            @php
                $persen = $sesi['jumlah_peserta'] > 0
                    ? round(($sesi['jumlah_selesai'] / $sesi['jumlah_peserta']) * 100)
                    : 0;
            @endphp
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-semibold text-slate-900">{{ $sesi['nama_sesi'] }}</h3>
                            <span class="inline-block rounded-md {{ $warnaStatus[$sesi['status']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                                {{ $sesi['status'] }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            <span>Departemen: <strong class="text-slate-700">{{ $sesi['departemen_terkait'] ?? '—' }}</strong></span>
                            <span class="mx-2 text-slate-300">|</span>
                            <span>Periode: <strong class="text-slate-700">{{ $tglId($sesi['tanggal_mulai']) }} – {{ $tglId($sesi['tanggal_selesai']) }}</strong></span>
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" disabled title="Detail belum diimplementasikan"
                                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Detail</button>
                        <button type="button" disabled title="Ubah belum diimplementasikan"
                                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Ubah</button>
                        <button type="button" disabled title="Hapus belum diimplementasikan"
                                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Hapus</button>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-slate-500">Alat Tes:</span>
                    @forelse ($sesi['daftar_alat_tes'] as $namaAlat)
                        @php $info = $formatLookup[$namaAlat] ?? null; @endphp
                        <span class="inline-flex items-center gap-1 rounded-md {{ $warnaFormat[$info['format']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                            {{ $namaAlat }}
                        </span>
                    @empty
                        <span class="text-xs text-slate-400">— belum dipilih</span>
                    @endforelse
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-700">
                            Progres Peserta: <strong>{{ $sesi['jumlah_selesai'] }}/{{ $sesi['jumlah_peserta'] }}</strong> selesai
                        </span>
                        <span class="font-semibold text-slate-700">{{ $persen }}%</span>
                    </div>
                    <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full {{ $persen >= 100 ? 'bg-emerald-600' : 'bg-[#2C5F6F]' }} transition-all"
                             style="width: {{ $persen }}%"></div>
                    </div>
                </div>

            </div>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500">
                Belum ada penjadwalan tes.
            </div>
        @endforelse
    </div>
</div>
@endsection