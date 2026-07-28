@extends('layouts.admin', ['judulHalaman' => 'Tambah Alat Tes'])

@section('content')
<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
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

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Tambah Alat Tes</h2>
            <p class="mt-0.5 text-xs text-slate-500">Lengkapi formulir di bawah untuk mendaftarkan alat tes baru.</p>
        </div>
        <a href="{{ route('admin.alat-tes.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.alat-tes.simpan') }}" class="space-y-4">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="nama" class="block text-sm font-medium text-slate-700">Nama Alat Tes <span class="text-rose-500">*</span></label>
                <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required maxlength="255"
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>

            <div>
                <label for="format_dasar" class="block text-sm font-medium text-slate-700">Format Dasar <span class="text-rose-500">*</span></label>
                <select id="format_dasar" name="format_dasar" x-model="formatDasar" required
                        class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    @foreach ($pilihanFormat as $format)
                        <option value="{{ $format }}" @selected(old('format_dasar') === $format)>{{ $format }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="durasi_total_menit" class="block text-sm font-medium text-slate-700">Durasi Total <span class="text-slate-400">(menit, opsional)</span></label>
                <input id="durasi_total_menit" name="durasi_total_menit" type="number" min="0" value="{{ old('durasi_total_menit') }}"
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>
        </div>

        <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                <input type="checkbox" name="batas_waktu_per_soal_aktif" value="1" x-model="batasAktif"
                       {{ old('batas_waktu_per_soal_aktif') ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-[#2C5F6F] focus:ring-[#2C5F6F]">
                <div>
                    <p class="text-sm font-medium text-slate-800">Aktifkan Batas Waktu per Soal</p>
                    <p class="text-xs text-slate-500">Jika aktif, peserta harus menjawab dalam waktu yang ditentukan.</p>
                </div>
            </label>

            <div x-show="batasAktif" x-cloak class="mt-3">
                <label for="batas_waktu_per_soal_detik" class="block text-sm font-medium text-slate-700">Detik per Soal</label>
                <input id="batas_waktu_per_soal_detik" name="batas_waktu_per_soal_detik" type="number" min="1"
                       value="{{ old('batas_waktu_per_soal_detik') }}"
                       class="mt-1 block w-40 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>
        </div>

        <div class="rounded-md border border-amber-200 bg-amber-50 p-4">
            <label class="flex items-start gap-3">
                <input type="hidden" name="is_sensitif" value="0">
                <input type="checkbox" name="is_sensitif" value="1" {{ old('is_sensitif') ? 'checked' : '' }}
                       class="mt-0.5 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-600">
                <div>
                    <p class="text-sm font-medium text-slate-800">Kategori Sensitif</p>
                    <p class="text-xs text-slate-500">Tandai jika alat tes memuat konten psikologis klinis (misalnya kepribadian patologis). Aksesnya akan dibatasi untuk psikolog terverifikasi.</p>
                </div>
            </label>
        </div>

        <section class="rounded-md border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-base font-semibold text-slate-900 mb-3">Konfigurasi Dimensi Penilaian</h3>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="tipe_kategori_global" class="block text-sm font-medium text-slate-700">Tipe Kategori <span class="text-rose-500">*</span></label>
                    <select id="tipe_kategori_global" name="tipe_kategori" x-model="tipe_kategori" required
                            class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="psikogram" @selected(old('tipe_kategori') === 'psikogram')>Psikogram</option>
                        <option value="klinis" @selected(old('tipe_kategori') === 'klinis')>Klinis</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Menentukan field ambang batas yang muncul per dimensi.</p>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <p class="text-sm text-slate-600">Tambahkan setiap dimensi penilaian untuk alat tes ini.</p>
                <button type="button" @click="addDimensi()" :disabled="!tipe_kategori"
                        class="rounded-md border border-[#2C5F6F] bg-white px-4 py-2 text-sm font-medium text-[#2C5F6F] shadow-sm hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">
                    + Tambah Dimensi
                </button>
            </div>

            <div class="mt-4 space-y-3" id="dimensi-container">
                <template x-for="(dimensi, index) in dimensiArr" :key="index">
                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                        <div class="mb-3 flex items-start justify-between">
                            <h4 class="text-sm font-semibold text-slate-900">
                                Dimensi #<span x-text="index + 1"></span>
                            </h4>
                            <button type="button" @click="removeDimensi(index)"
                                    class="rounded-md p-1 text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Nama Dimensi</label>
                                <input type="text" x-model="dimensi.nama_dimensi"
                                       class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]"
                                       placeholder="Contoh: Dominance">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Kode <span class="text-slate-400">(opsional)</span></label>
                                <input type="text" x-model="dimensi.kode"
                                       class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]"
                                       placeholder="Contoh: D">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Bidang Psikogram</label>
                                <select x-model="dimensi.bidang_psikogram"
                                        class="block w-full rounded-md border border-slate-300 bg-white px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                    <option value="">-- Pilih Bidang --</option>
                                    <option value="Intelektual">Intelektual</option>
                                    <option value="Sikap Kerja">Sikap Kerja</option>
                                    <option value="Kepribadian">Kepribadian</option>
                                    <option value="Potensi Kerja">Potensi Kerja</option>
                                    <option value="Sensitif">Sensitif</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="mb-1 block text-xs font-medium text-slate-600">Deskripsi Aspek <span class="text-slate-400">(1 kalimat)</span></label>
                                <textarea x-model="dimensi.deskripsi_aspek" rows="2"
                                          class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]"
                                          placeholder="Contoh: Mengukur tingkat kepemimpinan dan kemauan mengontrol situasi."></textarea>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Skor Min</label>
                                <input type="number" min="0" x-model.number="dimensi.skor_min"
                                       class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-slate-600">Skor Max</label>
                                <input type="number" min="0" x-model.number="dimensi.skor_max"
                                       class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            </div>

                            <template x-if="tipe_kategori === 'psikogram'">
                                <div class="md:col-span-2 lg:col-span-3">
                                    <label class="mb-2 block text-xs font-medium text-slate-600">Ambang Batas <span class="text-slate-400">(batas ATAS kategori; di atas ambang_b otomatis BS)</span></label>
                                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                        <div>
                                            <label class="block text-xs text-rose-600">R (Red)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_r"
                                                   class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-amber-600">K (Kuning)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_k"
                                                   class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-emerald-600">C (Hijau)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_c"
                                                   class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-sky-600">B (Biru)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_b"
                                                   class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="tipe_kategori === 'klinis'">
                                <div class="md:col-span-2 lg:col-span-3">
                                    <label class="mb-2 block text-xs font-medium text-slate-600">Ambang Batas <span class="text-slate-400">(batas ATAS; di atas ambang_perlu_perhatian otomatis Signifikan)</span></label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-emerald-600">Normal (ATAS)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_normal"
                                                   class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-amber-600">Perlu Perhatian (ATAS)</label>
                                            <input type="number" min="0" x-model.number="dimensi.ambang_perlu_perhatian"
                                                   class="block w-full rounded-md border border-slate-300 px-2 py-1 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="dimensiArr.length === 0" x-cloak class="rounded-md border border-dashed border-slate-300 bg-white p-6 text-center text-xs text-slate-500">
                    Belum ada dimensi. Pilih tipe kategori lalu klik "+ Tambah Dimensi" untuk mulai menambahkan.
                </div>
            </div>
        </section>

        <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.alat-tes.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Simpan</button>
        </div>
    </form>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection