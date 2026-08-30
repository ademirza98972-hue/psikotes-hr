@extends('layouts.admin', ['judulHalaman' => 'Kelola Alat Tes'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'border-[#71787c] text-[#40484b]',
        'Skala Likert'  => 'border-[#71787c] text-[#40484b]',
        'Forced Choice' => 'border-[#71787c] text-[#40484b]',
    ];
    // totalAlat, totalSoal, alatPopuler dikirim dari controller
@endphp

<div class="space-y-6"
     x-data="{
         modalTambah: false,
         batasAktif: {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }},
         modalUbah: false,
         ubah: { id:'', kode:'', nama:'', formatDasar:'Pilihan Ganda', polaSkoring:'kognitif', durasi:'', deskripsi:'', batasAktif:false, batasDet:'', isSensitif:false },
         openUbah(id, kode, nama, formatDasar, polaSkoring, durasi, deskripsi, batasAktif, batasDet, isSensitif) {
             this.ubah = { id: String(id), kode, nama, formatDasar, polaSkoring,
                           durasi: String(durasi ?? ''), deskripsi,
                           batasAktif: Boolean(batasAktif), batasDet: String(batasDet ?? ''), isSensitif: Boolean(isSensitif) };
             this.modalUbah = true;
         }
     }"
     x-init="
         @if(old('_form') === 'tambah') $nextTick(() => { modalTambah = true; }); @endif
         @if(old('_form') === 'ubah') $nextTick(() => { openUbah({{ (int)old('id',0) }}, @js(old('kode','')), @js(old('nama','')), @js(old('format_dasar','Pilihan Ganda')), @js(old('pola_skoring','kognitif')), @js(old('durasi_total_menit','')), @js(old('deskripsi','')), {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }}, @js(old('batas_waktu_per_soal_detik','')), {{ old('is_sensitif') ? 'true' : 'false' }}); }); @endif
     ">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-[28px] leading-9 font-semibold text-[#00303c]">Daftar Alat Tes</h2>
            <p class="mt-0.5 text-[14px] text-[#40484b]">Kelola jenis instrumen tes psikometri yang tersedia dalam sistem.</p>
        </div>
        @if(auth()->user()->hasIzin('kategori_tes.kelola'))
        <button type="button" @click="modalTambah = true"
           class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95 whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Alat Tes
        </button>
        @endif
    </div>
    </div>{{-- /STICKY HEADER --}}

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">quiz</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Total Instrumen</p>
                <p class="text-[18px] leading-6 font-bold text-[#191c1e]">{{ $totalAlat }}</p>
            </div>
        </div>
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Aktif</p>
                <p class="text-[18px] leading-6 font-bold text-[#191c1e]">{{ $totalAlat }}</p>
            </div>
        </div>
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-violet-50 flex items-center justify-center text-violet-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">inventory_2</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Total Soal</p>
                <p class="text-[18px] leading-6 font-bold text-[#191c1e]">{{ number_format($totalSoal ?? 0) }}</p>
            </div>
        </div>
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">trending_up</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Terpopuler</p>
                <p class="text-[14px] leading-5 font-bold text-[#191c1e]">
                    {{ $alatPopuler->nama ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white border border-[#e0e3e5] rounded-xl overflow-hidden">
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left text-sm border-collapse">
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center" style="color:#7db8c2;width:52px">No.</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold w-24" style="color: #7db8c2;">KODE</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold" style="color: #7db8c2;">Nama Alat Tes</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-center" style="color: #7db8c2;">Soal</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold" style="color: #7db8c2;">Format</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold" style="color: #7db8c2;">Status</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]/60">
                    @forelse ($alatTes as $alat)
                        @php
                            $kode = $alat['kode'];

                            $badgeKode = kode_badge_class($kode);
                        @endphp
                        <tr class="hover:bg-[#f2f4f6] transition-colors group">
                            <td class="px-4 py-4 text-sm text-[#71787c] tabular-nums text-center">{{ ($alatTes->currentPage() - 1) * $alatTes->perPage() + $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badgeKode }}">
                                    {{ $kode }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-[#191c1e]">{{ $alat['nama'] }}</p>
                                <p class="text-[11px] text-[#40484b] mt-0.5">{{ $alat['format_dasar'] }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center font-medium text-[#40484b]">
                                {{ number_format($alat->soal_count) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block border border-[#c0c8cb] px-3 py-1 rounded-full text-[12px] font-medium text-[#40484b]">
                                    {{ $alat['format_dasar'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($alat['is_sensitif'])
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Sensitif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if(auth()->user()->hasIzin('kategori_tes.kelola'))
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button"
                                                @click="openUbah({{ $alat['id'] }}, @js($alat['kode']), @js($alat['nama']), @js($alat['format_dasar']), @js($alat['pola_skoring']), @js($alat['durasi_total_menit']), @js($alat['deskripsi'] ?? ''), {{ $alat['batas_waktu_per_soal_aktif'] ? 'true' : 'false' }}, @js($alat['batas_waktu_per_soal_detik']), {{ $alat['is_sensitif'] ? 'true' : 'false' }})"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                        </button>
                                        <a href="{{ route('admin.alat-tes.norma', $alat['id']) }}"
                                           class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#e0e3e5] hover:text-[#2C5F6F] transition-colors"
                                           title="Kelola Norma">
                                            <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                                        </a>
                                        <a href="{{ route('admin.alat-tes.dimensi', $alat['id']) }}"
                                           class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#e0e3e5] hover:text-[#2C5F6F] transition-colors"
                                           title="Kelola Dimensi">
                                            <span class="material-symbols-outlined text-[18px]">psychology</span>
                                        </a>
                                        <button type="button"
                                                class="p-2 text-[#ba1a1a] hover:bg-[#fef2f2] rounded-lg transition-colors"
                                                title="Hapus"
                                                onclick="confirmHapusAlatTes({{ $alat['id'] }}, '{{ addslashes($alat['nama']) }}')">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">quiz</span>
                                <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum Ada Alat Tes</p>
                                <p class="text-[12px] text-[#71787c] mb-4">Tambahkan instrumen tes psikometri untuk digunakan dalam sesi.</p>
                                @if(auth()->user()->hasIzin('kategori_tes.kelola'))
                                    <button type="button" @click="modalTambah = true" class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                                        <span class="material-symbols-outlined text-[16px]">add</span>
                                        Tambah Alat Tes
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 border-t border-[#e0e3e5] bg-[#f2f4f6]">
            <p class="text-[12px] text-[#71787c]">
                Menampilkan
                <span class="font-semibold text-[#191c1e]">{{ $alatTes->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#191c1e]">{{ $alatTes->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-[#191c1e]">{{ $alatTes->total() }}</span> data
            </p>
            @if($alatTes->hasPages())
                {{ $alatTes->links() }}
            @endif
        </div>
    </div>

    {{-- INFO CARD --}}
    <div class="p-5 bg-[#0d4757]/5 border border-[#0d4757]/10 rounded-xl flex items-start gap-4">
        <div class="p-2 bg-[#0d4757]/10 rounded-lg text-[#2C5F6F] shrink-0">
            <span class="material-symbols-outlined text-[20px]">info</span>
        </div>
        <div>
            <h4 class="text-[14px] font-semibold text-[#00303c]">Informasi Status</h4>
            <p class="text-[13px] text-[#40484b] mt-1">
                Alat tes bertanda <span class="inline-flex items-center gap-1 text-rose-600 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-rose-600 inline-block"></span>Sensitif</span> memuat konten psikologis klinis dan hanya dapat diakses oleh psikolog terverifikasi.
                Status Nonaktif akan menyembunyikan instrumen dari panel peserta.
            </p>
        </div>
    </div>

{{-- MODAL TAMBAH ALAT TES --}}
<template x-teleport="body">
    <div x-show="modalTambah" x-cloak
         style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="modalTambah = false">
        <div class="bg-white rounded-2xl shadow-xl w-full" style="max-width:500px" @click.stop>
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#e0e3e5]">
                <h3 class="text-[15px] font-semibold text-[#191c1e]">Tambah Alat Tes</h3>
                <button type="button" @click="modalTambah = false"
                        class="p-1 rounded-lg text-[#71787c] hover:bg-[#f2f4f6] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.alat-tes.simpan') }}" class="px-5 py-4 space-y-3">
                @csrf
                <input type="hidden" name="_form" value="tambah">
                {{-- Kode + Nama --}}
                <div class="flex gap-3">
                    <div class="w-28 shrink-0">
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Kode</label>
                        <input name="kode" type="text" value="{{ old('kode') }}" required maxlength="20" placeholder="DISC"
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Nama Lengkap</label>
                        <input name="nama" type="text" value="{{ old('nama') }}" maxlength="150" placeholder="Nama lengkap alat tes"
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                    </div>
                </div>
                {{-- Format + Pola --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Format Soal</label>
                        <div class="relative">
                            <select name="format_dasar" required
                                    class="w-full appearance-none bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none cursor-pointer">
                                @foreach ($pilihanFormat as $format)
                                    <option value="{{ $format }}" @selected(old('format_dasar') === $format)>{{ $format }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#40484b] text-[18px]">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Pola Skoring</label>
                        <div class="relative">
                            <select name="pola_skoring" required
                                    class="w-full appearance-none bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none cursor-pointer">
                                <option value="kognitif" @selected(old('pola_skoring') === 'kognitif')>Kognitif</option>
                                <option value="forced_choice" @selected(old('pola_skoring') === 'forced_choice')>Forced Choice</option>
                                <option value="forced_choice_rollup" @selected(old('pola_skoring') === 'forced_choice_rollup')>Forced Choice Rollup</option>
                                <option value="grid" @selected(old('pola_skoring') === 'grid')>Grid</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#40484b] text-[18px]">expand_more</span>
                        </div>
                    </div>
                </div>
                {{-- Durasi --}}
                <div>
                    <label class="block text-[12px] font-medium text-[#40484b] mb-1">Durasi Total (menit)</label>
                    <input name="durasi_total_menit" type="number" min="0" value="{{ old('durasi_total_menit') }}"
                           class="w-36 bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                </div>
                {{-- Deskripsi --}}
                <div>
                    <label class="block text-[12px] font-medium text-[#40484b] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Jelaskan tujuan dan fungsi alat tes ini..."
                              class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none resize-none">{{ old('deskripsi') }}</textarea>
                </div>
                {{-- Batas Waktu --}}
                <div class="p-3 bg-[#f2f4f6] rounded-xl border border-[#e0e3e5]">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                        <input type="checkbox" name="batas_waktu_per_soal_aktif" value="1" x-model="batasAktif"
                               {{ old('batas_waktu_per_soal_aktif') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] cursor-pointer">
                        <span class="text-[13px] font-medium text-[#191c1e]">Aktifkan Batas Waktu per Soal</span>
                    </label>
                    <div x-show="batasAktif" x-cloak class="mt-2 flex items-center gap-2">
                        <input name="batas_waktu_per_soal_detik" type="number" min="1" placeholder="30"
                               value="{{ old('batas_waktu_per_soal_detik') }}"
                               class="w-24 bg-white border border-[#e0e3e5] rounded-lg px-3 py-1.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                        <span class="text-[12px] text-[#40484b]">detik per soal</span>
                    </div>
                </div>
                {{-- Sensitif --}}
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="hidden" name="is_sensitif" value="0">
                        <input type="checkbox" name="is_sensitif" value="1" {{ old('is_sensitif') ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 rounded border-amber-400 text-amber-600 focus:ring-amber-600 cursor-pointer">
                        <div>
                            <p class="text-[13px] font-medium text-[#191c1e]">Kategori Sensitif</p>
                            <p class="text-[11px] text-[#854d0e] mt-0.5">Akses terbatas untuk psikolog terverifikasi.</p>
                        </div>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="modalTambah = false"
                            class="rounded-xl border border-[#e0e3e5] px-4 py-2 text-[13px] font-semibold text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#2C5F6F] px-5 py-2 text-[13px] font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[15px]">save</span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

{{-- MODAL EDIT ALAT TES --}}
<template x-teleport="body">
    <div x-show="modalUbah" x-cloak
         style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="modalUbah = false">
        <div class="bg-white rounded-2xl shadow-xl w-full" style="max-width:500px" @click.stop>
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#e0e3e5]">
                <h3 class="text-[15px] font-semibold text-[#191c1e]">Edit Alat Tes</h3>

                <button type="button" @click="modalUbah = false"
                        class="p-1 rounded-lg text-[#71787c] hover:bg-[#f2f4f6] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <form method="POST" :action="'{{ url('admin/alat-tes') }}/' + ubah.id" class="px-5 py-4 space-y-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="ubah">
                <input type="hidden" name="id" :value="ubah.id">
                {{-- Kode + Nama --}}
                <div class="flex gap-3">
                    <div class="w-28 shrink-0">
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Kode</label>
                        <input name="kode" type="text" :value="ubah.kode" required maxlength="20"
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Nama Lengkap</label>
                        <input name="nama" type="text" :value="ubah.nama" maxlength="150"
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                    </div>
                </div>
                {{-- Format + Pola --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Format Soal</label>
                        <div class="relative">
                            <select name="format_dasar" required
                                    class="w-full appearance-none bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none cursor-pointer">
                                @foreach ($pilihanFormat as $format)
                                    <option value="{{ $format }}" :selected="ubah.formatDasar === '{{ $format }}'">{{ $format }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#40484b] text-[18px]">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#40484b] mb-1">Pola Skoring</label>
                        <div class="relative">
                            <select name="pola_skoring" required
                                    class="w-full appearance-none bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none cursor-pointer">
                                <option value="kognitif" :selected="ubah.polaSkoring === 'kognitif'">Kognitif</option>
                                <option value="forced_choice" :selected="ubah.polaSkoring === 'forced_choice'">Forced Choice</option>
                                <option value="forced_choice_rollup" :selected="ubah.polaSkoring === 'forced_choice_rollup'">Forced Choice Rollup</option>
                                <option value="grid" :selected="ubah.polaSkoring === 'grid'">Grid</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#40484b] text-[18px]">expand_more</span>
                        </div>
                    </div>
                </div>
                {{-- Durasi --}}
                <div>
                    <label class="block text-[12px] font-medium text-[#40484b] mb-1">Durasi Total (menit)</label>
                    <input name="durasi_total_menit" type="number" min="0" :value="ubah.durasi"
                           class="w-36 bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                </div>
                {{-- Deskripsi --}}
                <div>
                    <label class="block text-[12px] font-medium text-[#40484b] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Jelaskan tujuan dan fungsi alat tes ini..."
                              class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none resize-none"
                              x-model="ubah.deskripsi"></textarea>
                </div>
                {{-- Batas Waktu --}}
                <div class="p-3 bg-[#f2f4f6] rounded-xl border border-[#e0e3e5]">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                        <input type="checkbox" name="batas_waktu_per_soal_aktif" value="1" x-model="ubah.batasAktif"
                               class="h-4 w-4 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] cursor-pointer">
                        <span class="text-[13px] font-medium text-[#191c1e]">Aktifkan Batas Waktu per Soal</span>
                    </label>
                    <div x-show="ubah.batasAktif" x-cloak class="mt-2 flex items-center gap-2">
                        <input name="batas_waktu_per_soal_detik" type="number" min="1" placeholder="30"
                               :value="ubah.batasDet"
                               class="w-24 bg-white border border-[#e0e3e5] rounded-lg px-3 py-1.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] outline-none">
                        <span class="text-[12px] text-[#40484b]">detik per soal</span>
                    </div>
                </div>
                {{-- Sensitif --}}
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="hidden" name="is_sensitif" value="0">
                        <input type="checkbox" name="is_sensitif" value="1" x-model="ubah.isSensitif"
                               class="mt-0.5 h-4 w-4 rounded border-amber-400 text-amber-600 focus:ring-amber-600 cursor-pointer">
                        <div>
                            <p class="text-[13px] font-medium text-[#191c1e]">Kategori Sensitif</p>
                            <p class="text-[11px] text-[#854d0e] mt-0.5">Akses terbatas untuk psikolog terverifikasi.</p>
                        </div>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="modalUbah = false"
                            class="rounded-xl border border-[#e0e3e5] px-4 py-2 text-[13px] font-semibold text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#2C5F6F] px-5 py-2 text-[13px] font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[15px]">save</span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

</div>{{-- /x-data --}}

<style>[x-cloak] { display: none !important; }</style>

@push('scripts')
<script>
    const searchInput = document.querySelector('#alat-tes-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(val) ? '' : 'none';
            });
        });
    }

    function confirmHapusAlatTes(id, nama) {
        Swal.fire({
            icon: 'warning', title: 'Hapus Alat Tes?',
            text: `Alat tes "${nama}" akan dihapus secara permanen.`,
            showCancelButton: true, confirmButtonColor: '#2C5F6F', cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/alat-tes/${id}`;
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        });
    }
</script>
@endpush
@endsection
