@extends('layouts.admin', ['judulHalaman' => 'Kelola Alat Tes'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
    ];
@endphp

<div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

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
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($alatTes as $alat)
                    <tr class="hover:bg-slate-50">
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
                            @if ($alat['is_sensitif'])
                                <span class="inline-block rounded-md bg-rose-600 px-2 py-0.5 text-xs font-semibold text-white">
                                    Sensitif
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                            Belum ada data alat tes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection