@extends('layouts.admin', ['judulHalaman' => "Dimensi — {$alatTes->nama}"])

@section('content')
@php
    $tipeLabel = ['psikogram' => 'Psikogram', 'klinis' => 'Klinis'];
    $arahLabel = ['tinggi_baik' => 'Tinggi = baik', 'rendah_baik' => 'Rendah = baik'];
    $jumlahLevel = $dimensi->sum(fn ($d) => $d->levelDimensi->count());

    $dataForm = fn ($d) => [
        'id' => (string) $d->id, 'kode_dimensi' => $d->kode_dimensi, 'nama_dimensi' => $d->nama_dimensi,
        'tipe_kategori' => $d->tipe_kategori, 'arah_skor' => $d->arah_skor, 'urutan' => (string) $d->urutan,
        'durasi_detik' => (string) ($d->durasi_detik ?? ''), 'deskripsi_aspek' => $d->deskripsi_aspek ?? '',
        'instruksi_subtes' => $d->instruksi_subtes ?? '',
    ];
    $formAwal = old('_form') ? ['mode' => old('_form'), 'data' => [
        'id' => (string) old('id', ''), 'kode_dimensi' => old('kode_dimensi', ''), 'nama_dimensi' => old('nama_dimensi', ''),
        'tipe_kategori' => old('tipe_kategori', 'psikogram'), 'arah_skor' => old('arah_skor', 'tinggi_baik'), 'urutan' => (string) old('urutan', ''),
        'durasi_detik' => (string) old('durasi_detik', ''), 'deskripsi_aspek' => old('deskripsi_aspek', ''), 'instruksi_subtes' => old('instruksi_subtes', ''),
    ]] : null;
@endphp

