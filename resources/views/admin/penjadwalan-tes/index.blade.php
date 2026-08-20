@extends('layouts.admin', ['judulHalaman' => 'Penjadwalan Tes'])

@section('content')
@php
    $warnaStatus = [
        'Draft'         => 'bg-slate-100 text-slate-600 border border-slate-200',
        'Aktif'         => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Selesai'       => 'bg-slate-100 text-slate-600 border border-slate-200',
        'Kedaluwarsa'   => 'bg-rose-50 text-rose-700 border border-rose-200',
        'Belum Dimulai' => 'bg-amber-50 text-amber-700 border border-amber-200',
    ];
    $warnaAlatTes = [
        'EPPS'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    ];
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

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-[28px] leading-9 font-semibold text-[#00303c]">Daftar Penjadwalan Tes</h2>
            <p class="mt-0.5 text-[14px] text-[#40484b]">Sesi tes yang dijadwalkan untuk kandidat dan karyawan.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.tambah') }}"
           class="self-start inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Penjadwalan
        </a>
    </div>
    </div>{{-- /STICKY HEADER --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($penjadwalan as $sesi)
            @php
                $persen = $sesi->jumlah_peserta_count > 0
                    ? round(($sesi->jumlah_selesai_count / $sesi->jumlah_peserta_count) * 100)
                    : 0;
            @endphp
            <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 transition-all duration-300 hover:-translate-y-0.5 hover:border-[#2C5F6F] hover:shadow-md flex flex-col justify-between">

                <div>
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-bold text-[#191c1e] text-base">{{ $sesi->nama_sesi }}</h3>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold border {{ $warnaStatus[$sesi->status_display] ?? $warnaStatus[$sesi->status] ?? 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $sesi->status_display ?? $sesi->status }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-[#41484b] text-sm">
                            <span class="material-symbols-outlined text-[18px]">business</span>
                            <span>Departemen: <strong class="text-[#191c1e]">{{ $sesi->departemenTerkait?->nama_departemen ?? '—' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2 text-[#41484b] text-sm">
                            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            <span>Periode: <strong class="text-[#191c1e]">{{ $tglId($sesi->tanggal_mulai) }} – {{ $tglId($sesi->tanggal_selesai) }}</strong></span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[#41484b] mb-2">ALAT TES:</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($sesi->alatTes as $alat)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $warnaAlatTes[$alat->kode] ?? 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                                    {{ $alat->kode ?? $alat->nama }}
                                </span>
                            @empty
                                <span class="text-xs text-[#919eab]">— belum dipilih</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-auto">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-[#41484b]">Progres Peserta: <span class="font-bold text-[#191c1e]">{{ $sesi->jumlah_selesai_count }}/{{ $sesi->jumlah_peserta_count }}</span> selesai</span>
                        <span class="text-sm font-bold text-[#2C5F6F]">{{ $persen }}%</span>
                    </div>
                    <div class="w-full h-2 bg-[#e6e8ea] rounded-full overflow-hidden mb-6">
                        <div class="h-full rounded-full transition-all" style="width: {{ $persen }}%; background-color: #2C5F6F;"></div>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-[#e0e3e5] pt-4">
                        <a href="{{ route('admin.penjadwalan-tes.detail', $sesi->id) }}"
                           class="px-4 py-2 text-sm font-semibold text-[#2C5F6F] hover:bg-[#2C5F6F]/5 rounded-lg transition-colors">
                            Detail
                        </a>
                        <a href="{{ route('admin.penjadwalan-tes.edit', $sesi->id) }}"
                           class="px-4 py-2 text-sm font-semibold text-[#41484b] hover:bg-[#f2f4f6] rounded-lg transition-colors">
                            Ubah
                        </a>
                        <form method="POST"
                              action="{{ route('admin.penjadwalan-tes.hapus', $sesi->id) }}"
                              onsubmit="return confirm('Hapus sesi {{ addslashes($sesi->nama_sesi) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-semibold text-rose-600 hover:text-rose-700 transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-14 text-center">
                <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">event_busy</span>
                <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum ada penjadwalan tes</p>
                <p class="text-[12px] text-[#71787c] mb-4">Buat sesi tes pertama untuk mulai menjadwalkan peserta.</p>
                <a href="{{ route('admin.penjadwalan-tes.tambah') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Penjadwalan
                </a>
            </div>
        @endforelse
    </div>

    @if ($penjadwalan->hasPages())
    <div class="flex justify-center">
        {{ $penjadwalan->links() }}
    </div>
    @endif

</div>
@endsection
