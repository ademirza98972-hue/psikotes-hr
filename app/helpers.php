<?php

if (! function_exists('kode_badge_class')) {
    /**
     * Deterministic badge colour for an alat tes kode.
     * Same kode always gets the same colour; new kodes get one automatically.
     *
     * $style = 'default'  → bg-X-100 text-X-700           (alat-tes, bank-soal)
     * $style = 'soft'     → bg-X-50 text-X-700 border border-X-200  (penjadwalan)
     */
    function kode_badge_class(string $kode, string $style = 'default'): string
    {
        $default = [
            'bg-emerald-100 text-emerald-700',
            'bg-blue-100 text-blue-700',
            'bg-violet-100 text-violet-700',
            'bg-rose-100 text-rose-700',
            'bg-amber-100 text-amber-700',
            'bg-cyan-100 text-cyan-700',
            'bg-indigo-100 text-indigo-700',
            'bg-orange-100 text-orange-700',
            'bg-pink-100 text-pink-700',
            'bg-teal-100 text-teal-700',
        ];

        $soft = [
            'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'bg-blue-50 text-blue-700 border border-blue-200',
            'bg-violet-50 text-violet-700 border border-violet-200',
            'bg-rose-50 text-rose-700 border border-rose-200',
            'bg-amber-50 text-amber-700 border border-amber-200',
            'bg-cyan-50 text-cyan-700 border border-cyan-200',
            'bg-indigo-50 text-indigo-700 border border-indigo-200',
            'bg-orange-50 text-orange-700 border border-orange-200',
            'bg-pink-50 text-pink-700 border border-pink-200',
            'bg-teal-50 text-teal-700 border border-teal-200',
        ];

        $palette = $style === 'soft' ? $soft : $default;
        return $palette[abs(crc32($kode)) % count($palette)];
    }
}

if (! function_exists('klasifikasi_iq')) {
    /** Klasifikasi IQ sesuai patokan IST_Norma_Usia.xls (batas atas inklusif). */
    function klasifikasi_iq(float $iq): string
    {
        return match (true) {
            $iq <= 0   => '-',
            $iq <= 65  => 'Mentally Defective',
            $iq <= 79  => 'Borderline',
            $iq <= 90  => 'Low Average (Di Bawah Rata-rata)',
            $iq <= 110 => 'Average (Rata-rata)',
            $iq <= 119 => 'High Average (Di Atas Rata-rata)',
            $iq <= 127 => 'Superior',
            $iq <= 139 => 'Very Superior',
            default    => 'Genius',
        };
    }
}

if (! function_exists('kategori_subtes_ist')) {
    /**
     * Kategori SW subtes IST, patokan IST_Norma_Usia.xls: ≤80 Sangat Rendah, ≤94 Rendah,
     * ≤99 Sedang + ≤104 Cukup (digabung jadi C), ≤118 Tinggi, sisanya Sangat Tinggi.
     */
    function kategori_subtes_ist(float $sw): string
    {
        return match (true) {
            $sw <= 0   => '',
            $sw <= 80  => 'SR',
            $sw <= 94  => 'R',
            $sw <= 104 => 'C',
            $sw <= 118 => 'T',
            default    => 'ST',
        };
    }
}

