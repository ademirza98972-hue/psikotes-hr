@extends('layouts.admin', ['judulHalaman' => 'Bank Soal'])

@section('content')
@php
    $u = auth()->user();
    $bisaTambah = $u->hasIzin('soal.tambah');
    $bisaUbah = $u->hasIzin('soal.edit');
    $bisaHapus = $u->hasIzin('soal.hapus');
    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');
@endphp

<div class="mx-auto max-w-7xl space-y-6" x-data="{ konfirmasi: { buka: false, judul: '', pesan: '', url: '', label: 'Hapus' } }">

@if (! $alatTesTerpilih)
    {{-- SEMUA ALAT TES --}}
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold tracking-tight">Bank Soal</h1>
        <p class="text-sm text-muted-foreground">Pilih alat tes untuk melihat, menambah, atau mengubah butir soalnya.</p>
    </div>

    @if ($alatTesSemua->isEmpty())
        <x-ui.card>
            <x-ui.empty icon="quiz" title="Belum ada alat tes" description="Buat alat tes terlebih dahulu sebelum menyusun soal.">
                <x-ui.button size="sm" :href="route('admin.alat-tes.index')"><span class="material-symbols-outlined">quiz</span>Ke Alat Tes</x-ui.button>
            </x-ui.empty>
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($alatTesSemua as $alat)
                <a href="{{ route('admin.bank-soal.index', ['alat_tes_id' => $alat->id]) }}"
                   class="group flex flex-col gap-4 rounded-xl border border-border bg-card p-5 shadow-sm transition-colors hover:border-primary/40">
                    <div class="flex items-start gap-3">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-ink font-mono text-sm font-semibold text-white">{{ $alat->kode }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold leading-tight">{{ $alat->nama }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ $alat->format_dasar }}</p>
                        </div>
                        @if (! $alat->is_aktif)
                            <x-ui.badge variant="secondary">Nonaktif</x-ui.badge>
                        @endif
                    </div>
                    <div class="mt-auto flex items-end justify-between">
                        <p><span class="text-2xl font-semibold tabular-nums">{{ $angka($alat->soal_count) }}</span> <span class="text-sm text-muted-foreground">soal</span></p>
                        <span class="material-symbols-outlined text-muted-foreground transition-colors group-hover:text-primary">arrow_forward</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@else
    {{-- SATU ALAT TES --}}
    @php
        $alat = $alatTesTerpilih;
        $isMixed = $alat->format_dasar === 'Mixed';
        $tersaring = $cari !== '' || ($isMixed && $subtes !== '');
    @endphp

    <div class="space-y-4">
        <a href="{{ route('admin.bank-soal.index') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>Semua bank soal
        </a>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-ink font-mono text-sm font-semibold text-white">{{ $alat->kode }}</span>
                <div class="min-w-0 space-y-1">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $alat->nama }}</h1>
                    <p class="text-sm text-muted-foreground">{{ $alat->format_dasar }} · {{ $angka($alat->soal_count) }} soal</p>
                </div>
            </div>
            @if ($bisaTambah)
                <x-ui.button :href="route('admin.bank-soal.tambah', $alat->id)">
                    <span class="material-symbols-outlined">add</span>Tambah Soal
                </x-ui.button>
            @endif
        </div>
    </div>

    {{-- TOOLBAR: pindah alat tes + saring --}}
    <x-ui.card class="flex flex-col gap-3 p-3 lg:flex-row lg:items-center lg:justify-between">
        <nav class="flex w-fit max-w-full items-center overflow-x-auto rounded-lg bg-muted p-[3px] text-sm font-medium text-muted-foreground" aria-label="Pilih alat tes">
            @foreach ($alatTesSemua as $a)
                @php $aktif = $a->id === $alat->id; @endphp
                <a href="{{ route('admin.bank-soal.index', ['alat_tes_id' => $a->id]) }}" @if ($aktif) aria-current="page" @endif title="{{ $a->nama }}"
                   class="inline-flex h-8 items-center gap-1.5 whitespace-nowrap rounded-md px-3 font-mono transition-all {{ $aktif ? 'bg-background text-foreground shadow-sm' : 'hover:text-foreground' }}">
                    {{ $a->kode }}<span class="font-sans text-xs text-muted-foreground tabular-nums">{{ $a->soal_count }}</span>
                </a>
            @endforeach
        </nav>
        <form method="GET" action="{{ route('admin.bank-soal.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="alat_tes_id" value="{{ $alat->id }}">
            <div class="relative min-w-0 flex-1 sm:w-64 sm:flex-none">
                <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-muted-foreground">search</span>
                <x-ui.input type="search" name="cari" value="{{ $cari }}" placeholder="Cari teks soal atau opsi" class="pl-9" aria-label="Cari soal" />
            </div>
            @if ($isMixed)
                <div class="w-40">
                    <x-ui.select name="subtes" aria-label="Subtes" onchange="this.form.submit()">
                        <option value="">Semua subtes</option>
                        @foreach (\App\Http\Controllers\Admin\BankSoalController::SUBTES_IST as $kode => [$dari, $sampai])
                            <option value="{{ $kode }}" @selected($subtes === $kode)>{{ $kode }} · {{ $dari }}–{{ $sampai }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
            @endif
            <x-ui.button type="submit" variant="secondary">Cari</x-ui.button>
            @if ($tersaring)
                <x-ui.button variant="ghost" :href="route('admin.bank-soal.index', ['alat_tes_id' => $alat->id])">Reset</x-ui.button>
            @endif
        </form>
    </x-ui.card>

    @if ($daftarSoal->isEmpty())
        <x-ui.card>
            @if ($tersaring)
                <x-ui.empty icon="search_off" title="Tidak ada soal yang cocok" description="Ubah kata kunci atau pilihan subtes." />
            @else
                <x-ui.empty icon="format_list_numbered" title="Belum ada soal" description="Alat tes ini belum memiliki butir soal.">
                    @if ($bisaTambah)
                        <x-ui.button size="sm" :href="route('admin.bank-soal.tambah', $alat->id)"><span class="material-symbols-outlined">add</span>Tambah Soal</x-ui.button>
                    @endif
                </x-ui.empty>
            @endif
        </x-ui.card>
    @else
        @if ($tersaring)
            <p class="text-sm text-muted-foreground">{{ $angka($daftarSoal->count()) }} dari {{ $angka($alat->soal_count) }} soal</p>
        @endif
        <div class="grid grid-cols-1 gap-4 {{ $alat->format_dasar === 'Grid' ? 'lg:grid-cols-2' : '' }}">
            @foreach ($daftarSoal as $soal)
                @include('admin.bank-soal.partials.kartu-soal')
            @endforeach
        </div>
    @endif
@endif

    @if ($bisaHapus)
        @include('admin.alat-tes._konfirmasi')
    @endif
</div>
@endsection
