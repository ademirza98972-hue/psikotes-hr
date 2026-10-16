{{-- Modal tambah/ubah sesi + dialog konfirmasi. Dipakai index & detail; butuh x-data="kelolaSesi(...)" di induk, $daftarAlatTes, $daftarDepartemen. --}}
<x-ui.dialog show="modalForm" maxWidth="sm:max-w-2xl">
    <div class="flex flex-col gap-2 pr-6">
        <h2 class="text-lg font-semibold leading-none" x-text="mode === 'tambah' ? 'Tambah Penjadwalan' : 'Ubah Sesi Tes'"></h2>
        <p class="text-sm text-muted-foreground">Peserta ditambahkan di halaman detail setelah sesi disimpan.</p>
    </div>
    <form method="POST" :action="mode === 'tambah' ? urlSimpan : urlDasar + '/' + form.id" class="grid gap-5">
        @csrf
        <input type="hidden" name="_method" value="PUT" :disabled="mode === 'tambah'">
        <input type="hidden" name="_form" :value="mode">
        <input type="hidden" name="id" :value="form.id">

        <fieldset class="grid gap-4 sm:grid-cols-2">
            <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Sesi</legend>
            <x-ui.field label="Nama sesi" for="s-nama" error="nama_sesi" class="sm:col-span-2">
                <x-ui.input id="s-nama" name="nama_sesi" required maxlength="255" x-model="form.nama_sesi" placeholder="Rekrutmen Staff Finance Batch 1" />
            </x-ui.field>
            <x-ui.field label="Tanggal mulai" for="s-mulai" error="tanggal_mulai">
                <x-ui.input id="s-mulai" name="tanggal_mulai" type="date" required x-model="form.tanggal_mulai" />
            </x-ui.field>
            <x-ui.field label="Tanggal selesai" for="s-selesai" error="tanggal_selesai">
                <x-ui.input id="s-selesai" name="tanggal_selesai" type="date" required x-model="form.tanggal_selesai" ::min="form.tanggal_mulai" />
            </x-ui.field>
            <div class="grid grid-cols-1 gap-2 sm:col-span-2">
                <x-ui.label>Status</x-ui.label>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach (['Draft' => 'Belum terlihat oleh peserta; aman untuk menyiapkan sesi.', 'Aktif' => 'Peserta bisa mengerjakan selama rentang tanggal di atas.'] as $nilai => $ket)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-border p-3 transition-colors has-checked:border-primary has-checked:bg-primary/5">
                            <input type="radio" name="status" value="{{ $nilai }}" x-model="form.status" class="mt-0.5 size-4 accent-primary">
                            <span class="grid gap-0.5"><span class="text-sm font-medium">{{ $nilai }}</span><span class="text-xs text-muted-foreground">{{ $ket }}</span></span>
                        </label>
                    @endforeach
                </div>
                @error('status')<p class="text-xs font-medium text-destructive">{{ $message }}</p>@enderror
            </div>
        </fieldset>

        <fieldset class="grid grid-cols-1 gap-2">
            <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Alat tes</legend>
            @if ($daftarAlatTes->isEmpty())
                <x-ui.alert variant="warning" icon="quiz">Belum ada alat tes aktif.</x-ui.alert>
            @endif
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($daftarAlatTes as $alat)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-border p-3 transition-colors has-checked:border-primary has-checked:bg-primary/5">
                        <x-ui.checkbox name="alat_tes_ids[]" value="{{ $alat->id }}" x-model="form.alat_tes_ids" />
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-md bg-brand-ink font-mono text-[11px] font-semibold text-white">{{ $alat->kode }}</span>
                        <span class="min-w-0"><span class="block truncate text-sm font-medium">{{ $alat->nama }}</span><span class="block text-xs text-muted-foreground">{{ $alat->format_dasar }}</span></span>
                    </label>
                @endforeach
            </div>
            @foreach (['alat_tes_ids', 'alat_tes_ids.*'] as $e)
                @error($e)<p class="text-xs font-medium text-destructive">{{ $message }}</p>@enderror
            @endforeach
        </fieldset>

        <fieldset class="grid grid-cols-1 gap-2">
            <div class="mb-1 flex items-center justify-between gap-2">
                <legend class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Departemen (opsional)</legend>
                <button type="button" class="text-xs font-medium text-primary hover:underline"
                        @click="form.departemen_ids = form.departemen_ids.length === semuaDept.length ? [] : [...semuaDept]"
                        x-text="form.departemen_ids.length === semuaDept.length ? 'Kosongkan' : 'Pilih semua'"></button>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($daftarDepartemen as $dept)
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-border px-3 py-1.5 text-sm transition-colors has-checked:border-primary has-checked:bg-primary/5">
                        <x-ui.checkbox name="departemen_ids[]" value="{{ $dept->id }}" x-model="form.departemen_ids" />{{ $dept->nama_departemen }}
                    </label>
                @endforeach
            </div>
            <p class="text-xs text-muted-foreground">Hanya label pengelompokan; tidak membatasi siapa yang bisa ditambahkan sebagai peserta.</p>
        </fieldset>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <x-ui.button variant="outline" @click="modalForm = false">Batal</x-ui.button>
            <x-ui.button type="submit"><span class="material-symbols-outlined">save</span><span x-text="mode === 'tambah' ? 'Simpan sesi' : 'Simpan perubahan'"></span></x-ui.button>
        </div>
    </form>
</x-ui.dialog>

@include('admin.alat-tes._konfirmasi')

<script>
function kelolaSesi(cfg) {
    const kosong = () => ({ id: '', nama_sesi: '', tanggal_mulai: '', tanggal_selesai: '', status: 'Draft', departemen_ids: [], alat_tes_ids: [] });
    return {
        ...cfg,
        modalForm: false, mode: 'tambah', form: kosong(),
        konfirmasi: { buka: false, judul: '', pesan: '', url: '', label: 'Hapus' },
        init() { if (cfg.formAwal) this.bukaForm(cfg.formAwal.mode, cfg.formAwal.data); },
        bukaForm(mode, data) { this.mode = mode; this.form = { ...kosong(), ...data }; this.modalForm = true; },
        tanya(opsi) { this.konfirmasi = { label: 'Hapus', ...opsi, buka: true }; },
    };
}
</script>