if (! function_exists('interpretasi_papi')) {
    /** Interpretasi skor faktor PAPI Kostick (0–9) sesuai kolom "Analisis" di Papi Kostick.xls. */
    function interpretasi_papi(string $faktor, float $skor): string
    {
        // [batas atas skor (inklusif), teks]; batas null = sisanya
        $tabel = [
            'N' => [[3, 'Cenderung ragu-ragu dalam situasi pengambilan keputusan, cenderung ragu-ragu, menunda atau menghindari situasi pengambilan keputusan'], [4, 'Berhati-hati dan cenderung ragu-ragu'], [6, 'Cukup bertanggung jawab terhadap pekerjaan'], [null, 'Ketekunan, tanggung jawab terhadap tugas tinggi']],
            'G' => [[4, 'Bekerja hanya untuk mengejar kesenangan saja bukan untuk memberikan suatu hasil yang baik'], [null, 'Kemauan bekerja keras tinggi']],
            'A' => [[5, 'Mencerminkan ketidakpastian tujuan. Juga mencerminkan kepuasan dalam suatu pekerjaan, tidak perlu melanjutkan usaha untuk sukses'], [null, 'Tujuan-tujuan didefinisikan secara jelas, kebutuhan untuk sukses tinggi, ambisi pribadi tinggi']],
            'L' => [[4, 'Cenderung tidak suka aktif menggunakan orang lain dalam bekerja'], [null, 'Yaitu tingkat dimana seseorang memproyeksikan dirinya sebagai pemimpin suatu tingkat, dimana ia mencoba menggunakan orang lain untuk mencapai tujuannya. Nilai S menunjukkan apakah pola kepemimpinannya bersifat persuasive, demokratis, atau otoriter']],
            'P' => [[4, 'Menurunnya keinginan untuk bertanggung jawab terhadap pekerjaan dan tindakan orang lain'], [null, 'Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab']],
            'I' => [[3, 'Ragu-ragu sampai penundaan/menolak situasi pengambilan keputusan'], [4, 'Berhati-hati sampai ragu-ragu dalam membuat keputusan'], [7, 'Mudah dan lancar sampai berhati-hati dalam membuat keputusan'], [null, 'Tidak ragu-ragu dalam proses pengambilan keputusan']],
            'T' => [[3, 'Melakukan segala sesuatu menurut kemauannya sendiri'], [null, 'Tergolong aktif secara internal dan mental']],
            'V' => [[4, 'Keaktifannya tergolong rendah, cenderung pasif (hanya duduk-duduk saja)'], [null, 'Keaktifannya secara fisik tergolong agak baik, cenderung tipe sportif']],
            'X' => [[1, 'Cenderung pemalu, suka menyendiri'], [3, 'Rendah hati, tulus'], [5, 'Khusus, memiliki pola yang nyata'], [null, 'Membutuhkan perhatian yang nyata']],
            'S' => [[5, 'Memiliki penilaian yang rendah terhadap hubungan sosial, cenderung kurang percaya pada orang lain'], [null, 'Tingkat kepercayaan dalam hubungan sosial tinggi, menyukai interaksi sosial']],
            'B' => [[3, 'Selektif, secara umum melepaskan diri dari kelompok'], [5, 'Ada kebutuhan untuk diterima dan diakui tetapi tidak terlalu mudah dipengaruhi oleh kelompok'], [null, 'Kebutuhan untuk disukai, diakui oleh semua orang. Mudah dipengaruhi kelompok']],
            'O' => [[2, 'Tidak menyukai hubungan antar pribadi. Tidak menyukai interaksi perseorangan'], [4, 'Sadar akan kebutuhan antar pribadi tetapi dapat melepaskan diri dari orang lain/tidak terlalu tergantung'], [null, 'Ketergantungan yang sangat besar akan pengakuan dan penerimaan diri']],
            'R' => [[4, 'Kurang perhatian-praktis'], [null, 'Penekanan pada nialai-nilai penalaran tergolong tinggi']],
            'D' => [[3, 'Menyadari kebutuhan akan kecermatan tetapi secara pribadi tidak berminat menangani hal-hal detail'], [null, 'Minat menangani hal-hal detail tergolong tinggi']],
            'C' => [[2, 'Fleksibilitas sampai ketidak-teraturan'], [5, 'Tergolong teratur tetapi dengan fleksibilitas'], [null, 'Memiliki keteraturan yang sangat tinggi, cenderung kaku']],
            'Z' => [[2, 'Tidak menyukai dan menolak perubahan. Cenderung menggunakan pendekatan-pendekatan tradisional'], [4, 'Tidak suka akan perubahan jika dipaksakan kepadanya'], [6, 'Mudah menyesuaikan diri'], [7, 'Pembuat perubahan yang selektif. Berpikir jauh ke depan'], [null, 'Mudah gelisah, mudah frustrasi mungkin karena segala sesuatu bergerak tidak cukup cepat']],
            'E' => [[1, 'Terbuka, cepat bereaksi, tidak memikirkan nilai dalam pengendalian diri'], [3, 'Terbuka'], [6, 'Memiliki pendekatan emosional yang seimbang. Mampu mengendalikan perasaannya'], [null, 'Sangat menepatkan nilai-nilai dalam setiap aktivitasnya. Kebutuhan pengendalian diri yang berlebih-lebihan, mungkin digunakan sebagai defence mechanisme']],
            'K' => [[2, 'Selalu menghindari masalah. Cenderung mengabaikan situasi atau cenderung menolak untuk mengenali sesuatu sebagai sebuah masalah'], [4, 'Lebih menyukai lingkungan yang tenang. Menghindari konflik. Cenderung menunda masalah'], [5, 'Kukuh pendirian, cenderung keras kepala'], [7, 'Agresi pribadi yang berkaitan dengan pekerjaan, dorongan dan semangat bersaing'], [null, 'Agresif, cenderung defensive']],
            'F' => [[1, 'Cenderung egois, kemungkinan bisa bersikap memberontak'], [3, 'Mengurus kepentingan diri sendiri'], [5, 'Setia terhadap perusahaan'], [null, 'Bersikap setia dan membantu secara pribadi, ada kemungkinan bantuannya bermotivasi politis']],
            'W' => [[3, 'Berorientasi pada tujuan, mandiri'], [5, 'Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya'], [null, 'Meningkatnya orientasi terhadap tugas dan membutuhkan instruksi yang jelas']],
        ];

        foreach ($tabel[$faktor] ?? [] as [$batas, $teks]) {
            if ($batas === null || $skor <= $batas) {
                return $teks;
            }
        }

        return '-';
    }
}

