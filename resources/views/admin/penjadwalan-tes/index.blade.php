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
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-4 px-4 pt-6 pb-4 border-b border-[#e0e3e5]">
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

    @php
        $temaStatus = [
            'Draft'         => ['header_bg' => '#f1f5f9', 'header_text' => '#64748b', 'badge' => '#94a3b8',  'bar' => '#94a3b8',  'icon' => 'draft'],
            'Aktif'         => ['header_bg' => '#ecfdf5', 'header_text' => '#065f46', 'badge' => '#10b981',  'bar' => '#10b981',  'icon' => 'play_circle'],
            'Selesai'       => ['header_bg' => '#f8fafc', 'header_text' => '#475569', 'badge' => '#64748b',  'bar' => '#64748b',  'icon' => 'check_circle'],
            'Kedaluwarsa'   => ['header_bg' => '#fff1f2', 'header_text' => '#9f1239', 'badge' => '#f43f5e',  'bar' => '#f43f5e',  'icon' => 'timer_off'],
            'Belum Dimulai' => ['header_bg' => '#fffbeb', 'header_text' => '#92400e', 'badge' => '#f59e0b',  'bar' => '#f59e0b',  'icon' => 'schedule'],
        ];
        $defaultTema = ['header_bg' => '#f1f5f9', 'header_text' => '#64748b', 'badge' => '#94a3b8', 'bar' => '#94a3b8', 'icon' => 'help'];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        @forelse ($penjadwalan as $sesi)
            @php
                $persen = $sesi->jumlah_peserta_count > 0
                    ? round(($sesi->jumlah_selesai_count / $sesi->jumlah_peserta_count) * 100)
                    : 0;
                $status = $sesi->status_display ?? $sesi->status;
                $tema = $temaStatus[$status] ?? $defaultTema;
            @endphp
            <div class="group flex flex-col rounded-2xl border border-[#e2e8f0] bg-white shadow-sm transition-all hover:shadow-lg hover:-translate-y-1 overflow-hidden">

                {{-- Colored header strip --}}
                <div class="flex items-start justify-between gap-3 px-5 py-4" style="background-color: {{ $tema['header_bg'] }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="shrink-0 flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: {{ $tema['badge'] }}1a">
                            <span class="material-symbols-outlined text-[18px]" style="color: {{ $tema['badge'] }}">{{ $tema['icon'] }}</span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-[15px] leading-tight truncate" style="color: {{ $tema['header_text'] }}">{{ $sesi->nama_sesi }}</h3>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold mt-0.5" style="color: {{ $tema['badge'] }}">
                                <span class="h-1.5 w-1.5 rounded-full inline-block" style="background-color: {{ $tema['badge'] }}"></span>
                                {{ $status }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 px-5 pt-4 pb-3 space-y-3">

                    {{-- Meta row --}}
                    <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-[12px] text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-slate-400">business</span>
                            {{ $sesi->departemenTerkait?->nama_departemen ?? 'Semua departemen' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-slate-400">date_range</span>
                            {{ $tglId($sesi->tanggal_mulai) }} — {{ $tglId($sesi->tanggal_selesai) }}
                        </span>
                    </div>

                    {{-- Alat tes --}}
                    <div class="flex flex-wrap gap-1.5">
                        @forelse ($sesi->alatTes as $alat)
                            <span class="rounded-lg border border-[#2C5F6F]/20 bg-[#2C5F6F]/5 px-2.5 py-0.5 text-[11px] font-bold text-[#2C5F6F] tracking-wide">
                                {{ $alat->kode }}
                            </span>
                        @empty
                            <span class="text-[11px] italic text-slate-400">Belum ada alat tes</span>
                        @endforelse
                    </div>

                    {{-- Progress --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5 text-[12px]">
                            <span class="text-slate-500">
                                Progres: <span class="font-bold text-slate-700">{{ $sesi->jumlah_selesai_count }}</span> / {{ $sesi->jumlah_peserta_count }} peserta
                            </span>
                            <span class="font-bold" style="color: {{ $tema['bar'] }}">{{ $persen }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all" style="width: {{ $persen }}%; background-color: {{ $tema['bar'] }}"></div>
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 px-5 py-3 border-t border-slate-100 bg-slate-50/60">
                    <a href="{{ route('admin.penjadwalan-tes.detail', $sesi->id) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#2C5F6F] px-4 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-[#1E414C] active:scale-95">
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                        Detail
                    </a>
                    <a href="{{ route('admin.penjadwalan-tes.edit', $sesi->id) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-[12px] font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                        <span class="material-symbols-outlined text-[14px]">edit</span>
                        Ubah
                    </a>
                    <form method="POST" action="{{ route('admin.penjadwalan-tes.hapus', $sesi->id) }}"
                          onsubmit="return confirm('Hapus sesi {{ addslashes($sesi->nama_sesi) }}?')" class="ml-auto">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[12px] font-semibold text-rose-400 transition hover:bg-rose-50 hover:text-rose-600">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-[#c0c8cb] bg-white py-20 text-center">
                <span class="material-symbols-outlined text-[48px] text-slate-300">event_busy</span>
                <p class="mt-3 text-sm font-semibold text-slate-500">Belum ada penjadwalan tes</p>
                <p class="mt-1 text-[12px] text-slate-400">Buat sesi tes pertama untuk mulai menjadwalkan peserta.</p>
                <a href="{{ route('admin.penjadwalan-tes.tambah') }}"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition">
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