<div class="mx-auto max-w-5xl space-y-6"
     x-data="kelolaDimensi({ urlSimpan: @js(route('admin.alat-tes.simpanDimensi', $alatTes->id)), urlDasar: @js(url("admin/alat-tes/{$alatTes->id}/dimensi")), formAwal: @js($formAwal) })">

    @include('admin.alat-tes._kepala', ['deskripsi' => 'Aspek yang diskor dari jawaban peserta dan level interpretasinya.'])

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-muted-foreground">
            <span class="font-medium text-foreground tabular-nums">{{ $dimensi->count() }}</span> dimensi ·
            <span class="font-medium text-foreground tabular-nums">{{ $jumlahLevel }}</span> level skor
        </p>
        <x-ui.button @click="bukaForm('tambah', {})"><span class="material-symbols-outlined">add</span>Tambah Dimensi</x-ui.button>
    </div>

    @if ($dimensi->isEmpty())
        <x-ui.card>
            <x-ui.empty icon="psychology" title="Belum ada dimensi" description="Dimensi menentukan aspek apa saja yang dihitung dari jawaban peserta.">
                <x-ui.button size="sm" @click="bukaForm('tambah', {})"><span class="material-symbols-outlined">add</span>Tambah Dimensi</x-ui.button>
            </x-ui.empty>
        </x-ui.card>
    @else
        <div class="space-y-3">
            @foreach ($dimensi as $dim)
                @php
                    $levels = $dim->levelDimensi->sortBy('urutan')->values()->map(fn ($l) => [
                        'label' => $l->label, 'skor_min' => (float) $l->skor_min, 'skor_max' => (float) $l->skor_max,
                    ]);
                @endphp
                <details class="group rounded-xl border border-border bg-card shadow-sm open:shadow-md">
                    <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-3.5 sm:px-5 [&::-webkit-details-marker]:hidden">
                        <span class="w-6 shrink-0 text-right text-xs tabular-nums text-muted-foreground">{{ $dim->urutan }}</span>
                        <x-ui.badge variant="outline" class="shrink-0 font-mono">{{ $dim->kode_dimensi }}</x-ui.badge>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $dim->nama_dimensi }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $tipeLabel[$dim->tipe_kategori] ?? $dim->tipe_kategori }} · {{ $arahLabel[$dim->arah_skor] ?? $dim->arah_skor }}
                                @if ($dim->durasi_detik) · {{ $dim->durasi_detik }} detik @endif
                            </p>
                        </div>
                        <div class="hidden shrink-0 gap-1 md:flex">
                            @forelse ($levels->take(5) as $l)
                                <span class="rounded bg-muted px-1.5 py-0.5 text-[11px] font-medium text-muted-foreground">{{ $l['label'] }}</span>
                            @empty
                                <span class="text-xs text-muted-foreground">Tanpa level</span>
                            @endforelse
                            @if ($levels->count() > 5)<span class="px-1 text-[11px] text-muted-foreground">+{{ $levels->count() - 5 }}</span>@endif
                        </div>
                        <span class="material-symbols-outlined shrink-0 text-[20px] text-muted-foreground transition-transform group-open:rotate-180">expand_more</span>
                    </summary>

                    <div class="space-y-5 border-t border-border px-4 py-5 sm:px-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-2 text-sm">
                                <p class="{{ $dim->deskripsi_aspek ? 'text-muted-foreground' : 'italic text-muted-foreground/70' }}">{{ $dim->deskripsi_aspek ?: 'Belum ada deskripsi aspek.' }}</p>
                                @if ($dim->instruksi_subtes)
                                    <p class="flex items-center gap-1.5 text-xs text-muted-foreground"><span class="material-symbols-outlined text-[16px]">description</span>Punya instruksi subtes</p>
                                @endif
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <x-ui.button variant="outline" size="sm" @click="bukaForm('ubah', {{ Js::from($dataForm($dim)) }})">
                                    <span class="material-symbols-outlined">edit</span>Ubah dimensi
                                </x-ui.button>
                                <x-ui.button variant="ghost" size="sm" class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                    @click="tanya({ judul: 'Hapus dimensi', pesan: {{ Js::from('Dimensi ' . $dim->nama_dimensi . ' beserta ' . $levels->count() . ' levelnya akan dihapus permanen.') }}, url: {{ Js::from(route('admin.alat-tes.hapusDimensi', [$alatTes->id, $dim->id])) }} })">
                                    <span class="material-symbols-outlined">delete</span>Hapus
                                </x-ui.button>
                            </div>
                        </div>

                        {{-- Editor level: perubahan lokal sampai "Simpan level" ditekan --}}
                        <form method="POST" action="{{ route('admin.alat-tes.simpanLevel', [$alatTes->id, $dim->id]) }}"
                              x-data="editorLevel({{ Js::from($levels) }}, @js($dim->tipe_kategori))" class="rounded-lg border border-border">
                            @csrf
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-border px-3 py-2.5">
                                <p class="flex items-center gap-2 text-sm font-medium">
                                    Level skor
                                    <x-ui.badge variant="warning" x-show="berubah" x-cloak>Belum disimpan</x-ui.badge>
                                </p>
                                <div class="flex gap-1">
                                    <x-ui.button variant="ghost" size="sm" @click="isiDefault()" title="Isi dengan level bawaan sesuai tipe kategori">
                                        <span class="material-symbols-outlined">auto_fix</span><span class="hidden sm:inline">Level bawaan</span>
                                    </x-ui.button>
                                    <x-ui.button variant="ghost" size="sm" @click="tambah()">
                                        <span class="material-symbols-outlined">add</span>Level
                                    </x-ui.button>
                                </div>
                            </div>

                            <div class="grid grid-cols-[minmax(0,1fr)_3.75rem_3.75rem_2rem] gap-2 px-3 pt-2.5 text-xs font-medium text-muted-foreground sm:grid-cols-[1.5rem_minmax(0,1fr)_7rem_7rem_2rem]">
                                <span class="hidden sm:block">#</span><span>Label</span><span>Skor min</span><span>Skor maks</span><span></span>
                            </div>
                            <div class="grid gap-2 p-3">
                                <template x-for="(level, i) in levels" :key="i">
                                    <div class="grid grid-cols-[minmax(0,1fr)_3.75rem_3.75rem_2rem] items-center gap-2 sm:grid-cols-[1.5rem_minmax(0,1fr)_7rem_7rem_2rem]">
                                        <span class="hidden text-xs tabular-nums text-muted-foreground sm:block" x-text="i + 1"></span>
                                        <x-ui.input ::name="`levels[${i}][label]`" x-model="level.label" required maxlength="50" ::aria-label="`Label level ${i + 1}`" />
                                        <x-ui.input type="number" step="0.01" ::name="`levels[${i}][skor_min]`" x-model.number="level.skor_min" required class="px-2 text-right font-mono" ::aria-label="`Skor minimum level ${i + 1}`" />
                                        <x-ui.input type="number" step="0.01" ::name="`levels[${i}][skor_max]`" x-model.number="level.skor_max" required class="px-2 text-right font-mono" ::aria-label="`Skor maksimum level ${i + 1}`" />
                                        <input type="hidden" :name="`levels[${i}][urutan]`" :value="i">
                                        <x-ui.button variant="ghost" size="icon-sm" @click="hapus(i)" ::disabled="levels.length <= 1" title="Hapus level">
                                            <span class="material-symbols-outlined">close</span>
                                            <span class="sr-only">Hapus level</span>
                                        </x-ui.button>
                                    </div>
                                </template>
                                <p x-show="levels.length === 0" class="py-4 text-center text-sm text-muted-foreground">Belum ada level. Tambah manual atau pakai level bawaan.</p>
                            </div>
                            <div class="flex justify-end gap-2 border-t border-border px-3 py-2.5">
                                <x-ui.button variant="ghost" size="sm" x-show="berubah" x-cloak @click="batal()">Batalkan</x-ui.button>
                                <x-ui.button type="submit" size="sm" ::disabled="levels.length === 0"><span class="material-symbols-outlined">save</span>Simpan level</x-ui.button>
                            </div>
                        </form>
                    </div>
                </details>
            @endforeach
        </div>
    @endif

    {{-- MODAL TAMBAH / UBAH DIMENSI --}}
    <x-ui.dialog show="modalForm" maxWidth="sm:max-w-2xl">
        <div class="flex flex-col gap-2 pr-6">
            <h2 class="text-lg font-semibold leading-none" x-text="mode === 'tambah' ? 'Tambah Dimensi' : 'Ubah Dimensi'"></h2>
            <p class="text-sm text-muted-foreground">{{ $alatTes->nama }} ({{ $alatTes->kode }})</p>
        </div>
        <form method="POST" :action="mode === 'tambah' ? urlSimpan : urlDasar + '/' + form.id" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'tambah'">
            <input type="hidden" name="_form" :value="mode">
            <input type="hidden" name="id" :value="form.id">

            <x-ui.field label="Kode dimensi" for="d-kode" error="kode_dimensi" description="Dipakai di file CSV norma.">
                <x-ui.input id="d-kode" name="kode_dimensi" required maxlength="50" x-model="form.kode_dimensi" class="font-mono" />
            </x-ui.field>
            <x-ui.field label="Nama dimensi" for="d-nama" error="nama_dimensi">
                <x-ui.input id="d-nama" name="nama_dimensi" required maxlength="150" x-model="form.nama_dimensi" />
            </x-ui.field>
            <x-ui.field label="Tipe kategori" for="d-tipe" error="tipe_kategori">
                <x-ui.select id="d-tipe" name="tipe_kategori" required x-model="form.tipe_kategori">
                    @foreach ($tipeLabel as $nilai => $teks)<option value="{{ $nilai }}">{{ $teks }}</option>@endforeach
                </x-ui.select>
            </x-ui.field>
            <x-ui.field label="Arah skor" for="d-arah" error="arah_skor">
                <x-ui.select id="d-arah" name="arah_skor" required x-model="form.arah_skor">
                    @foreach ($arahLabel as $nilai => $teks)<option value="{{ $nilai }}">{{ $teks }}</option>@endforeach
                </x-ui.select>
            </x-ui.field>
            <x-ui.field label="Urutan" for="d-urut" error="urutan" description="Kosongkan untuk ditaruh paling akhir.">
                <x-ui.input id="d-urut" name="urutan" type="number" min="0" x-model="form.urutan" placeholder="Otomatis" />
            </x-ui.field>
            <x-ui.field label="Durasi subtes (detik, opsional)" for="d-durasi" error="durasi_detik" description="Untuk subtes berwaktu seperti IST; kosong = 420 detik.">
                <x-ui.input id="d-durasi" name="durasi_detik" type="number" min="0" x-model="form.durasi_detik" />
            </x-ui.field>
            <x-ui.field label="Deskripsi aspek (opsional)" for="d-desk" error="deskripsi_aspek" class="sm:col-span-2">
                <x-ui.textarea id="d-desk" name="deskripsi_aspek" rows="2" x-model="form.deskripsi_aspek" />
            </x-ui.field>
            <x-ui.field label="Instruksi subtes (opsional)" for="d-instruksi" error="instruksi_subtes" class="sm:col-span-2" description="Ditampilkan ke peserta sebelum subtes dimulai.">
                <x-ui.textarea id="d-instruksi" name="instruksi_subtes" rows="3" x-model="form.instruksi_subtes" />
            </x-ui.field>

            <div class="flex flex-col-reverse gap-2 sm:col-span-2 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" @click="modalForm = false">Batal</x-ui.button>
                <x-ui.button type="submit"><span class="material-symbols-outlined">save</span><span x-text="mode === 'tambah' ? 'Simpan dimensi' : 'Simpan perubahan'"></span></x-ui.button>
            </div>
        </form>
    </x-ui.dialog>

    @include('admin.alat-tes._konfirmasi')
