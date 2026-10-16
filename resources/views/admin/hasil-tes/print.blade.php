<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Psikogram — {{ $hasilTes['nama_peserta'] }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px; color: #1a1a1a; margin: 0; padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Toolbar (screen only) ── */
        .print-toolbar {
            position: fixed; top: 0; right: 0; z-index: 9999;
            display: flex; gap: 8px; padding: 10px 16px;
            background: #1e2d3d; border-bottom-left-radius: 10px;
            box-shadow: 0 -2px 16px rgba(0,0,0,.22);
        }
        .print-toolbar button {
            padding: 7px 18px; border: none; border-radius: 6px;
            font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s;
        }
        .btn-print { background: #00843D; color: #fff; }
        .btn-print:hover { background: #1e4550; }
        .btn-close  { background: #475569; color: #fff; }
        .btn-close:hover { background: #334155; }

        /* ── Document ── */
        .dokumen {
            width: 210mm; margin: 0 auto; padding: 12mm 14mm;
            background: #fff; color: #1a1a1a;
        }

        /* ── Header ── */
        .header {
            display: flex; align-items: center; justify-content: space-between;
            padding-bottom: 8px; margin-bottom: 10px;
            border-bottom: 2.5px solid #1a1a1a;
        }
        .header-left { display: flex; align-items: center; gap: 10px; }
        .header-logo { width: 44px; height: 44px; object-fit: contain; }
        .header-title { font-size: 15px; font-weight: 800; letter-spacing: .4px; color: #0f172a; line-height: 1.3; }
        .header-sub  { font-size: 10px; color: #64748b; margin-top: 1px; }
        .rahasia-badge {
            border: 1.5px solid #b91c1c; color: #b91c1c;
            font-size: 7.5px; font-weight: 700; padding: 3px 7px;
            letter-spacing: .8px; white-space: nowrap;
        }

        /* ── Typography ── */
        .doc-title {
            text-align: center; font-weight: 800; font-size: 13px;
            letter-spacing: 1px; margin: 10px 0 2px 0; color: #0f172a;
        }
        .doc-sub {
            text-align: center; font-size: 10px; color: #64748b;
            margin-bottom: 14px; letter-spacing: .3px;
        }
        .section-title {
            font-weight: 700; font-size: 10px; letter-spacing: .8px;
            text-transform: uppercase; color: #4b5563;
            margin: 18px 0 8px 0; padding-bottom: 4px;
            border-bottom: 1.5px solid #9ca3af;
        }
        .sub-title {
            font-weight: 700; font-size: 10.5px; margin: 12px 0 5px 0;
            color: #1e293b; letter-spacing: .3px;
        }

        /* ── Tables ── */
        table { border-collapse: collapse; font-size: 10px; margin-bottom: 6px; width: 100%; }
        td, th {
            border: 1px solid #94a3b8; padding: 4px 7px;
            vertical-align: middle; word-wrap: break-word; overflow-wrap: break-word;
        }
        .th-row th {
            background: #4b5563; color: #fff; font-weight: 700;
            text-align: left; font-size: 9px; letter-spacing: .4px;
        }
        .th-row td { background: #4b5563; color: #fff; font-weight: 700; }
        .label-cell {
            background: #f1f5f9; font-weight: 700; width: 130px;
            font-size: 9.5px; color: #334155;
        }
        .mark { text-align: center; font-weight: 700; }

        /* ── Boxes ── */
        .narasi-box {
            padding: 8px 10px; font-size: 10px; line-height: 1.7;
            text-align: justify; word-wrap: break-word; background: #fafafa;
        }
        .footnote {
            font-size: 8.5px; color: #64748b; border-top: 1px solid #e2e8f0;
            padding-top: 5px; margin: 6px 0 4px 0; line-height: 1.6;
        }
        .access-denied {
            border: 1px dashed #94a3b8; padding: 14px;
            text-align: center; color: #64748b; font-size: 10px;
            margin: 10px 0; background: #f8fafc;
        }
        .grid-summary {
            margin-top: 8px; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 4px; font-size: 9px; color: #334155;
            width: 100%; border-collapse: collapse;
        }
        .grid-summary td { padding: 4px 10px; border: none; width: 50%; }

        /* ── Signature ── */
        .ttd-space { margin-bottom: 40px; font-size: 11px; }
        .ttd-line {
            border-top: 1.5px solid #1a1a1a; width: 160px;
            padding-top: 3px; font-size: 10.5px; font-weight: 600;
        }

        /* ── Page break ── */
        .page-break { page-break-before: always; break-before: page; }

        /* ── Lampiran header (pages 2+) ── */
        .lam-header {
            display: flex; align-items: center; justify-content: space-between;
            padding-bottom: 6px; margin-bottom: 10px;
            border-bottom: 1.5px solid #1a1a1a;
        }
        .lam-header .header-logo { width: 36px; height: 36px; }
        .lam-header .header-title { font-size: 13px; }
        .lam-header .header-sub  { font-size: 9px; }
        .lam-title {
            text-align: center; font-weight: 700; font-size: 11px;
            letter-spacing: .8px; margin-bottom: 12px; color: #1e293b;
        }

        /* ── PAP chart ── */
        .pap-chart-box {
            margin-top: 18px; padding: 12px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 6px;
            min-height: 500px;
        }
        .pap-chart-title {
            font-weight: 700; font-size: 10px; letter-spacing: .6px;
            margin-bottom: 10px; text-transform: uppercase; color: #4b5563;
        }

        /* ── PAP section header ── */
        .kelompok-header {
            background: #4b5563; color: #fff; font-weight: 700;
            font-size: 9.5px; padding: 3px 8px; margin-top: 8px;
            letter-spacing: .5px;
        }

        /* ── Print ── */
        @media print {
            .no-print { display: none !important; }
            .dokumen { width: 100%; margin: 0; padding: 0; }
            body { font-size: 10.5px; margin: 0; }
            table { page-break-inside: avoid; }
            .section-title { page-break-after: avoid; }
            .sub-title { page-break-after: avoid; }
            .kelompok-header { page-break-after: avoid; }
            tr { page-break-inside: avoid; }
        }
        @page { size: A4 portrait; margin: 15mm 14mm 20mm 14mm; }
    </style>
</head>
<body>

<div class="no-print print-toolbar">
    <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
    <button class="btn-close" onclick="window.close()">Tutup</button>
</div>

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
    $namaSesi = $sesi->nama_sesi ?? '-';
    $tujuanTes = $hasilTes['tujuan_tes'];
@endphp

<div class="dokumen">

    {{-- ═══════════════════════════════════════════════════
        HALAMAN 1 — PSIKOGRAM UTAMA
    ═══════════════════════════════════════════════════ --}}

    <div class="header">
        <div class="header-left">
            <img src="{{ url('images/logo.png') }}" alt="Logo" class="header-logo">
            <div>
                <div class="header-title">PT Jhonlin Group</div>
                <div class="header-sub">Departemen Human Capital</div>
            </div>
        </div>
        <span class="rahasia-badge">RAHASIA</span>
    </div>

    <div class="doc-title">PSIKOGRAM &mdash; HASIL PEMERIKSAAN PSIKOLOGI</div>
    <div class="doc-sub">{{ $namaSesi }} &middot; PT Jhonlin Group</div>

    {{-- Identitas Peserta --}}
    <div class="section-title">Identitas Peserta</div>
    <table>
        <tr>
            <td class="label-cell">Nama</td>
            <td>{{ $hasilTes['nama_peserta'] }}</td>
            <td class="label-cell">No. Peserta</td>
            <td>{{ $hasilTes['no_peserta'] ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jenis Peserta</td>
            <td>{{ $hasilTes['jenis_peserta'] }}</td>
            <td class="label-cell">Departemen</td>
            <td>{{ $hasilTes['departemen'] ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Posisi</td>
            <td>{{ $hasilTes['posisi'] ?? '—' }}</td>
            <td class="label-cell">Nama Sesi</td>
            <td>{{ $namaSesi }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tanggal Pengerjaan</td>
            <td>{{ $tglId($hasilTes['tanggal_pengerjaan']) }}</td>
            <td class="label-cell">Tujuan Tes</td>
            <td>{{ $tujuanTes }}</td>
        </tr>
    </table>

    {{-- Ringkasan Alat Tes --}}
    <div class="section-title">Ringkasan Alat Tes</div>
    <table>
        <thead class="th-row">
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:40%;">Nama Alat Tes</th>
                <th style="width:20%;">Format</th>
                <th style="width:15%;">Jumlah Dimensi</th>
                <th style="width:20%;">Durasi Aktual</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hasilTes['hasil_alat_tes'] as $i => $alat)
            <tr>
                <td class="mark">{{ $i + 1 }}</td>
                <td>{{ $alat['nama_alat_tes'] }}</td>
                <td>{{ $alat['format_dasar'] ?? '—' }}</td>
                <td class="mark">{{ is_countable($alat['skor_ringkas'] ?? []) ? count($alat['skor_ringkas']) : 0 }}</td>
                <td>{{ $alat['durasi_pengerjaan_aktual'] ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#94a3b8; font-style:italic;">Belum ada hasil tes</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Psikogram (hanya bila ada aspek yang dipetakan ke bidang laporan) --}}
    @php $adaPsikogram = collect($psikogram)->except('Narasi_Evaluasi')->filter()->isNotEmpty(); @endphp
    @if ($adaPsikogram)
    <div class="section-title">Psikogram</div>

    @foreach (['Intelektual', 'Sikap Kerja', 'Kepribadian', 'Potensi Kerja', 'Sensitif'] as $bidang)
        @if (!empty($psikogram[$bidang]))
            @php
                switch ($bidang) {
                    case 'Intelektual':  $huruf = 'A'; $namaDisplay = 'BIDANG INTELEKTUAL'; break;
                    case 'Sikap Kerja':  $huruf = 'B'; $namaDisplay = 'BIDANG SIKAP KERJA'; break;
                    case 'Kepribadian':  $huruf = 'C'; $namaDisplay = 'BIDANG KEPRIBADIAN'; break;
                    case 'Potensi Kerja':$huruf = 'D'; $namaDisplay = 'BIDANG POTENSI KERJA'; break;
                    case 'Sensitif':     $huruf = 'E'; $namaDisplay = 'BIDANG SENSITIF';   break;
                }
            @endphp
            <div class="sub-title">{{ $huruf }}. {{ $namaDisplay }}</div>

            @if ($bidang !== 'Sensitif')
                <table>
                    <thead class="th-row">
                        <tr>
                            <th style="width:50%;">Aspek</th>
                            <th class="mark" style="width:10%;">R</th>
                            <th class="mark" style="width:10%;">K</th>
                            <th class="mark" style="width:10%;">C</th>
                            <th class="mark" style="width:10%;">B</th>
                            <th class="mark" style="width:10%;">BS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($psikogram[$bidang] as $aspek)
                        <tr>
                            <td style="padding:3px 6px;">
                                <strong>{{ $aspek['nama_dimensi'] }}</strong><br>
                                <span style="font-size:9px;color:#64748b;">{{ $aspek['deskripsi_aspek'] ?? '' }}</span>
                            </td>
                            <td class="mark">{!! $aspek['kategori_hasil'] === 'R' ? '&#10003;' : '&bull;' !!}</td>
                            <td class="mark">{!! $aspek['kategori_hasil'] === 'K' ? '&#10003;' : '&bull;' !!}</td>
                            <td class="mark">{!! $aspek['kategori_hasil'] === 'C' ? '&#10003;' : '&bull;' !!}</td>
                            <td class="mark">{!! $aspek['kategori_hasil'] === 'B' ? '&#10003;' : '&bull;' !!}</td>
                            <td class="mark">{!! $aspek['kategori_hasil'] === 'BS' ? '&#10003;' : '&bull;' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                @if ($bisaLihatSensitif)
                    <table>
                        <thead class="th-row">
                            <tr>
                                <th style="width:50%;">Aspek</th>
                                <th class="mark" style="width:25%;">Skor T</th>
                                <th>Interpretasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($psikogram[$bidang] as $aspek)
                            <tr>
                                <td style="padding:3px 6px;">
                                    <strong>{{ $aspek['nama_dimensi'] }}</strong><br>
                                    <span style="font-size:9px;color:#64748b;">{{ $aspek['deskripsi_aspek'] ?? '' }}</span>
                                </td>
                                <td class="mark">{{ $aspek['skor_t'] ?? '-' }}</td>
                                <td>{{ $aspek['kategori_hasil'] ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="access-denied">
                        Anda tidak memiliki akses untuk melihat aspek psikogram yang bersumber dari data tes sensitif.
                        Hubungi Super Admin atau Admin HR untuk informasi lebih lanjut.
                    </div>
                @endif
            @endif
        @endif
    @endforeach

    <div class="footnote">
        <strong>Keterangan Skala:</strong> R = Rendah &middot; K = Kurang &middot; C = Cukup &middot; B = Baik &middot; BS = Baik Sekali.
        Skor mentah dan rincian per alat tes tersedia di Lampiran.
    </div>

    @endif

    @include('admin.hasil-tes.partials.ringkasan')

</div><!-- /.dokumen (hal 1) -->

<div class="page-break"></div>

<div class="dokumen">

    {{-- ═══════════════════════════════════════════════════
        HALAMAN 2+ — LAMPIRAN PER INSTRUMEN
    ═══════════════════════════════════════════════════ --}}

    <div class="lam-header">
        <div class="header-left">
            <img src="{{ url('images/logo.png') }}" alt="Logo" class="header-logo">
            <div>
                <div class="header-title">PT Jhonlin Group</div>
                <div class="header-sub">Departemen Human Capital</div>
            </div>
        </div>
        <span class="rahasia-badge">RAHASIA</span>
    </div>

    <div class="lam-title">LAMPIRAN &mdash; HASIL PER INSTRUMEN</div>

    @foreach ($hasilTes['hasil_alat_tes'] as $index => $alat)

        {{-- EPPS --}}
        @if (str_contains(strtoupper($alat['nama_alat_tes']), 'EPPS') && !empty($alat['skor_ringkas']))
            <div class="section-title">{{ $index + 1 }}. {{ $alat['nama_alat_tes'] }} &ndash; {{ $alat['format_dasar'] }}</div>
            @include('admin.hasil-tes.partials.epps', ['alatTes' => $alat])

        {{-- CFIT --}}
        @elseif (str_contains($alat['nama_alat_tes'], 'CFIT') && !empty($alat['skor_ringkas']))
            @php $s = $alat['skor_ringkas'][0]; @endphp
            <div class="section-title">{{ $index + 1 }}. {{ $alat['nama_alat_tes'] }} &ndash; {{ $alat['format_dasar'] }}</div>
            <table style="border:none; margin-bottom:0;">
                <tr style="border:none;">
                    <td style="width:80px; text-align:center; background:#eff6ff; border:1px solid #bfdbfe; padding:10px; border:none; vertical-align:middle;">
                        <div style="font-size:9px; font-weight:700; color:#1d4ed8; letter-spacing:.5px;">SKOR IQ</div>
                        <div style="font-size:28px; font-weight:800; color:#1e3a5f; line-height:1.1;">{{ $s['skor_skala'] ?? '?' }}</div>
                    </td>
                    <td style="padding-left:12px; font-size:10px; color:#334155; border:none; vertical-align:middle;">
                        <p>Skor Mentah: <strong>{{ $s['skor_mentah'] ?? '—' }}</strong></p>
                        @if (($s['kategori'] ?? '') !== '—')
                        <p style="margin-top:4px;">Kategori: <strong>{{ $s['kategori'] }}</strong></p>
                        @endif
                    </td>
                </tr>
            </table>

        {{-- Kraepelin / Grid --}}
        @elseif ($alat['format_dasar'] === 'Grid' && ($gridRingkasan = $alat['grid_ringkasan'] ?? null))
            <div class="section-title">{{ $index + 1 }}. {{ $alat['nama_alat_tes'] }} &ndash; {{ $alat['format_dasar'] }}</div>
            @include('admin.hasil-tes.partials.kraepelin', ['alatTes' => $alat])

        {{-- PAPIKOSTIK --}}
        @elseif (str_contains(strtoupper($alat['nama_alat_tes']), 'PAPIKOSTIK'))
            <div class="section-title">{{ $index + 1 }}. {{ $alat['nama_alat_tes'] }} &ndash; {{ $alat['format_dasar'] }}</div>
            @include('admin.hasil-tes.partials.papi', ['alatTes' => $alat])

        {{-- IST --}}
        @elseif (str_contains(strtoupper($alat['nama_alat_tes']), 'IST'))
            <div class="section-title">{{ $index + 1 }}. {{ $alat['nama_alat_tes'] }} &ndash; {{ $alat['format_dasar'] }}</div>
            @include('admin.hasil-tes.partials.ist', ['alatTes' => $alat])

        {{-- Generic --}}
        @elseif (!empty($alat['skor_ringkas']))
            <div class="section-title">{{ $index + 1 }}. {{ $alat['nama_alat_tes'] }} &ndash; {{ $alat['format_dasar'] }}</div>
            <table>
                <thead class="th-row">
                    <tr>
                        <th style="width:50%;">Dimensi</th>
                        <th class="mark" style="width:20%;">Skor Mentah</th>
                        <th class="mark" style="width:20%;">Skor Akhir</th>
                        <th style="width:10%;">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alat['skor_ringkas'] as $sk)
                    <tr>
                        <td style="padding:3px 6px;">{{ $sk['dimensi'] }}</td>
                        <td class="mark">{{ $sk['skor_mentah'] ?? '—' }}</td>
                        <td class="mark">{{ $sk['skor_skala'] ?? '—' }}</td>
                        <td>{{ $sk['kategori'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    @endforeach

</div><!-- /.dokumen (lampiran) -->

</body>
</html>
