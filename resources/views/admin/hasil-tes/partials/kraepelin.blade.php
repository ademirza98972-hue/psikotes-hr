{{--
    Hasil Kraepelin format standar: rekap skoring (kotak SCORING lembar jawab Kraepelin),
    grafik kerja + garis regresi, dan interpretasi 4 faktor. Dipakai oleh detail, print, dan pdf.
    Grafik berupa SVG di <img> agar tampil sama di browser dan dompdf (tanpa JavaScript).
--}}
@php
    $_skor = collect($alatTes['skor_ringkas'] ?? [])->keyBy(fn ($s) => trim(explode(' - ', $s['dimensi'] ?? '')[0]));
    $_rekap = $alatTes['grid_ringkasan'] ?? null;
    $_chart = $alatTes['grid_chart'] ?? [];
    $_hanker = $alatTes['grid_hanker'] ?? ['a' => 0, 'b' => 0, 'y0' => 0, 'y50' => 0, 'delta' => 0, 'trend' => '-'];
    $_angka = fn ($v, $d = 2) => rtrim(rtrim(number_format((float) $v, $d, ',', '.'), '0'), ',');
    $_warna = ['Rendah' => '#b91c1c', 'Kurang' => '#d97706', 'Cukup' => '#64748b', 'Baik' => '#15803d', 'Baik Sekali' => '#166534'];

    // Interpretasi umum tiap faktor Kraepelin per kategori
    $_faktor = [
        'KECEPATAN' => ['Kecepatan Kerja', 'Panker', 'Rata-rata jumlah benar per kolom', [
            'Rendah' => 'Tempo kerja lambat; membutuhkan waktu lebih lama dari rata-rata untuk menyelesaikan tugas rutin.',
            'Kurang' => 'Tempo kerja sedikit di bawah rata-rata; tugas rutin dapat diselesaikan namun belum cepat.',
            'Cukup' => 'Tempo kerja sedang; mampu menyelesaikan tugas rutin dengan kecepatan yang wajar.',
            'Baik' => 'Tempo kerja cepat; mampu menyelesaikan volume pekerjaan rutin di atas rata-rata.',
            'Baik Sekali' => 'Tempo kerja sangat cepat; produktivitas pada tugas rutin sangat tinggi.',
        ]],
        'KETELITIAN' => ['Ketelitian Kerja', 'Tianker', 'Jumlah salah + terlampaui (makin kecil makin teliti)', [
            'Baik Sekali' => 'Sangat teliti; kesalahan dan bagian yang terlewat sangat sedikit.',
            'Baik' => 'Teliti; kesalahan kerja relatif sedikit.',
            'Cukup' => 'Ketelitian cukup; sesekali membuat kesalahan sehingga hasil kerja perlu diperiksa ulang.',
            'Kurang' => 'Kurang teliti; kesalahan cukup banyak, perlu pengawasan pada pekerjaan yang menuntut akurasi.',
            'Rendah' => 'Tidak teliti; banyak kesalahan, kurang sesuai untuk pekerjaan yang menuntut akurasi tinggi.',
        ]],
        'KEAJEGAN' => ['Keajegan Kerja', 'Janker', 'Rata-rata simpangan dari Panker (makin kecil makin stabil)', [
            'Baik Sekali' => 'Ritme kerja sangat stabil; hasil kerja dari waktu ke waktu sangat konsisten.',
            'Baik' => 'Ritme kerja stabil; konsentrasi dan emosi terjaga selama bekerja.',
            'Cukup' => 'Ritme kerja cukup stabil dengan naik-turun yang wajar.',
            'Kurang' => 'Ritme kerja kurang stabil; hasil kerja naik-turun, konsentrasi mudah terganggu.',
            'Rendah' => 'Ritme kerja tidak stabil; fluktuasi besar, konsentrasi/emosi mudah terpengaruh.',
        ]],
        'KETAHANAN' => ['Ketahanan Kerja', 'Hanker', 'Selisih garis tren kolom 50 dan kolom 0', [
            'Baik Sekali' => 'Daya tahan kerja sangat baik; prestasi justru meningkat seiring waktu.',
            'Baik' => 'Daya tahan kerja baik; prestasi terjaga bahkan sedikit meningkat sampai akhir.',
            'Cukup' => 'Daya tahan kerja cukup; prestasi relatif stabil dari awal hingga akhir.',
            'Kurang' => 'Daya tahan kerja kurang; prestasi mulai menurun saat bekerja dalam waktu lama.',
            'Rendah' => 'Daya tahan kerja rendah; prestasi menurun tajam, cepat lelah pada tugas yang panjang.',
        ]],
    ];

    // Grafik kerja (SVG): y per kolom + garis regresi y = a + bx
    $_n = max(1, count($_chart));
    $_maxY = max(26, ...array_map(fn ($c) => $c['benar'], $_chart ?: [['benar' => 0]]));
    [$_w, $_h, $_l, $_r, $_t, $_bw] = [680, 230, 34, 10, 10, 28];
    $_px = fn ($x) => $_l + ($x - 1) * ($_w - $_l - $_r) / max(1, $_n - 1);
    $_py = fn ($v) => $_t + ($_h - $_t - $_bw) * (1 - $v / $_maxY);
    $_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$_w.'" height="'.$_h.'" viewBox="0 0 '.$_w.' '.$_h.'" font-family="DejaVu Sans, Arial, sans-serif" font-size="9">';
    $_svg .= '<rect x="0" y="0" width="'.$_w.'" height="'.$_h.'" fill="#ffffff"/>';
    for ($_g = 0; $_g <= $_maxY; $_g += 5) {
        $_svg .= '<line x1="'.$_l.'" y1="'.$_py($_g).'" x2="'.($_w - $_r).'" y2="'.$_py($_g).'" stroke="#e2e8f0" stroke-width="1"/>';
        $_svg .= '<text x="'.($_l - 6).'" y="'.($_py($_g) + 3).'" text-anchor="end" fill="#64748b">'.$_g.'</text>';
    }
    foreach ($_chart as $_c) {
        if ($_c['kolom'] === 1 || $_c['kolom'] % 5 === 0) {
            $_svg .= '<text x="'.$_px($_c['kolom']).'" y="'.($_h - $_bw + 14).'" text-anchor="middle" fill="#64748b">'.$_c['kolom'].'</text>';
        }
    }
    $_svg .= '<text x="'.(($_w + $_l) / 2).'" y="'.($_h - 2).'" text-anchor="middle" fill="#334155">Kolom ke-</text>';
    $_svg .= '<line x1="'.$_px(1).'" y1="'.$_py(max(0, $_hanker['a'] + $_hanker['b'])).'" x2="'.$_px($_n).'" y2="'.$_py(max(0, $_hanker['a'] + $_hanker['b'] * $_n)).'" stroke="#d97706" stroke-width="1.5" stroke-dasharray="5,3"/>';
    $_svg .= '<polyline fill="none" stroke="#00843D" stroke-width="1.6" points="'.collect($_chart)->map(fn ($c) => round($_px($c['kolom']), 1).','.round($_py($c['benar']), 1))->implode(' ').'"/>';
    foreach ($_chart as $_c) {
        $_svg .= '<circle cx="'.round($_px($_c['kolom']), 1).'" cy="'.round($_py($_c['benar']), 1).'" r="1.8" fill="#00843D"/>';
    }
    $_svg .= '</svg>';

    $_th = 'background:#4b5563;color:#fff;font-weight:700;border:1px solid #000;padding:4px 5px;font-size:9.5px;';
    $_td = 'border:1px solid #000;padding:3px 6px;font-size:9.5px;vertical-align:middle;';
    $_lbl = $_td.'background:#f2f2f2;font-weight:700;';
