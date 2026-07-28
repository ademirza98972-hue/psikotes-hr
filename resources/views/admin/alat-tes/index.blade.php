@extends('layouts.admin', ['judulHalaman' => 'Kelola Alat Tes'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
    ];

    $warnaBidang = [
        'Intelektual'    => 'bg-sky-100 text-sky-700 border-sky-200',
        'Sikap Kerja'    => 'bg-amber-100 text-amber-700 border-amber-200',
        'Kepribadian'    => 'bg-violet-100 text-violet-700 border-violet-200',
        'Potensi Kerja'  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Sensitif'       => 'bg-rose-100 text-rose-700 border-rose-200',
    ];
@endphp

<div x-data="{ openDimensiId: null }" class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Daftar Alat Tes</h2>
            <p class="mt-0.5 text-xs text-slate-500">Konfigurasi alat tes yang tersedia untuk kandidat dan karyawan.</p>
        </div>
        <a href="{{ route('admin.alat-tes.tambah') }}"
           class="self-start rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] whitespace-nowrap">
            + Tambah Alat Tes
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Format Dasar</th>
                    <th class="px-4 py-3 text-center">Durasi Total</th>
                    <th class="px-4 py-3 text-center">Batas per Soal</th>
                    <th class="px-4 py-3 text-center">Jumlah Soal</th>
                    <th class="px-4 py-3 text-center">Dimensi</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($alatTes as $alat)
                    <tr class="hover:bg-slate-50" :class="{ 'bg-slate-50': openDimensiId === {{ $alat['id'] }} }">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $alat['nama'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-md {{ $warnaFormat[$alat['format_dasar']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                                {{ $alat['format_dasar'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-600">
                            @if ($alat['durasi_total_menit'])
                                {{ $alat['durasi_total_menit'] }} menit
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($alat['batas_waktu_per_soal_aktif'])
                                <span class="inline-block rounded-md bg-emerald-600 px-2 py-0.5 text-xs font-semibold text-white">
                                    Aktif ({{ $alat['batas_waktu_per_soal_detik'] }} dtk)
                                </span>
                            @else
                                <span class="inline-block rounded-md bg-slate-300 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    Mati
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-slate-600">{{ $alat['jumlah_soal'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @php $jumlahDimensi = count($alat['dimensi'] ?? []); @endphp
                            @if ($jumlahDimensi > 0)
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs text-slate-600">
                                        {{ $jumlahDimensi }} dimensi dikonfigurasi
                                    </span>
                                    <button type="button"
                                            @click="openDimensiId = (openDimensiId === {{ $alat['id'] }} ? null : {{ $alat['id'] }})"
                                            class="text-xs font-medium text-[#2C5F6F] hover:text-[#234853] hover:underline">
                                        <span x-show="openDimensiId !== {{ $alat['id'] }}">Lihat Detail Dimensi ▾</span>
                                        <span x-show="openDimensiId === {{ $alat['id'] }}" x-cloak>Sembunyikan ▴</span>
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($alat['is_sensitif'])
                                <span class="inline-block rounded-md bg-rose-600 px-2 py-0.5 text-xs font-semibold text-white">
                                    Sensitif
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @if ($jumlahDimensi > 0)
                        <tr x-show="openDimensiId === {{ $alat['id'] }}" x-cloak>
                            <td colspan="7" class="bg-slate-50 px-4 py-4">
                                <div class="rounded-md border border-slate-200 bg-white p-4">
                                    <div class="mb-2 flex items-center justify-between">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Detail Dimensi — {{ $alat['nama'] }}
                                        </p>
                                        <span class="text-xs text-slate-500">
                                            Tipe kategori: <span class="font-medium">{{ $alat['dimensi'][0]['tipe_kategori'] }}</span>
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($alat['dimensi'] as $dimensi)
                                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs {{ $warnaBidang[$dimensi['bidang_psikogram']] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                @if (!empty($dimensi['kode']))
                                                    <span class="font-mono text-[10px] font-semibold opacity-70">[{{ $dimensi['kode'] }}]</span>
                                                @endif
                                                <span class="font-medium">{{ $dimensi['nama_dimensi'] }}</span>
                                                <span class="text-[10px] opacity-60">·</span>
                                                <span class="text-[10px]">{{ $dimensi['bidang_psikogram'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                            Belum ada data alat tes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection