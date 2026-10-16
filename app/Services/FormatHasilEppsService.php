<?php

namespace App\Services;

use App\Models\AlatTes;
use App\Models\HasilSkorPeserta;
use App\Models\LevelDimensi;
use Illuminate\Support\Collection;

class FormatHasilEppsService
{
    /** Urutan standar lembar EPPS: kode => [nama aspek (istilah perusahaan), nama kebutuhan asli Edwards]. */
    public const NAMA_KEBUTUHAN = [
        'ach' => ['Motivasi Berprestasi', 'Achievement'],
        'def' => ['Ketaatan', 'Deference'],
        'ord' => ['Sistematika Kerja', 'Order'],
        'exh' => ['Menonjolkan Diri', 'Exhibition'],
        'aut' => ['Kemandirian', 'Autonomy'],
        'aff' => ['Keterampilan Sosial', 'Affiliation'],
        'int' => ['Empati', 'Intraception'],
        'suc' => ['Kebutuhan Afeksi', 'Succorance'],
        'dom' => ['Dominasi', 'Dominance'],
        'aba' => ['Kejujuran', 'Abasement'],
        'nur' => ['Kontak Sosial', 'Nurturance'],
        'chg' => ['Kreativitas', 'Change'],
        'end' => ['Ketekunan', 'Endurance'],
        'het' => ['Interaksi Sosial', 'Heterosexuality'],
        'agg' => ['Agresifitas', 'Aggression'],
    ];

    /** Kategori persentil (EPPS.xls) → kunci teks interpretasi. */
    public const KODE_KATEGORI = [
        'Sangat Rendah' => 'R', 'Rendah' => 'K', 'Sedang' => 'C', 'Tinggi' => 'B', 'Sangat Tinggi' => 'T',
    ];

    /** Teks interpretasi per kebutuhan & kategori (R = Sangat Rendah … T = Sangat Tinggi). */
    public const INTERPRETASI = [
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

    public static function interpretasi(string $kode, ?string $kategori): string
    {
        return self::INTERPRETASI[$kode][self::KODE_KATEGORI[$kategori] ?? ''] ?? '-';
    }

    /**
     * Format hasil skor EPPS untuk tampilan.
     *
     * Mengembalikan array/collection dengan 16 baris (15 kebutuhan + con):
     * [
     *   ['dimensi' => 'Achievement', 'skor_mentah' => 5, 'skor_persentil' => 72.5, 'kategori' => 'Tinggi'],
     *   ...
     * ]
     *
     * Mengembalikan Collection kosong bila EPPS tidak ditemukan atau belum ada skor.
     */
    public function formatHasilEppsUntukTampilan(int $userId, int $sesiTesId): Collection
    {
        $alatTesEpps = AlatTes::where('kode', 'EPPS')->first();

        if (! $alatTesEpps) {
            return collect();
        }

        $rows = HasilSkorPeserta::query()
            ->selectRaw('
                hasil_skor_peserta.dimensi_id AS dimensi_id,
                hasil_skor_peserta.skor_mentah AS skor_mentah,
                hasil_skor_peserta.skor_akhir AS skor_akhir,
                dimensi_alat_tes.kode_dimensi AS kode_dimensi,
                dimensi_alat_tes.nama_dimensi AS nama_dimensi
            ')
            ->join('dimensi_alat_tes', 'dimensi_alat_tes.id', '=', 'hasil_skor_peserta.dimensi_id')
            ->where('hasil_skor_peserta.user_id', $userId)
            ->where('hasil_skor_peserta.sesi_tes_id', $sesiTesId)
            ->where('hasil_skor_peserta.alat_tes_id', $alatTesEpps->id)
            ->orderBy('dimensi_alat_tes.urutan')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $dimensiIds = $rows->pluck('dimensi_id')->unique();

        $levelByDimensi = LevelDimensi::query()
            ->where('alat_tes_id', $alatTesEpps->id)
            ->whereIn('dimensi_id', $dimensiIds)
            ->get()
            ->groupBy('dimensi_id');

        return $rows->map(function ($skor) use ($levelByDimensi) {
            $kategori = $this->cariKategoriLevel(
                $levelByDimensi->get($skor->dimensi_id, collect()),
                (float) $skor->skor_akhir
            );

            return [
                'dimensi'        => $skor->kode_dimensi . ' - ' . $skor->nama_dimensi,
                'skor_mentah'    => $skor->skor_mentah,
                'skor_persentil' => $skor->skor_akhir,
                'kategori'       => $kategori,
            ];
        });
    }

    /**
     * Cari label level_dimensi yang rentang [skor_min, skor_max] memuat $skorPersentil.
     * Mengembalikan null bila tidak ada level yang cocok.
     */
    private function cariKategoriLevel(Collection $levels, float $skorPersentil): ?string
    {
        foreach ($levels as $level) {
            if ((float) $level->skor_min <= $skorPersentil && (float) $level->skor_max >= $skorPersentil) {
                return $level->label;
            }
        }

        return null;
    }
}
