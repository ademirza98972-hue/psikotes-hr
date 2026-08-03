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
<div>

    {{-- HEADER SECTION --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
        <div>
            <h2 class="text-[36px] leading-[44px] -tracking-[0.02em] font-semibold text-primary">Data Terhapus</h2>
            <p class="text-[16px] leading-[24px] text-on-surface-variant mt-1">Kelola data yang telah dihapus untuk dipulihkan atau dihapus secara permanen.</p>
        </div>
        <div class="bg-surface-container-low px-4 py-2 rounded-lg border border-outline-variant shrink-0">
            <span class="text-[12px] text-on-surface-variant">Menampilkan <span class="font-bold text-action-teal">{{ $items->total() }}</span> {{ strtolower($konfigAktif['label']) }}</span>
        </div>
    </div>

    {{-- WARNING BANNER --}}
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl flex items-start gap-4 mb-6">
        <span class="material-symbols-outlined text-amber-600 mt-0.5">warning</span>
        <div class="space-y-1">
            <h4 class="text-[12px] font-bold text-amber-900 uppercase">Peringatan Penghapusan Otomatis</h4>
            <p class="text-[14px] text-amber-800">Data yang berada di tempat sampah selama lebih dari 30 hari akan dihapus secara <span class="font-bold">permanen oleh sistem</span> secara otomatis. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
    </div>

    {{-- HORIZONTAL TABS --}}
    <div class="border-b border-outline-variant mb-6">
        <nav class="flex overflow-x-auto gap-8">
            @foreach ($daftarJenis as $key => $konfig)
                <a href="{{ route('admin.data-terhapus.index', ['jenis' => $key]) }}"
                   class="pb-3 text-[12px] font-medium whitespace-nowrap transition-colors flex items-center gap-2
                          {{ $jenisAktif === $key
                              ? 'text-action-teal font-bold border-b-2 border-action-teal'
                              : 'text-on-surface-variant hover:text-action-teal border-b-2 border-transparent' }}">
                    <span>{{ $konfig['label'] }}</span>
                    @php $bc = $badgeCounts[$key]; @endphp
                    @if ($bc > 0)
                        <span class="inline-flex items-center justify-center rounded-full badge-{{ $konfig['badge'] }} px-1.5 py-0.5 text-[10px] font-bold">
                            {{ $bc }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>

    {{-- ====== AKUN KARYAWAN ====== --}}
    @if ($jenisAktif === 'karyawan')
        @include('admin.data-terhapus._table_karyawan')
    @endif

    {{-- ====== AKUN KANDIDAT ====== --}}
    @if ($jenisAktif === 'kandidat')
        @include('admin.data-terhapus._table_kandidat')
    @endif

    {{-- ====== AKUN ADMIN/STAFF ====== --}}
    @if ($jenisAktif === 'admin')
        @include('admin.data-terhapus._table_admin')
    @endif

    {{-- ====== DATA KARYAWAN ====== --}}
    @if ($jenisAktif === 'data_karyawan')
        @include('admin.data-terhapus._table_data_karyawan')
    @endif

    {{-- ====== DEPARTEMEN ====== --}}
    @if ($jenisAktif === 'departemen')
        @include('admin.data-terhapus._table_departemen')
    @endif

    {{-- ====== POSISI ====== --}}
    @if ($jenisAktif === 'posisi')
        @include('admin.data-terhapus._table_posisi')
    @endif

    {{-- ====== PERAN ====== --}}
    @if ($jenisAktif === 'peran')
        @include('admin.data-terhapus._table_peran')
    @endif

</div>
@endsection
