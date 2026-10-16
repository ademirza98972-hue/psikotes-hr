@extends('layouts.admin', ['judulHalaman' => "Norma — {$alatTes->nama}"])

@section('content')
@php
    $angka = fn ($n) => number_format((float) $n, 0, ',', '.');
    $desimal = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',');

    // Nama kelompok yang mudah dibaca. EPPS: 3/4 dipakai sistem (jenis kelamin), lihat ScoringEngineService::kelompokSegmen
    $namaKelompok = fn ($k) => match (true) {
        (bool) preg_match('/^USIA_(\d+)_(\d+)$/', $k, $m) => "Usia {$m[1]}–{$m[2]}",
        $k === '3' => 'Dewasa pria',
        $k === '4' => 'Dewasa wanita',
        $k === 'default' => 'Semua peserta',
        default => "Kelompok {$k}",
    };
    $kelompokTakTerpakai = $alatTes->format_dasar === 'Forced Choice' ? ['1', '2'] : [];
    $judulTahap = fn ($tahap) => match (true) {
        $dipilih?->kode_dimensi === 'IQ' && (int) $tahap === 1 => 'Tahap 1 — jumlah skor mentah ke skor standar (SW)',
        $dipilih?->kode_dimensi === 'IQ' && (int) $tahap === 2 => 'Tahap 2 — skor standar (SW) ke IQ',
        count($matriks) > 1 => "Tahap {$tahap}",
        default => null,
    };
@endphp

