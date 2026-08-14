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

    $eppsDesc = [
        'ach' => ['R'=>'Kurang memiliki dorongan untuk berprestasi dan cenderung puas dengan hasil yang biasa-biasa saja.','K'=>'Motivasi berprestasi masih kurang optimal, perlu dorongan dari luar untuk mencapai hasil yang lebih baik.','C'=>'Memiliki motivasi berprestasi yang cukup dan berusaha menyelesaikan pekerjaan dengan hasil yang memadai.','B'=>'Memiliki motivasi berprestasi yang baik, berorientasi pada hasil dan berusaha memberikan yang terbaik.','T'=>'Memiliki dorongan berprestasi yang sangat kuat, selalu berusaha mencapai hasil terbaik dan tidak mudah puas.'],
        'def' => ['R'=>'Kurang dapat mengikuti aturan dan arahan, cenderung bertindak sesuai keinginan sendiri tanpa mempertimbangkan otoritas.','K'=>'Ketaatan terhadap aturan dan arahan masih perlu ditingkatkan, kadang kurang konsisten dalam mengikuti prosedur.','C'=>'Cukup patuh terhadap aturan dan arahan atasan, mampu mengikuti prosedur yang berlaku dengan baik.','B'=>'Memiliki ketaatan yang baik terhadap aturan dan otoritas, dapat diandalkan dalam mengikuti prosedur kerja.','T'=>'Sangat patuh dan taat terhadap aturan, arahan, dan otoritas, selalu mengikuti prosedur dengan disiplin tinggi.'],
        'ord' => ['R'=>'Kurang teratur dan sistematis dalam bekerja, cenderung tidak memperhatikan kerapian dan urutan pekerjaan.','K'=>'Sistematika kerja masih perlu ditingkatkan, kadang kurang memperhatikan keteraturan dalam menyelesaikan tugas.','C'=>'Cukup teratur dan sistematis dalam bekerja, mampu mengatur pekerjaan dengan cara yang cukup terstruktur.','B'=>'Bekerja dengan cara yang teratur dan sistematis, memperhatikan kerapian dan urutan dalam setiap pekerjaan.','T'=>'Sangat teratur, sistematis, dan detail dalam bekerja, selalu memastikan setiap pekerjaan dilakukan secara terstruktur dan rapi.'],
        'exh' => ['R'=>'Kurang memiliki keinginan untuk menonjolkan diri, cenderung tidak ingin menjadi pusat perhatian.','K'=>'Dorongan untuk menonjolkan diri masih rendah, kurang aktif mengekspresikan diri di hadapan orang lain.','C'=>'Cukup memiliki keinginan untuk menonjolkan diri dan mengekspresikan diri di hadapan orang lain.','B'=>'Memiliki keinginan yang baik untuk menonjolkan diri, aktif mengekspresikan pendapat dan menarik perhatian orang lain.','T'=>'Sangat ingin menjadi pusat perhatian, aktif dan ekspresif dalam mengungkapkan diri di hadapan orang lain.'],
        'aut' => ['R'=>'Kurang mandiri, cenderung bergantung pada orang lain dalam mengambil keputusan dan menyelesaikan masalah.','K'=>'Kemandirian masih perlu ditingkatkan, kadang masih membutuhkan bimbingan dalam menentukan arah tindakan.','C'=>'Cukup mandiri dalam mengambil keputusan dan menyelesaikan pekerjaan tanpa bergantung berlebihan pada orang lain.','B'=>'Memiliki kemandirian yang baik, mampu mengambil keputusan dan bertindak sesuai pertimbangan sendiri.','T'=>'Sangat mandiri dan bebas dalam bertindak, tidak ingin terikat aturan dan selalu mengambil keputusan secara independen.'],
        'aff' => ['R'=>'Kurang memiliki kebutuhan untuk bergaul dan menjalin hubungan sosial, cenderung menyukai kesendirian.','K'=>'Keterampilan sosial masih perlu dikembangkan, kurang aktif dalam menjalin dan mempertahankan hubungan sosial.','C'=>'Cukup memiliki keterampilan sosial, mampu bergaul dan menjalin hubungan yang cukup baik dengan orang lain.','B'=>'Memiliki keterampilan sosial yang baik, aktif bergaul dan mampu membina hubungan yang harmonis dengan orang lain.','T'=>'Sangat suka bergaul dan bersosialisasi, aktif menjalin hubungan dan memiliki jaringan pertemanan yang luas.'],
        'int' => ['R'=>'Kurang memiliki kemampuan untuk memahami dan menganalisa perasaan orang lain, kurang empati.','K'=>'Kemampuan empati masih perlu dikembangkan, kurang peka terhadap perasaan dan kondisi orang lain.','C'=>'Cukup mampu memahami perasaan dan kondisi orang lain, memiliki kepekaan empati yang memadai.','B'=>'Memiliki empati yang baik, mampu memahami dan merasakan kondisi orang lain dengan baik.','T'=>'Sangat peka dan empatik, mampu mendalami perasaan dan motivasi orang lain secara mendalam.'],
        'suc' => ['R'=>'Sangat mandiri dan tidak membutuhkan dukungan atau bantuan dari orang lain dalam menghadapi kesulitan.','K'=>'Kebutuhan afeksi rendah, cenderung tidak mengharapkan simpati atau bantuan dari orang lain.','C'=>'Memiliki kebutuhan afeksi yang cukup, kadang mengharapkan dukungan dan perhatian dari orang-orang terdekat.','B'=>'Memiliki kebutuhan afeksi yang cukup tinggi, menginginkan dukungan dan perhatian dari lingkungan sekitar.','T'=>'Sangat membutuhkan dukungan, perhatian, dan simpati dari orang lain, terutama saat menghadapi kesulitan.'],
        'dom' => ['R'=>'Kurang memiliki keinginan untuk memimpin dan mengarahkan orang lain, cenderung mengikuti arahan.','K'=>'Potensi memimpin masih perlu dikembangkan, kurang aktif dalam mengambil peran kepemimpinan.','C'=>'Cukup memiliki kemampuan untuk memimpin dan mengarahkan orang lain dalam situasi tertentu.','B'=>'Memiliki jiwa kepemimpinan yang baik, mampu mengarahkan dan mempengaruhi orang lain secara efektif.','T'=>'Sangat dominan dan berjiwa pemimpin, selalu ingin mengambil kendali dan mengarahkan orang lain.'],
        'aba' => ['R'=>'Kurang jujur dalam mengungkapkan diri, cenderung menutup-nutupi kelemahan dan kesalahan.','K'=>'Kejujuran dalam mengungkapkan diri masih perlu ditingkatkan, kadang kurang terbuka tentang kelemahan diri.','C'=>'Cukup jujur dalam mengungkapkan diri, mampu mengakui kesalahan dan kelemahan dengan cukup terbuka.','B'=>'Memiliki kejujuran yang baik, mampu mengakui kesalahan dan mengungkapkan diri secara terbuka.','T'=>'Sangat jujur dan terbuka, selalu mengakui kesalahan dan kelemahan diri tanpa rasa sungkan.'],
        'nur' => ['R'=>'Kurang memiliki kepedulian terhadap orang lain, jarang memberikan bantuan atau dukungan kepada sesama.','K'=>'Kepedulian sosial masih perlu ditingkatkan, kurang aktif memberikan bantuan kepada orang yang membutuhkan.','C'=>'Cukup peduli terhadap orang lain, mampu memberikan bantuan dan dukungan kepada sesama saat dibutuhkan.','B'=>'Memiliki kepedulian sosial yang baik, aktif memberikan bantuan dan dukungan kepada orang lain.','T'=>'Sangat peduli dan murah hati, selalu siap memberikan bantuan, dukungan, dan perhatian kepada orang lain.'],
        'chg' => ['R'=>'Kurang menyukai perubahan, cenderung memilih rutinitas dan cara-cara yang sudah terbiasa.','K'=>'Kreativitas dan keterbukaan terhadap perubahan masih perlu dikembangkan.','C'=>'Cukup terbuka terhadap perubahan dan hal-hal baru, mampu beradaptasi dengan situasi yang berubah.','B'=>'Menyukai perubahan dan hal-hal baru, aktif mencari pengalaman dan cara baru dalam bekerja.','T'=>'Sangat kreatif dan menyukai perubahan, selalu mencari hal-hal baru dan tidak menyukai rutinitas.'],
        'end' => ['R'=>'Kurang tekun dan mudah menyerah saat menghadapi pekerjaan yang sulit atau membutuhkan waktu lama.','K'=>'Ketekunan masih perlu ditingkatkan, kadang mudah putus asa saat menghadapi hambatan dalam pekerjaan.','C'=>'Cukup tekun dalam menyelesaikan pekerjaan, mampu bertahan menghadapi hambatan dengan cukup baik.','B'=>'Memiliki ketekunan yang baik, mampu bertahan dan bekerja keras hingga pekerjaan selesai.','T'=>'Sangat tekun dan gigih, tidak mudah menyerah dan selalu berusaha menyelesaikan pekerjaan hingga tuntas.'],
        'het' => ['R'=>'Kurang tertarik pada interaksi sosial dengan lawan jenis dalam konteks profesional.','K'=>'Interaksi sosial dengan lawan jenis dalam konteks profesional masih terbatas.','C'=>'Cukup mampu berinteraksi secara sosial dengan lawan jenis dalam konteks profesional.','B'=>'Memiliki kemampuan interaksi sosial yang baik dengan lawan jenis dalam konteks profesional.','T'=>'Sangat aktif dan nyaman dalam berinteraksi sosial dengan lawan jenis dalam berbagai konteks.'],
        'agg' => ['R'=>'Sangat rendah agresifitasnya, cenderung menghindari konflik dan tidak mudah marah.','K'=>'Agresifitas rendah, jarang menunjukkan sikap menyerang atau bertentangan dengan orang lain.','C'=>'Memiliki agresifitas yang cukup, kadang dapat bersikap tegas dan mempertahankan pendapat.','B'=>'Cukup agresif dalam mempertahankan pendapat dan posisi, tidak segan mengkritik atau menentang.','T'=>'Sangat agresif, mudah marah, dan sering menunjukkan sikap menyerang atau menentang orang lain.'],
        'con' => ['R'=>'Validitas meragukan, peserta kemungkinan tidak konsisten atau tidak jujur dalam menjawab.','K'=>'Konsistensi jawaban kurang, hasil perlu diinterpretasikan dengan hati-hati.','C'=>'Konsistensi jawaban cukup, hasil dapat digunakan dengan pertimbangan.','B'=>'Konsistensi jawaban baik, hasil dapat dipercaya.','T'=>'Konsistensi jawaban sangat baik, hasil sangat dapat dipercaya.'],
    ];

    $eppsKelompok = [
        'SIKAP DAN POTENSI KERJA' => [
            'ach' => 'Motivasi Berprestasi',
            'end' => 'Ketekunan',
            'ord' => 'Sistematika Kerja',
            'dom' => 'Dominasi',
            'agg' => 'Agresifitas',
            'def' => 'Ketaatan',
            'aba' => 'Kejujuran',
            'aut' => 'Kemandirian',
            'chg' => 'Kreativitas',
        ],
        'KEMAMPUAN INTERPERSONAL' => [
            'aff' => 'Keterampilan Sosial',
            'suc' => 'Kebutuhan Afeksi',
            'int' => 'Empati',
            'exh' => 'Menonjolkan Diri',
            'nur' => 'Kontak Sosial',
            'het' => 'Interaksi Sosial',
        ],
    ];

    $katMap = [
        'Sangat Rendah' => 'R',
        'Rendah'        => 'K',
        'Sedang'        => 'C',
        'Cukup'         => 'C',
        'Baik'          => 'B',
        'Tinggi'        => 'T',
        'Sangat Tinggi' => 'T',
    ];
