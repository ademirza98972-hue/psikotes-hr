@extends('layouts.admin', ['judulHalaman' => 'Penjadwalan Tes'])

@section('content')
@php
    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');
    $tgl = fn ($d) => $d?->locale('id')->translatedFormat('j M Y') ?? '–';
    $badgeStatus = ['Aktif' => 'success', 'Belum Dimulai' => 'warning', 'Draft' => 'secondary', 'Selesai' => 'outline'];
    $dataForm = fn ($s) => [
        'id' => (string) $s->id, 'nama_sesi' => $s->nama_sesi, 'status' => $s->status === 'Draft' ? 'Draft' : 'Aktif',
        'tanggal_mulai' => $s->tanggal_mulai?->format('Y-m-d') ?? '', 'tanggal_selesai' => $s->tanggal_selesai?->format('Y-m-d') ?? '',
        'departemen_ids' => $s->departemen->pluck('id')->map(fn ($i) => (string) $i)->all(),
        'alat_tes_ids' => $s->alatTes->pluck('id')->map(fn ($i) => (string) $i)->all(),
    ];
    // Form yang dibuka saat halaman dimuat: isian lama (validasi gagal) > ?tambah=1
    $formAwal = match (true) {
        old('_form') === 'tambah' => ['mode' => 'tambah', 'data' => [
            'nama_sesi' => old('nama_sesi', ''), 'tanggal_mulai' => old('tanggal_mulai', ''), 'tanggal_selesai' => old('tanggal_selesai', ''),
            'status' => old('status', 'Draft'), 'departemen_ids' => array_map('strval', (array) old('departemen_ids', [])),
            'alat_tes_ids' => array_map('strval', (array) old('alat_tes_ids', [])),
        ]],
        $bukaTambah => ['mode' => 'tambah', 'data' => []],
        default => null,
    };
@endphp

