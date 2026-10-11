<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Psikogram - {{ $hasilTes['nama_peserta'] }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
        .dokumen { width: 100%; box-sizing: border-box; }
        .company-name { font-weight: 700; font-size: 15px; margin: 0; }
        .company-sub { font-size: 10px; color: #444; margin: 2px 0 0 0; }
        .rahasia-badge { border: 1px solid #b91c1c; color: #b91c1c; font-size: 8px; font-weight: bold; padding: 2px 6px; white-space: nowrap; }
        .doc-title { text-align: center; font-weight: 700; font-size: 13px; letter-spacing: 0.5px; margin: 8px 0 4px 0; }
        .doc-sub { text-align: center; font-size: 10px; color: #666; margin-bottom: 14px; }
        .section-title { font-weight: 700; font-size: 11.5px; margin: 16px 0 8px 0; letter-spacing: 0.5px; text-transform: uppercase; }
        .sub-title { font-weight: 700; font-size: 10.5px; margin: 10px 0 4px 0; color: #333; }
        table { border-collapse: collapse; font-size: 10px; margin-bottom: 6px; }
        td, th { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; word-wrap: break-word; overflow-wrap: break-word; }
        .th-row td, .th-row th { background: #4b5563; color: #fff; font-weight: 700; }
        .th-row th { text-align: left; }
        .label-cell { background: #f2f2f2; font-weight: 700; width: 110px; }
        .mark { text-align: center; font-weight: 700; }
        .narasi-box { padding: 8px; font-size: 10px; line-height: 1.7; text-align: justify; word-wrap: break-word; overflow-wrap: break-word; }
        .footnote { font-size: 8.5px; color: #888; border-top: 1px solid #ccc; padding-top: 6px; margin: 6px 0 4px 0; line-height: 1.6; }
        .access-denied { border: 1px dashed #999; padding: 24px; text-align: center; color: #666; font-size: 11px; margin-bottom: 14px; }
        .ttd-space { margin-bottom: 36px; }
        .ttd-line { border-top: 1px solid #000; width: 150px; padding-top: 2px; }
        .container-formal { border: 1px solid #000; padding: 12px; margin-bottom: 12px; }
        .page2-header { border-bottom: 1px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
        .page2-header .company-name { font-size: 12px; }
        .page2-header .company-sub { font-size: 9px; }
        .page2-header .rahasia-badge { font-size: 8px; padding: 2px 6px; }
        .page2-title { font-weight: 700; font-size: 11px; letter-spacing: 0.5px; margin-bottom: 10px; text-align: center; }
        .page-break { page-break-after: always; break-after: page; }
        .container-formal { page-break-inside: avoid; }
        table { page-break-inside: avoid; }
        @page {
            size: A4 portrait;
            margin: 15mm 14mm 20mm 14mm;
        }
        p, div, td { orphans: 3; widows: 3; }
        .bidang-header { background: #4b5563; color: #fff; font-weight: 700; font-size: 10.5px; padding: 4px 8px; margin-top: 8px; letter-spacing: 0.5px; }
    </style>
</head>
<body>
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

    $katMap = [
        'Sangat Rendah' => 'R',
        'Rendah'        => 'K',
        'Sedang'        => 'C',
        'Cukup'         => 'C',
        'Baik'          => 'B',
        'Tinggi'        => 'T',
        'Sangat Tinggi' => 'T',
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
        'N' => ['db'=>'TGS_PRIBADI',    'nama'=>'Penyelesaian secara prestasi','R'=>'Cenderung ragu-ragu dalam situasi pengambilan keputusan, menunda atau menghindari penyelesaian tugas.','K'=>'Kurang konsisten dalam menyelesaikan tugas hingga tuntas, perlu dorongan dari luar.','C'=>'Cukup mampu menyelesaikan tugas dengan standar yang memadai.','B'=>'Berorientasi pada penyelesaian tugas secara tuntas dan bertanggung jawab.','T'=>'Sangat berorientasi pada penyelesaian tugas, tidak mudah berhenti sebelum pekerjaan selesai.'],
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

    $aspekPsikologisIST = [
        ['no'=>1, 'nama'=>'Kecerdasan Umum',             'deskripsi'=>'Kemampuan dasar untuk menghadapi, mengolah, mempelajari dan memecahkan masalah di sekitarnya.',                                   'kode'=>'WU'],
        ['no'=>2, 'nama'=>'Kemampuan Analisis - Sintesa', 'deskripsi'=>'Kemampuan analisa terhadap sebuah masalah.',                                                                                      'kode'=>'WA'],
        ['no'=>3, 'nama'=>'Kemampuan Berfikir Fleksibel', 'deskripsi'=>'Kemampuan untuk berpikir secara fleksibel dan adanya kejelasan dalam proses pikir.',                                              'kode'=>'AN'],
        ['no'=>4, 'nama'=>'Kemampuan Verbal',             'deskripsi'=>'Potensi untuk mencerna konsep dalam kata dan memahami polanya sehingga menarik kesimpulan secara umum.',                         'kode'=>'SE'],
        ['no'=>5, 'nama'=>'Kemampuan Berpikir Praktis',   'deskripsi'=>'Kemampuan untuk memahami realita dan membuat keputusan yang didasarkan atas fakta.',                                             'kode'=>'ZR'],
        ['no'=>6, 'nama'=>'Kemampuan Numerikal',          'deskripsi'=>'Potensi untuk mencerna konsep angka dan menggunakannya dalam memecahkan persoalan hitungan.',                                    'kode'=>'RA'],
        ['no'=>7, 'nama'=>'Daya Kreatifitas',             'deskripsi'=>'Kemampuan untuk melepaskan diri dari pemikiran yang bersifat konvensional.',                                                     'kode'=>'FA'],
        ['no'=>8, 'nama'=>'Kemampuan Abstraksi',          'deskripsi'=>'Potensi untuk membayangkan secara konstruktif yang bersifat menyeluruh dan analitis.',                                           'kode'=>'GE'],
        ['no'=>9, 'nama'=>'Kemampuan Daya Ingat / Memori','deskripsi'=>'Kemampuan untuk mengingat informasi yang diterima secara akurat.',                                                               'kode'=>'ME'],
    ];

    function kategoriIST(float $skor): string {
        return match(true) {
            $skor >= 120 => 'ST',
            $skor >= 110 => 'T',
            $skor >= 90  => 'C',
            $skor >= 80  => 'R',
            $skor > 0    => 'SR',
            default      => '',
        };
    }
@endphp
<div class="dokumen">

    {{-- HEADER --}}
    <table style="border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:14px; border-collapse:collapse;">
        <tr>
            <td style="border:none; padding:0; width:65%;">
                <table style="border-collapse:collapse;">
                    <tr>
                        <td style="border:none; padding:0; width:40px; vertical-align:middle;">
                            <img src="{{ public_path('images/logo.png') }}" width="36" height="36">
                        </td>
                        <td style="border:none; padding:0 0 0 10px; vertical-align:middle;">
                            <div class="company-name">PT Jhonlin Group</div>
                            <div class="company-sub">Departemen Human Capital</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border:none; padding:0 0 0 10px; width:35%; text-align:right; vertical-align:top;">
                <span class="rahasia-badge">DATA BERSIFAT RAHASIA</span>
            </td>
        </tr>
    </table>

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

        {{-- TANDA TANGAN --}}
        <table style="border-collapse:collapse; margin-top:24px; font-size:11px;">
            <tr>
                <td style="border:none; padding:0; width:50%;">
                    <div class="ttd-space">Diperiksa oleh,</div>
                    <div class="ttd-line">Admin HR</div>
                </td>
                <td style="border:none; padding:0; width:50%; text-align:right;">
                    <div class="ttd-space">Banjarbaru, 28 Juli 2026</div>
                    <div style="border-top:1px solid #000; width:130px; padding-top:2px;">Super Admin</div>
                </td>
            </tr>
        </table>
    </div>
    @endif
    <div class="page-break"></div>

    {{-- HALAMAN 2: LAMPIRAN --}}
    <table style="border-bottom:1px solid #000; padding-bottom:6px; margin-bottom:10px; border-collapse:collapse;" class="page2-header">
        <tr>
            <td style="border:none; padding:0; vertical-align:middle;">
                <table style="border-collapse:collapse;">
                    <tr>
                        <td style="border:none; padding:0; width:32px; vertical-align:middle;">
                            <img src="{{ public_path('images/logo.png') }}" width="28" height="28">
                        </td>
                        <td style="border:none; padding:0 0 0 8px; vertical-align:middle;">
                            <div class="company-name">PT Jhonlin Group</div>
                            <div class="company-sub">Departemen Human Capital</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border:none; padding:0; text-align:right; vertical-align:middle;">
                <span class="rahasia-badge">DATA BERSIFAT RAHASIA</span>
            </td>
        </tr>
    </table>
    <div class="page2-title">LAMPIRAN &mdash; HASIL PER INSTRUMEN</div>

    {{-- HASIL PER INSTRUMEN --}}
    @foreach ($hasilTes['hasil_alat_tes'] as $index => $alatTes)
        <div class="container-formal">
            <div class="sub-title">{{ $index + 1 }}. {{ $alatTes['nama_alat_tes'] }} &ndash; {{ $alatTes['format_dasar'] }}</div>

            {{-- EPPS: Psikogram per kelompok --}}
            @if (str_contains(strtoupper($alatTes['nama_alat_tes']), 'EPPS') && is_array($alatTes['skor_ringkas']) && count($alatTes['skor_ringkas']) > 0)
                @php
                    $eppsScores = [];
                    foreach ($alatTes['skor_ringkas'] as $_s) {
                        $_parts = explode(' - ', $_s['dimensi'] ?? '');
                        $_kode  = strtolower($_parts[0] ?? '');
                        $eppsScores[$_kode] = $_s;
                    }
                @endphp
                <p style="margin:6px 0 10px 0; color:#666;font-size:10px;">Format: Forced Choice &ndash; Skor Mentah (1-100), Skor Skala (1-10)</p>

                @foreach ($eppsKelompok as $_kelompokNama => $_dimensiPeta)
                    <div class="bidang-header">{{ $_kelompokNama }}</div>
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
                                    <td class="mark">{!! $_skor && ($katMap[$_skor['kategori']] ?? '') === 'R' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_skor && ($katMap[$_skor['kategori']] ?? '') === 'K' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_skor && ($katMap[$_skor['kategori']] ?? '') === 'C' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_skor && ($katMap[$_skor['kategori']] ?? '') === 'B' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_skor && ($katMap[$_skor['kategori']] ?? '') === 'T' ? '&#10003;' : '&bull;' !!}</td>
                                    <td style="font-size:9px;color:#333;line-height:1.4;">{{ $_skor ? ($eppsDesc[$_kode][$katMap[$_skor['kategori']] ?? ''] ?? '-') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                @if (isset($eppsScores['con']))
                    <div class="bidang-header" style="margin-top:8px;">CATATAN VALIDITAS</div>
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
                            @php $_con = $eppsScores['con']; $_conKat = $katMap[$_con['kategori']] ?? ''; @endphp
                            <tr>
                                <td style="padding:3px 6px;"><strong>Validitas</strong></td>
                                <td class="mark">{!! $_conKat === 'R' ? '&#10003;' : '&bull;' !!}</td>
                                <td class="mark">{!! $_conKat === 'K' ? '&#10003;' : '&bull;' !!}</td>
                                <td class="mark">{!! $_conKat === 'C' ? '&#10003;' : '&bull;' !!}</td>
                                <td class="mark">{!! $_conKat === 'B' ? '&#10003;' : '&bull;' !!}</td>
                                <td class="mark">{!! $_conKat === 'T' ? '&#10003;' : '&bull;' !!}</td>
                                <td style="font-size:9px;color:#333;line-height:1.4;">{{ $eppsDesc['con'][$_conKat] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

            {{-- CFIT: tampilkan skor IQ tunggal --}}
            @elseif (str_contains($alatTes['nama_alat_tes'], 'CFIT') && !empty($alatTes['skor_ringkas']))
                @php $skorCfit = $alatTes['skor_ringkas'][0] ?? null; @endphp
                @if ($skorCfit)
                    <table style="border:none; margin-bottom:0;">
                        <tr>
                            <td style="width:80px; text-align:center; background:#eff6ff; border:1px solid #bfdbfe; padding:8px;">
                                <div style="font-size:9px; font-weight:bold; color:#1d4ed8;">SKOR IQ</div>
                                <div style="font-size:24px; font-weight:bold; color:#1e3a5f;">{{ $skorCfit['skor_skala'] }}</div>
                            </td>
                            <td style="padding-left:10px; font-size:9px; color:#333; border:none;">
                                <p>Skor Mentah: <strong>{{ $skorCfit['skor_mentah'] }}</strong></p>
                                @if ($skorCfit['kategori'] !== '—')
                                <p style="margin-top:3px;">Kategori: <strong>{{ $skorCfit['kategori'] }}</strong></p>
                                @endif
                            </td>
                        </tr>
                    </table>
                @endif

            {{-- Kraepelin — Grid: tabel Panker/Janker/Hanker/Tianker --}}
            @elseif ($alatTes['format_dasar'] === 'Grid' && ($gridRingkasan = $alatTes['grid_ringkasan'] ?? null))
                @php
                    $gridSkor       = collect($alatTes['skor_ringkas'])->keyBy(fn($s) => explode(' - ', $s['dimensi'])[0]);
                    $panker         = $gridSkor->get('KECEPATAN');
                    $janker         = $gridSkor->get('KEAJEGAN');
                    $tianker        = $gridSkor->get('KETELITIAN');
                    $hankerRaw      = $gridSkor->get('KETAHANAN');
                    $hanker         = $alatTes['grid_hanker'] ?? ['y0'=>0,'y50'=>0,'delta'=>0,'trend'=>'stabil'];
                    $titikTertinggi = $alatTes['grid_titikTertinggi'] ?? null;
                    $titikTerendah  = $alatTes['grid_titikTerendah']  ?? null;
                    $trendLabel = match($hanker['trend']) { 'meningkat'=>'meningkat','menurun'=>'menurun',default=>'stabil' };
                    $trendColor = match($hanker['trend']) { 'meningkat'=>'#16a34a','menurun'=>'#dc2626',default=>'#6b7280' };
                @endphp

                <table style="margin-top:10px; font-size:11px; width:100%;">
                    <thead>
                        <tr style="background:#f3f4f6; border-bottom:2px solid #000;">
                            <th style="padding:4px 8px; text-align:left; width:130px; border:1px solid #ccc;"></th>
                            <th style="padding:4px 8px; text-align:center; width:100px; border:1px solid #ccc;">Nilai</th>
                            <th style="padding:4px 8px; text-align:center; width:100px; border:1px solid #ccc;">Pembulatan</th>
                            <th style="padding:4px 8px; text-align:left; width:130px; border:1px solid #ccc;"></th>
                            <th style="padding:4px 8px; text-align:left; border:1px solid #ccc;">Analisis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:4px 8px; font-weight:700; border:1px solid #ccc;">Panker</td>
                            <td style="padding:4px 8px; text-align:center; border:1px solid #ccc;">{{ number_format($panker['skor_mentah'] ?? 0, 2) }}</td>
                            <td style="padding:4px 8px; text-align:center; background:#fee2e2; font-weight:700; border:1px solid #fca5a5;">{{ round($panker['skor_mentah'] ?? 0) }}</td>
                            <td style="padding:4px 8px; color:#6b7280; font-style:italic; border:1px solid #ccc;">(speed factor)</td>
                            <td style="padding:4px 8px; color:#1d4ed8; font-weight:600; border:1px solid #ccc;">{{ $panker && $panker['kategori'] !== '—' ? $panker['kategori'] : '—' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 8px; font-weight:700; border:1px solid #ccc;">Janker</td>
                            <td style="padding:4px 8px; text-align:center; border:1px solid #ccc;">{{ number_format($janker['skor_mentah'] ?? 0, 2) }}</td>
                            <td style="padding:4px 8px; text-align:center; background:#fee2e2; font-weight:700; border:1px solid #fca5a5;">{{ round($janker['skor_mentah'] ?? 0, 2) }}</td>
                            <td style="padding:4px 8px; color:#6b7280; font-style:italic; border:1px solid #ccc;">(rithme factor)</td>
                            <td style="padding:4px 8px; color:#1d4ed8; font-weight:600; border:1px solid #ccc;">{{ $janker && $janker['kategori'] !== '—' ? $janker['kategori'] : '—' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 8px; font-weight:700; border:1px solid #ccc;">Hanker</td>
                            <td colspan="2" style="border:1px solid #ccc;"></td>
                            <td style="padding:4px 8px; color:#6b7280; font-style:italic; border:1px solid #ccc;">(ausdeur factor)</td>
                            <td style="border:1px solid #ccc;"></td>
                        </tr>
                        <tr>
                            <td style="padding:2px 8px 2px 20px; color:#555; border:1px solid #ccc;">y(0)</td>
                            <td style="padding:2px 8px; text-align:center; border:1px solid #ccc;">{{ number_format($hanker['y0'], 7) }}</td>
                            <td colspan="3" style="border:1px solid #ccc;"></td>
                        </tr>
                        <tr>
                            <td style="padding:2px 8px 2px 20px; color:#555; border:1px solid #ccc;">y(50)</td>
                            <td style="padding:2px 8px; text-align:center; border:1px solid #ccc;">{{ number_format($hanker['y50'], 7) }}</td>
                            <td colspan="3" style="border:1px solid #ccc;"></td>
                        </tr>
                        <tr>
                            <td style="padding:2px 8px 2px 20px; color:#555; border:1px solid #ccc;">y(50)-y(0)</td>
                            <td style="padding:2px 8px; text-align:center; border:1px solid #ccc;">{{ number_format($hanker['delta'], 7) }}</td>
                            <td style="padding:2px 8px; text-align:center; background:#fee2e2; font-weight:700; border:1px solid #fca5a5;">{{ round($hanker['delta'], 2) }}</td>
                            <td style="padding:2px 8px; font-weight:600; color:{{ $trendColor }}; border:1px solid #ccc;">{{ $trendLabel }}</td>
                            <td style="padding:2px 8px; color:#1d4ed8; font-weight:600; border:1px solid #ccc;">{{ $hankerRaw && $hankerRaw['kategori'] !== '—' ? $hankerRaw['kategori'] : '—' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 8px; font-weight:700; border:1px solid #ccc;">Tianker</td>
                            <td style="padding:4px 8px; text-align:center; border:1px solid #ccc;">{{ $tianker['skor_mentah'] ?? 0 }}</td>
                            <td style="padding:4px 8px; text-align:center; background:#fee2e2; font-weight:700; border:1px solid #fca5a5;">{{ round($tianker['skor_mentah'] ?? 0) }}</td>
                            <td style="border:1px solid #ccc;"></td>
                            <td style="padding:4px 8px; color:#1d4ed8; font-weight:600; border:1px solid #ccc;">{{ $tianker && $tianker['kategori'] !== '—' ? $tianker['kategori'] : '—' }}</td>
                        </tr>
                    </tbody>
                </table>

                <table style="margin-top:6px; font-size:9px; background:#f8fafc; border:1px solid #e2e8f0; width:100%;">
                    <tr>
                        <td style="border:none; padding:4px 8px; width:50%;"><strong>Total Jawaban Benar:</strong> {{ $gridRingkasan->total_benar }}</td>
                        <td style="border:none; padding:4px 8px; width:50%;"><strong>Total Jawaban Salah:</strong> {{ $gridRingkasan->total_salah }}</td>
                    </tr>
                    <tr>
                        <td style="border:none; padding:4px 8px;"><strong>Total yang Dilewati:</strong> {{ $gridRingkasan->total_kelewat }}</td>
                        <td style="border:none; padding:4px 8px;"><strong>Total Kolom Dikerjakan:</strong> {{ $gridRingkasan->total_kolom }}</td>
                    </tr>
                    @if ($titikTertinggi)
                    <tr>
                        <td style="border:none; padding:4px 8px;"><strong style="color:#16a34a;">Titik Tertinggi:</strong> Kolom {{ $titikTertinggi['kolom'] }} = {{ $titikTertinggi['benar'] }}</td>
                        <td style="border:none; padding:4px 8px;"><strong style="color:#dc2626;">Titik Terendah:</strong> Kolom {{ $titikTerendah['kolom'] ?? '—' }} = {{ $titikTerendah['benar'] ?? '—' }}</td>
                    </tr>
                    @endif
                </table>

            {{-- Papikostik: psikogram per kelompok --}}
            @elseif (str_contains(strtoupper($alatTes['nama_alat_tes']), 'PAPIKOSTIK') && !empty($alatTes['skor_ringkas']))
                @php
                    $papScores = [];
                    foreach ($alatTes['skor_ringkas'] as $_s) {
                        $_kode = explode(' - ', $_s['dimensi'] ?? '')[0] ?? '';
                        $papScores[trim($_kode)] = $_s;
                    }
                @endphp
                <p style="margin:6px 0 10px 0; color:#666;font-size:10px;">Format: Self-Report &ndash; Skor Mentah (0-9), Kategori: R = Rendah, K = Kurang, C = Cukup, B = Baik, T = Tinggi</p>
                @foreach ($papKelompok as $_kelompokNama => $_dimensiKode)
                    <div class="bidang-header">{{ $_kelompokNama }}</div>
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
                                @php
                                    $_dbKode = $papDesc[$_skala]['db'] ?? '';
                                    $_skor = $papScores[$_dbKode] ?? null;
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
                                    <td class="mark">{!! $_katHuruf === 'R' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_katHuruf === 'K' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_katHuruf === 'C' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_katHuruf === 'B' ? '&#10003;' : '&bull;' !!}</td>
                                    <td class="mark">{!! $_katHuruf === 'T' ? '&#10003;' : '&bull;' !!}</td>
                                    <td style="font-size:9px;color:#333;line-height:1.4;">{{ $_skor && $_katHuruf !== '-' ? ($papDesc[$_skala][$_katHuruf] ?? '-') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
                <div class="footnote"><strong>Keterangan:</strong> R = Rendah &middot; K = Kurang &middot; C = Cukup &middot; B = Baik &middot; T = Tinggi</div>

            {{-- IST: psikogram SR/R/C/T/ST + klasifikasi IQ + kesimpulan --}}
            @elseif (str_contains(strtoupper($alatTes['nama_alat_tes']), 'IST') && !empty($alatTes['skor_ringkas']))
                @php
                    $skorByKode = collect($alatTes['skor_ringkas'])->keyBy(fn($s) => explode(' - ', $s['dimensi'])[0]);
                    $iqData = $skorByKode->get('IQ');
                    $iqSkor = (float) ($iqData['skor_skala'] ?? $iqData['skor_mentah'] ?? 0);
                    $klasifikasiIQ = match(true) {
                        $iqSkor >= 141 => 'Genius',
                        $iqSkor >= 128 => 'Very Superior',
                        $iqSkor >= 120 => 'Superior',
                        $iqSkor >= 111 => 'High Average (Di Atas Rata-rata)',
                        $iqSkor >= 91  => 'Average (Rata-rata)',
                        $iqSkor >= 80  => 'Low Average (Di Bawah Rata-rata)',
                        $iqSkor > 0    => 'Borderline',
                        default        => '-',
                    };
                    $aspekKuat = [];
                    $aspekLemah = [];
                    foreach ($aspekPsikologisIST as $_ap) {
                        $_s = $skorByKode->get($_ap['kode']);
                        $_skor = (float)($_s['skor_skala'] ?? 0);
                        if ($_skor >= 110) $aspekKuat[] = $_ap['nama'];
                        if ($_skor > 0 && $_skor < 90) $aspekLemah[] = $_ap['nama'];
                    }
                @endphp

                <table style="width:100%;border-collapse:collapse;font-size:11px;">
                    <thead>
                        <tr>
                            <th colspan="2" style="background:#4b5563;color:#fff;padding:8px 10px;text-align:left;border:1px solid #ccc;" rowspan="2">ASPEK-ASPEK PSIKOLOGIS</th>
                            <th colspan="5" style="background:#4b5563;color:#fff;padding:6px;text-align:center;border:1px solid #ccc;">KATEGORI</th>
                        </tr>
                        <tr>
                            <th style="background:#6b7280;color:#fff;padding:5px 8px;text-align:center;border:1px solid #ccc;width:8%;">SR<br><span style="font-weight:400;font-size:9px;">Sangat Rendah</span></th>
                            <th style="background:#6b7280;color:#fff;padding:5px 8px;text-align:center;border:1px solid #ccc;width:8%;">R<br><span style="font-weight:400;font-size:9px;">Rendah</span></th>
                            <th style="background:#6b7280;color:#fff;padding:5px 8px;text-align:center;border:1px solid #ccc;width:8%;">C<br><span style="font-weight:400;font-size:9px;">Cukup</span></th>
                            <th style="background:#6b7280;color:#fff;padding:5px 8px;text-align:center;border:1px solid #ccc;width:8%;">T<br><span style="font-weight:400;font-size:9px;">Tinggi</span></th>
                            <th style="background:#6b7280;color:#fff;padding:5px 8px;text-align:center;border:1px solid #ccc;width:8%;">ST<br><span style="font-weight:400;font-size:9px;">Sangat Tinggi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aspekPsikologisIST as $aspek)
                            @php
                                $s = $skorByKode->get($aspek['kode']);
                                $skor = (float)($s['skor_skala'] ?? 0);
                                $kat = kategoriIST($skor);
                            @endphp
                            <tr style="border-bottom:1px solid #e0e3e5;">
                                <td style="padding:6px 8px;border:1px solid #ddd;color:#64748b;text-align:center;width:4%;">{{ $aspek['no'] }}</td>
                                <td style="padding:6px 10px;border:1px solid #ddd;">
                                    <strong style="color:#1a1a1a;font-size:11px;">{{ $aspek['nama'] }}</strong><br>
                                    <span style="color:#64748b;font-size:9px;">{{ $aspek['deskripsi'] }}</span>
                                </td>
                                <td style="padding:6px;text-align:center;border:1px solid #ddd;">{{ $kat === 'SR' ? 'X' : '' }}</td>
                                <td style="padding:6px;text-align:center;border:1px solid #ddd;">{{ $kat === 'R'  ? 'X' : '' }}</td>
                                <td style="padding:6px;text-align:center;border:1px solid #ddd;">{{ $kat === 'C'  ? 'X' : '' }}</td>
                                <td style="padding:6px;text-align:center;border:1px solid #ddd;">{{ $kat === 'T'  ? 'X' : '' }}</td>
                                <td style="padding:6px;text-align:center;border:1px solid #ddd;">{{ $kat === 'ST' ? 'X' : '' }}</td>
                            </tr>
                        @endforeach
                        @if($iqData)
                        <tr style="background:#f0f7f9;font-weight:700;">
                            <td style="padding:6px 10px;text-align:center;border:1px solid #ddd;"></td>
                            <td style="padding:6px 10px;border:1px solid #ddd;color:#00843D;">IQ TOTAL: <strong>{{ $iqSkor > 0 ? number_format($iqSkor, 0) : '-' }}</strong> &mdash; {{ $klasifikasiIQ }}</td>
                            <td colspan="5" style="border:1px solid #ddd;"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <div style="margin-top:10px; padding:10px; background:#f8fafc; border:1px solid #e2e8f0; font-size:10px; line-height:1.7; text-align:justify;">
                    <strong>KESIMPULAN:</strong>
                    @if($iqSkor > 0)
                        Hasil pemeriksaan psikologis menunjukkan bahwa peserta memiliki tingkat kecerdasan umum yang tergolong <strong>{{ $klasifikasiIQ }}</strong> dengan skor IQ {{ number_format($iqSkor, 0) }}.
                        @if(count($aspekKuat) > 0)
                            Kemampuan yang menonjol meliputi: <strong>{{ implode(', ', $aspekKuat) }}</strong>.
                        @endif
                        @if(count($aspekLemah) > 0)
                            Aspek yang masih perlu dikembangkan: <strong>{{ implode(', ', $aspekLemah) }}</strong>.
                        @endif
                    @else
                        Kesimpulan akan tersedia setelah scoring selesai diproses.
                    @endif
                </div>

            {{-- Alat tes lain: tabel skor per dimensi --}}
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

</div>
</body>
</html>
