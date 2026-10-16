@extends('layouts.admin', ['judulHalaman' => 'Akun Kandidat'])

@section('content')
@php
    $saya = auth()->user();
    $bisaTambah = $saya->hasIzin('pengguna.tambah');
    $bisaEdit = $saya->hasIzin('pengguna.edit');
    $bisaHapus = $saya->hasIzin('pengguna.hapus');
    $bisaVerifikasi = $saya->hasIzin('pengguna.verifikasi');
    $adaFilter = $kataKunci || $filterStatus || $filterPosisi;
    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');

    $dataForm = fn ($k) => [
        'id' => (string) $k->id, 'nama' => $k->name, 'nik' => $k->profilKandidat?->nik_kandidat ?? '', 'email' => $k->email ?? '',
        'no_hp' => $k->profilKandidat?->no_hp ?? '', 'tanggal_lahir' => substr((string) ($k->profilKandidat?->tanggal_lahir ?? ''), 0, 10),
        'jenis_kelamin' => $k->profilKandidat?->jenis_kelamin ?? '', 'departemen' => (string) ($k->profilKandidat?->posisi?->departemen_id ?? ''),
        'posisi_dilamar' => (string) ($k->profilKandidat?->posisi_id ?? ''), 'pendidikan_terakhir' => $k->profilKandidat?->pendidikan_terakhir ?? '',
    ];

    // Form yang dibuka saat halaman dimuat: isian lama (validasi gagal) > ?ubah={id} > ?tambah=1
    $formAwal = match (true) {
        (bool) old('_form') => ['mode' => old('_form'), 'data' => [
            'id' => (string) old('id', ''), 'nama' => old('nama_kandidat', ''), 'nik' => old('nik_kandidat', ''), 'email' => old('email', ''),
            'no_hp' => old('no_hp', ''), 'tanggal_lahir' => old('tanggal_lahir', ''), 'jenis_kelamin' => old('jenis_kelamin', ''),
            'departemen' => (string) old('departemen', ''), 'posisi_dilamar' => (string) old('posisi_dilamar', ''),
            'pendidikan_terakhir' => old('pendidikan_terakhir', ''),
        ]],
        $bukaUbah && $bisaEdit => ['mode' => 'ubah', 'data' => $dataForm($bukaUbah)],
        $bukaTambah && $bisaTambah => ['mode' => 'tambah', 'data' => []],
        default => null,
    };

    $infoStatus = [
        'menunggu_verifikasi' => ['Menunggu verifikasi', 'warning', 'bg-amber-500'],
        'aktif' => ['Aktif', 'success', 'bg-emerald-600'],
        'nonaktif' => ['Nonaktif', 'secondary', 'bg-zinc-400'],
        'ditolak' => ['Ditolak', 'danger', 'bg-red-500'],
    ];
@endphp

