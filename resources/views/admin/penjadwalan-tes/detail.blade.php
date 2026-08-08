@extends('layouts.admin', ['judulHalaman' => 'Detail Sesi Tes'])

@section('content')
@php
    $warnaStatus = [
        'Draft'   => 'bg-slate-100 text-slate-600 border border-slate-200',
        'Aktif'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'Selesai' => 'bg-slate-100 text-slate-600 border border-slate-200',
    ];
    $warnaStatusPengerjaan = [
        'Belum Mengerjakan' => 'bg-slate-100 text-slate-600 border border-slate-200',
        'Sedang Berjalan'   => 'bg-amber-50 text-amber-700 border border-amber-200',
        'Selesai'           => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    ];
    $warnaAlatTes = [
        'EPPS'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    ];
    $bulanId = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $tglId = function ($iso) use ($bulanId) {
        if (! $iso) return '-';
        [$y, $m, $d] = explode('-', substr($iso, 0, 10));
        return (int) $d . ' ' . $bulanId[(int) $m] . ' ' . $y;
    };
    $persen = $sesi->jumlah_peserta > 0
        ? round(($sesi->jumlah_selesai / $sesi->jumlah_peserta) * 100)
        : 0;
@endphp

<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-[#191c1e]">{{ $sesi->nama_sesi }}</h2>
            <p class="mt-0.5 text-sm text-[#41484b]">Detail sesi penjadwalan dan manajemen peserta.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.index') }}"
           class="inline-flex items-center gap-1 text-[#2C5F6F] font-semibold text-sm hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if (session('alert'))
        <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('alert') }}
        </div>
    @endif
    @if (session('sukses'))
        <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('sukses') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KOLOM KIRI: INFO SESI --}}
        <div class="lg:col-span-1 space-y-5">
            <section class="rounded-xl border border-[#c1c7cb] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-base font-semibold text-[#191c1e]">Informasi Sesi</h3>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold border {{ $warnaStatus[$sesi->status] ?? 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                        {{ $sesi->status }}
                    </span>
                </div>

                <div class="space-y-3 mb-5">
                    <div class="flex items-center gap-2 text-[#41484b] text-sm">
                        <span class="material-symbols-outlined text-[18px]">business</span>
                        <span>Departemen: <strong class="text-[#191c1e]">{{ $sesi->departemenTerkait?->nama_departemen ?? '—' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-[#41484b] text-sm">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>Periode: <strong class="text-[#191c1e]">{{ $tglId($sesi->tanggal_mulai) }} – {{ $tglId($sesi->tanggal_selesai) }}</strong></span>
                    </div>
                </div>

                <div class="mb-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#41484b] mb-2">ALAT TES:</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($sesi->alatTes as $alat)
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $warnaAlatTes[$alat->kode] ?? 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                                {{ $alat->kode ?? $alat->nama }}
                            </span>
                        @empty
                            <span class="text-xs text-[#919eab]">— belum dipilih</span>
                        @endforelse
                    </div>
                </div>

                <div class="mb-5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-[#41484b]">Progres Peserta: <span class="font-bold text-[#191c1e]">{{ $sesi->jumlah_selesai }}/{{ $sesi->jumlah_peserta }}</span> selesai</span>
                        <span class="text-sm font-bold text-[#2C5F6F]">{{ $persen }}%</span>
                    </div>
                    <div class="w-full h-2 bg-[#e6e8ea] rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all" style="width: {{ $persen }}%; background-color: #2C5F6F;"></div>
                    </div>
                </div>

                <a href="{{ route('admin.penjadwalan-tes.edit', $sesi->id) }}"
                   class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Sesi
                </a>
            </section>
        </div>

        {{-- KOLOM KANAN: MANAJEMEN PESERTA --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Form Tambah Peserta --}}
            <section class="rounded-xl border border-[#c1c7cb] bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-[#2C5F6F]">person_add</span>
                    <h3 class="text-base font-semibold text-[#191c1e]">Tambah Peserta</h3>
                </div>

                @if ($errors->any())
                    <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700 mb-4">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('admin.penjadwalan-tes.tambahPeserta', $sesi->id) }}"
                      class="space-y-4">
                    @csrf

                    <div class="flex gap-3 mb-4">
                        <div class="flex rounded-xl border border-[#e0e3e5] overflow-hidden text-sm">
                            <button type="button" id="btn-karyawan"
                                    onclick="filterTipe('karyawan')"
                                    class="px-4 py-2 font-semibold bg-[#2C5F6F] text-white transition-colors">
                                Karyawan
                            </button>
                            <button type="button" id="btn-kandidat"
                                    onclick="filterTipe('kandidat')"
                                    class="px-4 py-2 font-semibold bg-white text-[#40484b] transition-colors">
                                Kandidat
                            </button>
                        </div>
                        <div class="relative flex-1">
                            <span class="material-symbols-outlined absolute left-3 top-1/2
                                         -translate-y-1/2 text-[#40484b] text-[18px]">search</span>
                            <input type="text" id="cari-peserta"
                                   oninput="filterPeserta()"
                                   onkeydown="return event.key !== 'Enter'"
                                   placeholder="Cari nama..."
                                   class="w-full pl-9 pr-4 py-2 bg-[#f2f4f6] border border-[#e0e3e5]
                                          rounded-xl text-sm text-[#191c1e] outline-none
                                          focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F]">
                        </div>
                    </div>

                    <div class="border border-[#e0e3e5] rounded-xl overflow-hidden mb-4">
                        <div class="bg-[#f2f4f6] px-4 py-2.5 flex items-center gap-3
                                    border-b border-[#e0e3e5]">
                            <input type="checkbox" id="select-all"
                                   onchange="toggleSelectAll(this)"
                                   class="h-4 w-4 rounded border-[#c0c8cb] text-[#2C5F6F]
                                          focus:ring-[#2C5F6F]">
                            <label for="select-all"
                                   class="text-[12px] font-semibold text-[#40484b] uppercase
                                          tracking-wider cursor-pointer">
                                Pilih Semua
                            </label>
                            <span id="counter-terpilih"
                                  class="ml-auto text-[12px] text-[#2C5F6F] font-semibold">
                                0 dipilih
                            </span>
                        </div>
                        <div id="daftar-user" class="max-h-64 overflow-y-auto divide-y divide-[#e0e3e5]/60">
                            @forelse ($daftarUser as $user)
                                <label id="row-{{ $user->id }}"
                                       data-tipe="{{ $user->tipe_akun }}"
                                       data-nama="{{ strtolower($user->name) }}"
                                       class="flex items-center gap-3 px-4 py-3 hover:bg-[#f2f4f6]
                                              transition-colors cursor-pointer user-row">
                                    <input type="checkbox" name="user_ids[]"
                                           value="{{ $user->id }}"
                                           onchange="updateCounter()"
                                           class="h-4 w-4 rounded border-[#c0c8cb] text-[#2C5F6F]
                                                  focus:ring-[#2C5F6F] user-checkbox">
                                    <div>
                                        <p class="text-sm font-semibold text-[#191c1e]">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-[11px] text-[#40484b]">
                                            {{ ucfirst($user->tipe_akun) }}
                                        </p>
                                    </div>
                                </label>
                            @empty
                                <p class="px-4 py-6 text-center text-sm text-[#40484b]">
                                    Tidak ada user tersedia.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-[12px] font-semibold text-[#40484b] uppercase
                                  tracking-wider mb-2">
                            Alat Tes yang Dikerjakan <span class="text-rose-500">*</span>
                        </p>
                        @if ($sesi->alatTes->isEmpty())
                            <p class="text-xs text-[#919eab] italic">Sesi ini belum memiliki alat tes. Tambahkan alat tes melalui Edit Sesi.</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($sesi->alatTes as $alat)
                                    <label class="flex items-center gap-3 p-3 rounded-xl
                                                  border border-[#e0e3e5] bg-[#f2f4f6]
                                                  cursor-pointer hover:border-[#2C5F6F]/40
                                                  transition-colors">
                                        <input type="checkbox" name="alat_tes_ids[]"
                                               value="{{ $alat->id }}"
                                               class="h-4 w-4 rounded border-[#c0c8cb]
                                                      text-[#2C5F6F] focus:ring-[#2C5F6F]">
                                        <div>
                                            <p class="text-sm font-semibold text-[#191c1e]">
                                                {{ $alat->nama }}
                                            </p>
                                            <p class="text-[11px] text-[#40484b]">
                                                {{ $alat->format_dasar }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2
                                   bg-[#2C5F6F] hover:bg-[#1E414C] text-white
                                   rounded-xl px-4 py-2.5 text-sm font-semibold
                                   transition-all active:scale-95"
                            {{ $sesi->alatTes->isEmpty() ? 'disabled' : '' }}>
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        Tambah Peserta Terpilih
                    </button>
                </form>
            </section>

            {{-- Tabel Peserta --}}
            <section class="rounded-xl border border-[#c1c7cb] bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-[#2C5F6F]">groups</span>
                    <h3 class="text-base font-semibold text-[#191c1e]">Daftar Peserta</h3>
                </div>

                @if ($sesi->pesertaSesiTesRecords->isEmpty())
                    <div class="rounded-xl border border-dashed border-[#c1c7cb] px-6 py-10 text-center text-sm text-[#41484b]">
                        Belum ada peserta pada sesi ini.
                    </div>
                @else
                    <div class="overflow-x-auto -mx-2">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-[#41484b] border-b border-[#e0e3e5]">
                                    <th class="px-3 py-3">Nama</th>
                                    <th class="px-3 py-3">Tipe</th>
                                    <th class="px-3 py-3">Alat Tes</th>
                                    <th class="px-3 py-3">Status</th>
                                    <th class="px-3 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e0e3e5]">
                                @foreach ($sesi->pesertaSesiTesRecords as $peserta)
                                    <tr>
                                        <td class="px-3 py-3 font-semibold text-[#191c1e]">{{ $peserta->user?->name ?? '—' }}</td>
                                        <td class="px-3 py-3 text-[#41484b]">{{ ucfirst($peserta->user?->tipe_akun ?? '-') }}</td>
                                        <td class="px-3 py-3">
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse ($peserta->alatTes as $alat)
                                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $warnaAlatTes[$alat->kode] ?? 'bg-slate-50 text-slate-600 border border-slate-200' }}">
                                                        {{ $alat->kode ?? $alat->nama }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-[#919eab]">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $warnaStatusPengerjaan[$peserta->status_pengerjaan] ?? 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                                {{ $peserta->status_pengerjaan }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <form method="POST"
                                                  action="{{ route('admin.penjadwalan-tes.hapusPeserta', [$sesi->id, $peserta->user_id]) }}"
                                                  onsubmit="return confirm('Hapus peserta {{ addslashes($peserta->user?->name ?? '') }} dari sesi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-700 text-sm font-semibold transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>

</div>

<script>
let tipeAktif = 'karyawan';

function filterTipe(tipe) {
    tipeAktif = tipe;
    const btnK = document.getElementById('btn-karyawan');
    const btnC = document.getElementById('btn-kandidat');
    btnK.classList.toggle('bg-[#2C5F6F]', tipe === 'karyawan');
    btnK.classList.toggle('text-white', tipe === 'karyawan');
    btnK.classList.toggle('bg-white', tipe !== 'karyawan');
    btnK.classList.toggle('text-[#40484b]', tipe !== 'karyawan');
    btnC.classList.toggle('bg-[#2C5F6F]', tipe === 'kandidat');
    btnC.classList.toggle('text-white', tipe === 'kandidat');
    btnC.classList.toggle('bg-white', tipe !== 'kandidat');
    btnC.classList.toggle('text-[#40484b]', tipe !== 'kandidat');
    filterPeserta();
}

function filterPeserta() {
    const q = document.getElementById('cari-peserta').value.toLowerCase();
    document.querySelectorAll('.user-row').forEach(row => {
        const tipeMatch = row.dataset.tipe === tipeAktif;
        const namaMatch = row.dataset.nama.includes(q);
        row.style.display = (tipeMatch && namaMatch) ? '' : 'none';
    });
    updateCounter();
}

function toggleSelectAll(cb) {
    document.querySelectorAll('.user-checkbox').forEach(chk => {
        const row = chk.closest('.user-row');
        if (row.style.display !== 'none') {
            chk.checked = cb.checked;
        }
    });
    updateCounter();
}

function updateCounter() {
    const n = document.querySelectorAll('.user-checkbox:checked').length;
    document.getElementById('counter-terpilih').textContent = n + ' dipilih';
    const visibleCheckboxes = document.querySelectorAll(
        '.user-row:not([style*="display: none"]) .user-checkbox'
    );
    document.getElementById('select-all').checked =
        n > 0 && n === visibleCheckboxes.length;
}

document.addEventListener('DOMContentLoaded', () => filterTipe('karyawan'));
</script>

@endsection
