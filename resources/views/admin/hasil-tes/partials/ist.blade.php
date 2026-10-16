{{--
    Hasil IST format standar: tabel skor subtes (RW, SW, kategori), profil SW, dan taraf kecerdasan (IQ).
    Dipakai oleh detail, print, dan pdf. Grafik berupa SVG di <img> agar tampil sama di browser dan dompdf.
    Patokan: Sofware IST.xlsx (norma per usia + "Norma IQ"); kategori & klasifikasi IQ: IST_Norma_Usia.xls.
--}}
@php
    $_skor = collect($alatTes['skor_ringkas'] ?? [])->keyBy(fn ($s) => trim(explode(' - ', $s['dimensi'] ?? '')[0]));
    $_iq = (float) ($_skor['IQ']['skor_skala'] ?? 0);
    $_kat = ['SR' => 'Sangat Rendah', 'R' => 'Rendah', 'C' => 'Sedang', 'T' => 'Tinggi', 'ST' => 'Sangat Tinggi'];
    $_warna = ['SR' => '#b91c1c', 'R' => '#d97706', 'C' => '#64748b', 'T' => '#15803d', 'ST' => '#166534'];
    $_norma = preg_match('/USIA_(\d+)_(\d+)/', $alatTes['kelompok_norma'] ?? '', $_m) ? "usia {$_m[1]}–{$_m[2]} tahun" : '-';
    $_subtes = subtes_ist();

    // Profil SW (SVG): sumbu x = 9 subtes, sumbu y = SW 60–140; pita 95–104 = Sedang, garis 100 = rata-rata
    [$_w, $_h, $_l, $_r, $_t, $_b, $_min, $_max] = [680, 220, 34, 16, 14, 26, 60, 140];
    $_px = fn ($i) => $_l + 24 + $i * ($_w - $_l - $_r - 48) / 8;
    $_py = fn ($v) => $_t + ($_h - $_t - $_b) * (1 - (max($_min, min($_max, $v)) - $_min) / ($_max - $_min));
    $_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$_w.'" height="'.$_h.'" viewBox="0 0 '.$_w.' '.$_h.'" font-family="DejaVu Sans, Arial, sans-serif" font-size="9">';
    $_svg .= '<rect x="0" y="0" width="'.$_w.'" height="'.$_h.'" fill="#ffffff"/>';
    $_svg .= '<rect x="'.$_l.'" y="'.$_py(104).'" width="'.($_w - $_l - $_r).'" height="'.($_py(95) - $_py(104)).'" fill="#e2e8f0"/>';
    for ($_g = $_min; $_g <= $_max; $_g += 10) {
        $_svg .= '<line x1="'.$_l.'" y1="'.$_py($_g).'" x2="'.($_w - $_r).'" y2="'.$_py($_g).'" stroke="#eef2f6" stroke-width="1"/>';
        $_svg .= '<text x="'.($_l - 6).'" y="'.($_py($_g) + 3).'" text-anchor="end" fill="#64748b">'.$_g.'</text>';
    }
    $_svg .= '<line x1="'.$_l.'" y1="'.$_py(100).'" x2="'.($_w - $_r).'" y2="'.$_py(100).'" stroke="#94a3b8" stroke-width="1" stroke-dasharray="4,3"/>';
    $_titik = [];
    foreach (array_keys($_subtes) as $_i => $_k) {
        $_svg .= '<text x="'.$_px($_i).'" y="'.($_h - 8).'" text-anchor="middle" fill="#334155" font-weight="bold">'.$_k.'</text>';
        if ($_s = $_skor->get($_k)) {
            $_titik[] = round($_px($_i), 1).','.round($_py((float) $_s['skor_skala']), 1);
        }
    }
    $_svg .= '<polyline fill="none" stroke="#00843D" stroke-width="1.8" points="'.implode(' ', $_titik).'"/>';
    foreach (array_keys($_subtes) as $_i => $_k) {
        if ($_s = $_skor->get($_k)) {
            $_sw = (float) $_s['skor_skala'];
            $_svg .= '<circle cx="'.round($_px($_i), 1).'" cy="'.round($_py($_sw), 1).'" r="3" fill="'.($_warna[kategori_subtes_ist($_sw)] ?? '#00843D').'"/>';
            $_svg .= '<text x="'.round($_px($_i), 1).'" y="'.round($_py($_sw) - 7, 1).'" text-anchor="middle" fill="#1f2937">'.(int) $_sw.'</text>';
        }
    }
    $_svg .= '</svg>';

    $_th = 'background:#4b5563;color:#fff;font-weight:700;border:1px solid #000;padding:4px 5px;font-size:9.5px;';
    $_td = 'border:1px solid #000;padding:3px 6px;font-size:9.5px;vertical-align:middle;';
@endphp

<p style="margin:4px 0 8px 0;color:#555;font-size:9.5px;">
    RW = jumlah jawaban benar (GE: nilai konversi dari skor 0/1/2), SW = skor standar (rata-rata 100) menurut norma
    <strong>{{ $_norma }}</strong>. Kategori SW: ≤80 Sangat Rendah &middot; 81–94 Rendah &middot; 95–104 Sedang &middot; 105–118 Tinggi &middot; ≥119 Sangat Tinggi.
