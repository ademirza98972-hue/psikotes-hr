@extends('layouts.admin', ['judulHalaman' => 'Tambah Penjadwalan Tes'])

@php
    $warnaAlatTes = [
        'DISC'   => 'bg-blue-50 text-blue-700 border border-blue-200',
        'IST'    => 'bg-violet-50 text-violet-700 border border-violet-200',
        'EPPS'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'MMPI-2' => 'bg-orange-50 text-orange-700 border border-orange-200',
    ];
@endphp

@section('content')
<div class="space-y-6" x-data="formPenjadwalan()">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-[#191c1e]">Tambah Penjadwalan Tes</h2>
            <p class="mt-0.5 text-sm text-[#41484b]">Buat sesi penilaian psikologis baru untuk karyawan atau kandidat.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.index') }}"
           class="inline-flex items-center gap-1 text-[#2C5F6F] font-semibold text-sm hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.penjadwalan-tes.simpan') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        @csrf

        {{-- LEFT COLUMN: SECTION 1 + 2 --}}
        <div class="lg:col-span-4 space-y-5">

            {{-- SECTION 1: Identitas Sesi --}}
            <section class="rounded-xl border border-[#c1c7cb] bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <span class="material-symbols-outlined text-[#2C5F6F]">info</span>
                    <h3 class="text-base font-semibold text-[#191c1e]">Identitas Sesi</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="nama_sesi" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Nama Sesi <span class="text-rose-500">*</span></label>
                        <input id="nama_sesi" name="nama_sesi" type="text" value="{{ old('nama_sesi') }}" required maxlength="255"
                               placeholder="mis. Rekrutmen Staff Finance Batch 1"
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                    </div>
                    <div>
                        <label for="departemen_terkait" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Departemen Terkait <span class="text-[#919eab]">(opsional)</span></label>
                        <select id="departemen_terkait" name="departemen_terkait"
                                class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                            <option value="">— Tidak terikat departemen —</option>
                            <option value="Lintas Departemen / Campuran" @selected(old('departemen_terkait') === 'Lintas Departemen / Campuran')>— Lintas Departemen / Campuran —</option>
                            @foreach ($daftarDepartemen as $dept)
                                <option value="{{ $dept }}" @selected(old('departemen_terkait') === $dept)>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="tanggal_mulai" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai') }}" required
                                   class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai') }}" required
                                   class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                        </div>
                    </div>
                </div>
            </section>

            {{-- SECTION 2: Pilih Peserta --}}
            <section class="rounded-xl border border-[#c1c7cb] bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <span class="material-symbols-outlined text-[#2C5F6F]">person_add</span>
                    <h3 class="text-base font-semibold text-[#191c1e]">Pilih Peserta</h3>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="sumber_peserta" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Sumber Peserta</label>
                            <select id="sumber_peserta" x-model="sumber"
                                    class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                <option value="karyawan">Data Karyawan</option>
                                <option value="kandidat">Data Kandidat</option>
                            </select>
                        </div>
                        <div>
                            <label for="filter_departemen" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Filter Departemen</label>
                            <select id="filter_departemen" x-model="filterDepartemen"
                                    class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                                <option value="Semua Departemen">Semua Departemen</option>
                                @foreach ($daftarDepartemen as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#41484b]">search</span>
                        <input id="cari_peserta" type="text" x-model="cariPeserta"
                               placeholder="Cari nama peserta..."
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl pl-10 pr-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                    </div>
                </div>

                <div class="border border-[#c1c7cb] rounded-xl overflow-hidden">
                    <div class="max-h-64 overflow-y-auto">
                        <template x-for="peserta in pesertaTampil" :key="peserta.name">
                            <label class="flex items-start gap-3 p-3 hover:bg-[#f2f4f6] transition-colors border-b border-[#e0e3e5] last:border-b-0 cursor-pointer"
                                   :class="pilihanPeserta[peserta.name] ? 'bg-[#f0fdf4] ring-1 ring-emerald-200' : ''">
                                <input type="checkbox" :value="peserta.name"
                                       @change="togglePeserta(peserta.name, $event.target.checked)"
                                       :checked="!!pilihanPeserta[peserta.name]"
                                       class="mt-0.5 h-4 w-4 rounded border-[#c1c7cb] text-[#2C5F6F] focus:ring-[#2C5F6F]">
                                <div>
                                    <p class="font-semibold text-[#191c1e] text-sm leading-tight" x-text="peserta.name"></p>
                                    <p class="text-[11px] text-[#41484b] mt-0.5">
                                        Departemen: <strong class="text-[#191c1e]" x-text="peserta.departemen"></strong>
                                        <span class="mx-1 text-[#c1c7cb]">·</span>
                                        Posisi: <span class="text-[#41484b]" x-text="peserta.posisi"></span>
                                    </p>
                                </div>
                            </label>
                        </template>
                        <p x-show="pesertaTampil.length === 0" class="py-6 text-center text-sm text-[#41484b]">
                            Tidak ada peserta yang cocok dengan filter.
                        </p>
                    </div>
                </div>
                <p class="mt-3 text-sm text-[#41484b] text-center">
                    <span class="font-bold text-[#2C5F6F]" x-text="jumlahPesertaTerpilih"></span> peserta dipilih dari
                    <span x-text="sumber === 'karyawan' ? @js(count($daftarKaryawan)) : @js(count($daftarKandidat))"></span> tersedia.
                </p>
            </section>

        </div>

        {{-- RIGHT COLUMN: SECTION 3 --}}
        <div class="lg:col-span-8">
            <section>
                <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#2C5F6F]">rule</span>
                        <h3 class="text-base font-semibold text-[#191c1e]">Assign Alat Tes per Peserta</h3>
                    </div>
                    <button type="button" @click="terapkanKeSemua()"
                            :disabled="jumlahPesertaTerpilih < 2"
                            :class="jumlahPesertaTerpilih >= 2 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-300 cursor-not-allowed'"
                            class="rounded-xl px-4 py-2 text-[11px] font-bold text-white transition-all shadow-sm flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">done_all</span>
                        Terapkan ke Semua Peserta
                    </button>
                </div>

                <div x-show="jumlahPesertaTerpilih === 0" class="rounded-xl border border-dashed border-[#c1c7cb] bg-white py-8 text-center text-sm text-[#41484b]">
                    Pilih peserta terlebih dahulu di atas.
                </div>

                <div x-show="jumlahPesertaTerpilih > 0" class="space-y-4">
                    <template x-for="(state, nama) in pilihanPeserta" :key="nama">
                        <div class="rounded-xl border border-[#c1c7cb] bg-white p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm" x-text="getInitials(nama)"></div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-[#191c1e] text-base" x-text="nama"></h4>
                                            <span class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                                Peserta Terpilih
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-[#41484b] mt-0.5" x-text="getInfoPeserta(nama)"></p>
                                    </div>
                                </div>
                                <button type="button" @click="togglePeserta(nama, false)"
                                        class="text-[#41484b] hover:text-rose-600 transition-colors p-1 rounded-lg hover:bg-rose-50">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="alat in daftarAlatTes" :key="alat.id">
                                    <label class="flex items-center gap-3 p-3 rounded-lg border border-[#c1c7cb] hover:bg-[#f2f4f6] transition-all cursor-pointer"
                                           :class="state.alat_tes.includes(alat.id) ? 'border-emerald-300 bg-emerald-50' : ''">
                                        <input type="checkbox" :value="alat.id"
                                               @change="toggleAlatTes(nama, alat.id, $event.target.checked)"
                                               :checked="state.alat_tes.includes(alat.id)"
                                               class="h-5 w-5 rounded border-[#c1c7cb] text-[#2C5F6F] focus:ring-[#2C5F6F]">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold text-[#191c1e] text-sm" x-text="alat.nama"></span>
                                                <span class="bg-[#e6e8ea] text-[#41484b] text-[10px] font-bold px-2 py-0.5 rounded border border-[#c1c7cb]"
                                                      x-text="alat.format_dasar"></span>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>

        {{-- STICKY FOOTER --}}
        <div class="lg:col-span-12 sticky bottom-0 -mx-2 bg-white border-t border-[#c1c7cb] px-6 py-4 flex justify-end gap-3 shadow-[0_-4px_20px_-10px_rgba(0,0,0,0.1)] z-10">
            <a href="{{ route('admin.penjadwalan-tes.index') }}"
               class="px-5 py-2.5 rounded-xl border border-[#c1c7cb] text-[#41484b] text-sm font-medium hover:bg-[#f2f4f6] transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#2C5F6F] text-white text-sm font-bold hover:opacity-90 active:scale-[0.98] transition-all shadow-md">
                Simpan Penjadwalan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('formPenjadwalan', () => ({
        sumber: 'karyawan',
        filterDepartemen: 'Semua Departemen',
        cariPeserta: '',
        pilihanPeserta: {},

        daftarKaryawan: @json($daftarKaryawan),
        daftarKandidat: @json($daftarKandidat),
        daftarAlatTes: @json($daftarAlatTes),

        init() {},

        get pesertaTampil() {
            const q = this.cariPeserta.toLowerCase().trim();
            const source = this.sumber === 'karyawan' ? this.daftarKaryawan : this.daftarKandidat;
            return source.filter(p => {
                const deptMatch = this.filterDepartemen === 'Semua Departemen' || p.departemen === this.filterDepartemen;
                const nameMatch = q === '' || p.name.toLowerCase().includes(q);
                return deptMatch && nameMatch;
            });
        },

        get jumlahPesertaTerpilih() {
            return Object.keys(this.pilihanPeserta).length;
        },

        getInfoPeserta(nama) {
            const all = [...this.daftarKaryawan, ...this.daftarKandidat];
            const p = all.find(x => x.name === nama);
            if (!p) return '';
            return p.departemen + ' · ' + p.posisi;
        },

        getInitials(nama) {
            return nama.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
        },

        togglePeserta(nama, checked) {
            if (checked) {
                this.pilihanPeserta[nama] = { alat_tes: [] };
            } else {
                delete this.pilihanPeserta[nama];
            }
        },

        toggleAlatTes(nama, alatId, checked) {
            if (!this.pilihanPeserta[nama]) return;
            const list = this.pilihanPeserta[nama].alat_tes;
            const idx = list.indexOf(alatId);
            if (checked && idx === -1) list.push(alatId);
            if (!checked && idx !== -1) list.splice(idx, 1);
        },

        terapkanKeSemua() {
            const names = Object.keys(this.pilihanPeserta);
            if (names.length < 2) return;
            const utama = this.pilihanPeserta[names[0]].alat_tes.slice();
            for (const nama of names) {
                this.pilihanPeserta[nama].alat_tes = utama.slice();
            }
        }
    }));
});
</script>
@endsection
