@extends('layouts.admin', ['judulHalaman' => "Norma — {$alatTes->nama}"])

@section('content')
@php
    $angka = fn ($n) => number_format((float) $n, 0, ',', '.');
    $desimal = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',');
@endphp

<div class="mx-auto max-w-5xl space-y-6" x-data="{ hapusLama: false, namaFile: '' }">

    @include('admin.alat-tes._kepala', ['deskripsi' => 'Tabel konversi skor mentah menjadi skor standar per dimensi dan kelompok.'])

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        {{-- IMPORT CSV --}}
        <x-ui.card class="py-6">
            <x-ui.card-header title="Import norma dari CSV" description="Satu baris = satu rentang skor mentah untuk satu dimensi." />
            <form action="{{ route('admin.alat-tes.uploadNorma', $alatTes->id) }}" method="POST" enctype="multipart/form-data" class="mt-5 grid gap-4 px-6">
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
        </x-ui.card>

        {{-- PANDUAN FORMAT --}}
        <x-ui.card class="py-6">
            <x-ui.card-header title="Format kolom" />
            <div class="mt-4 grid gap-4 px-6 text-sm">
                <div class="overflow-x-auto rounded-md bg-brand-ink p-3 font-mono text-xs leading-relaxed text-white/90">
                    <p class="whitespace-nowrap text-white/50">dimensi_kode,kelompok_segmen,tahap,skor_mentah_min,skor_mentah_max,skor_hasil</p>
                    <p class="whitespace-nowrap">{{ $dimensi->first()->kode_dimensi ?? 'KODE' }},default,1,0,0,38</p>
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
        </x-ui.card>
    </div>

    {{-- DATA NORMA --}}
    <x-ui.card>
        <div class="flex flex-col gap-3 border-b border-border p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold">Norma tersimpan</h2>
                <p class="text-sm text-muted-foreground"><span class="tabular-nums">{{ $angka($jumlahNorma) }}</span> baris untuk {{ $alatTes->kode }}</p>
            </div>
            @if ($jumlahNorma > 0)
                <form method="GET" class="grid grid-cols-[minmax(0,1fr)_7rem] gap-2 sm:w-96">
                    <x-ui.select name="dimensi" aria-label="Filter dimensi" onchange="this.form.submit()">
                        <option value="">Semua dimensi</option>
                        @foreach ($dimensi as $d)
                            <option value="{{ $d->id }}" @selected($filterDimensi == $d->id)>{{ $d->kode_dimensi }} — {{ $d->nama_dimensi }}</option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.select name="per_page" aria-label="Baris per halaman" onchange="this.form.submit()">
                        @foreach ([25, 50, 100, 250] as $n)
                            <option value="{{ $n }}" @selected($perPage == $n)>{{ $n }} baris</option>
                        @endforeach
                    </x-ui.select>
                </form>
            @endif
        </div>

        @if ($jumlahNorma === 0)
            <x-ui.empty icon="table_chart" title="Belum ada norma" description="Import file CSV di atas. Alat tes tanpa norma tetap bisa dipakai bila skornya tidak dikonversi (mis. PAPI, Kraepelin)." />
        @else
            <x-ui.table>
                <thead>
                    <tr class="border-b border-border">
                        <x-ui.th class="pl-4">Dimensi</x-ui.th>
                        <x-ui.th>Kelompok</x-ui.th>
                        <x-ui.th class="hidden text-center sm:table-cell">Tahap</x-ui.th>
                        <x-ui.th class="text-right">Skor mentah</x-ui.th>
                        <x-ui.th class="pr-4 text-right">Hasil</x-ui.th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contohNorma as $n)
                        <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/40">
                            <x-ui.td class="pl-4"><x-ui.badge variant="outline" class="font-mono">{{ $n->dimensi->kode_dimensi ?? '—' }}</x-ui.badge></x-ui.td>
                            <x-ui.td class="text-muted-foreground">{{ $n->kelompok_segmen }}</x-ui.td>
                            <x-ui.td class="hidden text-center tabular-nums text-muted-foreground sm:table-cell">{{ $n->tahap }}</x-ui.td>
                            <x-ui.td class="text-right font-mono tabular-nums text-muted-foreground">
                                {{ $desimal($n->skor_mentah_min) }}@if ((float) $n->skor_mentah_max !== (float) $n->skor_mentah_min)–{{ $desimal($n->skor_mentah_max) }}@endif
                            </x-ui.td>
                            <x-ui.td class="pr-4 text-right font-mono font-semibold tabular-nums">{{ $desimal($n->skor_hasil) }}</x-ui.td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
            <div class="flex flex-col items-center justify-between gap-3 border-t border-border px-4 py-3 sm:flex-row">
                <p class="text-xs text-muted-foreground">
                    Menampilkan <span class="font-medium text-foreground tabular-nums">{{ $contohNorma->firstItem() }}–{{ $contohNorma->lastItem() }}</span>
                    dari <span class="font-medium text-foreground tabular-nums">{{ $angka($contohNorma->total()) }}</span> baris
                </p>
                {{ $contohNorma->onEachSide(1)->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
@endsection