@endphp

{{-- A. REKAP SKORING --}}
<div style="font-weight:700;font-size:10.5px;margin:6px 0 4px;">A. Rekap Skoring</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">
    <tr>
        <td style="{{ $_lbl }}width:24%;">Kolom dikerjakan</td><td style="{{ $_td }}width:26%;">{{ $_n }} kolom &middot; {{ (int) ($_rekap->total_benar ?? 0) }} benar</td>
        <td style="{{ $_lbl }}width:24%;">Mean (Panker)</td><td style="{{ $_td }}">{{ $_angka($_skor['KECEPATAN']['skor_mentah'] ?? 0) }} benar/kolom</td>
    </tr>
    <tr>
        <td style="{{ $_lbl }}">Range</td><td style="{{ $_td }}">{{ $alatTes['grid_range'] ?? '-' }} (tertinggi {{ $alatTes['grid_titikTertinggi']['benar'] ?? '-' }} di kolom {{ $alatTes['grid_titikTertinggi']['kolom'] ?? '-' }}, terendah {{ $alatTes['grid_titikTerendah']['benar'] ?? '-' }} di kolom {{ $alatTes['grid_titikTerendah']['kolom'] ?? '-' }})</td>
        <td style="{{ $_lbl }}">Av. Deviation (Janker)</td><td style="{{ $_td }}">{{ $_angka($_skor['KEAJEGAN']['skor_mentah'] ?? 0) }}</td>
    </tr>
    <tr>
        <td style="{{ $_lbl }}">Sum of errors</td><td style="{{ $_td }}">{{ (int) ($_rekap->total_salah ?? 0) }}</td>
        <td style="{{ $_lbl }}">Sum of skippeds</td><td style="{{ $_td }}">{{ (int) ($_rekap->total_kelewat ?? 0) }}</td>
    </tr>
    <tr>
        <td style="{{ $_lbl }}">Tianker (errors + skippeds)</td><td style="{{ $_td }}">{{ (int) ($_skor['KETELITIAN']['skor_mentah'] ?? 0) }}</td>
        <td style="{{ $_lbl }}">Hanker = y(50) &minus; y(0)</td><td style="{{ $_td }}">{{ $_angka($_hanker['y50']) }} &minus; {{ $_angka($_hanker['y0']) }} = <strong>{{ $_angka($_hanker['delta']) }}</strong> ({{ $_hanker['trend'] }})</td>
    </tr>
