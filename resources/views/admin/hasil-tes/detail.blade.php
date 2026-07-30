@extends('layouts.admin', ['judulHalaman' => 'Laporan Hasil Tes'])

@section('content')
@php
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
@endphp
<style>
    @page { size: A4; margin: 0; }
    @media print {
        body { background: #fff; }
        .dokumen { border: none; margin: 0; box-shadow: none; }
    }
    .dokumen { width: 210mm; min-height: 297mm; margin: 0 auto 40px auto; padding: 20mm; background: white; border: 1px solid #000; box-sizing: border-box; }
    .header-row { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 14px; }
    .company-logo { width: 36px; height: 36px; background: #0f6e56; border-radius: 0; }
    .company-name { font-weight: 700; font-size: 15px; margin: 0; }
    .company-sub { font-size: 10px; color: #444; margin: 2px 0 0 0; }
    .rahasia-badge { border: 1px solid #b91c1c; color: #b91c1c; font-size: 9px; font-weight: 700; padding: 3px 8px; letter-spacing: 0.5px; display: inline-block; white-space: nowrap; }
    .doc-title { text-align: center; font-weight: 700; font-size: 13px; letter-spacing: 0.5px; margin: 8px 0 4px 0; }
    .doc-sub { text-align: center; font-size: 10px; color: #666; margin-bottom: 14px; }
    .section-title { font-weight: 700; font-size: 11.5px; margin: 16px 0 8px 0; letter-spacing: 0.5px; text-transform: uppercase; }
    .sub-title { font-weight: 700; font-size: 10.5px; margin: 10px 0 4px 0; color: #333; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; }
    td, th { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; }
    .th-row td, .th-row th { background: #000; color: #fff; font-weight: 700; }
    .th-row th { text-align: left; }
    .label-cell { background: #f2f2f2; font-weight: 700; width: 110px; }
    .bidang-row td { background: #d9d9d9; font-weight: 700; }
    .mark { text-align: center; font-weight: 700; }
    .narasi-box { padding: 10px; font-size: 10.5px; line-height: 1.7; text-align: justify; }
    .footnote { font-size: 8.5px; color: #888; border-top: 1px solid #ccc; padding-top: 6px; margin: 6px 0 4px 0; line-height: 1.6; }
    .ellipsis-row td { text-align: center; color: #999; font-style: italic; }
    .access-denied { border: 1px dashed #999; padding: 24px; text-align: center; color: #666; font-size: 11px; margin-bottom: 14px; }
    .ttd-row { display: flex; justify-content: space-between; margin-top: 24px; font-size: 11px; }
    .ttd-space { margin-bottom: 36px; }
    .ttd-line { border-top: 1px solid #000; width: 150px; padding-top: 2px; }
    .container-formal { border: 1px solid #000; border-radius: 0; padding: 12px; margin-bottom: 12px; }
</style>

<div style="background: #e9ecef; padding: 24px 0; margin: -24px;">

    <div style="max-width: 210mm; margin: 0 auto 12px auto; display: flex; justify-content: flex-end;">
        <button onclick="alert('Fitur cetak PDF akan aktif setelah backend selesai dibangun.')" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Cetak PDF</button>
    </div>

<div class="dokumen">
    {{-- HEADER DENGAN Cetak PDF --}}
    <div class="header-row">
        <div class="header-content">
            <div style="display:flex; align-items:center; gap:10px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="company-logo">
                <div>
                    <div class="company-name">PT Jhonlin Group</div>
                    <div class="company-sub">Departemen Human Capital</div>
                </div>
            </div>
        </div>
        <div class="rahasia-badge">DATA BERSIFAT RAHASIA</div>
    </div>

    <div class="doc-title">PSIKOGRAM &mdash; HASIL PEMERIKSAAN PSIKOLOGI</div>
    <div class="doc-sub">Rekrutmen Staff Finance Batch 1 &middot; PT Jhonlin Group</div>

    {{-- IDENTITAS PESERTA --}}
    <div class="container-formal">
        <div class="section-title">IDENTITAS PESERTA</div>
        <table>
            <tr>
                <td class="label-cell">Nama</td>
                <td>{{ $hasilTes['nama_peserta'] }}</td>
                <td class="label-cell">No. Peserta</td>
                <td>{{ $hasilTes['no_peserta'] ?? 'HT-2026-XXX' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Jenis Peserta</td>
                <td>{{ $hasilTes['jenis_peserta'] }}</td>
                <td class="label-cell">Departemen</td>
                <td>{{ $hasilTes['departemen'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Posisi</td>
                <td>{{ $hasilTes['posisi'] }}</td>
                <td class="label-cell">Nama Sesi</td>
                <td>{{ $sesi['nama_sesi'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Tanggal Pengerjaan</td>
                <td>{{ $tglId($hasilTes['tanggal_pengerjaan']) }}</td>
                <td class="label-cell">Tujuan Tes</td>
                <td>Seleksi Karyawan Baru</td>
            </tr>
        </table>
    </div>

    {{-- RINGKASAN ALAT TES --}}
    <div class="container-formal">
        <div class="section-title">RINGKASAN ALAT TES</div>
        <table>
            <thead class="th-row"><tr class="text-left"><th>Alat Tes</th><th>Format Dasar</th><th>Durasi Aktual</th></tr></thead>
            <tbody>
                @forelse ($hasilTes['hasil_alat_tes'] as $alat)
                <tr><td>{{ $alat['nama_alat_tes'] }}</td><td>{{ $alat['format_dasar'] }}</td><td>{{ $alat['durasi_pengerjaan_aktual'] }}</td></tr>
                @empty
                <tr><td colspan="3" style="color:#999; font-style:italic;">Belum ada hasil tes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PSIKOGRAM --}}
    @if (!empty($psikogram))
    <div class="container-formal">
        <div class="section-title">PSIKOGRAM</div>

        @foreach (['Intelektual', 'Sikap Kerja', 'Kepribadian', 'Potensi Kerja', 'Sensitif'] as $bidang)
            @if (!empty($psikogram[$bidang]))
                @php
                    switch ($bidang) {
                        case 'Intelektual': $huruf = 'A'; $namaDisplay = 'BIDANG INTELEKTUAL'; break;
                        case 'Sikap Kerja': $huruf = 'B'; $namaDisplay = 'BIDANG SIKAP KERJA'; break;
                        case 'Kepribadian': $huruf = 'C'; $namaDisplay = 'BIDANG KEPRIBADIAN'; break;
                        case 'Potensi Kerja': $huruf = 'D'; $namaDisplay = 'BIDANG POTENSI KERJA'; break;
                        case 'Sensitif': $huruf = 'E'; $namaDisplay = 'BIDANG SENSITIF'; break;
                    }
                @endphp
                <div class="sub-title">{{ $huruf }}. {{ $namaDisplay }}</div>

                @if ($bidang !== 'Sensitif')
                    <table>
                        <thead class="th-row"><tr><th style="width:50%;">Aspek</th><th style="width:10%;">R</th><th style="width:10%;">K</th><th style="width:10%;">C</th><th style="width:10%;">B</th><th style="width:10%;">BS</th></tr></thead>
                        <tbody>
                            @foreach ($psikogram[$bidang] as $aspek)
                            <tr>
                                <td style="padding:3px 5px;"><strong>{{ $aspek['nama_dimensi'] }}</strong><br><span style="font-size:9px;color:#666">{{ $aspek['deskripsi_aspek'] }}</span></td>
                                <td class="mark">@if ($aspek['kategori_hasil'] === 'R') X @else &bull; @endif</td>
                                <td class="mark">@if ($aspek['kategori_hasil'] === 'K') X @else &bull; @endif</td>
                                <td class="mark">@if ($aspek['kategori_hasil'] === 'C') X @else &bull; @endif</td>
                                <td class="mark">@if ($aspek['kategori_hasil'] === 'B') X @else &bull; @endif</td>
                                <td class="mark">@if ($aspek['kategori_hasil'] === 'BS') X @else &bull; @endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    @if ($bisaLihatSensitif)
                        <table>
                            <thead class="th-row"><tr><th style="width:50%;">Aspek</th><th style="width:25%;">Skor T</th><th>Interpretasi</th></tr></thead>
                            <tbody>
                                @foreach ($psikogram[$bidang] as $aspek)
                                <tr>
                                    <td style="padding:3px 5px;"><strong>{{ $aspek['nama_dimensi'] }}</strong><br><span style="font-size:9px;color:#666">{{ $aspek['deskripsi_aspek'] }}</span></td>
                                    <td class="mark">{{ $aspek['skor_t'] ?? '-' }}</td>
                                    <td>{{ $aspek['kategori_hasil'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="access-denied">Anda tidak memiliki akses untuk melihat aspek psikogram yang bersumber dari data tes sensitif (MMPI-2). Hubungi Super Admin atau Admin HR untuk informasi lebih lanjut.</div>
                    @endif
                @endif
            @endif
        @endforeach

        <div class="footnote"><strong>Keterangan Skala:</strong> R = Rendah &middot; K = Kurang &middot; C = Cukup &middot; B = Baik &middot; BS = Baik Sekali. Skor mentah dan rincian per alat tes tersedia di Lampiran.</div>

        <div class="section-title" style="margin-top:16px;">RINGKASAN EVALUASI PSIKOLOGIS</div>
        <table>
            <tbody>
                <tr>
                    <td class="narasi-box" style="min-height:60px;">
                        @if (!empty($psikogram['Narasi_Evaluasi']))
                            {{ $psikogram['Narasi_Evaluasi'] }}
                        @else
                            Berdasarkan hasil pemeriksaan psikologi, terdapat taraf kecerdasan umum baik dengan kemampuan menonjol pada salah satu aspek. Sikap kerja menunjukkan sistematika dan daya tahan kerja yang baik. Dari segi kepribadian, mampu bekerja sama dan berkomunikasi dalam kelompok, dengan kepercayaan diri yang memadai. Motivasi berprestasi menunjukkan dorongan kuat untuk mencapai hasil kerja optimal.
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="section-title" style="margin-top:16px;">KESIMPULAN DAN REKOMENDASI HR</div>
        <table>
            <tbody>
                <tr><td class="narasi-box" style="color:#888;min-height:50px;">Diisi manual oleh HR / Psikolog setelah peninjauan hasil di atas.</td></tr>
            </tbody>
        </table>

        <div class="ttd-row">
            <div><div class="ttd-space">Diperiksa oleh,</div><div class="ttd-line">Admin HR</div></div>
            <div><div class="ttd-space">Banjarbaru, 28 Juli 2026</div><div class="ttd-line">Super Admin</div></div>
        </div>
    </div>
    @endif

    {{-- HASIL PER INSTRUMEN --}}
    @foreach ($hasilTes['hasil_alat_tes'] as $index => $alatTes)
        <div class="container-formal">
            <div class="sub-title">{{ $index + 1 }}. {{ $alatTes['nama_alat_tes'] }} &ndash; {{ $alatTes['format_dasar'] }}</div>

            {{-- IST: Skor per subtes --}}
            @if ($alatTes['nama_alat_tes'] === 'IST' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['nama_subtes']))
                <p class="text-sm" style="margin:6px 0 10px 0; color:#666;">Format: Pilihan Ganda &ndash; Skor Mentah (RS) &amp; Skor Skala (SS)</p>
                <table>
                    <thead class="th-row"><tr><th style="width:45%;">Subtes</th><th style="width:25%;">Skor Mentah (RS)</th><th style="width:25%;">Skor Skala (SS)</th><th>Kategori</th></tr></thead>
                    <tbody>
                        @foreach ($alatTes['skor_ringkas'] as $subtes)
                        <tr><td>{{ $subtes['nama_subtes'] }}</td><td class="mark">{{ $subtes['skor_mentah'] }}</td><td class="mark">{{ $subtes['skor_skala'] }}</td><td>{{ $subtes['kategori'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:10px;">
                    <div class="sub-title" style="margin:10px 0 6px 0;">Detail Jawaban</div>
                    <table>
                        <thead class="th-row"><tr><th style="width:5%;">No</th><th style="width:60%;">Pertanyaan</th><th>Jawaban Peserta</th><th>Jawaban Benar</th></tr></thead>
                        <tbody>
                            @foreach ([
                                ['nomor'=>1,'pertanyaan'=>'Saya lebih suka bekerja dengan orang lain.','jawaban_peserta'=>'Saya lebih suka bekerja mandiri.','jawaban_benar'=>'Setuju'],
                                ['nomor'=>2,'pertanyaan'=>'Saat menghadapi masalah, saya cenderung:','jawaban_peserta'=>'Menyusun rencana.','jawaban_benar'=>'Mengambil keputusan spontan'],
                                ['nomor'=>3,'pertanyaan'=>'Saya suka:','jawaban_peserta'=>'Bekerja secara kreatif dan bebas.','jawaban_benar'=>'Bekerja dalam tim'],
                                ['nomor'=>4,'pertanyaan'=>'Saya merasa lebih nyaman bekerja di:','jawaban_peserta'=>'Lingkungan tenang dan terstruktur.','jawaban_benar'=>'Tim kolaboratif'],
                            ] as $soal)
                            <tr><td class="mark">{{ $soal['nomor'] }}</td><td style="padding:3px 5px;">{{ $soal['pertanyaan'] }}</td><td>{{ $soal['jawaban_peserta'] }}</td><td>{{ $soal['jawaban_benar'] }}</td></tr>
                            @endforeach
                            <tr class="ellipsis-row"><td colspan="4">&hellip; seluruh soal ditampilkan berurutan &hellip;</td></tr>
                        </tbody>
                    </table>
                </div>

            {{-- DISC: Skor per dimensi --}}
            @elseif ($alatTes['nama_alat_tes'] === 'DISC' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['dimensi']))
                <p class="text-sm" style="margin:6px 0 10px 0; color:#666;">Format: Skala Likert &ndash; Skor Mentah (1-100), Skor Skala (1-10)</p>
                <table>
                    <thead class="th-row"><tr><th style="width:40%;">Dimensi</th><th style="width:25%;">Skor Mentah</th><th style="width:25%;">Skor Skala</th><th>Kategori</th></tr></thead>
                    <tbody>
                        @foreach ($alatTes['skor_ringkas'] as $dimensi)
                        <tr><td>{{ $dimensi['dimensi'] }}</td><td class="mark">{{ $dimensi['skor_mentah'] }}</td><td class="mark">{{ $dimensi['skor_skala'] }}</td><td>{{ $dimensi['kategori'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="display:flex;gap:16px;align-items:flex-start;margin-top:8px;">
                    <svg viewBox="0 0 160 160" width="130" height="130" style="border:1px solid #000;flex-shrink:0;">
                        <polygon points="80,20 140,80 80,140 20,80" fill="none" stroke="#ccc" stroke-width="1"/>
                        <polygon points="80,50 110,80 80,110 50,80" fill="none" stroke="#ccc" stroke-width="1"/>
                        <line x1="80" y1="20" x2="80" y2="140" stroke="#ccc" stroke-width="1"/>
                        <line x1="20" y1="80" x2="140" y2="80" stroke="#ccc" stroke-width="1"/>
                        <!-- Label D (top) - centered, positioned slightly above the top point -->
                        <text x="80" y="12" text-anchor="middle" font-size="9" fill="#111" dominant-baseline="middle">D</text>
                        <!-- Label I (right) - aligned right, positioned slightly right of right point -->
                        <text x="145" y="80" text-anchor="start" font-size="9" fill="#111" dominant-baseline="middle">I</text>
                        <!-- Label S (bottom) - centered, positioned slightly below bottom point -->
                        <text x="80" y="152" text-anchor="middle" font-size="9" fill="#111" dominant-baseline="hanging">S</text>
                        <!-- Label C (left) - aligned left, positioned slightly left of left point -->
                        <text x="15" y="80" text-anchor="end" font-size="9" fill="#111" dominant-baseline="middle">C</text>
                        <polygon points="80,32 122,80 80,106 44,80" fill="#111" fill-opacity="0.12" stroke="#111" stroke-width="1.5"/>
                        <circle cx="80" cy="32" r="2" fill="#111"/>
                        <circle cx="122" cy="80" r="2" fill="#111"/>
                        <circle cx="80" cy="106" r="2" fill="#111"/>
                        <circle cx="44" cy="80" r="2" fill="#111"/>
                    </svg>
                </div>

            {{-- EPPS: Skor per dimensi --}}
            @elseif ($alatTes['nama_alat_tes'] === 'EPPS' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['dimensi']))
                <p class="text-sm" style="margin:6px 0 10px 0; color:#666;">Format: Forced Choice &ndash; Skor Mentah (1-100), Skor Skala (1-10)</p>
                <table>
                    <thead class="th-row"><tr><th style="width:40%;">Dimensi</th><th style="width:25%;">Skor Mentah</th><th style="width:25%;">Skor Skala</th><th>Kategori</th></tr></thead>
                    <tbody>
                        @foreach ($alatTes['skor_ringkas'] as $dimensi)
                        <tr><td>{{ $dimensi['dimensi'] }}</td><td class="mark">{{ $dimensi['skor_mentah'] }}</td><td class="mark">{{ $dimensi['skor_skala'] }}</td><td>{{ $dimensi['kategori'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>

            {{-- MMPI-2: Skor skala klinis --}}
            @elseif ($alatTes['nama_alat_tes'] === 'MMPI-2' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['skala_klinis']))
                @if ($alatTes['is_sensitif'])
                    @if ($bisaLihatSensitif)
                        <p class="text-sm" style="margin:6px 0 10px 0; color:#666;">Format: Skala Likert &ndash; Skor T (40-90), Interpretasi</p>
                        <table>
                            <thead class="th-row"><tr><th style="width:50%;">Skala Klinis</th><th style="width:25%;">Skor T</th><th>Interpretasi</th></tr></thead>
                            <tbody>
                                @foreach ($alatTes['skor_ringkas'] as $skala)
                                <tr><td>{{ $skala['skala_klinis'] }}</td><td class="mark">{{ $skala['skor_t'] }}</td><td>{{ $skala['interpretasi'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="access-denied">Anda tidak memiliki akses untuk melihat hasil tes sensitif ini. Hubungi Super Admin atau Admin HR untuk informasi lebih lanjut.</div>
                    @endif
                @else
                    <p class="text-sm" style="margin:6px 0 10px 0; color:#666;">Format: Skala Likert &ndash; Skor T (40-90), Interpretasi</p>
                    <table>
                        <thead class="th-row"><tr><th style="width:50%;">Skala Klinis</th><th style="width:25%;">Skor T</th><th>Interpretasi</th></tr></thead>
                        <tbody>
                            @foreach ($alatTes['skor_ringkas'] as $skala)
                            <tr><td>{{ $skala['skala_klinis'] }}</td><td class="mark">{{ $skala['skor_t'] }}</td><td>{{ $skala['interpretasi'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>
    @endforeach

    {{-- CATATAN & REKOMENDASI HR --}}
    <div class="container-formal">
        <div class="section-title">CATATAN & REKOMENDASI HR</div>
        <textarea id="catatanHR" rows="4" placeholder="Masukkan catatan atau rekomendasi HR di sini..." class="block w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-[#2C5F6F] focus:ring-[#2C5F6F] resize-none"></textarea>
        <div style="margin-top:10px;">
            <button type="button" onclick="alert('Catatan telah disimpan. (Placeholder)');" class="bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white border border-[#2C5F6F] hover:bg-[#234853]">Simpan Catatan</button>
        </div>
    </div>

</div>

</div>

@endsection