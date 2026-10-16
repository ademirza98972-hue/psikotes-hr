{{--
    Hasil PAPI Kostick format standar: skor 20 faktor per aspek + interpretasi (Papi Kostick.xls),
    roda profil (SVG, tampil sama di browser & dompdf), dan kontrol jumlah atas/bawah (masing-masing 45).
--}}
@php
    $_skor = collect($alatTes['skor_ringkas'] ?? [])->keyBy(fn ($s) => trim(explode(' - ', $s['dimensi'] ?? '')[0]));
    $_faktor = faktor_papi();
    $_nilai = collect($_faktor)->map(fn ($f) => isset($_skor[$f[0]]) ? (int) $_skor[$f[0]]['skor_mentah'] : null);
    // Papi Kostick.xls: "Total Atas" (G L I T V S R D C E) dan "Total Bawah" (N A P X B O Z K F W) harus 45
    $_atas = $_nilai->only(['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E'])->sum();
    $_bawah = $_nilai->only(['N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'])->sum();

    // Skor > 9 mustahil (tiap faktor muncul 9 kali) → tanda data jawaban rusak
    $_lebih = $_nilai->filter(fn ($v) => $v !== null && $v > 9);

    // Roda profil PAPI: 20 faktor @ 18°, dikelompokkan 7 sektor aspek; cincin 1–9 dari pusat
    $_warnaAspek = [
        'Arah Kerja' => '#1d4ed8', 'Kepemimpinan' => '#7c3aed', 'Aktivitas' => '#ea580c', 'Pergaulan' => '#0e7490',
        'Gaya Kerja' => '#15803d', 'Sifat' => '#b91c1c', 'Ketaatan' => '#a16207',
    ];
    [$_W, $_H, $_cx, $_cy, $_R] = [620, 560, 310, 280, 172];
    $_pt = fn ($deg, $r) => [round($_cx + $r * sin(deg2rad($deg)), 1), round($_cy - $r * cos(deg2rad($deg)), 1)];
    $_huruf = array_keys($_faktor);
    $_rv = fn ($v) => $_R * max(0, min(9, (float) $v)) / 9;

    $_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$_W.'" height="'.$_H.'" viewBox="0 0 '.$_W.' '.$_H.'" font-family="DejaVu Sans, Arial, sans-serif">';
    $_svg .= '<rect x="0" y="0" width="'.$_W.'" height="'.$_H.'" fill="#ffffff"/>';

    // sektor aspek: isian tipis di dalam lingkaran + pita warna di tepi + nama aspek di luar
    foreach (collect($_faktor)->groupBy(fn ($f) => $f[2], true) as $_aspek => $_grup) {
        $_i0 = array_search(array_key_first($_grup->all()), $_huruf);
        $_i1 = array_search(array_key_last($_grup->all()), $_huruf);
        [$_a0, $_a1] = [($_i0 - 0.5) * 18, ($_i1 + 0.5) * 18];
        $_besar = ($_a1 - $_a0) > 180 ? 1 : 0;
        $_c = $_warnaAspek[$_aspek];
        [$_x0, $_y0] = $_pt($_a0, $_R);
        [$_x1, $_y1] = $_pt($_a1, $_R);
        $_svg .= '<path d="M'.$_cx.','.$_cy.' L'.$_x0.','.$_y0.' A'.$_R.','.$_R.' 0 '.$_besar.' 1 '.$_x1.','.$_y1.' Z" fill="'.$_c.'" fill-opacity="0.06" stroke="#cbd5e1" stroke-width="1"/>';
        [$_r1, $_r2] = [$_R + 26, $_R + 34];
        [$_p0x, $_p0y] = $_pt($_a0 + 0.8, $_r1);
        [$_p1x, $_p1y] = $_pt($_a0 + 0.8, $_r2);
        [$_p2x, $_p2y] = $_pt($_a1 - 0.8, $_r2);
        [$_p3x, $_p3y] = $_pt($_a1 - 0.8, $_r1);
        $_svg .= '<path d="M'.$_p0x.','.$_p0y.' L'.$_p1x.','.$_p1y.' A'.$_r2.','.$_r2.' 0 '.$_besar.' 1 '.$_p2x.','.$_p2y.' L'.$_p3x.','.$_p3y.' A'.$_r1.','.$_r1.' 0 '.$_besar.' 0 '.$_p0x.','.$_p0y.' Z" fill="'.$_c.'"/>';
        $_mid = ($_a0 + $_a1) / 2;
        [$_tx, $_ty] = $_pt($_mid, $_R + 50);
        $_anchor = abs(sin(deg2rad($_mid))) < 0.25 ? 'middle' : (sin(deg2rad($_mid)) > 0 ? 'start' : 'end');
        $_svg .= '<text x="'.$_tx.'" y="'.($_ty + 4).'" text-anchor="'.$_anchor.'" font-size="11" font-weight="bold" fill="'.$_c.'">'.strtoupper($_aspek).'</text>';
    }

    // cincin skala 1–9 (5 dan 9 lebih tegas) + angka skala di celah W–N
    for ($_g = 1; $_g <= 9; $_g++) {
        $_svg .= '<circle cx="'.$_cx.'" cy="'.$_cy.'" r="'.round($_R * $_g / 9, 1).'" fill="none" stroke="'.(in_array($_g, [5, 9]) ? '#94a3b8' : '#e2e8f0').'" stroke-width="'.($_g === 9 ? 1.4 : 1).'"/>';
        if ($_g % 2 === 1) {
            [$_lx, $_ly] = $_pt(-9, $_R * $_g / 9);
            $_svg .= '<text x="'.$_lx.'" y="'.($_ly + 3).'" text-anchor="middle" font-size="8" fill="#94a3b8">'.$_g.'</text>';
        }
    }

    // jari-jari & huruf faktor
    foreach ($_huruf as $_i => $_h) {
        [$_x, $_y] = $_pt($_i * 18, $_R);
        $_svg .= '<line x1="'.$_cx.'" y1="'.$_cy.'" x2="'.$_x.'" y2="'.$_y.'" stroke="#e2e8f0" stroke-width="1"/>';
        [$_lx, $_ly] = $_pt($_i * 18, $_R + 14);
        $_svg .= '<text x="'.$_lx.'" y="'.($_ly + 4).'" text-anchor="middle" font-size="12" font-weight="bold" fill="#1f2937">'.$_h.'</text>';
    }

    // profil peserta (skor di atas 9 dibatasi di cincin terluar dan ditandai merah)
    $_poly = collect($_huruf)->map(fn ($h, $i) => implode(',', $_pt($i * 18, $_rv($_nilai[$h] ?? 0))))->implode(' ');
    $_svg .= '<polygon points="'.$_poly.'" fill="#00843D" fill-opacity="0.22" stroke="#00843D" stroke-width="2" stroke-linejoin="round"/>';
    foreach ($_huruf as $_i => $_h) {
        $_v = $_nilai[$_h] ?? 0;
        [$_x, $_y] = $_pt($_i * 18, $_rv($_v));
        $_svg .= '<circle cx="'.$_x.'" cy="'.$_y.'" r="3.2" fill="#ffffff" stroke="'.($_v > 9 ? '#b91c1c' : '#00843D').'" stroke-width="1.8"/>';
        [$_vx, $_vy] = $_pt($_i * 18, $_rv($_v) >= $_R / 2 ? $_rv($_v) - 12 : $_rv($_v) + 12);
        $_svg .= '<text x="'.$_vx.'" y="'.($_vy + 3).'" text-anchor="middle" font-size="9" font-weight="bold" fill="'.($_v > 9 ? '#b91c1c' : '#14532d').'">'.$_v.'</text>';
    }
    $_svg .= '</svg>';

    $_th = 'background:#4b5563;color:#fff;font-weight:700;border:1px solid #000;padding:4px 5px;font-size:9.5px;';
    $_td = 'border:1px solid #000;padding:3px 6px;font-size:9.5px;vertical-align:middle;';
