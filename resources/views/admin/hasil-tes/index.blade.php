@extends('layouts.admin', ['judulHalaman' => 'Hasil Tes'])

@section('content')
@php
    // Warna untuk status pengerjaan
    $warnaStatus = [
     'Selesai'         => 'bg-emerald-600',
     'Belum Mengerjakan' => 'bg-slate-500',
     'Sedang Berjalan'   => 'bg-amber-500',
    ];

    // Warna untuk jenis peserta
    $warnaJenis = [
        'Karyawan' => 'bg-blue-600',
        'Kandidat' => 'bg-indigo-600',
    ];

    // Warna untuk status sesi
    $warnaSesi = [
        'Aktif'   => 'bg-emerald-600',
        'Selesai' => 'bg-blue-600',
        'Draft'   => 'bg-slate-500',
    ];

    // Warna untuk badge alat tes
    $warnaAlatTes = [
        'IST'      => 'bg-teal-600',
        'DISC'     => 'bg-cyan-600',
        'EPPS'     => 'bg-purple-600',
        'MMPI-2'   => 'bg-pink-600',
    ];

    // Kelompokkan hasil_tes berdasarkan sesi_id
    $pesertaBySesi = [];
    foreach ($hasilTes as $row) {
      $pesertaBySesi[$row['sesi_id']][] = $row;
    }

    // Kelompokkan hasil_tes berdasarkan nama_peserta (untuk tab per peserta)
    $pesertaByNama = [];
    foreach ($hasilTes as $row) {
        $key = $row['nama_peserta'];
        if (!isset($pesertaByNama[$key])) {
            $pesertaByNama[$key] = [
                'nama_peserta' => $row['nama_peserta'],
                'departemen' => $row['departemen'],
                'posisi' => $row['posisi'],
                'jenis_peserta' => $row['jenis_peserta'],
                'peserta_id' => $row['peserta_id'],
                'sesi_diikuti' => [],
            ];
        }
        $pesertaByNama[$key]['sesi_diikuti'][] = [
            'sesi_id' => $row['sesi_id'],
            'peserta_id' => $row['peserta_id'],
            'nama_sesi' => collect($penjadwalan)->where('id', $row['sesi_id'])->first()['nama_sesi'] ?? '—',
            'status' => $row['status_pengerjaan'],
            'tanggal' => $row['tanggal_pengerjaan'],
        ];
    }
    $pesertaByNama = collect($pesertaByNama)->sortBy('nama_peserta')->values()->all();

    // Format tanggal menjadi DD Month YYYY
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

    // Generate inisial foto avatar (2 huruf pertama nama)
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

