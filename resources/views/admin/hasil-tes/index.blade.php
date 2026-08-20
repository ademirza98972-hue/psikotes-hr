@extends('layouts.admin', ['judulHalaman' => 'Hasil Tes'])

@section('content')
@php
    $badgeSesi = [
        'Aktif'   => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
        'Selesai' => ['bg' => 'bg-slate-100',  'text' => 'text-slate-600', 'border' => 'border-slate-200'],
        'Draft'   => ['bg' => 'bg-slate-100',  'text' => 'text-slate-500',  'border' => 'border-slate-200'],
        'Kedaluwarsa' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'border' => 'border-red-200'],
        'Belum Dimulai' => ['bg' => 'bg-slate-50',  'text' => 'text-slate-400',  'border' => 'border-slate-200'],
    ];

    $badgeAlatTes = [
        'EPPS'  => ['bg' => 'bg-emerald-50','text' => 'text-emerald-600','border' => 'border-emerald-100'],
    ];

    $pesertaBySesi = [];
    foreach ($hasilTes as $row) {
        $pesertaBySesi[$row['sesi_id']][] = $row;
    }

    $pesertaByNama = [];
    foreach ($hasilTes as $row) {
        $key = $row['nama_peserta'];
        if (!isset($pesertaByNama[$key])) {
            $pesertaByNama[$key] = [
                'nama_peserta'  => $row['nama_peserta'],
                'departemen'    => $row['departemen'],
                'posisi'        => $row['posisi'],
                'jenis_peserta' => $row['jenis_peserta'],
                'peserta_id'    => $row['peserta_id'],
                'sesi_diikuti'  => [],
            ];
        }
        $pesertaByNama[$key]['sesi_diikuti'][] = [
            'sesi_id'    => $row['sesi_id'],
            'peserta_id' => $row['peserta_id'],
            'nama_sesi'  => collect($penjadwalan)->where('id', $row['sesi_id'])->first()['nama_sesi'] ?? '—',
            'status'     => $row['status_pengerjaan'],
            'tanggal'    => $row['tanggal_pengerjaan'],
        ];
    }
    $pesertaByNama = collect($pesertaByNama)->sortBy('nama_peserta')->values()->all();

    $bulanId = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $tglId = function ($iso) use ($bulanId) {
        if (!$iso) return '-';
        [$y, $m, $d] = explode('-', substr($iso, 0, 10));
        return (int) $d . ' ' . $bulanId[(int) $m] . ' ' . $y;
    };

    if (!function_exists('avatarInitialsHasilTes')) {
        function avatarInitialsHasilTes($nama) {
            $parts = explode(' ', $nama);
            $initials = '';
            foreach (array_slice($parts, 0, 2) as $part) {
                $initials .= strtoupper(trim(substr($part, 0, 1)));
            }
            return $initials ?: '?';
        }
    }
@endphp