<div class="mx-auto max-w-5xl space-y-6" x-data="{ hapusLama: false, namaFile: '' }">

    @include('admin.alat-tes._kepala', ['deskripsi' => 'Tabel konversi skor mentah peserta menjadi skor standar, dibaca seperti di buku manual.'])

    @if ($dimensiNorma->isEmpty())
        <x-ui.card>
            <x-ui.empty icon="table_chart" title="Belum ada norma" description="Alat tes tanpa norma tetap bisa dipakai bila skornya tidak dikonversi (mis. PAPI, Kraepelin). Bila perlu, import dari CSV di bawah." />
        </x-ui.card>
    @else
        {{-- CARA MEMBACA --}}
        <div class="flex gap-3 rounded-xl border border-brand-teal/20 bg-brand-teal/5 px-4 py-3 text-sm">
            <span class="material-symbols-outlined mt-px text-[20px] text-brand-teal">lightbulb</span>
            <p class="text-muted-foreground">
                <span class="font-medium text-foreground">Cara membaca:</span> cari baris skor mentah peserta, lalu kolom kelompoknya.
                Angka di persilangan itulah skor yang dipakai di laporan. Sistem memilih kelompok otomatis
                @if ($alatTes->kode === 'IST') dari usia peserta pada tanggal tes. @elseif ($kelompokTakTerpakai) dari jenis kelamin peserta. @else untuk semua peserta. @endif
            </p>
        </div>

        <x-ui.card>
            {{-- Pilih dimensi --}}
            <div class="border-b border-border p-4">
                <p class="mb-2 text-xs font-medium text-muted-foreground">Dimensi ({{ $dimensiNorma->count() }})</p>
                <nav class="flex flex-wrap gap-1.5" aria-label="Pilih dimensi">
                    @foreach ($dimensiNorma as $d)
                        @php $aktif = $d->id === $dipilih->id; @endphp
                        <a href="{{ route('admin.alat-tes.norma', [$alatTes->id, 'dimensi' => $d->id]) }}" title="{{ $d->nama_dimensi }}"
                           @if ($aktif) aria-current="page" @endif
                           class="inline-flex h-8 items-center rounded-md border px-2.5 font-mono text-xs font-medium transition-colors {{ $aktif ? 'border-brand-ink bg-brand-ink text-white' : 'border-border hover:bg-muted' }}">
                            {{ $d->kode_dimensi }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="space-y-6 p-4 sm:p-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="font-semibold">{{ $dipilih->nama_dimensi }}</h2>
                        <p class="text-sm text-muted-foreground">{{ $dipilih->deskripsi_aspek ?: 'Kode ' . $dipilih->kode_dimensi }}</p>
                    </div>
                    <p class="text-xs text-muted-foreground"><span class="tabular-nums">{{ $angka($barisPerDimensi[$dipilih->id]) }}</span> baris data</p>
                </div>

                @foreach ($matriks as $tahap => $m)
                    <section class="space-y-2">
                        @if ($judul = $judulTahap($tahap))
                            <h3 class="text-sm font-medium">{{ $judul }}</h3>
                        @endif
                        {{-- tabel boleh bergulir di dalam kotaknya sendiri; kepala & kolom skor mentah tetap terlihat --}}
                        <div class="max-h-[32rem] overflow-auto rounded-lg border border-border">
                            <table class="w-full border-separate border-spacing-0 text-sm">
                                <thead>
                                    <tr>
                                        <th scope="col" class="sticky left-0 top-0 z-20 border-b border-r border-border bg-muted px-3 py-2 text-left text-xs font-medium text-muted-foreground">Skor mentah</th>
                                        @foreach ($m['kolom'] as $k)
                                            <th scope="col" class="sticky top-0 z-10 whitespace-nowrap border-b border-border bg-muted px-3 py-2 text-right text-xs font-medium {{ in_array($k, $kelompokTakTerpakai, true) ? 'text-muted-foreground/60' : 'text-foreground' }}"
                                                @if (in_array($k, $kelompokTakTerpakai, true)) title="Tidak dipakai sistem saat ini" @endif>
                                                {{ $namaKelompok($k) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($m['sel'] as $rentang => $isi)
                                        @php [$min, $max] = explode('|', $rentang); @endphp
                                        <tr class="group">
                                            <th scope="row" class="sticky left-0 z-10 border-b border-r border-border bg-card px-3 py-1.5 text-left font-mono text-xs font-medium tabular-nums group-last:border-b-0 group-hover:bg-muted">
                                                {{ $desimal($min) }}@if ((float) $max !== (float) $min)–{{ $desimal($max) }}@endif
                                            </th>
                                            @foreach ($m['kolom'] as $k)
                                                <td class="border-b border-border px-3 py-1.5 text-right font-mono tabular-nums group-last:border-b-0 group-hover:bg-muted/60 {{ in_array($k, $kelompokTakTerpakai, true) ? 'text-muted-foreground/60' : '' }}">
                                                    {{ isset($isi[$k]) ? $desimal($isi[$k]) : '·' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($kelompokTakTerpakai && $m['kolom']->intersect($kelompokTakTerpakai)->isNotEmpty())
                            <p class="text-xs text-muted-foreground">Kolom abu-abu (kelompok {{ implode(' & ', $kelompokTakTerpakai) }}) tersimpan dari sumber norma tetapi tidak dipakai saat skoring.</p>
                        @endif
                    </section>
                @endforeach
            </div>
        </x-ui.card>
    @endif

    {{-- IMPORT CSV (sekunder, dilipat) --}}
    <details class="group rounded-xl border border-border bg-card shadow-sm" @error('file_csv') open @enderror>
        <summary class="flex cursor-pointer list-none items-center gap-3 px-5 py-4 [&::-webkit-details-marker]:hidden">
            <span class="material-symbols-outlined text-[20px] text-brand-teal">upload_file</span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium">Import atau ganti norma dari CSV</p>
                <p class="text-xs text-muted-foreground">Total {{ $angka($jumlahNorma) }} baris norma tersimpan untuk {{ $alatTes->kode }}</p>
            </div>
            <span class="material-symbols-outlined text-[20px] text-muted-foreground transition-transform group-open:rotate-180">expand_more</span>
        </summary>

        <div class="grid gap-6 border-t border-border p-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <form action="{{ route('admin.alat-tes.uploadNorma', $alatTes->id) }}" method="POST" enctype="multipart/form-data" class="grid gap-4">
                @csrf
                <label class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border border-dashed border-border bg-muted/30 px-4 py-6 text-center transition-colors hover:border-brand-teal/50 hover:bg-muted/60 has-focus-visible:ring-[3px] has-focus-visible:ring-ring/50">
                    <span class="material-symbols-outlined text-[28px] text-brand-teal">upload_file</span>
                    <span class="text-sm font-medium" x-text="namaFile || 'Pilih file CSV'"></span>
                    <span class="text-xs text-muted-foreground">Format .csv atau .txt, maksimal 2 MB</span>
                    <input type="file" name="file_csv" accept=".csv,.txt" required class="sr-only" @change="namaFile = $event.target.files[0]?.name ?? ''">
                </label>
                @error('file_csv')
                    <x-ui.alert variant="destructive" icon="error" title="Import gagal">{{ $message }}</x-ui.alert>
                @enderror

                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-border p-3 transition-colors has-checked:border-red-200 has-checked:bg-red-50">
                    <input type="hidden" name="hapus_lama" value="0">
                    <x-ui.checkbox name="hapus_lama" value="1" class="mt-0.5" x-model="hapusLama" />
                    <span class="grid gap-0.5">
                        <span class="text-sm font-medium">Ganti semua norma lama</span>
                        <span class="text-xs text-muted-foreground" x-show="!hapusLama">Bila tidak dicentang, baris baru ditambahkan ke norma yang sudah ada.</span>
                        <span class="text-xs font-medium text-red-700" x-show="hapusLama" x-cloak>{{ $angka($jumlahNorma) }} baris norma yang ada akan dihapus sebelum import. Tidak bisa dibatalkan.</span>
                    </span>
                </label>

                <div class="flex justify-end">
                    <x-ui.button type="submit" ::class="hapusLama && 'bg-destructive hover:bg-destructive/90'">
                        <span class="material-symbols-outlined">upload</span><span x-text="hapusLama ? 'Ganti & import' : 'Import'"></span>
                    </x-ui.button>
                </div>
            </form>

            <div class="grid content-start gap-4 text-sm">
                <div class="grid gap-2">
                    <p class="text-xs font-medium text-muted-foreground">Kolom CSV (satu baris = satu sel tabel di atas)</p>
                    <ol class="grid gap-1 font-mono text-xs">
                        @foreach (['dimensi_kode' => 'kode dimensi', 'kelompok_segmen' => 'kolom kelompok', 'tahap' => '1, atau 2 untuk tahap kedua', 'skor_mentah_min' => 'baris skor mentah', 'skor_mentah_max' => 'sama dengan min bila satu angka', 'skor_hasil' => 'isi sel'] as $kolom => $arti)
                            <li class="flex justify-between gap-2"><span>{{ $kolom }}</span><span class="font-sans text-muted-foreground">{{ $arti }}</span></li>
                        @endforeach
                    </ol>
                </div>
                <div class="grid gap-2">
                    <p class="text-xs font-medium text-muted-foreground">Kode dimensi yang dikenali</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse ($dimensi as $d)
                            <x-ui.badge variant="outline" class="font-mono" title="{{ $d->nama_dimensi }}">{{ $d->kode_dimensi }}</x-ui.badge>
                        @empty
                            <p class="text-xs text-amber-700">Belum ada dimensi. <a href="{{ route('admin.alat-tes.dimensi', $alatTes->id) }}" class="font-medium underline">Tambahkan dulu</a>.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </details>
</div>
@endsection