<div class="mx-auto max-w-7xl space-y-6"
     x-data="akunKandidat({
         posisiUrl: @js(route('api.posisi.daftar', ['departemen' => '__D__'])),
         urlSimpan: @js(route('admin.data-kandidat.simpan')),
         urlDasar: @js(url('admin/data-kandidat')),
         formAwal: @js($formAwal),
     })">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight">Akun Kandidat</h1>
            <p class="text-sm text-muted-foreground">Verifikasi dan kelola akses kandidat untuk proses rekrutmen.</p>
        </div>
        @if ($bisaTambah)
            <x-ui.button @click="bukaTambah()">
                <span class="material-symbols-outlined">person_add</span>Tambah Kandidat
            </x-ui.button>
        @endif
    </div>

    {{-- RINGKASAN: tiap sel sekaligus pintasan filter status --}}
    <x-ui.card class="overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-border lg:grid-cols-4">
            @foreach ([
                ['Total kandidat', $ringkasan->total, 'groups', 'text-brand-indigo bg-brand-indigo/10', null],
                ['Menunggu verifikasi', $ringkasan->menunggu, 'hourglass_top', 'text-amber-700 bg-amber-100', 'menunggu_verifikasi'],
                ['Aktif', $ringkasan->aktif, 'check_circle', 'text-brand-green-deep bg-brand-green/10', 'aktif'],
                ['Ditolak', $ringkasan->ditolak, 'block', 'text-red-700 bg-red-100', 'ditolak'],
            ] as [$judul, $nilai, $ikon, $warna, $status])
                @php $terpilih = $filterStatus === $status; @endphp
                <a href="{{ route('admin.data-kandidat.index', array_filter(['status' => $status, 'posisi' => $filterPosisi, 'cari' => $kataKunci])) }}"
                   @if ($terpilih) aria-current="true" @endif
                   class="relative flex items-center gap-3 bg-card px-4 py-4 transition-colors hover:bg-muted/40 sm:px-5">
                    @if ($terpilih)<span class="absolute inset-x-0 bottom-0 h-0.5 bg-brand-green" aria-hidden="true"></span>@endif
                    <span class="hidden size-9 shrink-0 items-center justify-center rounded-lg sm:flex {{ $warna }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-muted-foreground">{{ $judul }}</p>
                        <p class="text-xl font-semibold tabular-nums">{{ $angka($nilai) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </x-ui.card>

    {{-- DAFTAR --}}
    <x-ui.card>
        <form method="GET" action="{{ route('admin.data-kandidat.index') }}"
              class="grid gap-2 border-b border-border p-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_13rem_13rem_auto]">
            <div class="relative sm:col-span-2 lg:col-span-1">
                <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-muted-foreground">search</span>
                <x-ui.input type="search" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama, email, atau NIK" aria-label="Cari nama, email, atau NIK" class="pl-9" />
            </div>
            <x-ui.select name="status" aria-label="Filter status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach ($infoStatus as $nilai => [$label])
                    <option value="{{ $nilai }}" @selected($filterStatus === $nilai)>{{ $label }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select name="posisi" aria-label="Filter posisi dilamar" onchange="this.form.submit()">
                <option value="">Semua posisi</option>
                @foreach ($semuaPosisi as $p)
                    <option value="{{ $p->id }}" @selected($filterPosisi == $p->id)>{{ $p->nama_posisi }}</option>
                @endforeach
            </x-ui.select>
            <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
                <x-ui.button type="submit" variant="secondary" class="flex-1 lg:flex-none">Cari</x-ui.button>
                @if ($adaFilter)
                    <x-ui.button variant="ghost" :href="route('admin.data-kandidat.index')" class="flex-1 lg:flex-none">
                        <span class="material-symbols-outlined">close</span>Reset
                    </x-ui.button>
                @endif
            </div>
        </form>

        {{-- Tanpa pembungkus overflow agar menu aksi tidak terpotong; kolom sekunder disembunyikan di layar sempit --}}
        <table class="w-full text-sm">
            <caption class="sr-only">Daftar akun kandidat</caption>
            <thead>
                <tr class="border-b border-border">
                    <x-ui.th class="hidden w-12 pl-5 text-center lg:table-cell">No.</x-ui.th>
                    <x-ui.th class="pl-4 lg:pl-3">Kandidat</x-ui.th>
                    <x-ui.th class="hidden xl:table-cell">NIK</x-ui.th>
                    <x-ui.th class="hidden md:table-cell">Posisi dilamar</x-ui.th>
                    <x-ui.th class="hidden sm:table-cell">Status</x-ui.th>
                    <x-ui.th class="pr-4 text-right"><span class="sr-only">Aksi</span></x-ui.th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kandidat as $item)
                    @php
                        $profil = $item->profilKandidat;
                        [$labelStatus, $varianStatus, $titik] = $infoStatus[$item->status] ?? [ucfirst((string) $item->status), 'secondary', 'bg-zinc-400'];
                        $badgeStatus = '<span class="size-1.5 rounded-full ' . $titik . '"></span>' . e($labelStatus);
                        $menunggu = $item->status === 'menunggu_verifikasi';
                        $bisaToggle = $bisaEdit && in_array($item->status, ['aktif', 'nonaktif'], true);
                    @endphp
                    <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/40 {{ $menunggu ? 'bg-amber-50/40' : '' }}">
                        <x-ui.td class="hidden pl-5 text-center tabular-nums text-muted-foreground lg:table-cell">{{ $kandidat->firstItem() + $loop->index }}</x-ui.td>
                        <x-ui.td class="whitespace-normal! pl-4 lg:pl-3">
                            <div class="flex items-center gap-3">
                                <x-ui.avatar :name="$item->name" size="size-9" />
                                <div class="min-w-0">
                                    <p class="font-medium">{{ $item->name }}</p>
                                    <p class="break-all text-xs text-muted-foreground">{{ $item->email }}</p>
                                    <p class="mt-1 text-xs text-muted-foreground md:hidden">{{ $profil?->posisi?->nama_posisi ?? 'Posisi belum diisi' }}</p>
                                    <x-ui.badge :variant="$varianStatus" class="mt-1.5 sm:hidden">{!! $badgeStatus !!}</x-ui.badge>
                                </div>
                            </div>
                        </x-ui.td>
                        <x-ui.td class="hidden font-mono text-xs text-muted-foreground xl:table-cell">{{ $profil?->nik_kandidat ?? '—' }}</x-ui.td>
                        <x-ui.td class="hidden whitespace-normal! md:table-cell">
                            <p>{{ $profil?->posisi?->nama_posisi ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">{{ $profil?->pendidikan_terakhir ?? '' }}</p>
                        </x-ui.td>
                        <x-ui.td class="hidden sm:table-cell">
                            <x-ui.badge :variant="$varianStatus">{!! $badgeStatus !!}</x-ui.badge>
                        </x-ui.td>
                        <x-ui.td class="pl-1 pr-4 text-right sm:pl-3">
                            {{-- ponsel: grid 2 kolom agar 4 tombol baris "menunggu" tidak melebar --}}
                            <div class="grid grid-cols-2 justify-items-end gap-1 sm:flex sm:items-center sm:justify-end">
                                @if ($menunggu && $bisaVerifikasi)
                                    <x-ui.button size="sm" title="Setujui" class="bg-brand-green px-2 hover:bg-brand-green-deep sm:px-3"
                                        @click="tanya({ judul: 'Setujui kandidat', pesan: {{ Js::from('Akun ' . $item->name . ' akan diaktifkan. Kandidat dapat login dan mengikuti tes.') }}, url: {{ Js::from(route('admin.data-kandidat.approve', $item->id)) }}, label: 'Setujui', ikon: 'how_to_reg' })">
                                        <span class="material-symbols-outlined">check</span><span class="hidden sm:inline">Setujui</span>
                                        <span class="sr-only sm:hidden">Setujui {{ $item->name }}</span>
                                    </x-ui.button>
                                    <x-ui.button size="sm" variant="outline" title="Tolak" class="px-2 text-destructive hover:text-destructive sm:px-3"
                                        @click="tanya({ judul: 'Tolak kandidat', pesan: {{ Js::from('Akun ' . $item->name . ' akan ditolak. Data tetap disimpan dengan status Ditolak.') }}, url: {{ Js::from(route('admin.data-kandidat.tolak', $item->id)) }}, label: 'Tolak', ikon: 'person_cancel', bahaya: true })">
                                        <span class="material-symbols-outlined">close</span><span class="hidden sm:inline">Tolak</span>
                                        <span class="sr-only sm:hidden">Tolak {{ $item->name }}</span>
                                    </x-ui.button>
                                @endif
                                @if ($bisaEdit)
                                    <x-ui.button variant="ghost" size="icon-sm" title="Ubah data" @click="bukaUbah({{ Js::from($dataForm($item)) }})">
                                        <span class="material-symbols-outlined">edit</span>
                                        <span class="sr-only">Ubah {{ $item->name }}</span>
                                    </x-ui.button>
                                @endif
                                @if ($bisaToggle || $bisaHapus)
                                    <x-ui.dropdown-menu width="w-52">
                                        <x-slot:trigger>
                                            <x-ui.button variant="ghost" size="icon-sm" title="Aksi lainnya">
                                                <span class="material-symbols-outlined">more_vert</span>
                                                <span class="sr-only">Aksi lainnya untuk {{ $item->name }}</span>
                                            </x-ui.button>
                                        </x-slot:trigger>
                                        @if ($bisaToggle)
                                            <form method="POST" action="{{ route('admin.data-kandidat.toggle-status', $item->id) }}">
                                                @csrf @method('PATCH')
                                                <x-ui.dropdown-item type="submit" :icon="$item->status === 'aktif' ? 'block' : 'check_circle'">
                                                    {{ $item->status === 'aktif' ? 'Nonaktifkan akun' : 'Aktifkan akun' }}
                                                </x-ui.dropdown-item>
                                            </form>
                                        @endif
                                        @if ($bisaHapus)
                                            @if ($bisaToggle)<x-ui.separator class="-mx-1 my-1 w-auto!" />@endif
                                            <x-ui.dropdown-item icon="delete" variant="destructive"
                                                @click="tanya({ judul: 'Hapus kandidat', pesan: {{ Js::from('Akun ' . $item->name . ' akan dipindahkan ke Data Terhapus.') }}, url: {{ Js::from(route('admin.data-kandidat.hapus', $item->id)) }}, method: 'DELETE', label: 'Hapus', ikon: 'delete', bahaya: true })">
                                                Hapus
                                            </x-ui.dropdown-item>
                                        @endif
                                    </x-ui.dropdown-menu>
                                @endif
                            </div>
                        </x-ui.td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            @if ($adaFilter)
                                <x-ui.empty icon="search_off" title="Tidak ada hasil yang cocok" description="Ubah kata kunci atau filter pencarian.">
                                    <x-ui.button variant="outline" size="sm" :href="route('admin.data-kandidat.index')">Reset filter</x-ui.button>
                                </x-ui.empty>
                            @else
                                <x-ui.empty icon="person_search" title="Belum ada kandidat" description="Kandidat mendaftar sendiri lewat halaman registrasi atau ditambahkan oleh admin.">
                                    @if ($bisaTambah)
                                        <x-ui.button size="sm" @click="bukaTambah()"><span class="material-symbols-outlined">person_add</span>Tambah Kandidat</x-ui.button>
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
                Menampilkan <span class="font-medium text-foreground tabular-nums">{{ $kandidat->firstItem() ?? 0 }}–{{ $kandidat->lastItem() ?? 0 }}</span>
                dari <span class="font-medium text-foreground tabular-nums">{{ $angka($kandidat->total()) }}</span> kandidat
            </p>
            {{ $kandidat->onEachSide(1)->links() }}
        </div>
    </x-ui.card>

    {{-- MODAL TAMBAH / UBAH (satu form, dua mode) --}}
    <x-ui.dialog show="modalForm" maxWidth="sm:max-w-2xl">
        <div class="flex flex-col gap-2 pr-6">
            <h2 class="text-lg font-semibold leading-none" x-text="mode === 'tambah' ? 'Tambah Kandidat' : 'Ubah Data Kandidat'"></h2>
            <p class="text-sm text-muted-foreground" x-text="mode === 'tambah' ? 'Akun yang dibuat admin langsung aktif tanpa verifikasi.' : 'Kosongkan password bila tidak ingin menggantinya.'"></p>
        </div>
        <form method="POST" :action="mode === 'tambah' ? urlSimpan : urlDasar + '/' + form.id" class="grid gap-5">
            @csrf
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'tambah'">
            <input type="hidden" name="_form" :value="mode">
            <input type="hidden" name="id" :value="form.id">

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Identitas</legend>
                <x-ui.field label="Nama lengkap" for="k-nama" error="nama_kandidat" class="sm:col-span-2">
                    <x-ui.input id="k-nama" name="nama_kandidat" required maxlength="255" x-model="form.nama" autocomplete="off" />
                </x-ui.field>
                <x-ui.field label="NIK KTP" for="k-nik" error="nik_kandidat" description="16 digit sesuai KTP.">
                    <x-ui.input id="k-nik" name="nik_kandidat" required inputmode="numeric" pattern="\d{16}" maxlength="16" x-model="form.nik" class="font-mono" />
                </x-ui.field>
                <x-ui.field label="Jenis kelamin" for="k-jk" error="jenis_kelamin">
                    <x-ui.select id="k-jk" name="jenis_kelamin" required x-model="form.jenis_kelamin">
                        <option value="">Pilih</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Tanggal lahir" for="k-tgl" error="tanggal_lahir">
                    <x-ui.input id="k-tgl" name="tanggal_lahir" type="date" required x-model="form.tanggal_lahir" />
                </x-ui.field>
                <x-ui.field label="Pendidikan terakhir" for="k-pend" error="pendidikan_terakhir">
                    <x-ui.input id="k-pend" name="pendidikan_terakhir" required maxlength="255" x-model="form.pendidikan_terakhir" placeholder="Contoh: S1 Psikologi" />
                </x-ui.field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Lamaran</legend>
                <x-ui.field label="Departemen" for="k-dept" error="departemen">
                    <x-ui.select id="k-dept" name="departemen" required x-model="form.departemen" @change="muatPosisi(true)">
                        <option value="">Pilih departemen</option>
                        @foreach ($daftarDepartemen as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
                <x-ui.field label="Posisi dilamar" for="k-posisi" error="posisi_dilamar">
                    <x-ui.select id="k-posisi" name="posisi_dilamar" required x-model="form.posisi_dilamar" ::disabled="!form.departemen">
                        <option value="" x-text="form.departemen ? 'Pilih posisi' : 'Pilih departemen dulu'"></option>
                        <template x-for="p in posisiList" :key="p.id">
                            <option :value="String(p.id)" x-text="p.nama_posisi"></option>
                        </template>
                    </x-ui.select>
                </x-ui.field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Akun &amp; kontak</legend>
                <x-ui.field label="Email" for="k-email" error="email" description="Dipakai kandidat untuk login.">
                    <x-ui.input id="k-email" name="email" type="email" required maxlength="255" x-model="form.email" placeholder="nama@email.com" />
                </x-ui.field>
                <x-ui.field label="No. HP" for="k-hp" error="no_hp">
                    <x-ui.input id="k-hp" name="no_hp" type="tel" required maxlength="30" x-model="form.no_hp" placeholder="08xxxxxxxxxx" />
                </x-ui.field>
                <x-ui.field for="k-pass" error="password">
                    <x-ui.label for="k-pass"><span x-text="mode === 'tambah' ? 'Password' : 'Password baru (opsional)'"></span></x-ui.label>
                    <x-ui.input-password id="k-pass" name="password" minlength="8" autocomplete="new-password" ::required="mode === 'tambah'" />
                </x-ui.field>
                <x-ui.field label="Konfirmasi password" for="k-pass2">
                    <x-ui.input-password id="k-pass2" name="password_confirmation" minlength="8" autocomplete="new-password" ::required="mode === 'tambah'" />
                </x-ui.field>
            </fieldset>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" @click="modalForm = false">Batal</x-ui.button>
                <x-ui.button type="submit"><span class="material-symbols-outlined">save</span><span x-text="mode === 'tambah' ? 'Simpan kandidat' : 'Simpan perubahan'"></span></x-ui.button>
            </div>
        </form>
    </x-ui.dialog>

    {{-- MODAL KONFIRMASI (setujui, tolak, hapus) --}}
    <x-ui.dialog show="konfirmasi.buka" maxWidth="sm:max-w-md">
        <div class="flex gap-4">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full"
                  :class="konfirmasi.bahaya ? 'bg-destructive/10 text-destructive' : 'bg-brand-green/10 text-brand-green-deep'">
                <span class="material-symbols-outlined text-[20px]" x-text="konfirmasi.ikon"></span>
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
function akunKandidat(cfg) {
    const kosong = () => ({ id: '', nama: '', nik: '', email: '', no_hp: '', tanggal_lahir: '', jenis_kelamin: '',
        departemen: '', posisi_dilamar: '', pendidikan_terakhir: '' });
    return {
        ...cfg,
        modalForm: false, mode: 'tambah', form: kosong(), posisiList: [],
        konfirmasi: { buka: false, judul: '', pesan: '', url: '', method: 'POST', label: '', ikon: '', bahaya: false },
        init() {
            if (cfg.formAwal) this.bukaForm(cfg.formAwal.mode, cfg.formAwal.data);
        },
        bukaTambah() { this.bukaForm('tambah', {}); },
        bukaUbah(data) { this.bukaForm('ubah', data); },
        bukaForm(mode, data) {
            this.mode = mode;
            this.form = { ...kosong(), ...data };
            this.muatPosisi();
            this.modalForm = true;
        },
        // reset = departemen diganti user → posisi lama tidak berlaku
        async muatPosisi(reset = false) {
            const pilihan = reset ? '' : this.form.posisi_dilamar;
            this.posisiList = [];
            this.form.posisi_dilamar = ''; // dipasang ulang setelah opsi ada, agar x-model memilihnya (lihat Fix #24)
            if (!this.form.departemen) return;
            const res = await fetch(this.posisiUrl.replace('__D__', this.form.departemen));
            this.posisiList = await res.json();
            await this.$nextTick();
            this.form.posisi_dilamar = String(pilihan);
        },
        tanya(opsi) { this.konfirmasi = { method: 'POST', bahaya: false, ...opsi, buka: true }; },
    };
}
</script>
@endsection
