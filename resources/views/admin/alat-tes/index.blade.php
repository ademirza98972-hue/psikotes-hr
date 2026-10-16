@extends('layouts.admin', ['judulHalaman' => 'Alat Tes'])

@section('content')
@php
    $bisaKelola = auth()->user()->hasIzin('kategori_tes.kelola');
    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');
    $pilihanPola = [
        'kognitif'             => 'Kognitif',
        'forced_choice'        => 'Forced choice',
        'forced_choice_rollup' => 'Forced choice rollup',
        'grid'                 => 'Grid',
    ];

    $dataForm = fn ($a) => [
        'id' => (string) $a->id, 'kode' => $a->kode, 'nama' => $a->nama, 'deskripsi' => $a->deskripsi ?? '',
        'format_dasar' => $a->format_dasar, 'pola_skoring' => $a->pola_skoring,
        'durasi_total_menit' => (string) ($a->durasi_total_menit ?? ''), 'batas_aktif' => (bool) $a->batas_waktu_per_soal_aktif,
        'batas_detik' => (string) ($a->batas_waktu_per_soal_detik ?? ''), 'is_sensitif' => (bool) $a->is_sensitif,
    ];

    // Form yang dibuka saat halaman dimuat: isian lama (validasi gagal) > ?ubah={id} > ?tambah=1
    $formAwal = match (true) {
        (bool) old('_form') => ['mode' => old('_form'), 'data' => [
            'id' => (string) old('id', ''), 'kode' => old('kode', ''), 'nama' => old('nama', ''), 'deskripsi' => old('deskripsi', ''),
            'format_dasar' => old('format_dasar', 'Pilihan Ganda'), 'pola_skoring' => old('pola_skoring', 'kognitif'),
            'durasi_total_menit' => (string) old('durasi_total_menit', ''), 'batas_aktif' => (bool) old('batas_waktu_per_soal_aktif'),
            'batas_detik' => (string) old('batas_waktu_per_soal_detik', ''), 'is_sensitif' => (bool) old('is_sensitif'),
        ]],
        $bukaUbah && $bisaKelola => ['mode' => 'ubah', 'data' => $dataForm($bukaUbah)],
        $bukaTambah && $bisaKelola => ['mode' => 'tambah', 'data' => []],
        default => null,
    };
@endphp

