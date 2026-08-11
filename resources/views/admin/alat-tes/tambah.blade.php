@extends('layouts.admin', ['judulHalaman' => 'Tambah Alat Tes'])

@section('content')
<div class="w-full"
     x-data="{
         batasAktif: {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }},
         formatDasar: '{{ old('format_dasar', 'Pilihan Ganda') }}'
     }">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-[20px] font-semibold text-[#00303c]">Tambah/Edit Alat Tes</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Lengkapi detail instrumen untuk diaktifkan dalam sistem.</p>
        </div>
        <a href="{{ route('admin.alat-tes.index') }}"
           class="inline-flex items-center gap-1.5 text-[13px] font-medium text-[#40484b] hover:text-[#2C5F6F] transition-colors border border-[#e0e3e5] hover:border-[#2C5F6F]/30 px-3 py-2 rounded-xl bg-white">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.alat-tes.simpan') }}" class="space-y-5">
        @csrf

        {{--基本信息--}}
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-6 space-y-5">
            <div class="grid gap-5 md:grid-cols-3">
                <div class="md:col-span-1">
                    <label for="kode" class="block text-[12px] font-medium text-[#40484b] mb-2">Kode Alat Tes</label>
                    <input id="kode" name="kode" type="text" value="{{ old('kode') }}" required maxlength="20"
                           placeholder="Kode"
                           class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                    <p class="mt-1.5 text-[11px] text-[#919eab]">Kode singkat instrumen (misal: DISC)</p>
                </div>

                <div class="md:col-span-2">
                    <label for="nama" class="block text-[12px] font-medium text-[#40484b] mb-2">Nama Lengkap Alat Tes</label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama') }}" maxlength="150"
                           placeholder="Dominance, Influence, Steadiness, Conscientiousness"
                           class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                </div>

                <div>
                    <label for="format_dasar" class="block text-[12px] font-medium text-[#40484b] mb-2">Format Soal</label>
                    <div class="relative">
                        <select id="format_dasar" name="format_dasar" x-model="formatDasar" required
                                class="w-full appearance-none bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none cursor-pointer">
                            @foreach ($pilihanFormat as $format)
                                <option value="{{ $format }}" @selected(old('format_dasar') === $format)>{{ $format }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#40484b] text-[18px]">expand_more</span>
                    </div>
                </div>

                <div>
                    <label for="pola_skoring" class="block text-[12px] font-medium text-[#40484b] mb-2">Pola Skoring</label>
                    <div class="relative">
                        <select id="pola_skoring" name="pola_skoring" required
                                class="w-full appearance-none bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none cursor-pointer">
                            <option value="kognitif" @selected(old('pola_skoring') === 'kognitif')">Kognitif — untuk CFIT</option>
                            <option value="forced_choice" @selected(old('pola_skoring') === 'forced_choice')">Forced Choice — untuk EPPS</option>
                            <option value="forced_choice_rollup" @selected(old('pola_skoring') === 'forced_choice_rollup')">Forced Choice Rollup — untuk Papikostik</option>
                            <option value="grid" @selected(old('pola_skoring') === 'grid')">Grid — untuk Kraepelin</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#40484b] text-[18px]">expand_more</span>
                    </div>
                </div>

                <div>
                    <label for="durasi_total_menit" class="block text-[12px] font-medium text-[#40484b] mb-2">Durasi Total (menit)</label>
                    <input id="durasi_total_menit" name="durasi_total_menit" type="number" min="0" value="{{ old('durasi_total_menit') }}"
                           class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-[12px] font-medium text-[#40484b] mb-2">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          placeholder="Jelaskan tujuan dan fungsi alat tes ini..."
                          class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none resize-none">{{ old('deskripsi') }}</textarea>
            </div>
        </div>

        {{-- Konfigurasi Tambahan --}}
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-6 space-y-5">
            <h3 class="text-[14px] font-semibold text-[#191c1e]">Konfigurasi Tambahan</h3>

            <div class="grid gap-5 md:grid-cols-2">
                {{-- Batas Waktu per Soal --}}
                <div class="p-4 bg-[#f2f4f6] rounded-xl border border-[#e0e3e5]">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                        <input type="checkbox" name="batas_waktu_per_soal_aktif" value="1" x-model="batasAktif"
                               {{ old('batas_waktu_per_soal_aktif') ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] cursor-pointer">
                        <div>
                            <p class="text-[13px] font-medium text-[#191c1e]">Aktifkan Batas Waktu per Soal</p>
                            <p class="text-[11px] text-[#40484b] mt-0.5">Peserta harus menjawab dalam waktu yang ditentukan.</p>
                        </div>
                    </label>
                    <div x-show="batasAktif" x-cloak class="mt-3">
                        <label for="batas_waktu_per_soal_detik" class="block text-[11px] font-medium text-[#40484b] mb-1.5">Detik per Soal</label>
                        <input id="batas_waktu_per_soal_detik" name="batas_waktu_per_soal_detik" type="number" min="1"
                               value="{{ old('batas_waktu_per_soal_detik') }}"
                               class="w-40 bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                    </div>
                </div>
            </div>

            {{-- Sensitif --}}
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_sensitif" value="0">
                    <input type="checkbox" name="is_sensitif" value="1" {{ old('is_sensitif') ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded border-amber-400 text-amber-600 focus:ring-amber-600 cursor-pointer">
                    <div>
                        <p class="text-[13px] font-medium text-[#191c1e]">Kategori Sensitif</p>
                        <p class="text-[11px] text-[#854d0e] mt-0.5">Tandai jika alat tes memuat konten psikologis klinis. Akses terbatas untuk psikolog terverifikasi.</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Sticky Footer --}}
        <div class="sticky bottom-0 -mx-6 -mb-6 flex items-center justify-end gap-3 border-t border-[#e0e3e5] bg-white/90 backdrop-blur-sm px-6 py-4 shadow-[0_-2px_8px_rgba(0,0,0,0.06)]">
            <a href="{{ route('admin.alat-tes.index') }}"
               class="rounded-xl border border-[#e0e3e5] px-5 py-2.5 text-[13px] font-semibold text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-[#2C5F6F] px-6 py-2.5 text-[13px] font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan
            </button>
        </div>
    </form>

</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