<div class="mx-auto max-w-7xl space-y-6"
     x-data="kelolaSesi({ urlSimpan: @js(route('admin.penjadwalan-tes.simpan')), urlDasar: @js(url('admin/penjadwalan-tes')), formAwal: @js($formAwal),
                          semuaDept: @js($daftarDepartemen->pluck('id')->map(fn ($i) => (string) $i)->values()) })">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight">Penjadwalan Tes</h1>
            <p class="text-sm text-muted-foreground">Sesi tes beserta periode, alat tes, dan peserta yang mengerjakannya.</p>
        </div>
        <x-ui.button @click="bukaForm('tambah', {})">
            <span class="material-symbols-outlined">add</span>Tambah Penjadwalan
        </x-ui.button>
    </div>

    {{-- RINGKASAN: tiap sel sekaligus pintasan filter status --}}
    <x-ui.card class="overflow-hidden">
        <div class="grid grid-cols-2 gap-px bg-border lg:grid-cols-4">
            @foreach ([
                ['Aktif', 'play_circle', 'text-brand-green-deep bg-brand-green/10', 'Bisa dikerjakan hari ini'],
                ['Belum Dimulai', 'schedule', 'text-amber-700 bg-amber-100', 'Menunggu tanggal mulai'],
                ['Draft', 'edit_note', 'text-muted-foreground bg-muted', 'Belum terlihat peserta'],
                ['Selesai', 'task_alt', 'text-brand-blue bg-brand-blue/10', 'Periode sudah lewat'],
            ] as [$status, $ikon, $warna, $sub])
                @php $terpilih = $filterStatus === $status; @endphp
                <a href="{{ route('admin.penjadwalan-tes.index', array_filter(['status' => $terpilih ? null : $status, 'cari' => $cari])) }}"
                   @if ($terpilih) aria-current="true" @endif
                   class="relative flex items-center gap-3 bg-card px-4 py-4 transition-colors hover:bg-muted/40 sm:px-5">
                    @if ($terpilih)<span class="absolute inset-x-0 bottom-0 h-0.5 bg-brand-green" aria-hidden="true"></span>@endif
                    <span class="hidden size-9 shrink-0 items-center justify-center rounded-lg sm:flex {{ $warna }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-muted-foreground">{{ $status }}</p>
                        <p class="text-xl font-semibold tabular-nums">{{ $angka($ringkasan[$status]) }}</p>
                        <p class="truncate text-xs text-muted-foreground">{{ $sub }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </x-ui.card>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.penjadwalan-tes.index') }}" class="flex flex-wrap items-center gap-2">
        <div class="relative min-w-0 flex-1 sm:max-w-sm">
            <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-muted-foreground">search</span>
            <x-ui.input type="search" name="cari" value="{{ $cari }}" placeholder="Cari nama sesi" class="pl-9" aria-label="Cari nama sesi" />
        </div>
        <div class="w-44">
            <x-ui.select name="status" aria-label="Status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach (array_keys($badgeStatus) as $st)
                    <option value="{{ $st }}" @selected($filterStatus === $st)>{{ $st }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <x-ui.button type="submit" variant="secondary">Cari</x-ui.button>
        @if ($cari || $filterStatus)
            <x-ui.button variant="ghost" :href="route('admin.penjadwalan-tes.index')">Reset</x-ui.button>
        @endif
    </form>

    {{-- DAFTAR SESI --}}
    @if ($penjadwalan->isEmpty())
        <x-ui.card>
            @if ($cari || $filterStatus)
                <x-ui.empty icon="search_off" title="Tidak ada sesi yang cocok" description="Ubah kata kunci atau filter status." />
            @else
                <x-ui.empty icon="event_busy" title="Belum ada penjadwalan tes" description="Buat sesi tes pertama untuk mulai menjadwalkan peserta.">
                    <x-ui.button size="sm" @click="bukaForm('tambah', {})"><span class="material-symbols-outlined">add</span>Tambah Penjadwalan</x-ui.button>
                </x-ui.empty>
            @endif
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @foreach ($penjadwalan as $sesi)
                @php
                    $status = $sesi->status_display;
                    $total = $sesi->jumlah_peserta_count;
                    $persen = $total ? round($sesi->jumlah_selesai_count / $total * 100) : 0;
                @endphp
                <x-ui.card class="flex flex-col">
                    <div class="flex items-start gap-3 p-5 pb-4">
                        <div class="min-w-0 flex-1 space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.penjadwalan-tes.detail', $sesi->id) }}" class="font-semibold leading-tight hover:underline">{{ $sesi->nama_sesi }}</a>
                                <x-ui.badge :variant="$badgeStatus[$status] ?? 'outline'">{{ $status }}</x-ui.badge>
                            </div>
                            <p class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                <span class="material-symbols-outlined text-[16px]">date_range</span>{{ $tgl($sesi->tanggal_mulai) }} – {{ $tgl($sesi->tanggal_selesai) }}
                            </p>
                            <p class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                <span class="material-symbols-outlined text-[16px]">business</span>
                                <span class="truncate">{{ $sesi->departemen->pluck('nama_departemen')->join(', ') ?: 'Lintas departemen' }}</span>
                            </p>
                        </div>
                        <x-ui.dropdown-menu width="w-44">
                            <x-slot:trigger>
                                <x-ui.button variant="ghost" size="icon-sm" class="-mr-2 -mt-1" title="Aksi">
                                    <span class="material-symbols-outlined">more_vert</span>
                                    <span class="sr-only">Aksi untuk {{ $sesi->nama_sesi }}</span>
                                </x-ui.button>
                            </x-slot:trigger>
                            <x-ui.dropdown-item icon="edit" @click="bukaForm('ubah', {{ Js::from($dataForm($sesi)) }})">Ubah</x-ui.dropdown-item>
                            <x-ui.separator class="-mx-1 my-1 w-auto!" />
                            <x-ui.dropdown-item icon="delete" variant="destructive"
                                @click="tanya({ judul: 'Hapus sesi tes', pesan: {{ Js::from('Sesi ' . $sesi->nama_sesi . ' (' . $total . ' peserta) akan dihapus dan tidak lagi tampil di daftar maupun dashboard peserta.') }}, url: {{ Js::from(route('admin.penjadwalan-tes.hapus', $sesi->id)) }} })">
                                Hapus
                            </x-ui.dropdown-item>
                        </x-ui.dropdown-menu>
                    </div>

                    <div class="flex flex-wrap gap-1.5 px-5">
                        @forelse ($sesi->alatTes as $alat)
                            <x-ui.badge variant="outline" class="font-mono" title="{{ $alat->nama }}">{{ $alat->kode }}</x-ui.badge>
                        @empty
                            <x-ui.badge variant="warning">Belum ada alat tes</x-ui.badge>
                        @endforelse
                    </div>

                    <div class="mt-auto space-y-2 px-5 pb-5 pt-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-muted-foreground"><span class="font-semibold text-foreground tabular-nums">{{ $sesi->jumlah_selesai_count }}</span> dari {{ $total }} peserta selesai</span>
                            <span class="font-semibold tabular-nums">{{ $persen }}%</span>
                        </div>
                        <x-ui.progress :value="$persen" />
                        <div class="flex justify-end pt-2">
                            <x-ui.button variant="outline" size="sm" :href="route('admin.penjadwalan-tes.detail', $sesi->id)">
                                <span class="material-symbols-outlined">groups</span>Kelola peserta
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        @if ($penjadwalan->hasPages())
            <div class="flex justify-center">{{ $penjadwalan->onEachSide(1)->links() }}</div>
        @endif
    @endif

    @include('admin.penjadwalan-tes._form')
</div>
@endsection
