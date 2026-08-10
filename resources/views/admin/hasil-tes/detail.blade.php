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
        .halaman-wrapper { padding: 0; margin: 0; }
        .dokumen-page { border: none; margin: 0; box-shadow: none; page-break-after: always; }
    }
    .halaman-wrapper {
        background: #e9ecef;
        padding: 24px 0;
        margin: -24px;
    }
    .dokumen-page {
        width: 210mm;
        height: 297mm;
        margin: 0 auto 20px auto;
        padding: 20mm;
        background: white;
        border: 1px solid #ccc;
        box-sizing: border-box;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
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
    /* repeat header on page 2 — smaller */
    .page2-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
    .page2-header .company-name { font-size: 12px; }
    .page2-header .company-sub { font-size: 9px; }
    .page2-header .rahasia-badge { font-size: 8px; padding: 2px 6px; }
    .page2-title { font-weight: 700; font-size: 11px; letter-spacing: 0.5px; margin-bottom: 10px; text-align: center; }
</style>

<div class="halaman-wrapper">

    <div style="max-width: 210mm; margin: 0 auto 12px auto; display: flex; justify-content: flex-end;">
        <a href="{{ route('admin.hasil-tes.exportPdf', [$sesi->id, $hasilTes['peserta_id']]) }}"
           class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white
                  shadow-sm hover:bg-[#234853] inline-block">Cetak PDF</a>
    </div>

    {{-- ========== HALAMAN 1 ========== --}}
    <div class="dokumen-page">
        {{-- Header --}}
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
                            <div class="access-denied">Anda tidak memiliki akses untuk melihat aspek psikogram yang bersumber dari data tes sensitif. Hubungi Super Admin atau Admin HR untuk informasi lebih lanjut.</div>
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
    </div>
    {{-- END HALAMAN 1 --}}

    {{-- ========== HALAMAN 2 ========== --}}
    <div class="dokumen-page">
        {{-- Repeat Header (small) --}}
        <div class="page2-header">
            <div style="display:flex; align-items:center; gap:8px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="company-logo" style="width:28px;height:28px;">
                <div>
                    <div class="company-name">PT Jhonlin Group</div>
                    <div class="company-sub">Departemen Human Capital</div>
                </div>
            </div>
            <div class="rahasia-badge">DATA BERSIFAT RAHASIA</div>
        </div>
        <div class="page2-title">LAMPIRAN &mdash; HASIL PER INSTRUMEN</div>

        {{-- HASIL PER INSTRUMEN --}}
        @foreach ($hasilTes['hasil_alat_tes'] as $index => $alatTes)
            <div class="container-formal">
                <div class="sub-title">{{ $index + 1 }}. {{ $alatTes['nama_alat_tes'] }} &ndash; {{ $alatTes['format_dasar'] }}</div>

                {{-- EPPS: Skor per dimensi --}}
                @if ($alatTes['nama_alat_tes'] === 'EPPS' && is_array($alatTes['skor_ringkas']) && isset($alatTes['skor_ringkas'][0]['dimensi']))
                    <p style="margin:6px 0 10px 0; color:#666;font-size:10px;">Format: Forced Choice &ndash; Skor Mentah (1-100), Skor Skala (1-10)</p>
                    <table>
                        <thead class="th-row"><tr><th style="width:40%;">Dimensi</th><th style="width:25%;">Skor Mentah</th><th style="width:25%;">Skor Skala</th><th>Kategori</th></tr></thead>
                        <tbody>
                            @foreach ($alatTes['skor_ringkas'] as $dimensi)
                            <tr><td>{{ $dimensi['dimensi'] }}</td><td class="mark">{{ $dimensi['skor_mentah'] }}</td><td class="mark">{{ $dimensi['skor_skala'] }}</td><td>{{ $dimensi['kategori'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>

                {{-- CFIT: tampilkan skor IQ tunggal --}}
                @elseif (str_contains($alatTes['nama_alat_tes'], 'CFIT') && !empty($alatTes['skor_ringkas']))
                    @php $skorCfit = $alatTes['skor_ringkas'][0] ?? null; @endphp
                    @if ($skorCfit)
                        <div style="display:flex; align-items:center; gap:20px; padding:10px; background:#f0f6ff; border:1px solid #b8d4f0; border-radius:4px;">
                            <div style="text-align:center;">
                                <p style="font-size:11px; font-weight:700; color:#1a5696; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Skor IQ</p>
                                <p style="font-size:32px; font-weight:700; color:#0a2463; line-height:1;">{{ $skorCfit['skor_skala'] }}</p>
                            </div>
                            <div style="flex:1; font-size:12px; color:#333;">
                                <p>Skor Mentah: <strong>{{ $skorCfit['skor_mentah'] }}</strong></p>
                                @if ($skorCfit['kategori'] !== '—')
                                <p style="margin-top:4px;">Kategori: <strong>{{ $skorCfit['kategori'] }}</strong></p>
                                @endif
                            </div>
                        </div>
                    @endif

                {{-- Papikostik & alat tes lain: tabel skor per dimensi --}}
                @elseif (!empty($alatTes['skor_ringkas']))
                    <table>
                        <thead class="th-row"><tr><th style="width:50%;">Dimensi</th><th style="width:20%; text-align:center;">Skor Mentah</th><th style="width:20%; text-align:center;">Skor Akhir</th><th style="width:10%; text-align:center;">Kategori</th></tr></thead>
                        <tbody>
                            @foreach ($alatTes['skor_ringkas'] as $skor)
                            <tr style="border-bottom:1px solid #e0e3e5;">
                                <td style="padding:3px 5px;">{{ $skor['dimensi'] }}</td>
                                <td style="padding:3px 5px; text-align:center;">{{ $skor['skor_mentah'] }}</td>
                                <td style="padding:3px 5px; text-align:center;">{{ $skor['skor_skala'] }}</td>
                                <td style="padding:3px 5px; text-align:center;">{{ $skor['kategori'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach

        {{-- CATATAN & REKOMENDASI HR --}}
        <div class="container-formal">
            <div class="section-title">CATATAN & REKOMENDASI HR</div>

            @if(session('sukses'))
                <div style="background:#f0fdf4; border:1px solid #86efac; padding:8px 12px; border-radius:6px; color:#166534; font-size:11px; margin-bottom:10px;">
                    {{ session('sukses') }}
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.hasil-tes.simpanCatatan', [$sesi->id, $hasilTes['peserta_id']]) }}">
                @csrf
                <textarea name="catatan_hr" rows="4"
                          placeholder="Masukkan catatan atau rekomendasi HR di sini..."
                          style="width:100%; border:1px solid #cbd5e1; border-radius:4px; padding:8px 10px; font-size:11px; resize:none; font-family:inherit; box-sizing:border-box;">{{ old('catatan_hr', $hasilTes['catatan_hr'] ?? '') }}</textarea>
                <div style="margin-top:8px;">
                    <button type="submit"
                            style="background:#2C5F6F; color:white; border:none; padding:8px 16px; font-size:11px; font-weight:600; border-radius:4px; cursor:pointer;">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- END HALAMAN 2 --}}

</div>

@endsection