@endphp
<style>
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

<div style="max-width: 900px; margin: 0 auto; padding: 0 8px;">

    <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
        <a href="{{ route('admin.hasil-tes.exportPdf', [$sesi->id, $hasilTes['peserta_id']]) }}"
           class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white
                  shadow-sm hover:bg-[#234853] inline-block">Cetak PDF</a>
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
                    <p style="margin:6px 0 10px 0; color:#666;font-size:10px;">Format: Forced Choice &ndash; Skor Mentah (1-100), Skor Skala (1-10)</p>

                    @php
                        // Index skor EPPS by kode dimensi (extracted from "kode - nama" string)
                        $eppsScores = [];
                        foreach ($alatTes['skor_ringkas'] as $_s) {
                            $_parts = explode(' - ', $_s['dimensi'] ?? '');
                            $_kode  = strtolower($_parts[0] ?? '');
                            $eppsScores[$_kode] = $_s;
                        }
                    @endphp

                    @foreach ($eppsKelompok as $_kelompokNama => $_dimensiPeta)
                        <div style="background:#000; color:#fff; font-weight:700; font-size:10.5px; padding:4px 8px; margin-top:8px; letter-spacing:0.5px;">{{ $_kelompokNama }}</div>
                        <table>
                            <thead class="th-row">
                                <tr>
                                    <th style="width:32%;">Aspek Psikologis</th>
                                    <th style="width:5%;text-align:center;">R</th>
                                    <th style="width:5%;text-align:center;">K</th>
                                    <th style="width:5%;text-align:center;">C</th>
                                    <th style="width:5%;text-align:center;">B</th>
                                    <th style="width:5%;text-align:center;">T</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($_dimensiPeta as $_kode => $_namaAspek)
                                    @php $_skor = $eppsScores[$_kode] ?? null; @endphp
                                    <tr>
                                        <td style="padding:3px 6px;"><strong>{{ $_namaAspek }}</strong></td>
                                        <td class="mark" style="font-size:12px;">{!! $_skor && $katMap[$_skor['kategori']] === 'R' ? '&check;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_skor && $katMap[$_skor['kategori']] === 'K' ? '&check;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_skor && $katMap[$_skor['kategori']] === 'C' ? '&check;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_skor && $katMap[$_skor['kategori']] === 'B' ? '&check;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_skor && $katMap[$_skor['kategori']] === 'T' ? '&check;' : '&bull;' !!}</td>
                                        <td style="font-size:9px;color:#333;line-height:1.4;">{{ $_skor ? ($eppsDesc[$_kode][$katMap[$_skor['kategori']]] ?? '-') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach

                    @if (isset($eppsScores['con']))
                        <div style="background:#1a1a1a; color:#fff; font-weight:700; font-size:10px; padding:3px 8px; margin-top:8px; letter-spacing:0.5px;">CATATAN VALIDITAS</div>
                        <table>
                            <thead class="th-row">
                                <tr>
                                    <th style="width:32%;">Aspek</th>
                                    <th style="width:5%;text-align:center;">R</th>
                                    <th style="width:5%;text-align:center;">K</th>
                                    <th style="width:5%;text-align:center;">C</th>
                                    <th style="width:5%;text-align:center;">B</th>
                                    <th style="width:5%;text-align:center;">T</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $_con = $eppsScores['con']; @endphp
                                <tr>
                                    <td style="padding:3px 6px;"><strong>Validitas</strong></td>
                                    <td class="mark" style="font-size:12px;">{!! $katMap[$_con['kategori']] === 'R' ? '&check;' : '&bull;' !!}</td>
                                    <td class="mark" style="font-size:12px;">{!! $katMap[$_con['kategori']] === 'K' ? '&check;' : '&bull;' !!}</td>
                                    <td class="mark" style="font-size:12px;">{!! $katMap[$_con['kategori']] === 'C' ? '&check;' : '&bull;' !!}</td>
                                    <td class="mark" style="font-size:12px;">{!! $katMap[$_con['kategori']] === 'B' ? '&check;' : '&bull;' !!}</td>
                                    <td class="mark" style="font-size:12px;">{!! $katMap[$_con['kategori']] === 'T' ? '&check;' : '&bull;' !!}</td>
                                    <td style="font-size:9px;color:#333;line-height:1.4;">{{ $eppsDesc['con'][$katMap[$_con['kategori']]] ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif

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

                {{-- Kraepelin — Grid: ringkasan jumlah kolom --}}
                @elseif ($alatTes['format_dasar'] === 'Grid' && ($gridRingkasan = $alatTes['grid_ringkasan'] ?? null))
                    <table style="margin-top:8px;">
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
                    <div style="margin-top:8px; padding:8px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; font-size:10px; color:#334155; display:grid; grid-template-columns:1fr 1fr; gap:4px 16px;">
                        <div><strong>Total Jawaban Benar:</strong> {{ $gridRingkasan->total_benar }}</div>
                        <div><strong>Total Jawaban Salah:</strong> {{ $gridRingkasan->total_salah }}</div>
                        <div><strong>Total Kelewat:</strong> {{ $gridRingkasan->total_kelewat }}</div>
                        <div><strong>Total Kolom Dikerjakan:</strong> {{ $gridRingkasan->total_kolom }}</div>
                    </div>
                @elseif (str_contains(strtoupper($alatTes['nama_alat_tes']), 'PAPIKOSTIK') && !empty($alatTes['skor_ringkas']))
                    @php
                        $papKelompok = [
                            'Arah Kerja'   => ['N', 'G', 'A'],
                            'Kepemimpinan' => ['L', 'P', 'I'],
                            'Aktivitas'    => ['T', 'V'],
                            'Pergaulan'    => ['X', 'S', 'B', 'O'],
                            'Gaya Kerja'   => ['R', 'D', 'C'],
                            'Sifat'        => ['Z', 'E', 'K'],
                            'Ketaatan'     => ['F', 'W'],
                        ];
                        $papDesc = [
                            'N' => ['db'=>'TGS_PRIBADI',    'nama'=>'Penyelesaian secara prestasi', 'R'=>'Cenderung ragu-ragu dalam situasi pengambilan keputusan, menunda atau menghindari penyelesaian tugas.','K'=>'Kurang konsisten dalam menyelesaikan tugas hingga tuntas, perlu dorongan dari luar.','C'=>'Cukup mampu menyelesaikan tugas dengan standar yang memadai.','B'=>'Berorientasi pada penyelesaian tugas secara tuntas dan bertanggung jawab.','T'=>'Sangat berorientasi pada penyelesaian tugas, tidak mudah berhenti sebelum pekerjaan selesai.'],
                            'G' => ['db'=>'KERJA_KERAS',    'nama'=>'Peranan sebagai pekerja keras','R'=>'Bekerja hanya untuk mengejar kesenangan saja, bukan untuk memberikan suatu hasil yang baik.','K'=>'Usaha kerja masih perlu ditingkatkan, kurang konsisten dalam menunjukkan etos kerja yang baik.','C'=>'Cukup bersungguh-sungguh dalam bekerja dan mampu menunjukkan etos kerja yang memadai.','B'=>'Memiliki etos kerja yang baik, bersungguh-sungguh dan tekun dalam menyelesaikan pekerjaan.','T'=>'Sangat berdedikasi dan pekerja keras, selalu memberikan usaha terbaik dalam setiap pekerjaan.'],
                            'A' => ['db'=>'BERPRESTASI',    'nama'=>'Hasrat untuk berprestasi','R'=>'Mencerminkan ketidakpastian tujuan, tidak perlu melanjutkan usaha untuk sukses.','K'=>'Motivasi berprestasi masih rendah, kurang terdorong untuk mencapai hasil yang lebih baik.','C'=>'Memiliki motivasi berprestasi yang cukup, berusaha mencapai target yang ditetapkan.','B'=>'Memiliki dorongan berprestasi yang baik, berorientasi pada pencapaian hasil yang optimal.','T'=>'Memiliki dorongan berprestasi yang sangat kuat, selalu berusaha melampaui standar yang ada.'],
                            'L' => ['db'=>'PIMPINAN',       'nama'=>'Peran sebagai pimpinan','R'=>'Cenderung tidak suka aktif menggunakan orang lain dalam bekerja, kurang berminat memimpin.','K'=>'Potensi kepemimpinan masih perlu dikembangkan, kurang aktif mengambil peran sebagai pemimpin.','C'=>'Cukup mampu menjalankan peran kepemimpinan dalam situasi yang terstruktur.','B'=>'Memiliki jiwa kepemimpinan yang baik, mampu mengarahkan dan memotivasi orang lain.','T'=>'Sangat berjiwa pemimpin, selalu berinisiatif mengambil kendali dan memimpin kelompok.'],
                            'P' => ['db'=>'KENDALI_ORG',    'nama'=>'Pengendalian orang lain','R'=>'Menurunnya keinginan untuk bertanggung jawab terhadap pekerjaan dan tindakan orang lain.','K'=>'Kurang aktif dalam mengendalikan atau mengarahkan orang lain untuk mencapai tujuan.','C'=>'Cukup mampu mengendalikan dan mengarahkan orang lain dalam situasi yang diperlukan.','B'=>'Memiliki kemampuan yang baik dalam mengendalikan dan mempengaruhi perilaku orang lain.','T'=>'Sangat dominan dalam mengendalikan orang lain, selalu berusaha mengarahkan tindakan kelompok.'],
                            'I' => ['db'=>'PUTUSAN',        'nama'=>'Mudah dalam mengambil keputusan','R'=>'Ragu-ragu sampai penundaan atau menolak situasi pengambilan keputusan.','K'=>'Lambat dan kurang percaya diri dalam mengambil keputusan, perlu pertimbangan berlebihan.','C'=>'Cukup mampu mengambil keputusan dengan pertimbangan yang memadai.','B'=>'Mampu mengambil keputusan secara cepat dan tepat dalam berbagai situasi.','T'=>'Sangat cepat dan tegas dalam mengambil keputusan, tidak mudah ragu dalam bertindak.'],
                            'T' => ['db'=>'SIBUK',          'nama'=>'Tipe selalu sibuk','R'=>'Melakukan segala sesuatu menurut kemauannya sendiri, kurang terstruktur dalam bekerja.','K'=>'Tingkat aktivitas masih rendah, kurang inisiatif dalam mencari pekerjaan atau tantangan baru.','C'=>'Cukup aktif dan produktif, mampu mengisi waktu kerja dengan kegiatan yang bermakna.','B'=>'Memiliki energi kerja yang tinggi, aktif dan produktif dalam menyelesaikan berbagai tugas.','T'=>'Sangat aktif dan selalu ingin bergerak, tidak nyaman bila tidak ada pekerjaan yang dikerjakan.'],
                            'V' => ['db'=>'SEMANGAT',       'nama'=>'Tipe yang bersemangat','R'=>'Keaktifannya tergolong rendah, cenderung pasif dan kurang bergairah dalam bekerja.','K'=>'Semangat kerja masih perlu ditingkatkan, kadang terlihat kurang antusias dalam aktivitas.','C'=>'Memiliki semangat kerja yang cukup, mampu menunjukkan antusiasme dalam situasi yang tepat.','B'=>'Memiliki semangat dan gairah kerja yang tinggi, antusias dalam menjalani aktivitas sehari-hari.','T'=>'Sangat bersemangat dan bergairah, selalu menunjukkan energi positif yang tinggi dalam bekerja.'],
                            'X' => ['db'=>'PERHATIAN',      'nama'=>'Kebutuhan untuk mendapat perhatian','R'=>'Cenderung pemalu dan suka menyendiri, tidak terlalu membutuhkan pengakuan dari orang lain.','K'=>'Kebutuhan perhatian rendah, kurang aktif mengekspresikan diri di hadapan orang lain.','C'=>'Cukup memiliki kebutuhan untuk diperhatikan dan diakui oleh lingkungan sekitar.','B'=>'Memiliki kebutuhan yang cukup besar untuk mendapat perhatian dan pengakuan dari orang lain.','T'=>'Sangat membutuhkan perhatian dan pengakuan, aktif menonjolkan diri agar diperhatikan.'],
                            'S' => ['db'=>'PERGAULAN_LUAS', 'nama'=>'Pergaulan luas','R'=>'Memiliki penilaian rendah terhadap hubungan sosial, cenderung kurang percaya pada orang lain.','K'=>'Pergaulan masih terbatas, kurang aktif dalam membangun relasi dan jaringan sosial yang luas.','C'=>'Memiliki pergaulan yang cukup luas, mampu berinteraksi dengan berbagai kalangan.','B'=>'Memiliki kemampuan bergaul yang baik, aktif membangun relasi dengan banyak orang.','T'=>'Sangat mudah bergaul dan memiliki jaringan sosial yang sangat luas di berbagai lingkungan.'],
                            'B' => ['db'=>'BETAH_KELOMPOK', 'nama'=>'Kebutuhan berkelompok','R'=>'Selektif dalam bergabung dengan kelompok, secara umum melepaskan diri dari kelompok.','K'=>'Kurang antusias dalam kegiatan kelompok, lebih menyukai bekerja secara individual.','C'=>'Cukup nyaman bekerja dalam kelompok, mampu berkontribusi dalam situasi tim.','B'=>'Menikmati bekerja dalam kelompok, aktif berkontribusi dan membangun kekompakan tim.','T'=>'Sangat menyukai kegiatan kelompok, merasa nyaman dan produktif saat bekerja dalam tim.'],
                            'O' => ['db'=>'DEKAT_SAYANG',   'nama'=>'Kebutuhan untuk dekat dan menyayangi','R'=>'Tidak menyukai hubungan antar pribadi yang intim, tidak menyukai interaksi perseorangan.','K'=>'Kurang memiliki kebutuhan untuk membangun kedekatan emosional dengan orang lain.','C'=>'Memiliki kebutuhan yang cukup untuk menjalin hubungan yang dekat dan hangat dengan orang lain.','B'=>'Memiliki kebutuhan yang besar untuk menjalin hubungan yang dekat dan penuh kasih sayang.','T'=>'Sangat membutuhkan kedekatan emosional, selalu berusaha membangun hubungan yang intim dan hangat.'],
                            'R' => ['db'=>'TEORITIS',       'nama'=>'Tipe teoritikal','R'=>'Kurang perhatian-praktis, lebih menyukai pendekatan yang konkret dan langsung.','K'=>'Kurang tertarik pada pemikiran teoritis, lebih menyukai hal-hal yang bersifat praktis.','C'=>'Mampu menyeimbangkan pendekatan teoritis dan praktis dalam menyelesaikan pekerjaan.','B'=>'Memiliki kecenderungan berpikir teoritis yang baik, mampu mengembangkan konsep dan ide.','T'=>'Sangat berorientasi teoritis, menyukai pemikiran konseptual dan analisis mendalam.'],
                            'D' => ['db'=>'DETAIL_KERJA',   'nama'=>'Suka pekerjaan yang terperinci','R'=>'Menyadari kebutuhan akan kecermatan tetapi secara pribadi tidak berminat menangani hal-hal detail.','K'=>'Kurang menyukai pekerjaan yang membutuhkan ketelitian dan perhatian pada detail.','C'=>'Cukup mampu mengerjakan tugas yang memerlukan ketelitian dan perhatian pada hal-hal detail.','B'=>'Memiliki perhatian yang baik pada detail, teliti dan cermat dalam menyelesaikan pekerjaan.','T'=>'Sangat menyukai pekerjaan yang terperinci, teliti hingga ke hal terkecil dalam setiap tugas.'],
                            'C' => ['db'=>'TYPE_PENGATUR',  'nama'=>'Tipe teratur','R'=>'Fleksibilitas sampai ketidak-teraturan, kurang memperhatikan kerapian dan sistematika kerja.','K'=>'Sistematika dan keteraturan kerja masih perlu ditingkatkan.','C'=>'Cukup teratur dan sistematis dalam bekerja, mampu menjaga kerapian pekerjaan.','B'=>'Memiliki keteraturan kerja yang baik, selalu mengorganisir pekerjaan secara rapi dan sistematis.','T'=>'Sangat teratur dan sistematis, selalu memastikan setiap pekerjaan tersusun dengan rapi dan terstruktur.'],
                            'Z' => ['db'=>'HASRAT_BERUBAH', 'nama'=>'Hasrat untuk berubah','R'=>'Tidak menyukai dan menolak perubahan, cenderung menggunakan pendekatan-pendekatan tradisional.','K'=>'Kurang terbuka terhadap perubahan, membutuhkan waktu lama untuk beradaptasi dengan hal baru.','C'=>'Cukup terbuka terhadap perubahan, mampu beradaptasi dengan situasi yang berubah.','B'=>'Menyukai perubahan dan hal-hal baru, aktif mencari cara-cara inovatif dalam bekerja.','T'=>'Sangat menyukai perubahan dan inovasi, tidak nyaman dengan rutinitas dan selalu mencari hal baru.'],
                            'E' => ['db'=>'KENDALI_EMOSI',  'nama'=>'Pengendalian emosi','R'=>'Terbuka, cepat bereaksi, tidak memikirkan nilai dalam pengendalian diri.','K'=>'Pengendalian emosi masih perlu ditingkatkan, kadang bereaksi berlebihan terhadap situasi.','C'=>'Cukup mampu mengendalikan emosi, umumnya tenang dalam menghadapi berbagai situasi.','B'=>'Memiliki pengendalian emosi yang baik, mampu tetap tenang dan rasional dalam situasi sulit.','T'=>'Sangat mampu mengendalikan emosi, selalu tampil tenang dan stabil dalam berbagai kondisi.'],
                            'K' => ['db'=>'AGRESI',         'nama'=>'Agresi','R'=>'Selalu menghindari masalah, cenderung mengabaikan situasi atau menolak untuk mengenali sesuatu sebagai masalah.','K'=>'Cenderung menghindari konflik, kurang berani mengungkapkan ketidaksetujuan secara tegas.','C'=>'Cukup mampu bersikap tegas dan mengungkapkan ketidaksetujuan bila diperlukan.','B'=>'Berani mengungkapkan pendapat dan bersikap tegas dalam mempertahankan posisinya.','T'=>'Sangat agresif dan tegas, tidak segan mengkritik atau menentang pendapat orang lain.'],
                            'F' => ['db'=>'DUKUNGAN_ATASAN','nama'=>'Dukungan terhadap atasan','R'=>'Cenderung egois, kemungkinan bisa bersikap memberontak terhadap atasan atau aturan.','K'=>'Kurang mendukung dan mengikuti arahan atasan, kadang bersikap tidak kooperatif.','C'=>'Cukup mendukung dan menghormati atasan, mampu mengikuti arahan dengan baik.','B'=>'Memiliki loyalitas dan dukungan yang baik terhadap atasan dan kebijakan organisasi.','T'=>'Sangat loyal dan mendukung atasan, selalu berusaha memenuhi harapan dan arahan pimpinan.'],
                            'W' => ['db'=>'TAAT_ATURAN',    'nama'=>'Kebutuhan taat pada aturan & pengarahan','R'=>'Berorientasi pada tujuan, mandiri, tidak terlalu membutuhkan aturan yang ketat.','K'=>'Kurang menyukai aturan yang mengikat, lebih suka bekerja dengan kebebasan yang besar.','C'=>'Cukup patuh terhadap aturan dan pengarahan yang berlaku di lingkungan kerja.','B'=>'Memiliki kepatuhan yang baik terhadap aturan, prosedur, dan pengarahan yang diberikan.','T'=>'Sangat membutuhkan aturan yang jelas, disiplin tinggi dalam mengikuti prosedur dan pengarahan.'],
                        ];
                        $papScores = [];
                        foreach ($alatTes['skor_ringkas'] as $_s) {
                            $_kode = explode(' - ', $_s['dimensi'] ?? '')[0] ?? '';
                            $papScores[trim($_kode)] = $_s;
                        }
                    @endphp
                    <p style="margin:6px 0 10px 0; color:#666;font-size:10px;">Format: Self-Report &ndash; Skor Mentah (0-9), Kategori: R = Rendah, K = Kurang, C = Cukup, B = Baik, T = Tinggi</p>
                    @foreach ($papKelompok as $_kelompokNama => $_dimensiKode)
                        <div style="background:#000; color:#fff; font-weight:700; font-size:10.5px; padding:4px 8px; margin-top:8px; letter-spacing:0.5px;">{{ $_kelompokNama }}</div>
                        <table>
                            <thead class="th-row">
                                <tr>
                                    <th style="width:38%;">ASPEK PSIKOLOGIS</th>
                                    <th style="width:5%;text-align:center;">R</th>
                                    <th style="width:5%;text-align:center;">K</th>
                                    <th style="width:5%;text-align:center;">C</th>
                                    <th style="width:5%;text-align:center;">B</th>
                                    <th style="width:5%;text-align:center;">T</th>
                                    <th>DESKRIPSI KEPRIBADIAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($_dimensiKode as $_skala)
                                    @php $_dbKode = $papDesc[$_skala]['db'] ?? ''; $_skor = $papScores[$_dbKode] ?? null; @endphp
                                    @php
                                        $_katHuruf = '-';
                                        if ($_skor) {
                                            $_sm = (float) $_skor['skor_mentah'];
                                            if ($_sm <= 2) $_katHuruf = 'R';
                                            elseif ($_sm <= 4) $_katHuruf = 'K';
                                            elseif ($_sm <= 6) $_katHuruf = 'C';
                                            elseif ($_sm <= 8) $_katHuruf = 'B';
                                            else $_katHuruf = 'T';
                                        }
                                    @endphp
                                    <tr>
                                        <td style="padding:3px 6px;"><strong>{{ $papDesc[$_skala]['nama'] }}</strong></td>
                                        <td class="mark" style="font-size:12px;">{!! $_katHuruf === 'R' ? '&#10003;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_katHuruf === 'K' ? '&#10003;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_katHuruf === 'C' ? '&#10003;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_katHuruf === 'B' ? '&#10003;' : '&bull;' !!}</td>
                                        <td class="mark" style="font-size:12px;">{!! $_katHuruf === 'T' ? '&#10003;' : '&bull;' !!}</td>
                                        <td style="font-size:9px;color:#333;line-height:1.4;">{{ $_skor && $_katHuruf !== '-' ? ($papDesc[$_skala][$_katHuruf] ?? '-') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                    <div class="footnote"><strong>Keterangan:</strong> R = Rendah &middot; K = Kurang &middot; C = Cukup &middot; B = Baik &middot; T = Tinggi</div>

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

@endsection
