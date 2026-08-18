@extends('layouts.admin', ['judulHalaman' => 'Kelola Alat Tes'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'border-[#71787c] text-[#40484b]',
        'Skala Likert'  => 'border-[#71787c] text-[#40484b]',
        'Forced Choice' => 'border-[#71787c] text-[#40484b]',
    ];

    // TODO: tambahkan field `status` (enum: aktif/nonaktif) ke tabel alat_tes saat backend siap
    // Saat ini semua alat tes dianggap Aktif
    $totalAlat = $alatTes->count();
    // totalSoal dikirim dari controller (sum of soal_count)
@endphp

<div class="space-y-6">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-[28px] leading-9 font-semibold text-[#00303c]">Daftar Alat Tes</h2>
            <p class="mt-0.5 text-[14px] text-[#40484b]">Kelola jenis instrumen tes psikometri yang tersedia dalam sistem.</p>
        </div>
        @if(auth()->user()->hasIzin('kategori_tes.kelola'))
        <a href="{{ route('admin.alat-tes.tambah') }}"
           class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95 whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Alat Tes
        </a>
        @endif
    </div>
    </div>{{-- /STICKY HEADER --}}

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">quiz</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Total Instrumen</p>
                <p class="text-[18px] leading-6 font-bold text-[#191c1e]">{{ $totalAlat }}</p>
            </div>
        </div>
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">check_circle</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Aktif</p>
                <p class="text-[18px] leading-6 font-bold text-[#191c1e]">{{ $totalAlat }}</p>
            </div>
        </div>
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-violet-50 flex items-center justify-center text-violet-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">inventory_2</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Total Soal</p>
                <p class="text-[18px] leading-6 font-bold text-[#191c1e]">{{ number_format($totalSoal ?? 0) }}</p>
            </div>
        </div>
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                <span class="material-symbols-outlined text-[22px]">trending_up</span>
            </div>
            <div>
                <p class="text-[11px] text-[#40484b] uppercase tracking-wider font-semibold">Terpopuler</p>
                <p class="text-[14px] leading-5 font-bold text-[#191c1e]">
                    @php $alatPopuler = $alatTes->sortByDesc('soal_count')->first(); @endphp
                    {{ $alatPopuler->nama ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white border border-[#e0e3e5] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-[#f2f4f6] border-b border-[#e0e3e5]">
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] w-24">KODE</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b]">Nama Alat Tes</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] text-center">Soal</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b]">Format</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b]">Status</th>
                        <th class="px-5 py-3.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]/60">
                    @forelse ($alatTes as $alat)
                        @php
                            $kode = $alat['kode'];

                            $warnaKode = [
                                'EPPS' => 'bg-emerald-100 text-emerald-700',
                            ];
                            $badgeKode = $warnaKode[$kode] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <tr class="hover:bg-[#f2f4f6] transition-colors group">
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badgeKode }}">
                                    {{ $kode }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-[#191c1e]">{{ $alat['nama'] }}</p>
                                <p class="text-[11px] text-[#40484b] mt-0.5">{{ $alat['format_dasar'] }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center font-medium text-[#40484b]">
                                {{ number_format($alat->soal_count) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block border border-[#c0c8cb] px-3 py-1 rounded-full text-[12px] font-medium text-[#40484b]">
                                    {{ $alat['format_dasar'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($alat['is_sensitif'])
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Sensitif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if(auth()->user()->hasIzin('kategori_tes.kelola'))
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.alat-tes.edit', $alat['id']) }}"
                                           class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#e0e3e5] hover:text-[#2C5F6F] transition-colors"
                                           title="Edit">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                        <a href="{{ route('admin.alat-tes.norma', $alat['id']) }}"
                                           class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#e0e3e5] hover:text-[#2C5F6F] transition-colors"
                                           title="Kelola Norma">
                                            <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                                        </a>
                                        <a href="{{ route('admin.alat-tes.dimensi', $alat['id']) }}"
                                           class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#e0e3e5] hover:text-[#2C5F6F] transition-colors"
                                           title="Kelola Dimensi">
                                            <span class="material-symbols-outlined text-[18px]">psychology</span>
                                        </a>
                                        <button type="button"
                                                class="p-1.5 rounded-lg text-[#40484b] hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                title="Hapus"
                                                onclick="confirmHapusAlatTes({{ $alat['id'] }}, '{{ addslashes($alat['nama']) }}')">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-[#40484b]">
                                Belum ada data alat tes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination info --}}
        @if ($totalAlat > 0)
        <div class="px-5 py-3.5 flex items-center justify-between border-t border-[#e0e3e5] bg-[#f2f4f6]">
            <p class="text-[12px] text-[#40484b]">
                Menampilkan <span class="font-semibold text-[#191c1e]">{{ $totalAlat }}</span> data
            </p>
        </div>
        @endif
    </div>

    {{-- INFO CARD --}}
    <div class="p-5 bg-[#0d4757]/5 border border-[#0d4757]/10 rounded-xl flex items-start gap-4">
        <div class="p-2 bg-[#0d4757]/10 rounded-lg text-[#2C5F6F] shrink-0">
            <span class="material-symbols-outlined text-[20px]">info</span>
        </div>
        <div>
            <h4 class="text-[14px] font-semibold text-[#00303c]">Informasi Status</h4>
            <p class="text-[13px] text-[#40484b] mt-1">
                Alat tes bertanda <span class="inline-flex items-center gap-1 text-rose-600 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-rose-600 inline-block"></span>Sensitif</span> memuat konten psikologis klinis dan hanya dapat diakses oleh psikolog terverifikasi.
                Status Nonaktif akan menyembunyikan instrumen dari panel peserta.
            </p>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const searchInput = document.querySelector('#alat-tes-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(val) ? '' : 'none';
            });
        });
    }

    function confirmHapusAlatTes(id, nama) {
        if (confirm(`Apakah Anda yakin ingin menghapus alat tes "${nama}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/alat-tes/${id}`;
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
@endsection
