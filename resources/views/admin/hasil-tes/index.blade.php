@extends('layouts.admin', ['judulHalaman' => 'Hasil Tes'])

@section('content')
@php
    $badgeSesi = [
        'Aktif'   => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
        'Selesai' => ['bg' => 'bg-slate-100',  'text' => 'text-slate-600', 'border' => 'border-slate-200'],
        'Draft'   => ['bg' => 'bg-slate-100',  'text' => 'text-slate-500',  'border' => 'border-slate-200'],
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
            'sesi_id'  => $row['sesi_id'],
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
    sesiTerpilih: '',
    search: '',
    checkedIds: [],
    allRowIds: @js(array_map(fn($r) => (string)$r['sesi_id'] . '-' . $r['peserta_id'], $hasilTes)),
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
            alert('Pilih minimal satu peserta untuk cetak PDF.');
            return;
        }
        alert('Fitur cetak PDF terpilih akan aktif setelah backend selesai dibangun.');
    }
}" x-init=""
class="w-full">

    {{-- PAGE HEADER --}}
    <div class="mb-6">
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
    <div class="flex border-b border-[var(--color-outline-variant)] mb-6">
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
                                    @php $bs = $badgeSesi[$sesi['status']] ?? $badgeSesi['Draft']; @endphp
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded border {{ $bs['bg'] }} {{ $bs['text'] }} {{ $bs['border'] }}">
                                        {{ $sesi['status'] }}
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
                            <button type="button" class="p-2 text-[var(--color-psikotes)] hover:bg-[var(--color-surface-container-low)] rounded-lg transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                            <button type="button" class="p-2 text-[var(--color-on-surface-variant)] hover:bg-[var(--color-surface-container-low)] rounded-lg transition-colors">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @foreach ($penjadwalan as $sesi)
        @php $listPeserta = $pesertaBySesi[$sesi['id']] ?? []; @endphp
        <div x-show="sesiTerpilih == '{{ $sesi['id'] }}'" x-transition class="bg-white border border-[var(--color-outline-variant)] rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[var(--color-surface-container)] border-b border-[var(--color-outline-variant)]/50">
                            <th class="py-4 px-6 w-12">
                                <input type="checkbox"
                                       class="w-4 h-4 rounded border-[var(--color-outline-variant)] text-[var(--color-psikotes)] focus:ring-[var(--color-psikotes)] cursor-pointer"
                                       @change="toggleAll($event.target.checked)">
                            </th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)]">PESERTA</th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)]">DEPARTEMEN / POSISI</th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)]">ALAT TES</th>
                            <th class="py-4 px-4 text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)]">STATUS</th>
                            <th class="py-4 px-6 text-right text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)]">AKSI</th>
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
                                <div class="flex flex-wrap gap-1">
                                    @if (!empty($row['hasil_alat_tes']))
                                        @foreach ($row['hasil_alat_tes'] as $alat)
                                            @php $ba = $badgeAlatTes[$alat['nama_alat_tes']] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100']; @endphp
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded border {{ $ba['bg'] }} {{ $ba['text'] }} {{ $ba['border'] }}">
                                                {{ $alat['nama_alat_tes'] }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-[11px] text-[var(--color-placeholder)]">—</span>
                                    @endif
                                </div>
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
                            <td colspan="6" class="py-8 text-center text-sm text-[var(--color-on-surface-variant)]">
                                Belum ada peserta dengan hasil tes pada sesi ini.
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
                <div class="flex items-center gap-2">
                    <button class="p-2 border border-[var(--color-outline-variant)] rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-highest)] transition-colors" disabled>
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button class="w-8 h-8 rounded-lg bg-[var(--color-psikotes)] text-white text-sm font-medium">1</button>
                    <button class="w-8 h-8 rounded-lg hover:bg-[var(--color-surface-variant)] text-[var(--color-secondary)] text-sm font-medium transition-colors">2</button>
                    <button class="w-8 h-8 rounded-lg hover:bg-[var(--color-surface-variant)] text-[var(--color-secondary)] text-sm font-medium transition-colors">3</button>
                    <span class="text-[var(--color-on-surface-variant)]">...</span>
                    <button class="w-8 h-8 rounded-lg hover:bg-[var(--color-surface-variant)] text-[var(--color-secondary)] text-sm font-medium transition-colors">12</button>
                    <button class="p-2 border border-[var(--color-outline-variant)] rounded-lg text-[var(--color-secondary)] hover:bg-[var(--color-surface-container-highest)] transition-colors">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <div x-show="!sesiTerpilih" x-cloak class="mt-6 rounded-xl border border-dashed border-[var(--color-outline-variant)] bg-white px-6 py-10 text-center text-sm text-[var(--color-on-surface-variant)]">
            Silakan pilih sesi penjadwalan terlebih dahulu untuk melihat daftar peserta.
        </div>

        {{-- Stats Overview --}}
        <div x-show="sesiTerpilih" x-transition class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1 bg-blue-50 border border-blue-100 rounded-xl p-5">
                <h5 class="text-blue-800 text-xs font-semibold tracking-widest uppercase mb-3">TINGKAT PENYELESAIAN</h5>
                <div class="flex items-end justify-between">
                    <span class="text-blue-900 text-3xl font-bold">82%</span>
                    <div class="text-blue-700 text-xs font-medium mb-2">+5% vs Batch 2</div>
                </div>
            </div>
            <div class="md:col-span-1 bg-emerald-50 border border-emerald-100 rounded-xl p-5">
                <h5 class="text-emerald-800 text-xs font-semibold tracking-widest uppercase mb-3">STATUS SELESAI</h5>
                <div class="flex items-end justify-between">
                    <span class="text-emerald-900 text-3xl font-bold">39</span>
                    <div class="text-emerald-700 text-xs font-medium mb-2">/ 48 Peserta</div>
                </div>
            </div>
            <div class="md:col-span-2 bg-white border border-[var(--color-outline-variant)] rounded-xl p-5 flex items-center justify-between">
                <div>
                    <h5 class="text-[var(--color-on-surface-variant)] text-xs font-semibold tracking-widest uppercase mb-1">PROGRES KOLEKTIF</h5>
                    <p class="text-[16px] font-semibold text-[var(--color-primary)] mb-3">Estimasi selesai: 2 Hari lagi</p>
                    <div class="w-full bg-[var(--color-surface-container-high)] rounded-full h-2 min-w-[200px]">
                        <div class="bg-[var(--color-psikotes)] h-2 rounded-full" style="width: 82%"></div>
                    </div>
                </div>
                <div class="hidden lg:block text-right">
                    <button class="text-[var(--color-psikotes)] text-sm font-medium flex items-center hover:underline">
                        Lihat Detail Progres
                        <span class="material-symbols-outlined text-[18px] ml-1">arrow_forward</span>
                    </button>
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
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach ($pesertaByNama as $peserta)
            <div x-show="!search || '{{ strtolower($peserta['nama_peserta']) }}'.includes(search.toLowerCase())"
                 class="bg-white border border-[var(--color-outline-variant)] rounded-xl overflow-hidden shadow-sm flex flex-col hover:border-[var(--color-psikotes)] transition-all duration-300">

                {{-- Card Header --}}
                <div class="p-4 pb-3 flex items-start gap-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white flex-shrink-0 bg-[var(--color-secondary-fixed)] flex items-center justify-center text-[var(--color-on-secondary-container)] font-bold text-lg">
                        {{ avatarInitialsHasilTes($peserta['nama_peserta']) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h4 class="text-lg font-semibold text-[var(--color-primary)] truncate">{{ $peserta['nama_peserta'] }}</h4>
                            @if ($peserta['jenis_peserta'] === 'Karyawan')
                                <span class="px-2 py-0.5 rounded-full border border-[var(--color-primary)]/20 bg-[var(--color-primary-fixed)]/30 text-[var(--color-primary)] text-xs font-semibold">KARYAWAN</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full border border-[var(--color-tertiary)]/20 bg-[var(--color-tertiary-fixed)]/30 text-[var(--color-tertiary)] text-xs font-semibold">KANDIDAT</span>
                            @endif
                        </div>
                        <p class="text-sm text-[var(--color-on-surface-variant)] mb-2">{{ $peserta['departemen'] }} - {{ $peserta['posisi'] }}</p>
                        <div class="flex gap-4">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px] text-[var(--color-on-surface-variant)]">id_card</span>
                                <span class="text-xs text-[var(--color-on-surface-variant)]">ID: {{ $peserta['peserta_id'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Body: test history --}}
                <div class="px-4 pb-4 pt-3 flex-1">
                    <h5 class="text-xs font-semibold tracking-widest uppercase text-[var(--color-on-surface-variant)] mb-3">Riwayat Tes</h5>
                    <div class="space-y-2">
                        @foreach ($peserta['sesi_diikuti'] as $sesiRow)
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-[var(--color-surface-container-low)] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-[var(--color-secondary-fixed)] flex items-center justify-center text-[var(--color-on-secondary-container)]">
                                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[var(--color-primary)]">{{ $sesiRow['nama_sesi'] }}</p>
                                    <p class="text-xs text-[var(--color-on-surface-variant)]">
                                        {{ $sesiRow['tanggal'] ? $tglId($sesiRow['tanggal']) : '-' }}
                                        @if ($sesiRow['status'] === 'Selesai')
                                            <span class="text-green-600"> • Selesai</span>
                                        @elseif ($sesiRow['status'] === 'Sedang Berjalan')
                                            <span class="text-amber-600"> • Sedang Berjalan</span>
                                        @else
                                            <span class="text-[var(--color-on-surface-variant)]"> • Belum Mengerjakan</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if ($sesiRow['status'] === 'Selesai')
                                <a href="{{ route('admin.hasil-tes.detail', [$sesiRow['sesi_id'], $sesiRow['peserta_id']]) }}"
                                   class="px-3 py-1.5 text-[var(--color-psikotes)] border border-[var(--color-psikotes)]/30 rounded-lg text-xs font-medium hover:bg-[var(--color-psikotes)] hover:text-white transition-all">
                                    Lihat Detail
                                </a>
                            @else
                                <button class="px-3 py-1.5 bg-[var(--color-psikotes)] text-white rounded-lg text-xs font-medium hover:opacity-90 transition-all">
                                    Monitor
                                </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            @if (empty($pesertaByNama))
            <div class="col-span-2 rounded-xl border border-dashed border-[var(--color-outline-variant)] bg-white px-6 py-10 text-center text-sm text-[var(--color-on-surface-variant)]">
                Belum ada data peserta.
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
