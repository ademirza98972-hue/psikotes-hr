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
    .header-row { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 14px; }
    .company-logo { width: 36px; height: 36px; border-radius: 0; }
    .company-name { font-weight: 700; font-size: 15px; margin: 0; }
    .company-sub { font-size: 10px; color: #444; margin: 2px 0 0 0; }
    .rahasia-badge { border: 1px solid #b91c1c; color: #b91c1c; font-size: 9px; font-weight: 700; padding: 3px 8px; letter-spacing: 0.5px; display: inline-block; white-space: nowrap; }
    .doc-title { text-align: center; font-weight: 700; font-size: 13px; letter-spacing: 0.5px; margin: 8px 0 4px 0; }
    .doc-sub { text-align: center; font-size: 10px; color: #666; margin-bottom: 14px; }
    .section-title { font-weight: 700; font-size: 11.5px; margin: 16px 0 8px 0; letter-spacing: 0.5px; text-transform: uppercase; }
    .sub-title { font-weight: 700; font-size: 10.5px; margin: 10px 0 4px 0; color: #333; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; }
    td, th { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; }
    .th-row td, .th-row th { background: #4b5563; color: #fff; font-weight: 700; }
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

<div style="max-width: 900px; margin: 0 auto; padding: 0 8px;">

    <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
        <a href="{{ route('admin.hasil-tes.print', [$sesi->id, $hasilTes['peserta_id']]) }}" target="_blank"
           class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white
                  shadow-sm hover:bg-primary/90 inline-block">Cetak PDF</a>
    </div>

    {{-- ========== HALAMAN 1 ========== --}}
    <div style="background: white; padding: 32px; margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 8px;">
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
        <div class="doc-sub">{{ $sesi['nama_sesi'] }} &middot; PT Jhonlin Group</div>

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
                    <td>{{ $hasilTes['tujuan_tes'] }}</td>
                </tr>
            </table>
        </div>


        {{-- PSIKOGRAM (hanya bila ada aspek yang dipetakan ke bidang laporan) --}}
        @php $adaPsikogram = collect($psikogram)->except('Narasi_Evaluasi')->filter()->isNotEmpty(); @endphp
        <div class="container-formal">
            @if ($adaPsikogram)
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
            @endif

            @include('admin.hasil-tes.partials.ringkasan')
        </div>
    </div>
    {{-- END HALAMAN 1 --}}

    {{-- ========== HALAMAN 2 ========== --}}
    <div style="background: white; padding: 32px; margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 8px;">
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

                {{-- EPPS: Psikogram per kelompok --}}
                @if (str_contains(strtoupper($alatTes['nama_alat_tes']), 'EPPS') && is_array($alatTes['skor_ringkas']) && count($alatTes['skor_ringkas']) > 0)
                    @include('admin.hasil-tes.partials.epps', ['alatTes' => $alatTes])

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

                {{-- Kraepelin — Grid: tampilan scoring ala Excel --}}
                @elseif ($alatTes['format_dasar'] === 'Grid' && ($gridRingkasan = $alatTes['grid_ringkasan'] ?? null))
                    @include('admin.hasil-tes.partials.kraepelin', ['alatTes' => $alatTes])

                @elseif (str_contains(strtoupper($alatTes['nama_alat_tes']), 'PAPIKOSTIK') && !empty($alatTes['skor_ringkas']))
                    @include('admin.hasil-tes.partials.papi', ['alatTes' => $alatTes])

                @elseif (str_contains(strtoupper($alatTes['nama_alat_tes']), 'IST') && !empty($alatTes['skor_ringkas']))
                    @include('admin.hasil-tes.partials.ist', ['alatTes' => $alatTes])

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


            <form method="POST"
                  action="{{ route('admin.hasil-tes.simpanCatatan', [$sesi->id, $hasilTes['peserta_id']]) }}">
                @csrf
                <textarea name="catatan_hr" rows="4"
                          placeholder="Masukkan catatan atau rekomendasi HR di sini..."
                          style="width:100%; border:1px solid #cbd5e1; border-radius:4px; padding:8px 10px; font-size:11px; resize:none; font-family:inherit; box-sizing:border-box;">{{ old('catatan_hr', $hasilTes['catatan_hr'] ?? '') }}</textarea>
                <div style="margin-top:8px;">
                    <button type="submit"
                            style="background:#00843D; color:white; border:none; padding:8px 16px; font-size:11px; font-weight:600; border-radius:4px; cursor:pointer;">
                        Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    @if($logMencurigakan->isNotEmpty())
    @php
    $labelKejadian = [
        'keluar_fullscreen' => 'Keluar Fullscreen',
        'pindah_tab'        => 'Pindah Tab',
        'halaman_ditutup'   => 'Halaman Ditutup / Dimuat Ulang',
    ];
    @endphp
    <div class="container-formal" style="border-left:3px solid #f59e0b;background:#fffbeb;margin-top:12px;">
        <div class="section-title" style="color:#b45309;">&#9888; CATATAN INTEGRITAS TES</div>
        <table>
            <thead>
                <tr class="th-row">
                    <th style="width:80px;">Waktu</th>
                    <th>Kejadian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logMencurigakan as $log)
                <tr>
                    <td style="font-variant-numeric:tabular-nums;">{{ $log->created_at->format('H:i:s') }}</td>
                    <td>{{ $labelKejadian[$log->tipe_kejadian] ?? $log->tipe_kejadian }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:10px;color:#92400e;margin-top:4px;">
            Total kejadian mencurigakan: {{ $logMencurigakan->count() }}
        </p>
    </div>
    @endif

    </div>
    {{-- END HALAMAN 2 --}}

@endsection