<div class="mx-auto max-w-7xl space-y-6"
     x-data="kelolaAlatTes({ urlSimpan: @js(route('admin.alat-tes.simpan')), urlDasar: @js(url('admin/alat-tes')), formAwal: @js($formAwal) })">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight">Alat Tes</h1>
            <p class="text-sm text-muted-foreground">Instrumen psikometri beserta dimensi, level skor, dan norma konversinya.</p>
        </div>
        @if ($bisaKelola)
            <x-ui.button @click="bukaTambah()">
                <span class="material-symbols-outlined">add</span>Tambah Alat Tes
            </x-ui.button>
        @endif
    </div>

    {{-- RINGKASAN --}}
    <x-ui.card class="overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-border lg:grid-cols-4">
            @foreach ([
                ['Alat tes', $ringkasan['alat'], 'quiz', 'text-brand-indigo bg-brand-indigo/10', "{$ringkasan['aktif']} aktif"],
                ['Butir soal', $ringkasan['soal'], 'format_list_numbered', 'text-brand-blue bg-brand-blue/10', 'Di semua alat tes'],
                ['Dimensi', $ringkasan['dimensi'], 'psychology', 'text-brand-teal bg-brand-teal/10', 'Aspek yang diskor'],
                ['Baris norma', $ringkasan['norma'], 'table_chart', 'text-brand-green-deep bg-brand-green/10', 'Tabel konversi skor'],
            ] as [$judul, $nilai, $ikon, $warna, $sub])
                <div class="flex items-center gap-3 bg-card px-4 py-4 sm:px-5">
                    <span class="hidden size-9 shrink-0 items-center justify-center rounded-lg sm:flex {{ $warna }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-muted-foreground">{{ $judul }}</p>
                        <p class="text-xl font-semibold tabular-nums">{{ $angka($nilai) }}</p>
                        <p class="truncate text-xs text-muted-foreground">{{ $sub }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    {{-- DAFTAR INSTRUMEN --}}
    @if ($alatTes->isEmpty())
        <x-ui.card>
            <x-ui.empty icon="quiz" title="Belum ada alat tes" description="Tambahkan instrumen psikometri untuk dipakai di sesi tes.">
                @if ($bisaKelola)
                    <x-ui.button size="sm" @click="bukaTambah()"><span class="material-symbols-outlined">add</span>Tambah Alat Tes</x-ui.button>
                @endif
            </x-ui.empty>
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @foreach ($alatTes as $alat)
                @php
                    $durasi = $alat->durasi_total_menit ? "{$alat->durasi_total_menit} menit" : 'Tanpa batas waktu total';
                    if ($alat->batas_waktu_per_soal_aktif && $alat->batas_waktu_per_soal_detik) {
                        $durasi .= " · {$alat->batas_waktu_per_soal_detik} detik per soal";
                    }
                @endphp
                <x-ui.card class="flex flex-col">
                    <div class="flex items-start gap-4 p-5">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-ink font-mono text-sm font-semibold text-white">{{ $alat->kode }}</span>
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-semibold leading-tight">{{ $alat->nama }}</h2>
                                @if (! $alat->is_aktif)
                                    <x-ui.badge variant="secondary">Nonaktif</x-ui.badge>
                                @endif
                                @if ($alat->is_sensitif)
                                    <x-ui.badge variant="danger"><span class="material-symbols-outlined text-[14px]">shield_lock</span>Sensitif</x-ui.badge>
                                @endif
                            </div>
                            <p class="text-xs text-muted-foreground">{{ $alat->format_dasar }} · {{ $pilihanPola[$alat->pola_skoring] ?? $alat->pola_skoring }}</p>
                            <p class="line-clamp-2 min-h-11 pt-1 text-sm {{ $alat->deskripsi ? 'text-muted-foreground' : 'italic text-muted-foreground/70' }}">{{ $alat->deskripsi ?: 'Belum ada deskripsi.' }}</p>
                        </div>
                        @if ($bisaKelola)
                            <x-ui.dropdown-menu width="w-44">
                                <x-slot:trigger>
                                    <x-ui.button variant="ghost" size="icon-sm" class="-mr-2 -mt-1" title="Aksi">
                                        <span class="material-symbols-outlined">more_vert</span>
                                        <span class="sr-only">Aksi untuk {{ $alat->nama }}</span>
                                    </x-ui.button>
                                </x-slot:trigger>
                                <x-ui.dropdown-item icon="edit" @click="bukaUbah({{ Js::from($dataForm($alat)) }})">Ubah</x-ui.dropdown-item>
                                <x-ui.separator class="-mx-1 my-1 w-auto!" />
                                <x-ui.dropdown-item icon="delete" variant="destructive"
                                    @click="tanya({ judul: 'Hapus alat tes', pesan: {{ Js::from('Alat tes ' . $alat->nama . ' (' . $alat->soal_count . ' soal) akan dipindahkan ke Data Terhapus dan tidak bisa dipilih di sesi baru.') }}, url: {{ Js::from(route('admin.alat-tes.hapus', $alat->id)) }} })">
                                    Hapus
                                </x-ui.dropdown-item>
                            </x-ui.dropdown-menu>
                        @endif
                    </div>

                    <dl class="mx-5 grid grid-cols-3 divide-x divide-border rounded-lg border border-border text-center">
                        @foreach ([['Soal', $alat->soal_count], ['Dimensi', $alat->dimensi_alat_tes_count], ['Norma', $alat->norma_konversi_count]] as [$label, $nilai])
                            <div class="px-2 py-2.5">
                                <dt class="text-xs text-muted-foreground">{{ $label }}</dt>
                                <dd class="text-lg font-semibold tabular-nums">{{ $angka($nilai) }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-auto flex flex-wrap items-center justify-between gap-3 px-5 pb-5 pt-4">
                        <p class="flex items-center gap-1.5 text-xs text-muted-foreground">
                            <span class="material-symbols-outlined text-[16px]">timer</span>{{ $durasi }}
                        </p>
                        <div class="flex gap-2">
                            <x-ui.button variant="outline" size="sm" :href="route('admin.alat-tes.dimensi', $alat->id)">
                                <span class="material-symbols-outlined">psychology</span>Dimensi
                            </x-ui.button>
                            <x-ui.button variant="outline" size="sm" :href="route('admin.alat-tes.norma', $alat->id)">
                                <span class="material-symbols-outlined">table_chart</span>Norma
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        @if ($alatTes->hasPages())
            <div class="flex justify-center">{{ $alatTes->onEachSide(1)->links() }}</div>
        @endif
    @endif

    {{-- MODAL TAMBAH / UBAH (satu form, dua mode) --}}
    @if ($bisaKelola)
    <x-ui.dialog show="modalForm" maxWidth="sm:max-w-2xl">
        <div class="flex flex-col gap-2 pr-6">
            <h2 class="text-lg font-semibold leading-none" x-text="mode === 'tambah' ? 'Tambah Alat Tes' : 'Ubah Alat Tes'"></h2>
            <p class="text-sm text-muted-foreground">Dimensi, level, dan norma diatur di halaman masing-masing setelah alat tes disimpan.</p>
        </div>
        <form method="POST" :action="mode === 'tambah' ? urlSimpan : urlDasar + '/' + form.id" class="grid gap-5">
            @csrf
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'tambah'">
            <input type="hidden" name="_form" :value="mode">
            <input type="hidden" name="id" :value="form.id">

            <fieldset class="grid gap-4 sm:grid-cols-[8rem_minmax(0,1fr)]">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Identitas</legend>
                <x-ui.field label="Kode" for="a-kode" error="kode">
                    <x-ui.input id="a-kode" name="kode" required maxlength="20" x-model="form.kode" placeholder="IST" class="font-mono uppercase" />
                </x-ui.field>
                <x-ui.field label="Nama lengkap" for="a-nama" error="nama">
                    <x-ui.input id="a-nama" name="nama" required maxlength="150" x-model="form.nama" placeholder="Intelligenz Struktur Test" />
                </x-ui.field>
                <x-ui.field label="Deskripsi (opsional)" for="a-desk" error="deskripsi" class="sm:col-span-2" description="Tampil di kartu alat tes untuk membantu admin memilih instrumen.">
                    <x-ui.textarea id="a-desk" name="deskripsi" rows="3" maxlength="1000" x-model="form.deskripsi" placeholder="Tujuan dan cakupan pengukuran alat tes ini" />
                </x-ui.field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Format &amp; skoring</legend>
                <x-ui.field label="Format soal" for="a-format" error="format_dasar">
                    <x-ui.select id="a-format" name="format_dasar" required x-model="form.format_dasar">
                        @foreach ($pilihanFormat as $format)
                            <option value="{{ $format }}">{{ $format }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Pola skoring" for="a-pola" error="pola_skoring" description="Menentukan cara jawaban diubah menjadi skor.">
                    <x-ui.select id="a-pola" name="pola_skoring" required x-model="form.pola_skoring">
                        @foreach ($pilihanPola as $nilai => $teks)
                            <option value="{{ $nilai }}">{{ $teks }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Waktu &amp; akses</legend>
                <x-ui.field label="Durasi total (menit)" for="a-durasi" error="durasi_total_menit" description="Kosongkan bila waktu diatur per subtes.">
                    <x-ui.input id="a-durasi" name="durasi_total_menit" type="number" min="0" x-model="form.durasi_total_menit" />
                </x-ui.field>
                <div class="grid content-start gap-2">
                    <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                    <x-ui.label for="a-batas" class="h-3.5">Batas waktu per soal</x-ui.label>
                    <div class="flex h-9 items-center gap-3">
                        <x-ui.switch id="a-batas" name="batas_waktu_per_soal_aktif" value="1" x-model="form.batas_aktif" />
                        <div x-show="form.batas_aktif" class="flex items-center gap-2">
                            <x-ui.input name="batas_waktu_per_soal_detik" type="number" min="1" x-model="form.batas_detik" ::required="form.batas_aktif" class="w-20!" aria-label="Detik per soal" />
                            <span class="text-sm text-muted-foreground">detik</span>
                        </div>
                        <span x-show="!form.batas_aktif" class="text-sm text-muted-foreground">Tidak aktif</span>
                    </div>
                    @error('batas_waktu_per_soal_detik')<p class="text-xs font-medium text-destructive">{{ $message }}</p>@enderror
                </div>
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-border p-3 transition-colors has-checked:border-red-200 has-checked:bg-red-50 sm:col-span-2">
                    <input type="hidden" name="is_sensitif" value="0">
                    <x-ui.checkbox name="is_sensitif" value="1" class="mt-0.5" x-model="form.is_sensitif" />
                    <span class="grid gap-0.5">
                        <span class="text-sm font-medium">Kategori sensitif</span>
                        <span class="text-xs text-muted-foreground">Memuat konten psikologis klinis; akses hasil dibatasi untuk psikolog terverifikasi.</span>
                    </span>
                </label>
            </fieldset>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" @click="modalForm = false">Batal</x-ui.button>
                <x-ui.button type="submit"><span class="material-symbols-outlined">save</span><span x-text="mode === 'tambah' ? 'Simpan alat tes' : 'Simpan perubahan'"></span></x-ui.button>
            </div>
        </form>
    </x-ui.dialog>

    @include('admin.alat-tes._konfirmasi')
    @endif
</div>

<script>
function kelolaAlatTes(cfg) {
    const kosong = () => ({ id: '', kode: '', nama: '', deskripsi: '', format_dasar: 'Pilihan Ganda', pola_skoring: 'kognitif',
        durasi_total_menit: '', batas_aktif: false, batas_detik: '', is_sensitif: false });
    return {
        ...cfg,
        modalForm: false, mode: 'tambah', form: kosong(),
        konfirmasi: { buka: false, judul: '', pesan: '', url: '', label: 'Hapus' },
        init() { if (cfg.formAwal) this.bukaForm(cfg.formAwal.mode, cfg.formAwal.data); },
        bukaTambah() { this.bukaForm('tambah', {}); },
        bukaUbah(data) { this.bukaForm('ubah', data); },
        bukaForm(mode, data) { this.mode = mode; this.form = { ...kosong(), ...data }; this.modalForm = true; },
        tanya(opsi) { this.konfirmasi = { label: 'Hapus', ...opsi, buka: true }; },
    };
}
</script>
@endsection
