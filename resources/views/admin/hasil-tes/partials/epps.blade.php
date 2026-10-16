{{--
    Hasil EPPS format standar: tabel skor (RS, persentil, kategori), grafik profil persentil,
    dan validitas (konsistensi + kontrol total 210). Dipakai oleh detail, print, dan pdf.
    Butuh: $alatTes['skor_ringkas'] berisi baris "kode - nama" dengan skor_mentah, skor_skala (persentil), kategori.
--}}
@use('App\Services\FormatHasilEppsService', 'Epps')
@php
    $_epps = collect($alatTes['skor_ringkas'] ?? [])
        ->keyBy(fn ($s) => strtolower(trim(explode(' - ', $s['dimensi'] ?? '')[0])));
    $_warna = ['Sangat Rendah' => '#b91c1c', 'Rendah' => '#d97706', 'Sedang' => '#64748b', 'Tinggi' => '#15803d', 'Sangat Tinggi' => '#166534'];
    $_totalRs = collect(Epps::NAMA_KEBUTUHAN)->keys()->sum(fn ($k) => (int) ($_epps[$k]['skor_mentah'] ?? 0));
    $_con = $_epps->get('con');
    $_th = 'background:#4b5563;color:#fff;font-weight:700;border:1px solid #000;padding:4px 5px;font-size:9.5px;';
    $_td = 'border:1px solid #000;padding:3px 5px;font-size:9.5px;vertical-align:middle;';
@endphp

<p style="margin:4px 0 8px 0;color:#555;font-size:9.5px;">
    Skor mentah (RS) 0–28 per kebutuhan, dikonversi ke persentil norma <em>General Adult</em>
    (pria/wanita sesuai jenis kelamin peserta).
    Kategori persentil: ≤3 Sangat Rendah &middot; 4–16 Rendah &middot; 17–84 Sedang &middot; 85–96 Tinggi &middot; ≥97 Sangat Tinggi.
</p>

{{-- A. TABEL SKOR --}}
<div style="font-weight:700;font-size:10.5px;margin:6px 0 4px;">A. Skor Kebutuhan</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">
    <thead>
        <tr>
            <th style="{{ $_th }}width:4%;text-align:center;">No</th>
            <th style="{{ $_th }}width:27%;text-align:left;">Aspek (Kebutuhan EPPS)</th>
            <th style="{{ $_th }}width:6%;text-align:center;">RS</th>
            <th style="{{ $_th }}width:8%;text-align:center;">Persentil</th>
            <th style="{{ $_th }}width:12%;text-align:center;">Kategori</th>
            <th style="{{ $_th }}text-align:left;">Interpretasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach (Epps::NAMA_KEBUTUHAN as $_kode => [$_aspek, $_asli])
            @php $_s = $_epps->get($_kode); $_kat = $_s['kategori'] ?? null; @endphp
            <tr>
                <td style="{{ $_td }}text-align:center;">{{ $loop->iteration }}</td>
                <td style="{{ $_td }}"><strong>{{ $_aspek }}</strong><br><span style="color:#666;font-size:8.5px;">{{ $_asli }} ({{ $_kode }})</span></td>
                <td style="{{ $_td }}text-align:center;">{{ $_s ? (int) $_s['skor_mentah'] : '-' }}</td>
                <td style="{{ $_td }}text-align:center;">{{ $_s ? (int) $_s['skor_skala'] : '-' }}</td>
                <td style="{{ $_td }}text-align:center;font-weight:700;color:{{ $_warna[$_kat] ?? '#333' }};">{{ $_kat ?? '-' }}</td>
                <td style="{{ $_td }}color:#333;line-height:1.35;">{{ $_s ? Epps::interpretasi($_kode, $_kat) : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- B. GRAFIK PROFIL --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">B. Profil Kebutuhan (Persentil)</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">
    <tr>
        <td style="width:27%;border:none;"></td>
        <td style="border:none;padding:0 0 2px;">
            <table style="width:100%;border-collapse:collapse;font-size:8px;color:#666;">
                <tr>
                    <td style="border:none;padding:0;width:16%;">0</td>
                    <td style="border:none;padding:0;width:34%;">16</td>
                    <td style="border:none;padding:0;width:34%;">50</td>
                    <td style="border:none;padding:0;width:12%;">84</td>
                    <td style="border:none;padding:0;text-align:right;">100</td>
                </tr>
            </table>
        </td>
        <td style="width:7%;border:none;"></td>
    </tr>
    @foreach (Epps::NAMA_KEBUTUHAN as $_kode => [$_aspek, $_asli])
        @php $_s = $_epps->get($_kode); $_p = max(0, min(100, (int) ($_s['skor_skala'] ?? 0))); @endphp
        <tr>
            <td style="border:none;padding:2px 6px 2px 0;font-size:9px;text-align:right;">{{ $_aspek }}</td>
            <td style="border:1px solid #cbd5e1;padding:0;background:#f8fafc;">
                {{-- pita 16–84 = rentang Sedang --}}
                <div style="position:relative;height:11px;">
                    <div style="position:absolute;left:16%;width:68%;top:0;height:11px;background:#e2e8f0;"></div>
                    <div style="position:absolute;left:0;top:2px;height:7px;width:{{ $_p }}%;background:{{ $_warna[$_s['kategori'] ?? ''] ?? '#94a3b8' }};"></div>
                </div>
            </td>
            <td style="border:none;padding:2px 0 2px 6px;font-size:9px;font-weight:700;">{{ $_s ? $_p : '-' }}</td>
        </tr>
    @endforeach
</table>
<div style="font-size:8.5px;color:#666;margin-bottom:8px;">Area abu-abu = rentang Sedang (persentil 17–84). Batang di kanan area = di atas rata-rata kelompok norma.</div>

{{-- C. VALIDITAS --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">C. Validitas Jawaban</div>
<table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="{{ $_td }}width:31%;background:#f2f2f2;font-weight:700;">Konsistensi (15 pasang soal ulangan)</td>
        <td style="{{ $_td }}">
            @if ($_con)
                <strong>{{ (int) $_con['skor_mentah'] }} / 15</strong> &middot; persentil {{ (int) $_con['skor_skala'] }} &middot;
                <strong style="color:{{ $_warna[$_con['kategori'] ?? ''] ?? '#333' }};">{{ $_con['kategori'] ?? '-' }}</strong><br>
                <span style="color:#333;">{{ Epps::interpretasi('con', $_con['kategori'] ?? null) }}</span>
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td style="{{ $_td }}background:#f2f2f2;font-weight:700;">Kontrol total skor</td>
        <td style="{{ $_td }}">
            {{ $_totalRs }} dari 210
            @if ($_totalRs === 210)
                <span style="color:#15803d;">&mdash; lengkap, seluruh 210 soal terskor.</span>
            @else
                <span style="color:#b91c1c;">&mdash; tidak 210: ada soal yang tidak terjawab, hasil perlu diperiksa.</span>
            @endif
        </td>
    </tr>
</table>