</p>

{{-- A. SKOR SUBTES --}}
<div style="font-weight:700;font-size:10.5px;margin:6px 0 4px;">A. Skor Subtes</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">
    <thead>
        <tr>
            <th style="{{ $_th }}width:4%;text-align:center;">No</th>
            <th style="{{ $_th }}width:27%;text-align:left;">Subtes</th>
            <th style="{{ $_th }}text-align:left;">Aspek yang Diukur</th>
            <th style="{{ $_th }}width:6%;text-align:center;">RW</th>
            <th style="{{ $_th }}width:6%;text-align:center;">SW</th>
            <th style="{{ $_th }}width:13%;text-align:center;">Kategori</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($_subtes as $_k => [$_nama, $_aspek, $_kelompok])
            @php $_s = $_skor->get($_k); $_kk = $_s ? kategori_subtes_ist((float) $_s['skor_skala']) : ''; @endphp
            <tr>
                <td style="{{ $_td }}text-align:center;">{{ $loop->iteration }}</td>
                <td style="{{ $_td }}"><strong>{{ $_k }}</strong> &middot; {{ $_nama }}</td>
                <td style="{{ $_td }}color:#333;">{{ $_aspek }} <span style="color:#888;font-size:8.5px;">({{ $_kelompok }})</span></td>
                <td style="{{ $_td }}text-align:center;">{{ $_s ? (int) $_s['skor_mentah'] : '-' }}</td>
                <td style="{{ $_td }}text-align:center;font-weight:700;">{{ $_s ? (int) $_s['skor_skala'] : '-' }}</td>
                <td style="{{ $_td }}text-align:center;font-weight:700;color:{{ $_warna[$_kk] ?? '#333' }};">{{ $_kat[$_kk] ?? '-' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" style="{{ $_td }}background:#f2f2f2;font-weight:700;text-align:right;">Jumlah RW &rarr; IQ</td>
            <td style="{{ $_td }}background:#f2f2f2;text-align:center;font-weight:700;">{{ (int) ($_skor['IQ']['skor_mentah'] ?? 0) }}</td>
            <td style="{{ $_td }}background:#f2f2f2;text-align:center;font-weight:700;color:#00843D;">{{ $_iq > 0 ? number_format($_iq, 0) : '-' }}</td>
            <td style="{{ $_td }}background:#f2f2f2;text-align:center;font-weight:700;">{{ klasifikasi_iq($_iq) }}</td>
        </tr>
    </tbody>
</table>

{{-- B. PROFIL --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">B. Profil IST (Skor Standar)</div>
<div style="border:1px solid #cbd5e1;padding:4px;text-align:center;">
    <img src="data:image/svg+xml;base64,{{ base64_encode($_svg) }}" alt="Profil IST" style="width:100%;max-width:{{ $_w }}px;height:auto;">
</div>
<div style="font-size:8.5px;color:#666;margin:3px 0 8px;">Area abu-abu = rentang Sedang (SW 95–104); garis putus-putus = rata-rata (SW 100). Titik di atas area = kemampuan di atas rata-rata kelompok usianya.</div>

{{-- C. TARAF KECERDASAN --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">C. Taraf Kecerdasan</div>
<table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="border:none;padding:0 10px 0 0;width:52%;vertical-align:top;">
            <table style="width:100%;border-collapse:collapse;">
                <tr><th style="{{ $_th }}text-align:center;width:35%;">IQ</th><th style="{{ $_th }}text-align:left;">Klasifikasi</th></tr>
                @foreach ([['≥ 140', 'Genius'], ['128 – 139', 'Very Superior'], ['120 – 127', 'Superior'], ['111 – 119', 'High Average (Di Atas Rata-rata)'], ['91 – 110', 'Average (Rata-rata)'], ['80 – 90', 'Low Average (Di Bawah Rata-rata)'], ['66 – 79', 'Borderline'], ['≤ 65', 'Mentally Defective']] as [$_rentang, $_label])
                    @php $_aktif = $_iq > 0 && klasifikasi_iq($_iq) === $_label; @endphp
                    <tr>
                        <td style="{{ $_td }}text-align:center;{{ $_aktif ? 'background:#dcfce7;font-weight:700;' : '' }}">{{ $_rentang }}</td>
                        <td style="{{ $_td }}{{ $_aktif ? 'background:#dcfce7;font-weight:700;' : '' }}">{{ $_label }}</td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td style="border:2px solid #9ca3af;padding:14px;vertical-align:middle;text-align:center;font-size:11px;line-height:1.7;">
            Taraf kecerdasan umum peserta tergolong<br>
            <strong style="font-size:13px;color:#00843D;">{{ klasifikasi_iq($_iq) }}</strong><br>
            dengan <strong>IQ {{ $_iq > 0 ? number_format($_iq, 0) : '-' }}</strong><br>
            <span style="font-size:9px;color:#666;">skala IST, norma {{ $_norma }}</span>
        </td>
    </tr>
</table>
