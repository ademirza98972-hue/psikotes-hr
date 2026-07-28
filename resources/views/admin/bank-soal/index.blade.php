@extends('layouts.admin', ['judulHalaman' => 'Bank Soal'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
    ];
@endphp

<div class="space-y-4">

    {{-- FILTER PILIH ALAT TES --}}
    <div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">
        <form method="GET" action="{{ route('admin.bank-soal.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                <div>
                    <label for="alat_tes_id" class="block text-sm font-medium text-slate-700">Pilih Alat Tes</label>
                    <select id="alat_tes_id" name="alat_tes_id"
                            class="mt-1 block w-64 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="">— Pilih —</option>
                        @foreach ($alatTes as $alat)
                            <option value="{{ $alat['id'] }}" @selected((int) request('alat_tes_id') === $alat['id'])>
                                {{ $alat['nama'] }} ({{ $alat['format_dasar'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Tampilkan Soal
                </button>
                @if (request('alat_tes_id'))
                    <a href="{{ route('admin.bank-soal.index') }}"
                       class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($alatTesTerpilih)
                    <a href="{{ route('admin.bank-soal.tambah', $alatTesTerpilih['id']) }}"
                       class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] whitespace-nowrap">
                        + Tambah Soal
                    </a>
                    <button type="button" disabled
                            class="cursor-not-allowed rounded-md bg-slate-300 px-4 py-2 text-sm font-semibold text-white opacity-60">
                        Import Excel
                    </button>
                @else
                    <button type="button" disabled
                            class="cursor-not-allowed rounded-md bg-slate-300 px-4 py-2 text-sm font-semibold text-white opacity-60">
                        + Tambah Soal
                    </button>
                    <button type="button" disabled
                            class="cursor-not-allowed rounded-md bg-slate-300 px-4 py-2 text-sm font-semibold text-white opacity-60">
                        Import Excel
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- STATE KOSONG: BELUM PILIH ALAT TES --}}
    @unless ($alatTesTerpilih)
        <div class="w-full rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-3 text-sm text-slate-600">Pilih Alat Tes untuk melihat soal.</p>
        </div>
    @else
        {{-- HEADER ALAT TES TERPILIH --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-base font-semibold text-slate-900">{{ $alatTesTerpilih['nama'] }}</h2>
                <span class="inline-block rounded-md {{ $warnaFormat[$alatTesTerpilih['format_dasar']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                    {{ $alatTesTerpilih['format_dasar'] }}
                </span>
            </div>
            <span class="text-xs text-slate-500">{{ count($daftarSoal) }} soal terdaftar</span>
        </div>

        {{-- DAFTAR SOAL BERDASARKAN FORMAT --}}
        @if (empty($daftarSoal))
            <div class="w-full rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500 shadow-sm">
                Belum ada soal untuk alat tes ini.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($daftarSoal as $idx => $soal)
                    @include('admin.bank-soal.partials.kartu-soal', [
                        'nomor'       => $idx + 1,
                        'soal'        => $soal,
                        'format'      => $alatTesTerpilih['format_dasar'],
                        'warnaFormat' => $warnaFormat,
                    ])
                @endforeach
            </div>
        @endif
    @endunless
</div>
@endsection