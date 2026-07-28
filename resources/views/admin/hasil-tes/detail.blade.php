@extends('layouts.admin', ['judulHalaman' => 'Laporan Hasil Tes'])

@section('content')
@php
    // Konversi tanggal ISO ke format lokal
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

    $kategoriBadge = [
        'Tinggi' => 'bg-emerald-600 text-white',
        'Sedang' => 'bg-blue-600 text-white',
        'Rendah' => 'bg-amber-600 text-white',
    ];

    $statusPengerjaanBadge = [
        'Selesai' => 'bg-emerald-600 text-white',
        'Belum Mengerjakan' => 'bg-slate-500 text-white',
        'Sedang Berjalan' => 'bg-amber-500 text-white',
    ];
@endphp

<div class="max-w-5xl mx-auto">
    {{-- HEADER DENGAN Cetak PDF --}}
    <div class="mb-6 flex justify-between items-start border-b border-slate-200 pb-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto">
            <h1 class="text-xl font-bold text-slate-900">PT Jhonlin Group</h1>
        </div>
        <button onclick="alert('Fitur cetak PDF akan aktif setelah backend selesai dibangun.')"
                class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
            🖨️ Cetak PDF
        </button>
    </div>

    {{-- IDENTITAS PESERTA --}}
    <div class="rounded-lg border border-slate-200 p-6 mb-6 bg-white shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 border-b border-slate-200 pb-2">Identitas Peserta</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><span class="text-slate-500">Nama:</span> <strong class="text-slate-900">{{ $hasilTes['nama_peserta'] }}</strong></div>
            <div><span class="text-slate-500">Jenis Peserta:</span> <strong class="text-slate-900">{{ $hasilTes['jenis_peserta'] }}</strong></div>
            <div><span class="text-slate-500">Departemen:</span> <strong class="text-slate-900">{{ $hasilTes['departemen'] }}</strong></div>
            <div><span class="text-slate-500">Posisi:</span> <strong class="text-slate-900">{{ $hasilTes['posisi'] }}</strong></div>
            <div><span class="text-slate-500">Nama Sesi:</span> <strong class="text-slate-900">{{ $sesi['nama_sesi'] }}</strong></div>
            <div><span class="text-slate-500">Tanggal Pengerjaan:</span> <strong class="text-slate-900">{{ $tglId($hasilTes['tanggal_pengerjaan']) }}</strong></div>
        </div>
    </div>

    {{-- RINGKASAN ALAT TES --}}
    <div class="rounded-lg border border-slate-200 p-6 mb-6 bg-white shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 border-b border-slate-200 pb-2">Ringkasan Alat Tes</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                        <th class="px-4 py-2">Alat Tes</th>
                        <th class="px-4 py-2">Format Dasar</th>
                        <th class="px-4 py-2">Durasi Aktual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($hasilTes['hasil_alat_tes'] as $alat)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2 font-medium text-slate-900">{{ $alat['nama_alat_tes'] }}</td>
                            <td class="px-4 py-2 text-slate-700">{{ $alat['format_dasar'] }}</td>
                            <td class="px-4 py-2 text-slate-700">{{ $alat['durasi_pengerjaan_aktual'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-slate-500">Belum ada hasil tes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- HASIL PER INSTRUMEN --}}
    <div class="space-y-6">
        @foreach ($hasilTes['hasil_alat_tes'] as $index => $alatTes)
            <div class="rounded-lg border border-slate-200 p-6 bg-white shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ $alatTes['nama_alat_tes'] }} - {{ $alatTes['format_dasar'] }}</h3>

                {{-- IST: Skor Total + Kategori --}}
                @if ($alatTes['nama_alat_tes'] === 'IST' && !empty($alatTes['skor_ringkas']['total_skor']))
                    <div class="bg-slate-50 rounded-md p-4 mb-4">
                        <p class="text-sm mb-2"><span class="text-slate-500">Skor Total:</span> <strong class="text-slate-900 text-2xl">{{ $alatTes['skor_ringkas']['total_skor'] }}</strong></p>
                        <p class="text-sm mb-2"><span class="text-slate-500">Kategori:</span> <span class="inline-block rounded-md {{ $kategoriBadge[$alatTes['skor_ringkas']['kategori']] ?? 'bg-slate-600' }} px-2 py-1 text-sm font-semibold text-white">{{ $alatTes['skor_ringkas']['kategori'] }}</span></p>
                        <p class="text-sm text-slate-700"><span class="text-slate-500">Deskripsi:</span> {{ $alatTes['skor_ringkas']['deskripsi_kategori'] }}</p>
                    </div>

                    {{-- Lampiran Detail Soal & Jawaban (hanya untuk contoh IST) --}}
                    <div class="mt-4">
                        <h4 class="text-sm font-semibold text-slate-900 mb-2">Contoh Soal & Jawaban Peserta</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600">
                                    <tr>
                                        <th class="px-4 py-2">No</th>
                                        <th class="px-4 py-2">Pertanyaan</th>
                                        <th class="px-4 py-2">Jawaban Peserta</th>
                                        <th class="px-4 py-2">Jawaban Benar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach ([
                                        [
                                            'nomor' => 1,
                                            'pertanyaan' => 'Saya lebih suka bekerja dengan orang lain.',
                                            'jawaban_peserta' => 'Saya lebih suka bekerja mandiri.',
                                            'jawaban_benar' => 'Setuju'
                                        ],
                                        [
                                            'nomor' => 2,
                                            'pertanyaan' => 'Saat menghadapi masalah, saya cenderung:',
                                            'jawaban_peserta' => 'Menyusun rencana',
                                            'jawaban_benar' => 'Mengambil keputusan spontan'
                                        ],
                                        [
                                            'nomor' => 3,
                                            'pertanyaan' => 'Saya suka:',
                                            'jawaban_peserta' => 'Bekerja secara kreatif dan bebas',
                                            'jawaban_benar' => 'Bekerja dalam tim'
                                        ],
                                        [
                                            'nomor' => 4,
                                            'pertanyaan' => 'Saya merasa lebih nyaman bekerja di:',
                                            'jawaban_peserta' => 'Lingkungan tenang dan terstruktur',
                                            'jawaban_benar' => 'Tim kolaboratif'
                                        ],
                                    ] as $soal)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-2">{{ $soal['nomor'] }}</td>
                                        <td class="px-4 py-2 text-slate-700 truncate max-w-xs">{{ $soal['pertanyaan'] }}</td>
                                        <td class="px-4 py-2"><span class="inline-block rounded bg-emerald-100 text-emerald-800 px-2 py-0.5 text-xs font-semibold">{{ $soal['jawaban_peserta'] }}</span></td>
                                        <td class="px-4 py-2">{{ $soal['jawaban_benar'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                {{-- DISC: 4 Skor D/I/S/C sebagai bar --}}
                @elseif ($alatTes['nama_alat_tes'] === 'DISC' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas']['D']))
                    <div class="space-y-3">
                        @foreach (['D' => 'Dominance', 'I' => 'Influence', 'S' => 'Steadiness', 'C' => 'Compliance'] as $dimensi => $label)
                            @if (isset($alatTes['skor_ringkas'][$dimensi]))
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-semibold text-slate-700">{{ $label }}</span>
                                        <span class="text-slate-600">{{ $alatTes['skor_ringkas'][$dimensi] }}</span>
                                    </div>
                                    <div class="w-full h-2 bg-slate-200 rounded overflow-hidden">
                                        <div class="h-full bg-[#2C5F6F] transition-all duration-500" style="width: {{ min($alatTes['skor_ringkas'][$dimensi], 100) }}%"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                {{-- EPPS: Skor per dimensi sebagai list --}}
                @elseif ($alatTes['nama_alat_tes'] === 'EPPS' && is_array($alatTes['skor_ringkas']))
                    <div class="space-y-2">
                        @foreach ($alatTes['skor_ringkas'] as $dimensi => $skor)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-700">{{ $dimensi }}</span>
                                <span class="inline-block rounded-md bg-[#2C5F6F] px-2 py-1 text-sm font-semibold text-white">{{ $skor }}</span>
                            </div>
                        @endforeach
                    </div>
                {{-- MMPI-2 --}}
                @elseif ($alatTes['nama_alat_tes'] === 'MMPI-2' && is_array($alatTes['skor_ringkas']))
                    @if ($alatTes['is_sensitif'])
                        {{-- MMPI Sensitif --}}
                        @if ($bisaLihatSensitif)
                            {{-- User punya izin — tunjukkan skor lengkap --}}
                            <div class="space-y-2">
                                @foreach ($alatTes['skor_ringkas'] as $skala => $skor)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-700">{{ $skala }}</span>
                                        <span class="inline-block rounded-md bg-rose-100 text-rose-800 px-2 py-1 text-sm font-semibold">{{ $skor }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- User tanpa izin — tunjukkan pesan akses --}}
                            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                                <h3 class="font-semibold text-slate-800 mb-2">MMPI-2 - Skala Likert</h3>
                                <p class="text-slate-500 text-sm">Anda tidak memiliki akses untuk melihat hasil tes sensitif ini.</p>
                            </div>
                        @endif
                    @else
                        {{-- MMPI biasa (non-sensitif) — tunjukkan skor biasa --}}
                        <div class="space-y-2">
                            @foreach ($alatTes['skor_ringkas'] as $skala => $skor)
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-700">{{ $skala }}</span>
                                    <span class="inline-block rounded-md bg-rose-100 text-rose-800 px-2 py-1 text-sm font-semibold">{{ $skor }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

            </div>
        @endforeach
    </div>

    {{-- CATATAN & REKOMENDASI HR --}}
    <div class="rounded-lg border border-slate-200 p-6 mt-6 bg-white shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 border-b border-slate-200 pb-2">Catatan & Rekomendasi HR</h2>
        <textarea id="catatanHR"
                  rows="4"
                  placeholder="Masukkan catatan atau rekomendasi HR di sini..."
                  class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#2C5F6F] focus:ring-[#2C5F6F]"></textarea>
        <div class="mt-3">
            <button type="button"
                    onclick="alert('Catatan telah disimpan. (Placeholder)');"
                    class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                Simpan Catatan
            </button>
        </div>
    </div>

</div>
@endsection