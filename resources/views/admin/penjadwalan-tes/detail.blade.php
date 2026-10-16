@extends('layouts.admin', ['judulHalaman' => 'Detail Sesi Tes'])

@section('content')
@php
    $tgl = fn ($d) => $d?->locale('id')->translatedFormat('j M Y') ?? '–';
    $status = $sesi->status_display;
    $badgeStatus = ['Aktif' => 'success', 'Belum Dimulai' => 'warning', 'Draft' => 'secondary', 'Selesai' => 'outline'];
    $badgePengerjaan = ['Belum Mengerjakan' => 'secondary', 'Selesai' => 'success'];
    $peserta = $sesi->pesertaSesiTesRecords->sortBy(fn ($p) => $p->user?->name);
    $total = $peserta->count();
    $selesai = $peserta->where('status_pengerjaan', 'Selesai')->count();
    $persen = $total ? round($selesai / $total * 100) : 0;
    $ids = fn ($c) => $c->pluck('id')->map(fn ($i) => (string) $i)->values()->all();

    $formAwal = match (true) {
        old('_form') === 'ubah' => ['mode' => 'ubah', 'data' => [
            'id' => (string) $sesi->id, 'nama_sesi' => old('nama_sesi', ''), 'tanggal_mulai' => old('tanggal_mulai', ''),
            'tanggal_selesai' => old('tanggal_selesai', ''), 'status' => old('status', 'Draft'),
            'departemen_ids' => array_map('strval', (array) old('departemen_ids', [])), 'alat_tes_ids' => array_map('strval', (array) old('alat_tes_ids', [])),
        ]],
        $bukaUbah => ['mode' => 'ubah', 'data' => null],
        default => null,
    };
    $dataSesi = [
        'id' => (string) $sesi->id, 'nama_sesi' => $sesi->nama_sesi, 'status' => $sesi->status === 'Draft' ? 'Draft' : 'Aktif',
        'tanggal_mulai' => $sesi->tanggal_mulai?->format('Y-m-d') ?? '', 'tanggal_selesai' => $sesi->tanggal_selesai?->format('Y-m-d') ?? '',
        'departemen_ids' => $ids($sesi->departemen), 'alat_tes_ids' => $ids($sesi->alatTes),
    ];
    if ($formAwal && $formAwal['data'] === null) $formAwal['data'] = $dataSesi;

    $calon = $daftarUser->map(fn ($u) => ['id' => (string) $u->id, 'nama' => $u->name, 'email' => $u->email, 'tipe' => $u->tipe_akun])->values();
@endphp