if (! function_exists('subtes_ist')) {
    /**
     * 9 subtes IST (Amthauer) urut lembar jawab: kode => [nama subtes, aspek yang diukur, kelompok kemampuan].
     */
    function subtes_ist(): array
    {
        return [
            'SE' => ['Satzergänzung — Melengkapi Kalimat', 'Penalaran praktis & pengambilan keputusan (common sense)', 'Verbal'],
            'WA' => ['Wortauswahl — Persamaan Kata', 'Pemahaman makna kata & kepekaan bahasa', 'Verbal'],
            'AN' => ['Analogien — Hubungan Kata', 'Fleksibilitas berpikir & kemampuan mengombinasikan', 'Verbal'],
            'GE' => ['Gemeinsamkeiten — Kesamaan Pengertian', 'Abstraksi verbal & pembentukan pengertian', 'Verbal'],
            'RA' => ['Rechenaufgaben — Hitungan', 'Berhitung praktis & penalaran numerik', 'Numerik'],
            'ZR' => ['Zahlenreihen — Deret Angka', 'Berpikir induktif-teoritis dengan angka', 'Numerik'],
            'FA' => ['Figurenauswahl — Memilih Bentuk', 'Membayangkan & menyusun bentuk (visualisasi)', 'Figural'],
            'WU' => ['Würfelaufgaben — Kubus', 'Kemampuan ruang tiga dimensi (spasial)', 'Figural'],
            'ME' => ['Merkaufgaben — Ingatan', 'Daya ingat & konsentrasi', 'Ingatan'],
        ];
    }
}

if (! function_exists('faktor_papi')) {
    /**
     * 20 faktor PAPI Kostick (Papi Kostick.xls, kolom Aspek/faktor): huruf => [kode dimensi DB, nama faktor, kelompok aspek].
     * Urutan = urutan kelompok di lembar skor.
     */
    function faktor_papi(): array
    {
        return [
            'N' => ['TGS_PRIBADI', 'Penyelesaian secara prestasi', 'Arah Kerja'],
            'G' => ['KERJA_KERAS', 'Peranan sebagai pekerja keras', 'Arah Kerja'],
            'A' => ['BERPRESTASI', 'Hasrat untuk berprestasi', 'Arah Kerja'],
            'L' => ['PIMPINAN', 'Peran sebagai pimpinan', 'Kepemimpinan'],
            'P' => ['KENDALI_ORG', 'Pengendalian orang lain', 'Kepemimpinan'],
            'I' => ['PUTUSAN', 'Mudah dalam mengambil keputusan', 'Kepemimpinan'],
            'T' => ['SIBUK', 'Tipe selalu sibuk', 'Aktivitas'],
            'V' => ['SEMANGAT', 'Tipe yang bersemangat', 'Aktivitas'],
            'X' => ['PERHATIAN', 'Kebutuhan untuk mendapatkan perhatian', 'Pergaulan'],
            'S' => ['PERGAULAN_LUAS', 'Pergaulan luas', 'Pergaulan'],
            'B' => ['BETAH_KELOMPOK', 'Kebutuhan berkelompok', 'Pergaulan'],
            'O' => ['DEKAT_SAYANG', 'Kebutuhan untuk dekat dan menyayangi', 'Pergaulan'],
            'R' => ['TEORITIS', 'Tipe teoritikal', 'Gaya Kerja'],
            'D' => ['DETAIL_KERJA', 'Suka pekerjaan yang terperinci', 'Gaya Kerja'],
            'C' => ['TYPE_PENGATUR', 'Tipe teratur', 'Gaya Kerja'],
            'Z' => ['HASRAT_BERUBAH', 'Hasrat untuk berubah', 'Sifat'],
            'E' => ['KENDALI_EMOSI', 'Pengendalian emosi', 'Sifat'],
            'K' => ['AGRESI', 'Agresi', 'Sifat'],
            'F' => ['DUKUNGAN_ATASAN', 'Dukungan terhadap atasan', 'Ketaatan'],
            'W' => ['TAAT_ATURAN', 'Kebutuhan taat pada aturan dan pengarahan', 'Ketaatan'],
        ];
    }
}
