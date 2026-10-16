@extends('layouts.admin', ['judulHalaman' => 'Akun Karyawan'])

@section('content')
@php
    $saya = auth()->user();
    $bisaTambah = $saya->hasIzin('pengguna.tambah');
    $bisaEdit = $saya->hasIzin('pengguna.edit');
    $bisaHapus = $saya->hasIzin('pengguna.hapus');
    $adaFilter = $kataKunci || $filterDepartemen || $filterPosisi;
    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');

    // Isian form saat validasi gagal → modal dibuka lagi dengan data terakhir
    $formLama = old('_form') ? [
        'mode' => old('_form'),
        'data' => [
            'id' => (string) old('id', ''), 'id_karyawan' => old('id_karyawan', ''), 'nik' => old('nik_karyawan', ''),
            'nama' => old('nama_karyawan', ''), 'jenis_kelamin' => old('jenis_kelamin', ''), 'tanggal_lahir' => old('tanggal_lahir', ''),
            'pendidikan_terakhir' => old('pendidikan_terakhir', ''), 'departemen_id' => (string) old('departemen_id', ''),
            'posisi_id' => (string) old('posisi_id', ''), 'email' => old('email', ''), 'no_hp' => old('no_hp', ''),
        ],
    ] : null;
@endphp

<div class="mx-auto max-w-7xl space-y-6"
     x-data="akunKaryawan({
         posisiUrl: @js(route('api.posisi.daftar', ['departemen' => '__D__'])),
         urlSimpan: @js(route('admin.akun-karyawan.simpan')),
         urlDasar: @js(url('admin/akun-karyawan')),
         formLama: @js($formLama),
         importGagal: @js($errors->has('file_excel')),
     })">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight">Akun Karyawan</h1>
            <p class="text-sm text-muted-foreground">Kelola data dan akses login seluruh karyawan perusahaan.</p>
        </div>
        @if ($bisaTambah)
            <div class="flex flex-wrap gap-2">
                <x-ui.button variant="outline" @click="modalImport = true">
                    <span class="material-symbols-outlined">upload_file</span>Import Excel
                </x-ui.button>
                <x-ui.button @click="bukaTambah()">
                    <span class="material-symbols-outlined">person_add</span>Tambah Karyawan
                </x-ui.button>
            </div>
        @endif
    </div>

    {{-- RINGKASAN --}}
    <x-ui.card class="overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-border lg:grid-cols-4">
            @foreach ([
                ['Total karyawan', $ringkasan->total, 'badge', 'text-brand-indigo bg-brand-indigo/10'],
                ['Aktif', $ringkasan->aktif, 'check_circle', 'text-brand-green-deep bg-brand-green/10'],
                ['Nonaktif', $ringkasan->nonaktif, 'person_off', 'text-zinc-600 bg-zinc-100'],
                ['Belum ganti password', $ringkasan->wajib_ganti, 'key', 'text-brand-blue bg-brand-blue/10'],
            ] as [$judul, $nilai, $ikon, $warna])
                <div class="flex items-center gap-3 bg-card px-4 py-4 sm:px-5">
                    <span class="hidden size-9 shrink-0 items-center justify-center rounded-lg sm:flex {{ $warna }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-muted-foreground">{{ $judul }}</p>
                        <p class="text-xl font-semibold tabular-nums">{{ $angka($nilai) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    {{-- DAFTAR --}}
    <x-ui.card>
        <form method="GET" action="{{ route('admin.akun-karyawan.index') }}"
              class="grid gap-2 border-b border-border p-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_13rem_13rem_auto]">
            <div class="relative sm:col-span-2 lg:col-span-1">
                <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-muted-foreground">search</span>
                <x-ui.input type="search" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau NIK" aria-label="Cari nama atau NIK" class="pl-9" />
            </div>
            <x-ui.select name="departemen" aria-label="Filter departemen" onchange="this.form.submit()">
                <option value="">Semua departemen</option>
                @foreach ($semuaDepartemen as $item)
                    <option value="{{ $item->id }}" @selected($filterDepartemen == $item->id)>{{ $item->nama_departemen }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select name="posisi" aria-label="Filter posisi" onchange="this.form.submit()">
                <option value="">Semua posisi</option>
                @foreach ($semuaPosisi as $item)
                    <option value="{{ $item->id }}" @selected($filterPosisi == $item->id)>{{ $item->nama_posisi }}</option>
                @endforeach
            </x-ui.select>
            <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
                <x-ui.button type="submit" variant="secondary" class="flex-1 lg:flex-none">Cari</x-ui.button>
                @if ($adaFilter)
                    <x-ui.button variant="ghost" :href="route('admin.akun-karyawan.index')" class="flex-1 lg:flex-none">
                        <span class="material-symbols-outlined">close</span>Reset
                    </x-ui.button>
                @endif
            </div>
        </form>

        {{-- Tanpa pembungkus overflow agar menu aksi tidak terpotong; kolom sekunder disembunyikan di layar sempit --}}
        <table class="w-full text-sm">
            <caption class="sr-only">Daftar akun karyawan</caption>
            <thead>
                <tr class="border-b border-border">
                    <x-ui.th class="hidden w-12 pl-5 text-center lg:table-cell">No.</x-ui.th>
                    <x-ui.th class="pl-4 lg:pl-3">Karyawan</x-ui.th>
                    <x-ui.th class="hidden md:table-cell">ID Karyawan</x-ui.th>
                    <x-ui.th class="hidden xl:table-cell">NIK</x-ui.th>
                    <x-ui.th class="hidden md:table-cell">Departemen &amp; Posisi</x-ui.th>
                    <x-ui.th class="hidden sm:table-cell">Status</x-ui.th>
                    <x-ui.th class="pr-4 text-right"><span class="sr-only">Aksi</span></x-ui.th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $dk)
                    @php
                        $u = $dk->user;
                        [$labelStatus, $varianStatus, $titik] = match ($u?->status) {
                            'aktif' => ['Aktif', 'success', 'bg-emerald-600'],
                            'nonaktif' => ['Nonaktif', 'secondary', 'bg-zinc-400'],
                            default => [$u ? ucfirst(str_replace('_', ' ', $u->status)) : 'Tanpa akun', 'warning', 'bg-amber-500'],
                        };
                        $badgeStatus = '<span class="size-1.5 rounded-full ' . $titik . '"></span>' . $labelStatus;
                        $akunSendiri = $u?->id === $saya->id;
                        $nama = $u?->name ?? $dk->nik_karyawan;
                    @endphp
                    <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/40">
                        <x-ui.td class="hidden pl-5 text-center tabular-nums text-muted-foreground lg:table-cell">{{ $data->firstItem() + $loop->index }}</x-ui.td>
                        <x-ui.td class="whitespace-normal! pl-4 lg:pl-3">
                            <div class="flex items-center gap-3">
                                <x-ui.avatar :name="$u?->name ?? '?'" size="size-9" />
                                <div class="min-w-0">
                                    <p class="font-medium">{{ $u?->name ?? '—' }}</p>
                                    <p class="break-all text-xs text-muted-foreground">{{ $u?->email ?? 'Tanpa email' }}</p>
                                    {{-- info kolom tersembunyi di ponsel --}}
                                    <p class="mt-1 text-xs text-muted-foreground md:hidden">
                                        <span class="font-mono text-foreground">{{ $u?->id_karyawan ?? '—' }}</span> · {{ $dk->posisi?->nama_posisi ?? '—' }}
                                    </p>
                                    <x-ui.badge :variant="$varianStatus" class="mt-1.5 sm:hidden">{!! $badgeStatus !!}</x-ui.badge>
                                </div>
                            </div>
                        </x-ui.td>
                        <x-ui.td class="hidden font-mono text-xs font-medium md:table-cell">{{ $u?->id_karyawan ?? '—' }}</x-ui.td>
                        <x-ui.td class="hidden font-mono text-xs text-muted-foreground xl:table-cell">{{ $dk->nik_karyawan }}</x-ui.td>
                        <x-ui.td class="hidden whitespace-normal! md:table-cell">
                            <p>{{ $dk->departemen?->nama_departemen ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">{{ $dk->posisi?->nama_posisi ?? '—' }}</p>
                        </x-ui.td>
                        <x-ui.td class="hidden sm:table-cell">
                            <x-ui.badge :variant="$varianStatus">{!! $badgeStatus !!}</x-ui.badge>
                        </x-ui.td>
                        <x-ui.td class="pl-1 pr-4 text-right sm:pl-3">
                            @if ($bisaEdit || ($bisaHapus && ! $akunSendiri))
                                <div class="flex items-center justify-end gap-1">
                                    @if ($bisaEdit)
                                        <x-ui.button variant="ghost" size="icon-sm" title="Ubah data"
                                            @click="bukaUbah({{ Js::from([
                                                'id' => (string) $dk->id, 'id_karyawan' => $u?->id_karyawan ?? '', 'nik' => $dk->nik_karyawan,
                                                'nama' => $u?->name ?? '', 'jenis_kelamin' => $dk->jenis_kelamin ?? '',
                                                'tanggal_lahir' => $dk->tanggal_lahir?->format('Y-m-d') ?? '', 'pendidikan_terakhir' => $dk->pendidikan_terakhir ?? '',
                                                'departemen_id' => (string) $dk->departemen_id, 'posisi_id' => (string) $dk->posisi_id,
                                                'email' => $u?->email ?? '', 'no_hp' => $dk->no_hp ?? '',
                                            ]) }})">
                                            <span class="material-symbols-outlined">edit</span>
                                            <span class="sr-only">Ubah {{ $nama }}</span>
                                        </x-ui.button>
                                    @endif
                                    @if (! $akunSendiri && $u)
                                        <x-ui.dropdown-menu width="w-56">
                                            <x-slot:trigger>
                                                <x-ui.button variant="ghost" size="icon-sm" title="Aksi lainnya">
                                                    <span class="material-symbols-outlined">more_vert</span>
                                                    <span class="sr-only">Aksi lainnya untuk {{ $nama }}</span>
                                                </x-ui.button>
                                            </x-slot:trigger>
                                            @if ($bisaEdit)
                                                <form method="POST" action="{{ route('admin.akun-karyawan.toggle-status', $dk->id) }}">
                                                    @csrf @method('PATCH')
                                                    <x-ui.dropdown-item type="submit" :icon="$u->status === 'aktif' ? 'block' : 'check_circle'">
                                                        {{ $u->status === 'aktif' ? 'Nonaktifkan akun' : 'Aktifkan akun' }}
                                                    </x-ui.dropdown-item>
                                                </form>
                                                <x-ui.dropdown-item icon="lock_reset"
                                                    @click="tanya({ judul: 'Reset password', pesan: {{ Js::from('Password ' . $nama . ' akan dikembalikan ke ID Karyawan (' . ($u->id_karyawan ?? '-') . ') dan wajib diganti saat login berikutnya.') }}, url: {{ Js::from(route('admin.akun-karyawan.reset-password', $dk->id)) }}, label: 'Reset password' })">
                                                    Reset password
                                                </x-ui.dropdown-item>
                                            @endif
                                            @if ($bisaHapus)
                                                <x-ui.separator class="-mx-1 my-1 w-auto!" />
                                                <x-ui.dropdown-item icon="delete" variant="destructive"
                                                    @click="tanya({ judul: 'Hapus karyawan', pesan: {{ Js::from('Data dan akun ' . $nama . ' akan dipindahkan ke Data Terhapus.') }}, url: {{ Js::from(route('admin.akun-karyawan.hapus', $dk->id)) }}, method: 'DELETE', label: 'Hapus', bahaya: true })">
                                                    Hapus
                                                </x-ui.dropdown-item>
                                            @endif
                                        </x-ui.dropdown-menu>
                                    @endif
                                </div>
                            @endif
                        </x-ui.td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            @if ($adaFilter)
                                <x-ui.empty icon="search_off" title="Tidak ada hasil yang cocok" description="Ubah kata kunci atau filter pencarian.">
                                    <x-ui.button variant="outline" size="sm" :href="route('admin.akun-karyawan.index')">Reset filter</x-ui.button>
                                </x-ui.empty>
                            @else
                                <x-ui.empty icon="group" title="Belum ada data karyawan" description="Tambahkan karyawan satu per satu atau impor dari file Excel.">
                                    @if ($bisaTambah)
                                        <x-ui.button size="sm" @click="bukaTambah()"><span class="material-symbols-outlined">person_add</span>Tambah Karyawan</x-ui.button>
                                    @endif
                                </x-ui.empty>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex flex-col items-center justify-between gap-3 border-t border-border px-4 py-3 sm:flex-row">
            <p class="text-xs text-muted-foreground">
                Menampilkan <span class="font-medium text-foreground tabular-nums">{{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }}</span>
                dari <span class="font-medium text-foreground tabular-nums">{{ $angka($data->total()) }}</span> karyawan
            </p>
            {{ $data->onEachSide(1)->links() }}
        </div>
    </x-ui.card>

    {{-- MODAL TAMBAH / UBAH (satu form, dua mode) --}}
    <x-ui.dialog show="modalForm" maxWidth="sm:max-w-2xl">
        <div class="flex flex-col gap-2 pr-6">
            <h2 class="text-lg font-semibold leading-none" x-text="mode === 'tambah' ? 'Tambah Karyawan' : 'Ubah Data Karyawan'"></h2>
            <p class="text-sm text-muted-foreground" x-text="mode === 'tambah' ? 'Akun dibuat otomatis dengan ID Karyawan sebagai password awal.' : 'Perubahan langsung berlaku pada akun login karyawan.'"></p>
        </div>
        <form method="POST" :action="mode === 'tambah' ? urlSimpan : urlDasar + '/' + form.id" class="grid gap-5">
            @csrf
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'tambah'">
            <input type="hidden" name="_form" :value="mode">
            <input type="hidden" name="id" :value="form.id">

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Identitas</legend>
                <x-ui.field label="Nama lengkap" for="f-nama" error="nama_karyawan" class="sm:col-span-2">
                    <x-ui.input id="f-nama" name="nama_karyawan" required maxlength="255" x-model="form.nama" autocomplete="off" />
                </x-ui.field>
                <x-ui.field label="ID Karyawan" for="f-id" error="id_karyawan" description="Dipakai untuk login dan password awal.">
                    <x-ui.input id="f-id" name="id_karyawan" required maxlength="30" x-model="form.id_karyawan" placeholder="Contoh: KRY-001" class="font-mono" />
                </x-ui.field>
                <x-ui.field label="NIK" for="f-nik" error="nik_karyawan">
                    <x-ui.input id="f-nik" name="nik_karyawan" required maxlength="30" x-model="form.nik" inputmode="numeric" class="font-mono" />
                </x-ui.field>
                <x-ui.field label="Jenis kelamin" for="f-jk" error="jenis_kelamin">
                    <x-ui.select id="f-jk" name="jenis_kelamin" required x-model="form.jenis_kelamin">
                        <option value="">Pilih</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Tanggal lahir" for="f-tgl" error="tanggal_lahir">
                    <x-ui.input id="f-tgl" name="tanggal_lahir" type="date" required x-model="form.tanggal_lahir" />
                </x-ui.field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Penempatan</legend>
                <x-ui.field label="Departemen" for="f-dept" error="departemen_id">
                    <x-ui.select id="f-dept" name="departemen_id" required x-model="form.departemen_id" @change="muatPosisi(true)">
                        <option value="">Pilih departemen</option>
                        @foreach ($semuaDepartemen as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Posisi" for="f-posisi" error="posisi_id">
                    <x-ui.select id="f-posisi" name="posisi_id" required x-model="form.posisi_id" ::disabled="!form.departemen_id">
                        <option value="" x-text="form.departemen_id ? 'Pilih posisi' : 'Pilih departemen dulu'"></option>
                        <template x-for="p in posisiList" :key="p.id">
                            <option :value="String(p.id)" x-text="p.nama_posisi"></option>
                        </template>
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Pendidikan terakhir (opsional)" for="f-pend" error="pendidikan_terakhir" class="sm:col-span-2">
                    <x-ui.input id="f-pend" name="pendidikan_terakhir" maxlength="255" x-model="form.pendidikan_terakhir" placeholder="Contoh: S1 Teknik Informatika" />
                </x-ui.field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Kontak</legend>
                <x-ui.field label="Email (opsional)" for="f-email" error="email">
                    <x-ui.input id="f-email" name="email" type="email" maxlength="255" x-model="form.email" placeholder="nama@perusahaan.com" />
                </x-ui.field>
                <x-ui.field label="No. HP (opsional)" for="f-hp" error="no_hp">
                    <x-ui.input id="f-hp" name="no_hp" type="tel" maxlength="30" x-model="form.no_hp" placeholder="08xxxxxxxxxx" />
                </x-ui.field>
            </fieldset>

            <x-ui.alert icon="info" x-show="mode === 'tambah'">
                Karyawan login memakai ID Karyawan sebagai password awal dan wajib menggantinya saat login pertama.
            </x-ui.alert>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" @click="modalForm = false">Batal</x-ui.button>
                <x-ui.button type="submit"><span class="material-symbols-outlined">save</span><span x-text="mode === 'tambah' ? 'Simpan karyawan' : 'Simpan perubahan'"></span></x-ui.button>
            </div>
        </form>
    </x-ui.dialog>

    {{-- MODAL IMPORT --}}
    @if ($bisaTambah)
        <x-ui.dialog show="modalImport" title="Import Karyawan dari Excel" description="Tambahkan banyak karyawan sekaligus memakai template yang disediakan.">
            <form method="POST" action="{{ route('admin.akun-karyawan.import') }}" enctype="multipart/form-data" class="grid gap-4">
                @csrf
                <ol class="grid gap-3 text-sm">
                    <li class="flex gap-3">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-indigo/10 text-xs font-semibold text-brand-indigo">1</span>
                        <div class="grid gap-2">
                            <p>Unduh template, lalu isi satu baris untuk setiap karyawan.</p>
                            <x-ui.button variant="outline" size="sm" :href="route('admin.akun-karyawan.template')" class="w-fit">
                                <span class="material-symbols-outlined">download</span>Unduh template (.xlsx)
                            </x-ui.button>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-indigo/10 text-xs font-semibold text-brand-indigo">2</span>
                        <x-ui.field label="Pilih file yang sudah diisi" for="f-excel" error="file_excel" description="Format .xlsx atau .xls, maksimal 5 MB. NIK yang sudah terdaftar akan dilewati." class="flex-1">
                            <x-ui.input id="f-excel" name="file_excel" type="file" required accept=".xlsx,.xls"
                                        class="h-auto py-1.5 file:mr-3 file:rounded file:border-0 file:bg-muted file:px-2 file:py-1 file:text-sm file:font-medium" />
                        </x-ui.field>
                    </li>
                </ol>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <x-ui.button variant="outline" @click="modalImport = false">Batal</x-ui.button>
                    <x-ui.button type="submit"><span class="material-symbols-outlined">upload</span>Import</x-ui.button>
                </div>
            </form>
        </x-ui.dialog>
    @endif

    {{-- MODAL KONFIRMASI (reset password, hapus) --}}
    <x-ui.dialog show="konfirmasi.buka" maxWidth="sm:max-w-md">
        <div class="flex gap-4">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full"
                  :class="konfirmasi.bahaya ? 'bg-destructive/10 text-destructive' : 'bg-brand-blue/10 text-brand-blue'">
                <span class="material-symbols-outlined text-[20px]" x-text="konfirmasi.bahaya ? 'delete' : 'lock_reset'"></span>
            </span>
            <div class="grid gap-1.5 pr-4">
                <h2 class="text-lg font-semibold leading-tight" x-text="konfirmasi.judul"></h2>
                <p class="text-sm text-muted-foreground" x-text="konfirmasi.pesan"></p>
            </div>
        </div>
        <form method="POST" :action="konfirmasi.url" class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            @csrf
            <input type="hidden" name="_method" :value="konfirmasi.method">
            <x-ui.button variant="outline" @click="konfirmasi.buka = false">Batal</x-ui.button>
            <x-ui.button type="submit" ::class="konfirmasi.bahaya && 'bg-destructive hover:bg-destructive/90'" x-text="konfirmasi.label"></x-ui.button>
        </form>
    </x-ui.dialog>
</div>

<script>
function akunKaryawan(cfg) {
    const kosong = () => ({ id: '', id_karyawan: '', nik: '', nama: '', jenis_kelamin: '', tanggal_lahir: '', pendidikan_terakhir: '',
        departemen_id: '', posisi_id: '', email: '', no_hp: '' });
    return {
        ...cfg,
        modalForm: false, modalImport: cfg.importGagal, mode: 'tambah', form: kosong(), posisiList: [],
        konfirmasi: { buka: false, judul: '', pesan: '', url: '', method: 'POST', label: '', bahaya: false },
        init() {
            if (cfg.formLama) this.bukaForm(cfg.formLama.mode, cfg.formLama.data);
        },
        bukaTambah() { this.bukaForm('tambah', kosong()); },
        bukaUbah(data) { this.bukaForm('ubah', data); },
        bukaForm(mode, data) {
            this.mode = mode;
            this.form = { ...kosong(), ...data };
            this.muatPosisi();
            this.modalForm = true;
        },
        // reset = departemen diganti user → posisi lama tidak berlaku
        async muatPosisi(reset = false) {
            const pilihan = reset ? '' : this.form.posisi_id;
            this.posisiList = [];
            this.form.posisi_id = ''; // nilai baru dipasang ulang setelah opsi ada, agar x-model memilihnya
            if (!this.form.departemen_id) return;
            const res = await fetch(this.posisiUrl.replace('__D__', this.form.departemen_id));
            this.posisiList = await res.json();
            await this.$nextTick();
            this.form.posisi_id = String(pilihan);
        },
        tanya(opsi) { this.konfirmasi = { method: 'POST', bahaya: false, ...opsi, buka: true }; },
    };
}
</script>
@endsection