<div class="mx-auto max-w-7xl space-y-6"
     x-data="kelolaSesi({ urlSimpan: @js(route('admin.penjadwalan-tes.simpan')), urlDasar: @js(url('admin/penjadwalan-tes')), formAwal: @js($formAwal),
                          semuaDept: @js($ids($daftarDepartemen)) })">

    {{-- KEPALA --}}
    <div class="space-y-4">
        <a href="{{ route('admin.penjadwalan-tes.index') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>Semua penjadwalan
        </a>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0 space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $sesi->nama_sesi }}</h1>
                    <x-ui.badge :variant="$badgeStatus[$status] ?? 'outline'">{{ $status }}</x-ui.badge>
                </div>
                <p class="text-sm text-muted-foreground">{{ $tgl($sesi->tanggal_mulai) }} – {{ $tgl($sesi->tanggal_selesai) }} · {{ $sesi->departemen->pluck('nama_departemen')->join(', ') ?: 'Lintas departemen' }}</p>
            </div>
            <x-ui.button variant="outline" @click="bukaForm('ubah', {{ Js::from($dataSesi) }})">
                <span class="material-symbols-outlined">edit</span>Ubah sesi
            </x-ui.button>
        </div>
    </div>

    @if ($status === 'Draft')
        <x-ui.alert variant="warning" icon="visibility_off" title="Sesi masih Draft">Peserta belum bisa melihat atau mengerjakan sesi ini. Ubah status menjadi Aktif bila sudah siap.</x-ui.alert>
    @endif

    {{-- RINGKASAN --}}
    <x-ui.card class="overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-border lg:grid-cols-4">
            @foreach ([['Peserta', $total, 'groups'], ['Selesai', $selesai, 'task_alt'], ['Belum mengerjakan', $total - $selesai, 'hourglass_empty']] as [$judul, $nilai, $ikon])
                <div class="flex items-center gap-3 bg-card px-4 py-4 sm:px-5">
                    <span class="hidden size-9 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground sm:flex"><span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span></span>
                    <div><p class="text-xs font-medium text-muted-foreground">{{ $judul }}</p><p class="text-xl font-semibold tabular-nums">{{ $nilai }}</p></div>
                </div>
            @endforeach
            <div class="grid content-center gap-2 bg-card px-4 py-4 sm:px-5">
                <div class="flex items-center justify-between text-xs"><span class="font-medium text-muted-foreground">Progres</span><span class="font-semibold tabular-nums">{{ $persen }}%</span></div>
                <x-ui.progress :value="$persen" />
                <div class="flex flex-wrap gap-1">
                    @forelse ($sesi->alatTes as $alat)
                        <x-ui.badge variant="outline" class="font-mono" title="{{ $alat->nama }}">{{ $alat->kode }}</x-ui.badge>
                    @empty
                        <x-ui.badge variant="warning">Belum ada alat tes</x-ui.badge>
                    @endforelse
                </div>
            </div>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_24rem]">

        {{-- DAFTAR PESERTA --}}
        <x-ui.card>
            <x-ui.card-header class="py-5" title="Peserta" description="Status pengerjaan tiap peserta pada sesi ini." />
            @if ($peserta->isEmpty())
                <x-ui.empty icon="group_add" title="Belum ada peserta" description="Pilih karyawan atau kandidat di panel Tambah peserta." />
            @else
                <x-ui.table class="border-t border-border">
                    <thead><tr class="border-b border-border">
                        <x-ui.th class="pl-6">Nama</x-ui.th><x-ui.th>Alat tes</x-ui.th><x-ui.th>Status</x-ui.th><x-ui.th class="pr-6"><span class="sr-only">Aksi</span></x-ui.th>
                    </tr></thead>
                    <tbody>
                        @foreach ($peserta as $p)
                            @php $terkunci = $p->status_pengerjaan !== 'Belum Mengerjakan' || $p->waktu_mulai; @endphp
                            <tr class="border-b border-border last:border-0 hover:bg-muted/40">
                                <x-ui.td class="pl-6">
                                    <p class="font-medium">{{ $p->user?->name ?? 'Akun terhapus' }}</p>
                                    <p class="text-xs capitalize text-muted-foreground">{{ $p->user?->tipe_akun ?? '–' }}</p>
                                </x-ui.td>
                                <x-ui.td>
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($p->alatTes as $alat)
                                            <x-ui.badge variant="outline" class="font-mono">{{ $alat->kode }}</x-ui.badge>
                                        @empty
                                            <span class="text-muted-foreground">–</span>
                                        @endforelse
                                    </div>
                                </x-ui.td>
                                <x-ui.td><x-ui.badge :variant="$badgePengerjaan[$p->status_pengerjaan] ?? 'warning'">{{ $p->status_pengerjaan }}</x-ui.badge></x-ui.td>
                                <x-ui.td class="pr-6 text-right">
                                    @if ($terkunci)
                                        <span class="material-symbols-outlined text-[18px] text-muted-foreground" title="Sudah mulai mengerjakan; tidak bisa dihapus">lock</span>
                                    @else
                                        <x-ui.button variant="ghost" size="icon-sm" class="hover:text-destructive" title="Keluarkan dari sesi"
                                            @click="tanya({ judul: 'Keluarkan peserta', label: 'Keluarkan', pesan: {{ Js::from(($p->user?->name ?? 'Peserta') . ' akan dikeluarkan dari sesi ini dan tidak bisa mengerjakan tesnya.') }}, url: {{ Js::from(route('admin.penjadwalan-tes.hapusPeserta', [$sesi->id, $p->user_id])) }} })">
                                            <span class="material-symbols-outlined">person_remove</span><span class="sr-only">Keluarkan {{ $p->user?->name }}</span>
                                        </x-ui.button>
                                    @endif
                                </x-ui.td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            @endif
        </x-ui.card>

        {{-- TAMBAH PESERTA --}}
        <x-ui.card class="lg:sticky lg:top-4"
            x-data="{ calon: {{ Js::from($calon) }}, tipe: 'karyawan', cari: '', pilih: {{ Js::from(array_map('strval', (array) old('user_ids', []))) }},
                      get tampil() { const q = this.cari.toLowerCase(); return this.calon.filter(u => u.tipe === this.tipe && (u.nama + ' ' + u.email).toLowerCase().includes(q)); },
                      get semuaTerpilih() { return this.tampil.length > 0 && this.tampil.every(u => this.pilih.includes(u.id)); },
                      togglSemua() { const id = this.tampil.map(u => u.id); this.pilih = this.semuaTerpilih ? this.pilih.filter(i => !id.includes(i)) : [...new Set([...this.pilih, ...id])]; } }">
            <x-ui.card-header class="py-5" title="Tambah peserta" description="Hanya akun aktif yang belum terdaftar di sesi ini." />
            <form method="POST" action="{{ route('admin.penjadwalan-tes.tambahPeserta', $sesi->id) }}" class="grid grid-cols-1 gap-4 border-t border-border p-6">
                @csrf
                <div class="grid grid-cols-2 rounded-lg bg-muted p-[3px] text-sm font-medium text-muted-foreground" role="tablist">
                    @foreach (['karyawan' => 'Karyawan', 'kandidat' => 'Kandidat'] as $nilai => $label)
                        <button type="button" role="tab" @click="tipe = '{{ $nilai }}'" :aria-selected="tipe === '{{ $nilai }}'"
                                :class="tipe === '{{ $nilai }}' ? 'bg-background text-foreground shadow-sm' : 'hover:text-foreground'"
                                class="h-8 rounded-md transition-all">{{ $label }} <span class="text-xs tabular-nums text-muted-foreground" x-text="calon.filter(u => u.tipe === '{{ $nilai }}').length"></span></button>
                    @endforeach
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-muted-foreground">search</span>
                    <x-ui.input type="search" x-model="cari" @keydown.enter.prevent="" placeholder="Cari nama atau email" class="pl-9" aria-label="Cari calon peserta" />
                </div>

                <div class="overflow-hidden rounded-lg border border-border">
                    <label class="flex cursor-pointer items-center gap-3 border-b border-border bg-muted/50 px-3 py-2 text-xs font-medium">
                        <x-ui.checkbox ::checked="semuaTerpilih" @change="togglSemua()" ::disabled="!tampil.length" />
                        Pilih semua yang tampil
                        <span class="ml-auto text-primary" x-text="pilih.length + ' dipilih'"></span>
                    </label>
                    <div class="max-h-72 overflow-y-auto">
                        <template x-for="u in tampil" :key="u.id">
                            <label class="flex cursor-pointer items-center gap-3 border-b border-border px-3 py-2 last:border-0 hover:bg-muted/40">
                                <x-ui.checkbox name="user_ids[]" ::value="u.id" x-model="pilih" />
                                <span class="min-w-0"><span class="block truncate text-sm font-medium" x-text="u.nama"></span><span class="block truncate text-xs text-muted-foreground" x-text="u.email"></span></span>
                            </label>
                        </template>
                        <p x-show="!tampil.length" class="px-3 py-6 text-center text-sm text-muted-foreground"
                           x-text="cari ? 'Tidak ada yang cocok.' : 'Semua akun ' + tipe + ' aktif sudah terdaftar.'"></p>
                    </div>
                </div>
                {{-- peserta terpilih di tab lain tetap terkirim --}}
                <template x-for="id in pilih.filter(i => !tampil.some(u => u.id === i))" :key="id"><input type="hidden" name="user_ids[]" :value="id"></template>
                @foreach (['user_ids', 'user_ids.*'] as $e)
                    @error($e)<p class="text-xs font-medium text-destructive">{{ $message }}</p>@enderror
                @endforeach

                <div class="grid grid-cols-1 gap-2">
                    <x-ui.label>Alat tes yang dikerjakan</x-ui.label>
                    @forelse ($sesi->alatTes as $alat)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-border px-3 py-2 transition-colors has-checked:border-primary has-checked:bg-primary/5">
                            <x-ui.checkbox name="alat_tes_ids[]" value="{{ $alat->id }}" :checked="in_array((string) $alat->id, array_map('strval', (array) old('alat_tes_ids', $ids($sesi->alatTes))), true)" />
                            <span class="font-mono text-xs font-semibold">{{ $alat->kode }}</span><span class="min-w-0 truncate text-sm">{{ $alat->nama }}</span>
                        </label>
                    @empty
                        <x-ui.alert variant="warning" icon="quiz">Sesi ini belum punya alat tes. Tambahkan lewat Ubah sesi.</x-ui.alert>
                    @endforelse
                    @foreach (['alat_tes_ids', 'alat_tes_ids.*'] as $e)
                        @error($e)<p class="text-xs font-medium text-destructive">{{ $message }}</p>@enderror
                    @endforeach
                </div>

                <x-ui.button type="submit" ::disabled="!pilih.length || {{ $sesi->alatTes->isEmpty() ? 'true' : 'false' }}">
                    <span class="material-symbols-outlined">person_add</span><span x-text="pilih.length ? 'Tambahkan ' + pilih.length + ' peserta' : 'Pilih peserta dulu'"></span>
                </x-ui.button>
            </form>
        </x-ui.card>
    </div>

    @include('admin.penjadwalan-tes._form')
</div>
@endsection
