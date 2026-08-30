@extends('layouts.admin', ['judulHalaman' => 'Penjadwalan Tes'])

@section('content')
@php
    $warnaStatus = [
        'Draft'         => 'bg-slate-100 text-slate-600 border border-slate-200',
        'Aktif'         => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Selesai'       => 'bg-blue-50 text-blue-700 border border-blue-200',
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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
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
        <form method="GET" action="{{ route('admin.penjadwalan-tes.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </span>
                <input type="text" name="cari" value="{{ $cari }}"
                    placeholder="Cari nama sesi..."
                    class="w-full rounded-lg border border-[#c0c8cb] py-2 pl-9 pr-4 text-sm text-[#191c1e] placeholder-[#71787c] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all bg-white">
            </div>
            <div class="relative min-w-[160px]">
                <select name="status" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-9 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Status</option>
                    <option value="Draft"   @selected($filterStatus === 'Draft')>Draft</option>
                    <option value="Aktif"   @selected($filterStatus === 'Aktif')>Aktif</option>
                    <option value="Selesai" @selected($filterStatus === 'Selesai')>Selesai</option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>
            @if($cari || $filterStatus)
                <a href="{{ route('admin.penjadwalan-tes.index') }}"
                   class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>{{-- /STICKY HEADER --}}

    @php
        $temaStatus = [
            'Draft'         => ['header_bg' => '#f1f5f9', 'header_text' => '#64748b', 'badge' => '#94a3b8', 'bar' => '#94a3b8', 'icon' => 'draft'],
            'Aktif'         => ['header_bg' => '#ecfdf5', 'header_text' => '#065f46', 'badge' => '#10b981', 'bar' => '#10b981', 'icon' => 'play_circle'],
            'Selesai'       => ['header_bg' => '#eff6ff', 'header_text' => '#1e40af', 'badge' => '#3b82f6', 'bar' => '#3b82f6', 'icon' => 'check_circle'],
            'Belum Dimulai' => ['header_bg' => '#fffbeb', 'header_text' => '#92400e', 'badge' => '#f59e0b', 'bar' => '#f59e0b', 'icon' => 'schedule'],
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
                            {{ $sesi->departemen->pluck('nama_departemen')->join(', ') ?: 'Semua departemen' }}
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
                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                       title="Edit">
                        <span class="material-symbols-outlined text-[20px]">edit_square</span>
                    </a>
                    <form method="POST" action="{{ route('admin.penjadwalan-tes.hapus', $sesi->id) }}"
                          onsubmit="return confirm('Hapus sesi {{ addslashes($sesi->nama_sesi) }}?')" class="ml-auto">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-2 text-[#ba1a1a] hover:bg-[#fef2f2] rounded-lg transition-colors"
                                title="Hapus">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
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