</table>

{{-- B. GRAFIK KERJA --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">B. Grafik Kerja</div>
<div style="border:1px solid #cbd5e1;padding:4px;text-align:center;">
    <img src="data:image/svg+xml;base64,{{ base64_encode($_svg) }}" alt="Grafik kerja Kraepelin" style="width:100%;max-width:{{ $_w }}px;height:auto;">
</div>
<div style="font-size:8.5px;color:#666;margin:3px 0 8px;">
    <span style="color:#00843D;font-weight:700;">&#9679;&mdash;</span> jumlah benar per kolom &nbsp;&middot;&nbsp;
    <span style="color:#d97706;font-weight:700;">- - -</span> garis tren (regresi linier y = a + bx); naik = ketahanan meningkat, turun = menurun.
</div>

{{-- C. INTERPRETASI --}}
<div style="font-weight:700;font-size:10.5px;margin:10px 0 4px;">C. Interpretasi Faktor Kerja</div>
<table style="width:100%;border-collapse:collapse;">
    <thead>
        <tr>
            <th style="{{ $_th }}width:22%;text-align:left;">Faktor</th>
            <th style="{{ $_th }}width:9%;text-align:center;">Skor</th>
            <th style="{{ $_th }}width:12%;text-align:center;">Kategori</th>
            <th style="{{ $_th }}text-align:left;">Interpretasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($_faktor as $_kode => [$_nama, $_istilah, $_ket, $_teks])
            @php $_s = $_skor->get($_kode); $_kat = $_s['kategori'] ?? null; @endphp
            <tr>
                <td style="{{ $_td }}"><strong>{{ $_nama }}</strong> <span style="color:#666;">({{ $_istilah }})</span><br><span style="color:#666;font-size:8.5px;">{{ $_ket }}</span></td>
                <td style="{{ $_td }}text-align:center;">{{ $_s ? $_angka($_s['skor_mentah']) : '-' }}</td>
                <td style="{{ $_td }}text-align:center;font-weight:700;color:{{ $_warna[$_kat] ?? '#333' }};">{{ $_kat ?? '-' }}</td>
                <td style="{{ $_td }}color:#333;line-height:1.35;">{{ $_teks[$_kat] ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div style="font-size:8.5px;color:#666;margin-top:4px;">
    Rumus sesuai Kraepelin.xls (N = {{ $_n }} kolom, kolom kosong bernilai 0). Kategori memakai norma internal aplikasi
    (Kelola Dimensi &rarr; Kraepelin), karena file patokan tidak memuat tabel norma.
</div>
