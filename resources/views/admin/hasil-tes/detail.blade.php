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
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
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

                {{-- IST: Skor per subtes dalam tabel formal --}}
                @if ($alatTes['nama_alat_tes'] === 'IST' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['nama_subtes']))
                    <p class="text-sm text-slate-600 mb-3">Format: Pilihan Ganda - Skor Mentah (RS) & Skor Skala (SS)</p>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 font-semibold border-b border-slate-200">
                                    <th class="text-left px-4 py-2">Subtes</th>
                                    <th class="text-left px-4 py-2">Skor Mentah (RS)</th>
                                    <th class="text-left px-4 py-2">Skor Skala (SS)</th>
                                    <th class="text-left px-4 py-2">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alatTes['skor_ringkas'] as $subtes)
                                    <tr class="border-b border-slate-200">
                                        <td class="px-4 py-2 text-sm">{{ $subtes['nama_subtes'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $subtes['skor_mentah'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $subtes['skor_skala'] }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            @php
                                                $cat = $subtes['kategori'];
                                                if ($cat === 'Kurang') $badgeClass = 'bg-red-100 text-red-800';
                                                elseif ($cat === 'Cukup') $badgeClass = 'bg-blue-100 text-blue-800';
                                                elseif ($cat === 'Baik') $badgeClass = 'bg-green-100 text-green-800';
                                                elseif ($cat === 'Sangat Baik') $badgeClass = 'bg-emerald-100 text-emerald-800';
                                                else $badgeClass = '';
                                            @endphp
                                            <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">{{ $cat }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Lampiran Detail Soal & Jawaban (hanya untuk contoh IST) --}}
                    <div class="mt-6">
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

                {{-- DISC: Skor per dimensi dalam tabel formal --}}
                @elseif ($alatTes['nama_alat_tes'] === 'DISC' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['dimensi']))
                    <p class="text-sm text-slate-600 mb-3">Format: Skala Likert - Skor Mentah (1-100), Skor Skala (1-10)</p>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 font-semibold border-b border-slate-200">
                                    <th class="text-left px-4 py-2">Dimensi</th>
                                    <th class="text-left px-4 py-2">Skor Mentah</th>
                                    <th class="text-left px-4 py-2">Skor Skala</th>
                                    <th class="text-left px-4 py-2">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alatTes['skor_ringkas'] as $dimensi)
                                    <tr class="border-b border-slate-200">
                                        <td class="px-4 py-2 text-sm">{{ $dimensi['dimensi'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $dimensi['skor_mentah'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $dimensi['skor_skala'] }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            @php
                                                $cat = $dimensi['kategori'];
                                                if ($cat === 'Rendah') $badgeClass = 'bg-amber-100 text-amber-800';
                                                elseif ($cat === 'Sedang') $badgeClass = 'bg-blue-100 text-blue-800';
                                                elseif ($cat === 'Tinggi') $badgeClass = 'bg-emerald-100 text-emerald-800';
                                                else $badgeClass = '';
                                            @endphp
                                            <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">{{ $cat }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                {{-- EPPS: Skor per dimensi dalam tabel formal --}}
                @elseif ($alatTes['nama_alat_tes'] === 'EPPS' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['dimensi']))
                    <p class="text-sm text-slate-600 mb-3">Format: Forced Choice - Skor Mentah (1-100), Skor Skala (1-10)</p>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 font-semibold border-b border-slate-200">
                                    <th class="text-left px-4 py-2">Dimensi</th>
                                    <th class="text-left px-4 py-2">Skor Mentah</th>
                                    <th class="text-left px-4 py-2">Skor Skala</th>
                                    <th class="text-left px-4 py-2">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alatTes['skor_ringkas'] as $dimensi)
                                    <tr class="border-b border-slate-200">
                                        <td class="px-4 py-2 text-sm">{{ $dimensi['dimensi'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $dimensi['skor_mentah'] }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $dimensi['skor_skala'] }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            @php
                                                $cat = $dimensi['kategori'];
                                                if ($cat === 'Rendah') $badgeClass = 'bg-amber-100 text-amber-800';
                                                elseif ($cat === 'Sedang') $badgeClass = 'bg-blue-100 text-blue-800';
                                                elseif ($cat === 'Tinggi') $badgeClass = 'bg-emerald-100 text-emerald-800';
                                                else $badgeClass = '';
                                            @endphp
                                            <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">{{ $cat }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                {{-- MMPI-2: Skor skala klinis dalam tabel formal --}}
                @elseif ($alatTes['nama_alat_tes'] === 'MMPI-2' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['skala_klinis']))
                    @if ($alatTes['is_sensitif'])
                        {{-- MMPI Sensitif --}}
                        @if ($bisaLihatSensitif)
                            <p class="text-sm text-slate-600 mb-3">Format: Skala Likert - Skor T (40-90), Interpretasi</p>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 font-semibold border-b border-slate-200">
                                            <th class="text-left px-4 py-2">Skala Klinis</th>
                                            <th class="text-left px-4 py-2">Skor T</th>
                                            <th class="text-left px-4 py-2">Interpretasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alatTes['skor_ringkas'] as $skala)
                                            <tr class="border-b border-slate-200">
                                                <td class="px-4 py-2 text-sm">{{ $skala['skala_klinis'] }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $skala['skor_t'] }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    @php
                                                        $interp = $skala['interpretasi'];
                                                        if ($interp === 'Signifikan') $colorClass = 'text-red-600 font-semibold';
                                                        elseif ($interp === 'Perlu Perhatian') $colorClass = 'text-amber-600 font-semibold';
                                                        elseif ($interp === 'Normal') $colorClass = 'text-emerald-600 font-semibold';
                                                        else $colorClass = 'text-slate-600';
                                                    @endphp
                                                    <span class="{{ $colorClass }}">{{ $interp }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            {{-- User tanpa izin — tunjukkan pesan akses --}}
                            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                                <h3 class="font-semibold text-slate-800 mb-2">MMPI-2 - Skala Likert</h3>
                                <p class="text-slate-500 text-sm">Anda tidak memiliki akses untuk melihat hasil tes sensitif ini.</p>
                            </div>
                        @endif
                    @else
                        {{-- MMPI biasa (non-sensitif) --}}
                        <p class="text-sm text-slate-600 mb-3">Format: Skala Likert - Skor T (40-90), Interpretasi</p>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 font-semibold border-b border-slate-200">
                                        <th class="text-left px-4 py-2">Skala Klinis</th>
                                        <th class="text-left px-4 py-2">Skor T</th>
                                        <th class="text-left px-4 py-2">Interpretasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alatTes['skor_ringkas'] as $skala)
                                        <tr class="border-b border-slate-200">
                                            <td class="px-4 py-2 text-sm">{{ $skala['skala_klinis'] }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $skala['skor_t'] }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                @php
                                                    $interp = $skala['interpretasi'];
                                                    if ($interp === 'Signifikan') $colorClass = 'text-red-600 font-semibold';
                                                    elseif ($interp === 'Perlu Perhatian') $colorClass = 'text-amber-600 font-semibold';
                                                    elseif ($interp === 'Normal') $colorClass = 'text-emerald-600 font-semibold';
                                                    else $colorClass = 'text-slate-600';
                                                @endphp
                                                <span class="{{ $colorClass }}">{{ $interp }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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