<div x-data="{
    tab: 'sesi',
    sesiTerpilih: '{{ $selectedSesiId ?? '' }}',
    hasilTes: @js($hasilTes),
    search: '',
    checkedIds: [],
    allRowIds: @js(array_map(fn($r) => (string)$r['sesi_id'] . '-' . $r['peserta_id'], $hasilTes)),
    get selesaiCount() {
        if (!this.sesiTerpilih) return 0;
        return this.hasilTes.filter(r => r.sesi_id == this.sesiTerpilih).filter(r => r.status_pengerjaan === 'Selesai').length;
    },
    get totalPeserta() {
        if (!this.sesiTerpilih) return 0;
        return this.hasilTes.filter(r => r.sesi_id == this.sesiTerpilih).length;
    },
    get penyelesaianRate() {
        return this.totalPeserta > 0 ? Math.round((this.selesaiCount / this.totalPeserta) * 100) : 0;
    },
    toggleCheck(rowId) {
        const idx = this.checkedIds.indexOf(rowId);
        if (idx >= 0) this.checkedIds.splice(idx, 1);
        else this.checkedIds.push(rowId);
    },
    toggleAll(checked) {
        this.checkedIds = checked ? [...this.allRowIds] : [];
    },
    printPdf() {
        if (!this.checkedIds.length) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih minimal satu peserta untuk cetak PDF.', confirmButtonColor: '#2C5F6F' });
            return;
        }
        Swal.fire({ icon: 'info', title: 'Info', text: 'Fitur cetak PDF terpilih akan aktif setelah backend selesai dibangun.', confirmButtonColor: '#2C5F6F' });
    }
}" x-init=""
class="w-full">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-4 px-4 pt-6 pb-0 border-b border-[#e0e3e5]">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h3 class="text-3xl font-bold text-[var(--color-primary)]">Hasil Tes Psikotes</h3>
                <p class="text-[16px] leading-6 text-[var(--color-on-surface-variant)] mt-1">Pantau kemajuan dan kelola laporan hasil penilaian psikologi peserta.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="flex items-center px-4 py-2 bg-white border border-[var(--color-outline-variant)] rounded-xl text-[var(--color-secondary)] text-sm font-medium hover:bg-[var(--color-surface-container-low)] transition-colors">
                    <span class="material-symbols-outlined mr-2 text-[18px]">filter_list</span>
                    Filter Lanjutan
                </button>
                <button type="button"
                        x-on:click="printPdf()"
                        class="flex items-center px-4 py-2 bg-[var(--color-psikotes)] text-white rounded-xl text-sm font-semibold hover:bg-[#1e414c] transition-all shadow-sm">
                    <span class="material-symbols-outlined mr-2 text-[18px]">download</span>
                    Cetak PDF Terpilih
                </button>
            </div>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="flex border-b border-[var(--color-outline-variant)]">
        <button type="button"
                @click="tab = 'sesi'"
                :class="tab === 'sesi' ? 'border-[var(--color-psikotes)] text-[var(--color-psikotes)] font-semibold' : 'border-transparent text-[var(--color-on-surface-variant)] hover:text-[var(--color-primary)]'"
                class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition-all">
            Per Sesi Penjadwalan
        </button>
        <button type="button"
                @click="tab = 'peserta'"
                :class="tab === 'peserta' ? 'border-[var(--color-psikotes)] text-[var(--color-psikotes)] font-semibold' : 'border-transparent text-[var(--color-on-surface-variant)] hover:text-[var(--color-primary)]'"
                class="px-6 py-3 text-sm font-medium border-b-2 -mb-px transition-all">
            Per Peserta
        </button>
    </div>

    </div>{{-- /STICKY HEADER --}}

    {{-- ============ TAB A: PER SESI ============ --}}
    <div x-show="tab === 'sesi'" x-cloak>

        {{-- Session selector + info card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)] mb-2">PILIH SESI PENJADWALAN</label>
                <div class="relative">
                    <select x-model="sesiTerpilih"
                            class="w-full bg-white border border-[var(--color-outline-variant)] rounded-xl py-3 pl-4 pr-10 appearance-none text-sm font-medium focus:ring-2 focus:ring-[var(--color-psikotes)] focus:border-transparent outline-none cursor-pointer">
                        <option value="">-- Pilih Sesi --</option>
                        @foreach ($penjadwalan as $sesi)
                            <option value="{{ $sesi['id'] }}">{{ $sesi['nama_sesi'] }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[var(--color-on-surface-variant)]">
                        <span class="material-symbols-outlined">expand_more</span>
                    </span>
                </div>
            </div>

            <div class="lg:col-span-2">
                @foreach ($penjadwalan as $sesi)
                <div x-show="sesiTerpilih == '{{ $sesi['id'] }}'" x-transition key="{{ $sesi['id'] }}">
                    <div class="bg-white border border-[var(--color-outline-variant)] rounded-xl p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-[var(--color-primary-container)] rounded-lg flex items-center justify-center text-[var(--color-on-primary-container)]">
                                <span class="material-symbols-outlined">event_available</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-lg font-semibold text-[var(--color-primary)]">{{ $sesi['nama_sesi'] }}</h4>
                                    @php $bs = $badgeSesi[$sesi['status_display']] ?? $badgeSesi['Draft']; @endphp
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded border {{ $bs['bg'] }} {{ $bs['text'] }} {{ $bs['border'] }}">
                                        {{ $sesi['status_display'] }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-[var(--color-on-surface-variant)]">
                                    <span class="flex items-center">
                                        <span class="material-symbols-outlined text-[16px] mr-1">business</span>
                                        {{ $sesi['departemen_terkait'] }}
                                    </span>
                                    <span class="flex items-center">
                                        <span class="material-symbols-outlined text-[16px] mr-1">calendar_today</span>
                                        {{ $tglId($sesi['tanggal_mulai']) }} - {{ $tglId($sesi['tanggal_selesai']) }}
                                    </span>
                                    <span class="flex items-center">
                                        <span class="material-symbols-outlined text-[16px] mr-1">group</span>
                                        {{ $sesi['jumlah_peserta'] }} Peserta
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="p-2 text-[var(--color-psikotes)] hover:bg-[var(--color-surface-container-low)] rounded-lg transition-colors"
                                onclick="window.location='{{ route('admin.penjadwalan-tes.edit', $sesi['id']) }}'">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" class="p-2 text-[var(--color-on-surface-variant)] hover:bg-[var(--color-surface-container-low)] rounded-lg transition-colors"
                                    x-on:click="open = !open"
                                    x-on:click.away="open = false">
                                    <span class="material-symbols-outlined">more_vert</span>
                                </button>
                                <div x-show="open" x-transition
                                    class="absolute right-0 mt-1 w-40 bg-white border border-[var(--color-outline-variant)] rounded-lg shadow-sm z-10 overflow-hidden">
                                    <a href="{{ route('admin.penjadwalan-tes.edit', $sesi['id']) }}"
                                        class="block px-4 py-2 text-sm text-[var(--color-primary)] hover:bg-[var(--color-surface-container-low)] transition-colors">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @foreach ($penjadwalan as $sesi)
        @php $listPeserta = $pesertaBySesi[$sesi['id']] ?? []; @endphp
        <div x-show="sesiTerpilih == '{{ $sesi['id'] }}'" x-transition class="bg-white border border-[var(--color-outline-variant)] rounded-xl overflow-hidden shadow-sm">
            <div style="overflow: auto; max-height: calc(100vh - 300px);">
                <table class="w-full text-left border-collapse">
                    <thead style="position: sticky; top: 0; z-index: 5;">
                        <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                            <th class="py-4 px-6 w-12">
                                <input type="checkbox"
                                       class="w-4 h-4 rounded border-[var(--color-outline-variant)] text-[var(--color-psikotes)] focus:ring-[var(--color-psikotes)] cursor-pointer"
                                       @change="toggleAll($event.target.checked)">
                            </th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase" style="color: #7db8c2;">PESERTA</th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase" style="color: #7db8c2;">DEPARTEMEN / POSISI</th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase" style="color: #7db8c2;">ALAT TES</th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase" style="color: #7db8c2;">STATUS</th>
                            <th class="py-4 px-6 text-right text-xs font-semibold tracking-widest uppercase" style="color: #7db8c2;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-outline-variant)]/40">
                        @forelse ($listPeserta as $row)
                        @php $rowId = (string)$row['sesi_id'] . '-' . $row['peserta_id']; @endphp
                        <tr class="hover:bg-[var(--color-surface-container-low)] transition-colors group">
                            <td class="py-4 px-6">
                                <input type="checkbox"
                                       class="w-4 h-4 rounded border-[var(--color-outline-variant)] text-[var(--color-psikotes)] focus:ring-[var(--color-psikotes)] cursor-pointer"
                                       :value="{{ $rowId }}"
                                       :checked="checkedIds.includes('{{ $rowId }}')"
                                       x-on:change="toggleCheck('{{ $rowId }}')">
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[var(--color-secondary-fixed)] flex items-center justify-center text-[var(--color-on-secondary-container)] font-bold text-[13px]">
                                        {{ avatarInitialsHasilTes($row['nama_peserta']) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--color-primary)]">{{ $row['nama_peserta'] }}</p>
                                        <p class="text-[11px] text-[var(--color-on-surface-variant)]">ID: {{ $row['peserta_id'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <p class="text-sm text-[var(--color-on-surface)] font-medium">{{ $row['departemen'] }}</p>
                                <p class="text-[11px] text-[var(--color-on-surface-variant)] mt-0.5">{{ $row['posisi'] }}</p>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-[11px] text-[var(--color-on-surface-variant)]">{{ $row['alat_tes_kode'] }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if ($row['status_pengerjaan'] === 'Selesai')
                                    <span class="flex items-center text-green-600">
                                        <span class="material-symbols-outlined text-[16px] mr-1" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        <span class="text-sm font-medium">{{ $row['status_pengerjaan'] }}</span>
                                    </span>
                                @elseif ($row['status_pengerjaan'] === 'Sedang Berjalan')
                                    <span class="flex items-center text-amber-600">
                                        <span class="material-symbols-outlined text-[16px] mr-1" style="font-variation-settings: 'FILL' 1;">pending</span>
                                        <span class="text-sm font-medium">{{ $row['status_pengerjaan'] }}</span>
                                    </span>
                                @else
                                    <span class="flex items-center text-[var(--color-on-surface-variant)]">
                                        <span class="material-symbols-outlined text-[16px] mr-1">history</span>
                                        <span class="text-sm font-medium">{{ $row['status_pengerjaan'] }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if ($row['status_pengerjaan'] === 'Selesai')
                                    <a href="{{ route('admin.hasil-tes.detail', [$row['sesi_id'], $row['peserta_id']]) }}"
                                       class="bg-[var(--color-psikotes)] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:shadow-md transition-all inline-block">
                                        Lihat Hasil
                                    </a>
                                @else
                                    <button class="bg-[var(--color-surface-container-high)] text-[var(--color-on-surface)]/40 px-4 py-1.5 rounded-lg text-sm font-medium cursor-not-allowed" disabled>
                                        Lihat Hasil
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">assignment</span>
                                <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum Ada Hasil Tes</p>
                                <p class="text-[12px] text-[#71787c] mb-4">Belum ada peserta dengan hasil tes pada sesi ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="px-6 py-4 bg-[var(--color-surface-container-low)] border-t border-[var(--color-outline-variant)]/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm font-medium text-[var(--color-on-surface-variant)]">
                    Menampilkan {{ count($listPeserta) }} dari {{ $sesi['jumlah_peserta'] }} Peserta
                </p>
            </div>
        </div>
        @endforeach

        <div x-show="!sesiTerpilih" x-cloak class="mt-6 rounded-xl border border-dashed border-[var(--color-outline-variant)] bg-white px-6 py-10 text-center text-sm text-[var(--color-on-surface-variant)]">
            Silakan pilih sesi penjadwalan terlebih dahulu untuk melihat daftar peserta.
        </div>

        {{-- Stats Overview --}}
        <div x-show="sesiTerpilih" x-transition
             class="mt-5 flex items-stretch gap-0 rounded-2xl border border-[#e0e3e5] bg-white shadow-sm overflow-hidden">
            <div class="flex flex-1 items-center gap-3 px-5 py-4 border-r border-[#e0e3e5]">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#2C5F6F]/10">
                    <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">percent</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Penyelesaian</p>
                    <p class="text-2xl font-bold text-[#2C5F6F]" x-text="penyelesaianRate + '%'"></p>
                </div>
            </div>
            <div class="flex flex-1 items-center gap-3 px-5 py-4 border-r border-[#e0e3e5]">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Selesai</p>
                    <p class="text-2xl font-bold text-emerald-600">
                        <span x-text="selesaiCount"></span>
                        <span class="text-sm font-normal text-slate-400"> / <span x-text="totalPeserta"></span></span>
                    </p>
                </div>
            </div>
            <div class="flex flex-[2] items-center gap-4 px-5 py-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                    <span class="material-symbols-outlined text-[18px] text-slate-500">bar_chart</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Progres Kolektif</p>
                    <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-[#2C5F6F] transition-all" :style="'width: ' + penyelesaianRate + '%'"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500"><span x-text="selesaiCount"></span> dari <span x-text="totalPeserta"></span> peserta selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TAB B: PER PESERTA ============ --}}
    <div x-show="tab === 'peserta'" x-cloak>

        {{-- Controls --}}
        <div class="flex flex-col md:flex-row gap-4 items-center mb-6">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-outline)]">search</span>
                <input type="text"
                       x-model="search"
                       placeholder="Cari Nama Peserta..."
                       class="w-full pl-12 pr-4 py-3 bg-white border border-[var(--color-outline-variant)] rounded-xl focus:ring-2 focus:ring-[var(--color-psikotes)] focus:border-transparent outline-none text-sm font-medium transition-all">
            </div>
            <button type="button" class="flex items-center gap-2 px-4 py-3 bg-white border border-[var(--color-outline-variant)] rounded-xl text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-low)] transition-colors text-sm font-medium">
                <span class="material-symbols-outlined">filter_list</span>
                Filter Dept
            </button>
            <button type="button" class="flex items-center gap-2 px-4 py-3 bg-white border border-[var(--color-outline-variant)] rounded-xl text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-low)] transition-colors text-sm font-medium">
                <span class="material-symbols-outlined">download</span>
                Export Semua
            </button>
        </div>

        {{-- Participant Grid --}}
        @php
            $paletAvatar = ['#2C5F6F','#0891b2','#7c3aed','#db2777','#d97706','#16a34a','#dc2626','#9333ea','#0284c7','#c2410c'];
        @endphp
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            @foreach ($pesertaByNama as $peserta)
            @php
                $colorIdx = abs(crc32($peserta['nama_peserta'])) % count($paletAvatar);
                $avatarColor = $paletAvatar[$colorIdx];
                $isKaryawan = $peserta['jenis_peserta'] === 'Karyawan';
                $selesaiCount = collect($peserta['sesi_diikuti'])->where('status', 'Selesai')->count();
                $totalSesi = count($peserta['sesi_diikuti']);
            @endphp
            <div x-show="!search || '{{ strtolower($peserta['nama_peserta']) }}'.includes(search.toLowerCase())"
                 class="flex flex-col rounded-2xl border border-[#e2e8f0] bg-white shadow-sm transition-all hover:shadow-lg hover:-translate-y-0.5 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center gap-4 px-5 py-4" style="background: linear-gradient(135deg, {{ $avatarColor }}18 0%, {{ $avatarColor }}06 100%); border-bottom: 1px solid {{ $avatarColor }}20;">
                    <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl text-white font-bold text-[16px] shadow-sm" style="background-color: {{ $avatarColor }}">
                        {{ avatarInitialsHasilTes($peserta['nama_peserta']) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-[15px] font-bold text-slate-800 truncate leading-tight">{{ $peserta['nama_peserta'] }}</h4>
                            @if ($isKaryawan)
                                <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-[#2C5F6F]/10 text-[#2C5F6F]">KARYAWAN</span>
                            @else
                                <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700">KANDIDAT</span>
                            @endif
                        </div>
                        <p class="text-[12px] text-slate-500 mt-0.5 truncate">
                            {{ $peserta['departemen'] ?: '—' }}@if($peserta['posisi']) · {{ $peserta['posisi'] }}@endif
                        </p>
                    </div>
                </div>

                {{-- Card Body: test history --}}
                <div class="flex-1 px-5 py-3">
                    <div class="flex items-center justify-between mb-2.5">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Riwayat Tes</p>
                        <span class="text-[10px] font-semibold text-slate-400">{{ $selesaiCount }}/{{ $totalSesi }} selesai</span>
                    </div>
                    <div class="space-y-1.5">
                        @foreach ($peserta['sesi_diikuti'] as $sesiRow)
                        @php
                            $rowSelesai = $sesiRow['status'] === 'Selesai';
                            $rowBerjalan = $sesiRow['status'] === 'Sedang Berjalan';
                        @endphp
                        <div class="flex items-center justify-between gap-3 rounded-xl border px-3 py-2.5 transition-colors hover:bg-slate-50
                            {{ $rowSelesai ? 'border-emerald-100 bg-emerald-50/40' : ($rowBerjalan ? 'border-amber-100 bg-amber-50/40' : 'border-slate-100') }}">
                            <div class="min-w-0 flex-1">
                                <p class="text-[13px] font-semibold text-slate-700 truncate">{{ $sesiRow['nama_sesi'] }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    @if ($rowSelesai)
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="text-[11px] text-emerald-600 font-medium">Selesai</span>
                                        @if($sesiRow['tanggal'])
                                            <span class="text-[11px] text-slate-400">· {{ $tglId($sesiRow['tanggal']) }}</span>
                                        @endif
                                    @elseif ($rowBerjalan)
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <span class="text-[11px] text-amber-600 font-medium">Sedang Berjalan</span>
                                    @else
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300 shrink-0"></span>
                                        <span class="text-[11px] text-slate-400">Belum Mengerjakan</span>
                                    @endif
                                </div>
                            </div>
                            @if ($rowSelesai)
                                <a href="{{ route('admin.hasil-tes.detail', [$sesiRow['sesi_id'], $sesiRow['peserta_id']]) }}"
                                   class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                    <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                    Lihat Detail
                                </a>
                            @else
                                <a href="{{ route('admin.penjadwalan-tes.detail', $sesiRow['sesi_id']) }}"
                                   class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-[#2C5F6F] px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-[#1E414C]">
                                    <span class="material-symbols-outlined text-[13px]">visibility</span>
                                    Monitor
                                </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endforeach

            @if (empty($pesertaByNama))
            <div class="col-span-2 rounded-2xl border border-dashed border-[#c0c8cb] bg-white py-16 text-center">
                <span class="material-symbols-outlined text-[44px] text-slate-300">person_off</span>
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada data peserta.</p>
            </div>
            @endif
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm font-medium text-[var(--color-on-surface-variant)]">Menampilkan {{ count($pesertaByNama) }} dari {{ count($pesertaByNama) }} peserta</p>
            <div class="flex gap-2">
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-outline-variant)] rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-low)] transition-colors" disabled>
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button class="w-10 h-10 flex items-center justify-center bg-[var(--color-psikotes)] text-white rounded-lg text-sm font-medium">1</button>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-outline-variant)] rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-low)] transition-colors text-sm font-medium">2</button>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-outline-variant)] rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-low)] transition-colors text-sm font-medium">3</button>
                <button class="w-10 h-10 flex items-center justify-center border border-[var(--color-outline-variant)] rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-low)] transition-colors">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>

@endsection
