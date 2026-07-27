@extends('layouts.admin', ['judulHalaman' => 'Data Terhapus'])

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .badge-blue { background: #dbeafe; color: #1d4ed8; }
    .badge-purple { background: #f3e8ff; color: #7e22ce; }
    .badge-amber { background: #fef3c7; color: #b45309; }
    .badge-teal { background: #ccfbf1; color: #0f766e; }
    .badge-green { background: #dcfce7; color: #15803d; }
    .badge-cyan { background: #cffafe; color: #0e7490; }
    .badge-rose { background: #ffe4e6; color: #be123c; }
</style>
@endpush

@section('content')
<div x-data="{ jenisAktif: '{{ $jenisAktif }}' }">
    <div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

        {{-- PAGE HEADING --}}
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Data Terhapus</h1>
                <p class="mt-1 text-xs text-slate-500">
                    Pulihkan data yang tidak sengaja dihapus atau hapus permanen dari sistem.
                </p>
            </div>
        </div>

        {{-- TABS --}}
        <div class="mb-4 -mx-6 flex overflow-x-auto border-b border-slate-200 px-6">
            @foreach ($daftarJenis as $key => $konfig)
                <button @click="jenisAktif = '{{ $key }}'"
                        :class="jenisAktif === '{{ $key }}'
                            ? 'border-teal-500 text-slate-900 bg-teal-50'
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                        class="group flex shrink-0 items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition-colors whitespace-nowrap">
                    <span>{{ $konfig['label'] }}</span>
                    @php $bc = $badgeCounts[$key]; @endphp
                    @if ($bc > 0)
                        <span class="inline-flex items-center justify-center rounded-full badge-{{ $konfig['badge'] }} px-2 py-0.5 text-xs font-bold"
                              x-show="jenisAktif === '{{ $key }}'">
                            {{ $bc }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- ====== AKUN KARYAWAN ====== --}}
        <div x-show="jenisAktif === 'karyawan'" x-cloak>
            @include('admin.data-terhapus._table_karyawan')
        </div>

        {{-- ====== AKUN KANDIDAT ====== --}}
        <div x-show="jenisAktif === 'kandidat'" x-cloak>
            @include('admin.data-terhapus._table_kandidat')
        </div>

        {{-- ====== AKUN ADMIN/STAFF ====== --}}
        <div x-show="jenisAktif === 'admin'" x-cloak>
            @include('admin.data-terhapus._table_admin')
        </div>

        {{-- ====== DATA KARYAWAN ====== --}}
        <div x-show="jenisAktif === 'data_karyawan'" x-cloak>
            @include('admin.data-terhapus._table_data_karyawan')
        </div>

        {{-- ====== DEPARTEMEN ====== --}}
        <div x-show="jenisAktif === 'departemen'" x-cloak>
            @include('admin.data-terhapus._table_departemen')
        </div>

        {{-- ====== POSISI ====== --}}
        <div x-show="jenisAktif === 'posisi'" x-cloak>
            @include('admin.data-terhapus._table_posisi')
        </div>

        {{-- ====== PERAN ====== --}}
        <div x-show="jenisAktif === 'peran'" x-cloak>
            @include('admin.data-terhapus._table_peran')
        </div>

    </div>
</div>
@endsection