</div>

<script>
function kelolaDimensi(cfg) {
    const kosong = () => ({ id: '', kode_dimensi: '', nama_dimensi: '', tipe_kategori: 'psikogram', arah_skor: 'tinggi_baik',
        urutan: '', durasi_detik: '', deskripsi_aspek: '', instruksi_subtes: '' });
    return {
        ...cfg,
        modalForm: false, mode: 'tambah', form: kosong(),
        konfirmasi: { buka: false, judul: '', pesan: '', url: '', label: 'Hapus' },
        init() { if (cfg.formAwal) this.bukaForm(cfg.formAwal.mode, cfg.formAwal.data); },
        bukaForm(mode, data) { this.mode = mode; this.form = { ...kosong(), ...data }; this.modalForm = true; },
        tanya(opsi) { this.konfirmasi = { label: 'Hapus', ...opsi, buka: true }; },
    };
}

function editorLevel(awal, tipe) {
    const salin = (x) => JSON.parse(JSON.stringify(x));
    const bawaan = {
        psikogram: ['R', 'K', 'C', 'B', 'BS'],
        klinis: ['Normal', 'Perlu Perhatian', 'Signifikan'],
    };
    return {
        levels: salin(awal),
        get berubah() { return JSON.stringify(this.levels) !== JSON.stringify(awal); },
        tambah() {
            const akhir = this.levels.at(-1);
            const mulai = akhir ? Number(akhir.skor_max) + 1 : 0;
            this.levels.push({ label: '', skor_min: mulai, skor_max: mulai });
        },
        hapus(i) { if (this.levels.length > 1) this.levels.splice(i, 1); },
        isiDefault() { this.levels = (bawaan[tipe] ?? bawaan.psikogram).map((label, i) => ({ label, skor_min: i, skor_max: i })); },
        batal() { this.levels = salin(awal); },
    };
}
</script>
@endsection
