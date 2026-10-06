@extends('layouts.admin', ['judulHalaman' => 'Kelola Alat Tes'])

@section('content')
@php
    $bisaKelola = auth()->user()->hasIzin('kategori_tes.kelola');
    $pilihanPola = [
        'kognitif'             => 'Kognitif',
        'forced_choice'        => 'Forced Choice',
        'forced_choice_rollup' => 'Forced Choice Rollup',
        'grid'                 => 'Grid',
    ];
@endphp

<div class="mx-auto max-w-7xl space-y-6 p-2"
     x-data="{
         modalTambah: false,
         batasAktif: {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }},
         modalUbah: false,
         ubah: { id:'', kode:'', nama:'', formatDasar:'Pilihan Ganda', polaSkoring:'kognitif', durasi:'', deskripsi:'', batasAktif:false, batasDet:'', isSensitif:false },
         openUbah(id, kode, nama, formatDasar, polaSkoring, durasi, deskripsi, batasAktif, batasDet, isSensitif) {
             this.ubah = { id: String(id), kode, nama, formatDasar, polaSkoring,
                           durasi: String(durasi ?? ''), deskripsi,
                           batasAktif: Boolean(batasAktif), batasDet: String(batasDet ?? ''), isSensitif: Boolean(isSensitif) };
             this.modalUbah = true;
         }
     }"
     x-init="
         @if(old('_form') === 'tambah') $nextTick(() => { modalTambah = true; }); @endif
         @if(old('_form') === 'ubah') $nextTick(() => { openUbah({{ (int)old('id',0) }}, @js(old('kode','')), @js(old('nama','')), @js(old('format_dasar','Pilihan Ganda')), @js(old('pola_skoring','kognitif')), @js(old('durasi_total_menit','')), @js(old('deskripsi','')), {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }}, @js(old('batas_waktu_per_soal_detik','')), {{ old('is_sensitif') ? 'true' : 'false' }}); }); @endif
     ">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight">Alat Tes</h1>
            <p class="text-sm text-muted-foreground">Kelola jenis instrumen tes psikometri yang tersedia dalam sistem.</p>
        </div>
        @if ($bisaKelola)
            <x-ui.button @click="modalTambah = true">
                <span class="material-symbols-outlined">add</span>
                Tambah Alat Tes
            </x-ui.button>
        @endif
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Total Instrumen', $totalAlat, 'quiz'],
            ['Aktif', $totalAlat, 'check_circle'],
            ['Total Soal', number_format($totalSoal ?? 0), 'inventory_2'],
            ['Terpopuler', $alatPopuler->nama ?? '-', 'trending_up'],
        ] as [$judul, $nilai, $ikon])
            <x-ui.card class="p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium">{{ $judul }}</p>
                    <span class="material-symbols-outlined text-[18px] text-muted-foreground">{{ $ikon }}</span>
                </div>
                <p class="mt-2 truncate {{ $loop->last ? 'text-base' : 'text-2xl' }} font-bold" title="{{ $nilai }}">{{ $nilai }}</p>
            </x-ui.card>
        @endforeach
    </div>

    {{-- TABLE --}}
    <x-ui.card class="overflow-hidden">
        <x-ui.table>
            <thead class="bg-muted/50">
                <tr class="border-b border-border">
                    <x-ui.th class="w-12 text-center">No.</x-ui.th>
                    <x-ui.th class="w-24">Kode</x-ui.th>
                    <x-ui.th>Nama Alat Tes</x-ui.th>
                    <x-ui.th class="text-center">Soal</x-ui.th>
                    <x-ui.th>Format</x-ui.th>
                    <x-ui.th>Status</x-ui.th>
                    <x-ui.th class="text-right">Aksi</x-ui.th>
                </tr>
            </thead>
            <tbody>
                @forelse ($alatTes as $alat)
                    <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/50">
                        <x-ui.td class="text-center tabular-nums text-muted-foreground">{{ ($alatTes->currentPage() - 1) * $alatTes->perPage() + $loop->iteration }}</x-ui.td>
                        <x-ui.td>
                            <x-ui.badge variant="outline" class="font-mono">{{ $alat['kode'] }}</x-ui.badge>
                        </x-ui.td>
                        <x-ui.td class="min-w-64 whitespace-normal!">
                            <p class="font-medium">{{ $alat['nama'] }}</p>
                            @if ($alat['deskripsi'])
                                <p class="mt-0.5 line-clamp-1 text-xs text-muted-foreground">{{ $alat['deskripsi'] }}</p>
                            @endif
                        </x-ui.td>
                        <x-ui.td class="text-center tabular-nums">{{ number_format($alat->soal_count) }}</x-ui.td>
                        <x-ui.td>
                            <x-ui.badge variant="secondary">{{ $alat['format_dasar'] }}</x-ui.badge>
                        </x-ui.td>
                        <x-ui.td>
                            @if ($alat['is_sensitif'])
                                <x-ui.badge variant="danger"><span class="size-1.5 rounded-full bg-red-600"></span>Sensitif</x-ui.badge>
                            @else
                                <x-ui.badge variant="success"><span class="size-1.5 rounded-full bg-emerald-600"></span>Aktif</x-ui.badge>
                            @endif
                        </x-ui.td>
                        <x-ui.td class="text-right">
                            @if ($bisaKelola)
                                <div class="flex items-center justify-end gap-1">
                                    <x-ui.button variant="ghost" size="icon-sm" title="Edit"
                                        @click="openUbah({{ $alat['id'] }}, {{ Js::from($alat['kode']) }}, {{ Js::from($alat['nama']) }}, {{ Js::from($alat['format_dasar']) }}, {{ Js::from($alat['pola_skoring']) }}, {{ Js::from($alat['durasi_total_menit']) }}, {{ Js::from($alat['deskripsi'] ?? '') }}, {{ $alat['batas_waktu_per_soal_aktif'] ? 'true' : 'false' }}, {{ Js::from($alat['batas_waktu_per_soal_detik']) }}, {{ $alat['is_sensitif'] ? 'true' : 'false' }})">
                                        <span class="material-symbols-outlined">edit</span>
                                    </x-ui.button>
                                    <x-ui.button variant="ghost" size="icon-sm" title="Kelola Norma" :href="route('admin.alat-tes.norma', $alat['id'])">
                                        <span class="material-symbols-outlined">bar_chart</span>
                                    </x-ui.button>
                                    <x-ui.button variant="ghost" size="icon-sm" title="Kelola Dimensi" :href="route('admin.alat-tes.dimensi', $alat['id'])">
                                        <span class="material-symbols-outlined">psychology</span>
                                    </x-ui.button>
                                    <x-ui.button variant="ghost" size="icon-sm" title="Hapus" class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        onclick="confirmHapusAlatTes({{ $alat['id'] }}, {{ Js::from($alat['nama']) }})">
                                        <span class="material-symbols-outlined">delete</span>
                                    </x-ui.button>
                                </div>
                            @endif
                        </x-ui.td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-muted">
                                <span class="material-symbols-outlined text-muted-foreground">quiz</span>
                            </div>
                            <p class="mt-4 font-medium">Belum ada alat tes</p>
                            <p class="mt-1 text-sm text-muted-foreground">Tambahkan instrumen tes psikometri untuk digunakan dalam sesi.</p>
                            @if ($bisaKelola)
                                <x-ui.button class="mt-4" size="sm" @click="modalTambah = true">
                                    <span class="material-symbols-outlined">add</span>
                                    Tambah Alat Tes
                                </x-ui.button>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-4 py-3">
            <p class="text-sm text-muted-foreground">
                Menampilkan {{ $alatTes->firstItem() ?? 0 }}–{{ $alatTes->lastItem() ?? 0 }} dari {{ $alatTes->total() }} data
            </p>
            {{ $alatTes->links() }}
        </div>
    </x-ui.card>

    {{-- ALERT --}}
    <div class="flex gap-3 rounded-lg border border-border bg-card px-4 py-3 text-sm">
        <span class="material-symbols-outlined text-[18px]">info</span>
        <div>
            <p class="font-medium">Informasi Status</p>
            <p class="mt-1 text-muted-foreground">
                Alat tes bertanda <span class="font-medium text-red-600">Sensitif</span> memuat konten psikologis klinis dan hanya dapat diakses oleh psikolog terverifikasi.
                Status nonaktif akan menyembunyikan instrumen dari panel peserta.
            </p>
        </div>
    </div>

    {{-- DIALOG TAMBAH --}}
    <x-ui.dialog show="modalTambah" title="Tambah Alat Tes" description="Isi detail instrumen tes baru.">
        <form method="POST" action="{{ route('admin.alat-tes.simpan') }}" class="grid gap-4">
            @csrf
            <input type="hidden" name="_form" value="tambah">

            <div class="grid grid-cols-[7rem_1fr] gap-3">
                <div class="grid gap-2">
                    <x-ui.label for="t-kode">Kode</x-ui.label>
                    <x-ui.input id="t-kode" name="kode" value="{{ old('kode') }}" required maxlength="20" placeholder="DISC" class="font-mono" />
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="t-nama">Nama Lengkap</x-ui.label>
                    <x-ui.input id="t-nama" name="nama" value="{{ old('nama') }}" maxlength="150" placeholder="Nama lengkap alat tes" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="grid gap-2">
                    <x-ui.label for="t-format">Format Soal</x-ui.label>
                    <x-ui.select id="t-format" name="format_dasar" required>
                        @foreach ($pilihanFormat as $format)
                            <option value="{{ $format }}" @selected(old('format_dasar') === $format)>{{ $format }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="t-pola">Pola Skoring</x-ui.label>
                    <x-ui.select id="t-pola" name="pola_skoring" required>
                        @foreach ($pilihanPola as $nilai => $teks)
                            <option value="{{ $nilai }}" @selected(old('pola_skoring') === $nilai)>{{ $teks }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
            </div>

            <div class="grid gap-2">
                <x-ui.label for="t-durasi">Durasi Total (menit)</x-ui.label>
                <x-ui.input id="t-durasi" name="durasi_total_menit" type="number" min="0" value="{{ old('durasi_total_menit') }}" class="w-36!" />
            </div>

            <div class="grid gap-2">
                <x-ui.label for="t-deskripsi">Deskripsi</x-ui.label>
                <x-ui.textarea id="t-deskripsi" name="deskripsi" rows="2" placeholder="Jelaskan tujuan dan fungsi alat tes ini...">{{ old('deskripsi') }}</x-ui.textarea>
            </div>

            <div class="rounded-md border border-border p-3">
                <x-ui.label class="cursor-pointer">
                    <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                    <x-ui.checkbox name="batas_waktu_per_soal_aktif" value="1" x-model="batasAktif" :checked="(bool) old('batas_waktu_per_soal_aktif')" />
                    Aktifkan batas waktu per soal
                </x-ui.label>
                <div x-show="batasAktif" x-cloak class="mt-3 flex items-center gap-2">
                    <x-ui.input name="batas_waktu_per_soal_detik" type="number" min="1" placeholder="30" value="{{ old('batas_waktu_per_soal_detik') }}" class="w-24!" />
                    <span class="text-sm text-muted-foreground">detik per soal</span>
                </div>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-md border border-amber-200 bg-amber-50 p-3">
                <input type="hidden" name="is_sensitif" value="0">
                <x-ui.checkbox name="is_sensitif" value="1" class="mt-0.5" :checked="(bool) old('is_sensitif')" />
                <div>
                    <p class="text-sm font-medium">Kategori sensitif</p>
                    <p class="text-xs text-amber-800">Akses terbatas untuk psikolog terverifikasi.</p>
                </div>
            </label>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" @click="modalTambah = false">Batal</x-ui.button>
                <x-ui.button type="submit">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.dialog>

    {{-- DIALOG EDIT --}}
    <x-ui.dialog show="modalUbah" title="Edit Alat Tes" description="Perbarui detail instrumen tes.">
        <form method="POST" :action="'{{ url('admin/alat-tes') }}/' + ubah.id" class="grid gap-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="ubah">
            <input type="hidden" name="id" :value="ubah.id">

            <div class="grid grid-cols-[7rem_1fr] gap-3">
                <div class="grid gap-2">
                    <x-ui.label for="u-kode">Kode</x-ui.label>
                    <x-ui.input id="u-kode" name="kode" ::value="ubah.kode" required maxlength="20" class="font-mono" />
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="u-nama">Nama Lengkap</x-ui.label>
                    <x-ui.input id="u-nama" name="nama" ::value="ubah.nama" maxlength="150" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="grid gap-2">
                    <x-ui.label for="u-format">Format Soal</x-ui.label>
                    <x-ui.select id="u-format" name="format_dasar" required>
                        @foreach ($pilihanFormat as $format)
                            <option value="{{ $format }}" :selected="ubah.formatDasar === @js($format)">{{ $format }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="grid gap-2">
                    <x-ui.label for="u-pola">Pola Skoring</x-ui.label>
                    <x-ui.select id="u-pola" name="pola_skoring" required>
                        @foreach ($pilihanPola as $nilai => $teks)
                            <option value="{{ $nilai }}" :selected="ubah.polaSkoring === '{{ $nilai }}'">{{ $teks }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
            </div>

            <div class="grid gap-2">
                <x-ui.label for="u-durasi">Durasi Total (menit)</x-ui.label>
                <x-ui.input id="u-durasi" name="durasi_total_menit" type="number" min="0" ::value="ubah.durasi" class="w-36!" />
            </div>

            <div class="grid gap-2">
                <x-ui.label for="u-deskripsi">Deskripsi</x-ui.label>
                <x-ui.textarea id="u-deskripsi" name="deskripsi" rows="2" placeholder="Jelaskan tujuan dan fungsi alat tes ini..." x-model="ubah.deskripsi"></x-ui.textarea>
            </div>

            <div class="rounded-md border border-border p-3">
                <x-ui.label class="cursor-pointer">
                    <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                    <x-ui.checkbox name="batas_waktu_per_soal_aktif" value="1" x-model="ubah.batasAktif" />
                    Aktifkan batas waktu per soal
                </x-ui.label>
                <div x-show="ubah.batasAktif" x-cloak class="mt-3 flex items-center gap-2">
                    <x-ui.input name="batas_waktu_per_soal_detik" type="number" min="1" placeholder="30" ::value="ubah.batasDet" class="w-24!" />
                    <span class="text-sm text-muted-foreground">detik per soal</span>
                </div>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-md border border-amber-200 bg-amber-50 p-3">
                <input type="hidden" name="is_sensitif" value="0">
                <x-ui.checkbox name="is_sensitif" value="1" class="mt-0.5" x-model="ubah.isSensitif" />
                <div>
                    <p class="text-sm font-medium">Kategori sensitif</p>
                    <p class="text-xs text-amber-800">Akses terbatas untuk psikolog terverifikasi.</p>
                </div>
            </label>

            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" @click="modalUbah = false">Batal</x-ui.button>
                <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </x-ui.dialog>
</div>

@push('scripts')
<script>
    function confirmHapusAlatTes(id, nama) {
        Swal.fire({
            icon: 'warning', title: 'Hapus Alat Tes?',
            text: `Alat tes "${nama}" akan dihapus.`,
            showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#737373',
            confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
        }).then(r => {
            if (!r.isConfirmed) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/alat-tes/${id}`;
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        });
    }
</script>
@endpush
@endsection