@endphp

<p style="margin:4px 0 8px 0;color:#555;font-size:9.5px;">
    Skor tiap faktor 0–9 (jumlah pilihan pada 90 pasang pernyataan). Interpretasi mengikuti kolom Analisis Papi Kostick.xls.
</p>

{{-- A. SKOR & INTERPRETASI --}}
<div style="font-weight:700;font-size:10.5px;margin:6px 0 4px;">A. Skor & Interpretasi Faktor</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">
    <thead>
        <tr>
            <th style="{{ $_th }}width:27%;text-align:left;">Faktor</th>
            <th style="{{ $_th }}width:6%;text-align:center;">Skor</th>
            <th style="{{ $_th }}width:16%;text-align:left;">0 &nbsp;&middot;&middot;&middot;&nbsp; 9</th>
            <th style="{{ $_th }}text-align:left;">Interpretasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach (collect($_faktor)->groupBy(fn ($f) => $f[2], true) as $_kelompok => $_daftar)
            <tr><td colspan="4" style="{{ $_td }}background:#d9d9d9;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">{{ $_kelompok }}</td></tr>
            @foreach ($_daftar as $_h => [$_kode, $_nama])
                @php $_v = $_nilai[$_h]; @endphp
                <tr>
                    <td style="{{ $_td }}"><strong>{{ $_h }}</strong> &middot; {{ $_nama }}</td>
                    <td style="{{ $_td }}text-align:center;font-weight:700;{{ $_v > 9 ? 'color:#b91c1c;' : '' }}">{{ $_v ?? '-' }}</td>
                    <td style="{{ $_td }}padding:3px 4px;">
                        <div style="background:#f1f5f9;height:8px;">
                            <div style="background:#00843D;height:8px;width:{{ round(min(9, $_v ?? 0) / 9 * 100) }}%;"></div>
                        </div>
                    </td>
                    <td style="{{ $_td }}color:#333;line-height:1.35;">{{ $_v === null ? '-' : interpretasi_papi($_h, $_v) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

{{-- B. PROFIL --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">B. Profil PAPI Kostick</div>
<div style="border:1px solid #cbd5e1;padding:6px;text-align:center;">
    <img src="data:image/svg+xml;base64,{{ base64_encode($_svg) }}" alt="Profil PAPI Kostick" style="width:100%;max-width:{{ $_W }}px;height:auto;">
</div>
<div style="font-size:8.5px;color:#666;margin:3px 0 8px;">
    Cincin 1–9 dari pusat: makin jauh dari pusat makin kuat kebutuhan/peran pada faktor tersebut. Warna pita menunjukkan aspek.
</div>

{{-- C. KONTROL --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">C. Kontrol Jawaban</div>
@php $_valid = $_atas === 45 && $_bawah === 45 && $_lebih->isEmpty(); @endphp
<table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="{{ $_td }}background:#f2f2f2;font-weight:700;width:31%;">Total atas <span style="font-weight:400;color:#666;">(G L I T V S R D C E)</span></td>
        <td style="{{ $_td }}width:19%;text-align:center;"><strong>{{ $_atas }}</strong> / 45</td>
        <td style="{{ $_td }}background:#f2f2f2;font-weight:700;width:31%;">Total bawah <span style="font-weight:400;color:#666;">(N A P X B O Z K F W)</span></td>
        <td style="{{ $_td }}text-align:center;"><strong>{{ $_bawah }}</strong> / 45</td>
    </tr>
    <tr>
        <td colspan="4" style="{{ $_td }}color:{{ $_valid ? '#15803d' : '#b91c1c' }};">
            @if ($_valid)
                Lengkap — seluruh 90 soal terskor, profil dapat ditafsirkan.
            @else
                Hasil perlu diperiksa:
                {{ $_atas !== 45 || $_bawah !== 45 ? 'total atas/bawah tidak 45 (ada soal tidak terjawab/tersimpan).' : '' }}
                {{ $_lebih->isNotEmpty() ? 'Skor melebihi 9 pada faktor ' . $_lebih->map(fn ($v, $h) => "$h = $v")->implode(', ') . ' — data jawaban tidak valid.' : '' }}
            @endif
        </td>
    </tr>
</table>
