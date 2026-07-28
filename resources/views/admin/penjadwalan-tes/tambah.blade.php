@extends('layouts.admin', ['judulHalaman' => 'Tambah Penjadwalan Tes'])

@php
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
    ];
@endphp

@section('content')
<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
     x-data="formPenjadwalan()">

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Tambah Penjadwalan Tes</h2>
            <p class="mt-0.5 text-xs text-slate-500">Lengkapi formulir di bawah untuk menjadwalkan sesi tes baru.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
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

    <form method="POST" action="{{ route('admin.penjadwalan-tes.simpan') }}" class="space-y-6">
        @csrf

        {{-- SECTION 1: IDENTITAS SESI --}}
        <section>
            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-700">Identitas Sesi</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="nama_sesi" class="block text-sm font-medium text-slate-700">Nama Sesi <span class="text-rose-500">*</span></label>
                    <input id="nama_sesi" name="nama_sesi" type="text" value="{{ old('nama_sesi') }}" required maxlength="255"
                           placeholder="mis. Rekrutmen Staff Finance Batch 1"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="departemen_terkait" class="block text-sm font-medium text-slate-700">Departemen Terkait <span class="text-slate-400">(opsional)</span></label>
                    <select id="departemen_terkait" name="departemen_terkait"
                            class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="">— Tidak terikat departemen —</option>
                        <option value="Lintas Departemen / Campuran" @selected(old('departemen_terkait') === 'Lintas Departemen / Campuran')>— Lintas Departemen / Campuran —</option>
                        @foreach ($daftarDepartemen as $dept)
                            <option value="{{ $dept }}" @selected(old('departemen_terkait') === $dept)>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-slate-700">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-slate-700">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>
        </section>

        {{-- SECTION 2: PILIH PESERTA (filter departemen + nama) --}}
        <section>
            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-700">Pilih Peserta</h3>

            <div class="grid gap-3 md:grid-cols-3">
                <div>
                    <label for="sumber_peserta" class="block text-sm font-medium text-slate-700">Sumber Peserta</label>
                    <select id="sumber_peserta" x-model="sumber"
                            class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="karyawan">Data Karyawan</option>
                        <option value="kandidat">Data Kandidat</option>
                    </select>
                </div>
                <div>
                    <label for="filter_departemen" class="block text-sm font-medium text-slate-700">Filter Departemen</label>
                    <select id="filter_departemen" x-model="filterDepartemen"
                            class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="Semua Departemen">Semua Departemen</option>
                        @foreach ($daftarDepartemen as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="cari_peserta" class="block text-sm font-medium text-slate-700">Cari Nama</label>
                    <input id="cari_peserta" type="text" x-model="cariPeserta"
                           placeholder="Ketik nama peserta..."
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="mt-4 max-h-72 overflow-y-auto rounded-md border border-slate-200 bg-slate-50 p-2">
                <template x-for="peserta in pesertaTampil" :key="peserta.name">
                    <label class="mb-1 flex cursor-pointer items-start gap-3 rounded-md p-2 text-sm hover:bg-white transition"
                           :class="pilihanPeserta[peserta.name] ? 'bg-white ring-1 ring-[#2C5F6F]/30' : ''">
                        <input type="checkbox" :value="peserta.name"
                               @change="togglePeserta(peserta.name, $event.target.checked)"
                               :checked="!!pilihanPeserta[peserta.name]"
                               class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#2C5F6F] focus:ring-[#2C5F6F]">
                        <div class="min-w-0 flex-1">
                            <div class="truncate font-medium text-slate-800" x-text="peserta.name"></div>
                            <div class="mt-0.5 text-xs text-slate-500">
                                Departemen: <strong class="text-slate-700" x-text="peserta.departemen"></strong>
                                <span class="mx-1 text-slate-300">·</span>
                                Posisi: <span class="text-slate-600" x-text="peserta.posisi"></span>
                            </div>
                        </div>
                    </label>
                </template>
                <p x-show="pesertaTampil.length === 0" class="py-6 text-center text-xs text-slate-500">
                    Tidak ada peserta yang cocok dengan filter.
                </p>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                <span x-text="jumlahPesertaTerpilih"></span> peserta dipilih dari
                <span x-text="sumber === 'karyawan' ? @js(count($daftarKaryawan)) : @js(count($daftarKandidat))"></span> tersedia.
            </p>
        </section>

        {{-- SECTION 3: ASSIGN ALAT TES PER PESERTA --}}
        <section class="border-t border-slate-200 pt-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Assign Alat Tes per Peserta</h3>
                <button type="button" @click="terapkanKeSemua()"
                        :disabled="jumlahPesertaTerpilih < 2"
                        :class="jumlahPesertaTerpilih >= 2 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-slate-300 cursor-not-allowed'"
                        class="rounded-md px-3 py-1.5 text-xs font-semibold text-white shadow-sm">
                    Terapkan ke Semua Peserta
                </button>
            </div>

            <div x-show="jumlahPesertaTerpilih === 0" class="rounded-md border border-dashed border-slate-300 bg-white py-8 text-center text-sm text-slate-500">
                Pilih peserta terlebih dahulu di atas.
            </div>

            <div x-show="jumlahPesertaTerpilih > 0" class="space-y-3">
                <template x-for="(state, nama) in pilihanPeserta" :key="nama">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900" x-text="nama"></p>
                                <p class="text-xs text-slate-500" x-text="getInfoPeserta(nama)"></p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                Peserta Terpilih
                            </span>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <template x-for="alat in daftarAlatTes" :key="alat.id">
                                <label class="flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm"
                                       :class="state.alat_tes.includes(alat.id) ? 'border-emerald-300 bg-emerald-50' : ''">
                                    <input type="checkbox" :value="alat.id"
                                           @change="toggleAlatTes(nama, alat.id, $event.target.checked)"
                                           :checked="state.alat_tes.includes(alat.id)"
                                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-600">
                                    <span class="font-medium text-slate-800" x-text="alat.nama"></span>
                                    <span class="ml-auto inline-block rounded-md bg-slate-700 px-2 py-0.5 text-[10px] font-semibold text-white"
                                          x-text="alat.format_dasar"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </section>

        <div class="sticky bottom-0 -mx-6 mt-6 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-4 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.penjadwalan-tes.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Simpan</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('formPenjadwalan', () => ({
        sumber: 'karyawan',
        filterDepartemen: 'Semua Departemen',
        cariPeserta: '',
        pilihanPeserta: {}, // { nama: { alat_tes: [id1, id2] } }

        daftarKaryawan: @json($daftarKaryawan),
        daftarKandidat: @json($daftarKandidat),
        daftarAlatTes: @json($daftarAlatTes),

        init() {
            // Auto-init struktur pilihanPeserta tetap kosong hingga user ceklis
        },

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