<div x-data="{ tab: 'sesi', sesiTerpilih: '', search: '' }"
     x-init="
        // Remove x-cloak from all descendants after Alpine initializes
        this.$el.querySelectorAll('[x-cloak]').forEach(el => el.removeAttribute('x-cloak'));
     "
     class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

    <div class="mb-4">
        <h2 class="text-base font-semibold text-slate-900">Hasil Tes Psikotes</h2>
        <p class="mt-0.5 text-xs text-slate-500">Lihat hasil tes berdasarkan sesi penjadwalan atau per peserta.</p>
    </div>

    {{-- TAB TOGGLE --}}
    <div class="flex border-b border-slate-200 mb-4">
        <button type="button"
                @click="tab = 'sesi'"
                :class="tab === 'sesi' ? 'border-[#2C5F6F] text-[#2C5F6F]' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors">
            Per Sesi Penjadwalan
        </button>
        <button type="button"
                @click="tab = 'peserta'"
                :class="tab === 'peserta' ? 'border-[#2C5F6F] text-[#2C5F6F]' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors">
            Per Peserta
        </button>
    </div>

    {{-- ============ TAB A: PER SESI ======== --}}
    <div x-show="tab === 'sesi'" x-cloak>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex-1 max-w-md">
                <label for="sesiSelect" class="block text-xs font-medium text-slate-600 mb-1">Pilih Sesi Penjadwalan</label>
                <select id="sesiSelect"
                        x-model="sesiTerpilih"
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#2C5F6F] focus:ring-[#2C5F6F]">
                    <option value="">-- Pilih Sesi --</option>
                    @foreach ($penjadwalan as $sesi)
                    <option value="{{ $sesi['id'] }}">{{ $sesi['nama_sesi'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="button"
                        onclick="alert('Fitur cetak PDF terpilih akan aktif setelah backend selesai dibangun.')"
                        class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!sesiTerpilih">
                    Cetak PDF Terpilih
                </button>
            </div>
        </div>
        @forelse ($penjadwalan as $sesi)
            @php $listPeserta = $pesertaBySesi[$sesi['id']] ?? []; @endphp
            <div x-show="sesiTerpilih == '{{ $sesi['id'] }}'"
                 class="border border-slate-300 bg-white overflow-visible shadow-sm rounded-md">

                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                   <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-slate-90 truncate">{{ $sesi['nama_sesi'] }}</h3>
                     <span class="inline-block rounded-md {{ $warnaSesi[$sesi['status']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                            {{ $sesi['status'] }}
                       </span>
                  </div>
                 <p class="mt-1 text-xs text-slate-600">
                        Departemen: <span class="font-medium text-slate-800">{{ $sesi['departemen_terkait'] }}</span>
                     <span class="mx-2 text-slate-300">|</span>
                        Periode: <span class="font-medium text-slate-800">{{ $tglId($sesi['tanggal_mulai']) }} – {{ $tglId($sesi['tanggal_selesai']) }}</span>
                    </p>
        </div>

                <table class="min-w-full bg-white">
                    <thead>
           <tr class="bg-slate-100 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 border-b border-slate-300">
                        <th class="px-4 py-3 w-10 text-center">
                               <input type="checkbox"
                                       onclick="this.closest('tbody').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = this.checked)"
                                    class="rounded border-slate-300 text-[#2C5F6F] focus:ring-[#2C5F6F]">
                         </th>
                      <th class="px-4 py-3 w-10">No</th>
                        <th class="px-4 py-3 w-14">Foto</th>
                          <th class="px-4 py-3">Nama Peserta</th>
                          <th class="px-4 py-3">Departemen - Posisi</th>
                      <th class="px-4 py-3">Alat Tes</th>
                        <th class="px-4 py-3">Status Pengerjaan</th>
                          <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 w-32">Aksi</th>
                       </tr>
                    </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($listPeserta as $idx => $row)
                          <tr class="hover:bg-slate-50 transition-colors">
                                <!-- Checkbox -->
                            <td class="px-4 py-3 text-center">
                                    <input type="checkbox" class="rounded border-slate-300 text-[#2C5F6F] focus:ring-[#2C5F6F]">
                                </td>

                                <!-- No -->
                            <td class="px-4 py-3 text-center text-sm text-slate-600">
                                   {{ $idx + 1 }}
                                </td>

                                <!-- Foto / avatar inisial -->
                            <td class="px-4 py-3">
                                 <div class="w-8 h-8 rounded-full bg-slate-300 flex items-center justify-center text-xs font-bold text-slate-700 border border-slate-400 mx-auto">
                             {{ avatarInitialsHasilTes($row['nama_peserta']) }}
                                   </div>
                                </td>

                                <!-- Nama Peserta -->
                            <td class="px-4 py-3 text-sm font-medium text-slate-900 whitespace-nowrap">
                            {{ $row['nama_peserta'] }}
                                </td>

                                <!-- Departemen - Posisi -->
                            <td class="px-4 py-3 text-sm text-slate-700 whitespace-nowrap">
                                    {{ $row['departemen'] }} - {{ $row['posisi'] }}
                                </td>

                            <!-- Alat Tes -->
                            <td class="px-4 py-3">
                                @if (!empty($row['hasil_alat_tes']))
                                  <div class="flex flex-wrap gap-1">
                                   @foreach ($row['hasil_alat_tes'] as $alat)
                                        <span class="inline-block {{ $warnaAlatTes[$alat['nama_alat_tes']] ?? 'bg-slate-500' }} text-white px-2 py-0.5 rounded text-[11px] font-medium">
                                              {{ $alat['nama_alat_tes'] }}
                                               </span>
                                            @endforeach
                                       </div>
                                    @else
                                     <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>

                              <!-- Status Pengerjaan -->
                            <td class="px-4 py-3 whitespace-nowrap">
                           <span class="inline-block rounded-md {{ $warnaStatus[$row['status_pengerjaan']] ?? 'bg-slate-600' }} px-2 py-1 text-xs font-semibold text-white">
                                    {{ $row['status_pengerjaan'] }}
                                   </span>
                                </td>

                                <!-- Tanggal -->
                            <td class="px-4 py-3 text-sm text-slate-700 whitespace-nowrap">
                          {{ $row['tanggal_pengerjaan'] ? $tglId($row['tanggal_pengerjaan']) : '-' }}
                                </td>

                                <!-- Aksi -->
                            <td class="px-4 py-3">
                              @if ($row['status_pengerjaan'] === 'Selesai')
                                        <div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
                                           <button type="button"
                                                 @click="open = !open"
                                                   class="inline-flex items-center gap-1 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none">
                                         <span>Lihat Hasil</span>
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                               </svg>
                                           </button>
                                           <div x-show="open"
                                                 x-transition
                                           class="absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-md bg-white border border-slate-200 shadow-lg">
                                             <div class="py-1">
                                                    <a href="{{ route('admin.hasil-tes.detail', [$row['sesi_id'], $row['peserta_id']]) }}"
                                                     class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                                        Lihat Detail
                                                   </a>
                                                   <button type="button"
                                                            onclick="alert('Cetak laporan individual akan aktif setelah backend selesai dibangun.')"
                                                class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                                        Cetak Laporan
                                                   </button>
                                               </div>
                                           </div>
                                       </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-slate-100 text-slate-400 cursor-not-allowed select-none">
                                      Lihat Hasil
                                       </span>
                                    @endif
                               </td>
                            </tr>
                       @empty
                            <tr>
                           <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada peserta dengan hasil tes pada sesi ini.
                               </td>
                            </tr>
                        @endforelse
                   </tbody>
               </table>
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500">
               Belum ada sesi penjadwalan.
            </div>
        @endforelse

        <div x-show="!sesiTerpilih"
             class="mt-6 rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500">
            Silakan pilih sesi penjadwalan terlebih dahulu untuk melihat daftar peserta.
        </div>
    </div>

    {{-- ============ TAB B: PER PESERTA ========== --}}
    <div x-show="tab === 'peserta'" x-cloak>
        <div class="mb-4 max-w-md">
            <label for="searchPeserta" class="block text-xs font-medium text-slate-600 mb-1">Cari Nama Peserta</label>
            <input type="text"
                   id="searchPeserta"
                   x-model="search"
                   placeholder="Ketik nama peserta..."
                   class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:border-[#2C5F6F] focus:ring-[#2C5F6F]">
       </div>

       <div class="grid gap-3 md:grid-cols-2">
           @forelse ($pesertaByNama as $peserta)
                <div x-show="!search || '{{ strtolower($peserta['nama_peserta']) }}'.includes(search.toLowerCase())"
                   class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">{{ $peserta['nama_peserta'] }}</h3>
                       <p class="text-xs text-slate-500">{{ $peserta['departemen'] }} - {{ $peserta['posisi'] }}</p>
                        </div>
                     <span class="inline-block rounded-md {{ $warnaJenis[$peserta['jenis_peserta']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                        {{ $peserta['jenis_peserta'] }}
                        </span>
                  </div>

                    <p class="text-xs font-medium text-slate-600 mb-2">Sesi yang pernah diikuti:</p>
                    <ul class="space-y-2">
                        @foreach ($peserta['sesi_diikuti'] as $sesiRow)
                            <li class="flex items-center justify-between rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                              <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-slate-900 truncate">{{ $sesiRow['nama_sesi'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $tglId($sesiRow['tanggal']) }}</p>
                                </div>
                             <span class="ml-2 inline-block rounded-md {{ $warnaStatus[$sesiRow['status']] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                                    {{ $sesiRow['status'] }}
                                </span>
                                <a href="{{ route('admin.hasil-tes.detail', [$sesiRow['sesi_id'], $sesiRow['peserta_id']]) }}"
                                   class="ml-2 rounded-md bg-[#2C5F6F] px-3 py-1 text-xs font-semibold text-white hover:bg-[#234853]">
                                    Lihat Detail
                               </a>
                            </li>
                       @endforeach
                   </ul>
                </div>
            @empty
                <div class="col-span-2 rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center text-sm text-slate-500">
                   Belum ada data peserta.
                </div>
            @endforelse
       </div>
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection