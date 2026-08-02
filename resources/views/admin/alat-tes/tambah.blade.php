@extends('layouts.admin', ['judulHalaman' => 'Tambah Alat Tes'])

@section('content')
<div class="w-full"
     x-data="{
         batasAktif: {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }},
         formatDasar: '{{ old('format_dasar', 'Pilihan Ganda') }}',
         tipe_kategori: '{{ old('tipe_kategori', '') }}',
         dimensiArr: [],
         addDimensi() {
             this.dimensiArr.push({
                 nama_dimensi: '',
                 kode: '',
                 bidang_psikogram: '',
                 deskripsi_aspek: '',
                 skor_min: 0,
                 skor_max: 0,
                 tipe_kategori: this.tipe_kategori,
                 ambang_r: 0,
                 ambang_k: 0,
                 ambang_c: 0,
                 ambang_b: 0,
                 ambang_normal: 0,
                 ambang_perlu_perhatian: 0
             });
         },
         removeDimensi(index) {
             this.dimensiArr.splice(index, 1);
         }
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
                {{-- TODO: field `kode` belum ada di database --}}
                {{-- Disarankan menambahkan kolom `kode` UNIQUE di migration alat_tes untuk menyimpan kode alat tes (DISC, IST, EPPS, dll) --}}
                <div class="md:col-span-1">
                    <label for="nama" class="block text-[12px] font-medium text-[#40484b] mb-2">Kode Alat Tes</label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required maxlength="255"
                           placeholder="DISC"
                           class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                    <p class="mt-1.5 text-[11px] text-[#919eab]">Kode singkat instrumen (misal: DISC, IST)</p>
                </div>

                <div class="md:col-span-2">
                    <label for="nama_lengkap" class="block text-[12px] font-medium text-[#40484b] mb-2">Nama Lengkap Alat Tes</label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}"
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

                {{-- Status Aktif Toggle --}}
                <div class="p-4 bg-[#f2f4f6] rounded-xl border border-[#e0e3e5] flex items-center justify-between">
                    <div>
                        <span class="block text-[13px] font-semibold text-[#191c1e]">Status Aktif</span>
                        <span class="text-[11px] text-[#40484b]">Aktifkan untuk penggunaan segera</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-[#e6e8ea] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2C5F6F]"></div>
                    </label>
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

        {{-- Dimensi Penilaian --}}
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-6 space-y-5">
            <h3 class="text-[14px] font-semibold text-[#191c1e]">Konfigurasi Dimensi Penilaian</h3>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="tipe_kategori_global" class="block text-[12px] font-medium text-[#40484b] mb-2">Tipe Kategori <span class="text-rose-500">*</span></label>
                    <select id="tipe_kategori_global" name="tipe_kategori" x-model="tipe_kategori" required
                            class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none cursor-pointer">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="psikogram" @selected(old('tipe_kategori') === 'psikogram')>Psikogram</option>
                        <option value="klinis" @selected(old('tipe_kategori') === 'klinis')>Klinis</option>
                    </select>
                    <p class="mt-1.5 text-[11px] text-[#919eab]">Menentukan field ambang batas yang muncul per dimensi.</p>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-[13px] text-[#40484b]">Tambahkan setiap dimensi penilaian untuk alat tes ini.</p>
                <button type="button" @click="addDimensi()" :disabled="!tipe_kategori"
                        class="inline-flex items-center gap-1.5 border border-[#2C5F6F] text-[#2C5F6F] hover:bg-[#2C5F6F]/5 px-4 py-2 rounded-xl text-[13px] font-semibold transition-all disabled:cursor-not-allowed disabled:opacity-50">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Dimensi
                </button>
            </div>

            <div class="space-y-4" id="dimensi-container">
                <template x-for="(dimensi, index) in dimensiArr" :key="index">
                    <div class="rounded-xl border border-[#e0e3e5] bg-[#f2f4f6] p-5">
                        <div class="mb-4 flex items-start justify-between">
                            <h4 class="text-[13px] font-semibold text-[#191c1e]">
                                Dimensi #<span x-text="index + 1"></span>
                            </h4>
                            <button type="button" @click="removeDimensi(index)"
                                    class="rounded-lg p-1.5 text-[#40484b] hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-[11px] font-medium text-[#40484b]">Nama Dimensi</label>
                                <input type="text" x-model="dimensi.nama_dimensi"
                                       class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none"
                                       placeholder="Contoh: Dominance">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[11px] font-medium text-[#40484b]">Kode <span class="text-[#919eab]">(opsional)</span></label>
                                <input type="text" x-model="dimensi.kode"
                                       class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none"
                                       placeholder="Contoh: D">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[11px] font-medium text-[#40484b]">Bidang Psikogram</label>
                                <select x-model="dimensi.bidang_psikogram"
                                        class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none cursor-pointer">
                                    <option value="">-- Pilih Bidang --</option>
                                    <option value="Intelektual">Intelektual</option>
                                    <option value="Sikap Kerja">Sikap Kerja</option>
                                    <option value="Kepribadian">Kepribadian</option>
                                    <option value="Potensi Kerja">Potensi Kerja</option>
                                    <option value="Sensitif">Sensitif</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="mb-1.5 block text-[11px] font-medium text-[#40484b]">Deskripsi Aspek <span class="text-[#919eab]">(1 kalimat)</span></label>
                                <textarea x-model="dimensi.deskripsi_aspek" rows="2"
                                          class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none resize-none"
                                          placeholder="Contoh: Mengukur tingkat kepemimpinan dan kemauan mengontrol situasi."></textarea>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[11px] font-medium text-[#40484b]">Skor Min</label>
                                <input type="number" min="0" x-model.number="dimensi.skor_min"
                                       class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[11px] font-medium text-[#40484b]">Skor Max</label>
                                <input type="number" min="0" x-model.number="dimensi.skor_max"
                                       class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                            </div>

                            <template x-if="tipe_kategori === 'psikogram'">
                                <div class="md:col-span-2 lg:col-span-3">
                                    <label class="mb-2 block text-[11px] font-medium text-[#40484b]">Ambang Batas <span class="text-[#919eab]">(batas ATAS; di atas ambang_b otomatis BS)</span></label>
                                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                        <div>
                                            <label class="block text-[11px] text-rose-600">R (Red)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_r"
                                                   class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-amber-600">K (Kuning)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_k"
                                                   class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-emerald-600">C (Hijau)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_c"
                                                   class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-sky-600">B (Biru)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_b"
                                                   class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="tipe_kategori === 'klinis'">
                                <div class="md:col-span-2 lg:col-span-3">
                                    <label class="mb-2 block text-[11px] font-medium text-[#40484b]">Ambang Batas <span class="text-[#919eab]">(batas ATAS; di atas ambang_perlu_perhatian otomatis Signifikan)</span></label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] text-emerald-600">Normal (ATAS)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_normal"
                                                   class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-amber-600">Perlu Perhatian (ATAS)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_perlu_perhatian"
                                                   class="w-full bg-white border border-[#e0e3e5] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="dimensiArr.length === 0" x-cloak class="rounded-xl border border-dashed border-[#c0c8cb] bg-[#f2f4f6] p-6 text-center text-[12px] text-[#919eab]">
                    Belum ada dimensi. Pilih tipe kategori lalu klik "+ Tambah Dimensi" untuk mulai menambahkan.
                </div>
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
