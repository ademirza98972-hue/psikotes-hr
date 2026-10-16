<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\JawabanPeserta;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

/**
 * 225 soal EPPS + kunci skoring sesuai patokan "12. EPPS/EPPS.xls" (softwarepsikotes).
 * Idempoten: aman dijalankan ulang, jawaban peserta tidak dihapus.
 */
class EppsSoalSeeder extends Seeder
{
    /**
     * Kunci per nomor: [kebutuhan bila pilih A, kebutuhan bila pilih B].
     * 15 soal diagonal matriks (1, 7, 13, 19, 25, 101, ..., 225) tidak diskor → [null, null],
     * sehingga total skor 15 kebutuhan selalu 210.
     */
    public const KUNCI = [
            1 => [null, null], 2 => ['def', 'ach'], 3 => ['ord', 'ach'], 4 => ['exh', 'ach'], 5 => ['aut', 'ach'],
            6 => ['ach', 'def'], 7 => [null, null], 8 => ['ord', 'def'], 9 => ['exh', 'def'], 10 => ['aut', 'def'],
            11 => ['ach', 'ord'], 12 => ['def', 'ord'], 13 => [null, null], 14 => ['exh', 'ord'], 15 => ['aut', 'ord'],
            16 => ['ach', 'exh'], 17 => ['def', 'exh'], 18 => ['ord', 'exh'], 19 => [null, null], 20 => ['aut', 'exh'],
            21 => ['ach', 'aut'], 22 => ['def', 'aut'], 23 => ['ord', 'aut'], 24 => ['exh', 'aut'], 25 => [null, null],
            26 => ['ach', 'aff'], 27 => ['def', 'aff'], 28 => ['ord', 'aff'], 29 => ['exh', 'aff'], 30 => ['aut', 'aff'],
            31 => ['ach', 'int'], 32 => ['def', 'int'], 33 => ['ord', 'int'], 34 => ['exh', 'int'], 35 => ['aut', 'int'],
            36 => ['ach', 'suc'], 37 => ['def', 'suc'], 38 => ['ord', 'suc'], 39 => ['exh', 'suc'], 40 => ['aut', 'suc'],
            41 => ['ach', 'dom'], 42 => ['def', 'dom'], 43 => ['ord', 'dom'], 44 => ['exh', 'dom'], 45 => ['aut', 'dom'],
            46 => ['ach', 'aba'], 47 => ['def', 'aba'], 48 => ['ord', 'aba'], 49 => ['exh', 'aba'], 50 => ['aut', 'aba'],
            51 => ['ach', 'nur'], 52 => ['def', 'nur'], 53 => ['ord', 'nur'], 54 => ['exh', 'nur'], 55 => ['aut', 'nur'],
            56 => ['ach', 'chg'], 57 => ['def', 'chg'], 58 => ['ord', 'chg'], 59 => ['exh', 'chg'], 60 => ['aut', 'chg'],
            61 => ['ach', 'end'], 62 => ['def', 'end'], 63 => ['ord', 'end'], 64 => ['exh', 'end'], 65 => ['aut', 'end'],
            66 => ['ach', 'het'], 67 => ['def', 'het'], 68 => ['ord', 'het'], 69 => ['exh', 'het'], 70 => ['aut', 'het'],
            71 => ['ach', 'agg'], 72 => ['def', 'agg'], 73 => ['ord', 'agg'], 74 => ['exh', 'agg'], 75 => ['aut', 'agg'],
            76 => ['aff', 'ach'], 77 => ['int', 'ach'], 78 => ['suc', 'ach'], 79 => ['dom', 'ach'], 80 => ['aba', 'ach'],
            81 => ['aff', 'def'], 82 => ['int', 'def'], 83 => ['suc', 'def'], 84 => ['dom', 'def'], 85 => ['aba', 'def'],
            86 => ['aff', 'ord'], 87 => ['int', 'ord'], 88 => ['suc', 'ord'], 89 => ['dom', 'ord'], 90 => ['aba', 'ord'],
            91 => ['aff', 'exh'], 92 => ['int', 'exh'], 93 => ['suc', 'exh'], 94 => ['dom', 'exh'], 95 => ['aba', 'exh'],
            96 => ['aff', 'aut'], 97 => ['int', 'aut'], 98 => ['suc', 'aut'], 99 => ['dom', 'aut'], 100 => ['aba', 'aut'],
            101 => [null, null], 102 => ['int', 'aff'], 103 => ['suc', 'aff'], 104 => ['dom', 'aff'], 105 => ['aba', 'aff'],
            106 => ['aff', 'int'], 107 => [null, null], 108 => ['suc', 'int'], 109 => ['dom', 'int'], 110 => ['aba', 'int'],
            111 => ['aff', 'suc'], 112 => ['int', 'suc'], 113 => [null, null], 114 => ['dom', 'suc'], 115 => ['aba', 'suc'],
            116 => ['aff', 'dom'], 117 => ['int', 'dom'], 118 => ['suc', 'dom'], 119 => [null, null], 120 => ['aba', 'dom'],
            121 => ['aff', 'aba'], 122 => ['int', 'aba'], 123 => ['suc', 'aba'], 124 => ['dom', 'aba'], 125 => [null, null],
            126 => ['aff', 'nur'], 127 => ['int', 'nur'], 128 => ['suc', 'nur'], 129 => ['dom', 'nur'], 130 => ['aba', 'nur'],
            131 => ['aff', 'chg'], 132 => ['int', 'chg'], 133 => ['suc', 'chg'], 134 => ['dom', 'chg'], 135 => ['aba', 'chg'],
            136 => ['aff', 'end'], 137 => ['int', 'end'], 138 => ['suc', 'end'], 139 => ['dom', 'end'], 140 => ['aba', 'end'],
            141 => ['aff', 'het'], 142 => ['int', 'het'], 143 => ['suc', 'het'], 144 => ['dom', 'het'], 145 => ['aba', 'het'],
            146 => ['aff', 'agg'], 147 => ['int', 'agg'], 148 => ['suc', 'agg'], 149 => ['dom', 'agg'], 150 => ['aba', 'agg'],
            151 => ['nur', 'ach'], 152 => ['chg', 'ach'], 153 => ['end', 'ach'], 154 => ['het', 'ach'], 155 => ['agg', 'ach'],
            156 => ['nur', 'def'], 157 => ['chg', 'def'], 158 => ['end', 'def'], 159 => ['het', 'def'], 160 => ['agg', 'def'],
            161 => ['nur', 'ord'], 162 => ['chg', 'ord'], 163 => ['end', 'ord'], 164 => ['het', 'ord'], 165 => ['agg', 'ord'],
            166 => ['nur', 'exh'], 167 => ['chg', 'exh'], 168 => ['end', 'exh'], 169 => ['het', 'exh'], 170 => ['agg', 'exh'],
            171 => ['nur', 'aut'], 172 => ['chg', 'aut'], 173 => ['end', 'aut'], 174 => ['het', 'aut'], 175 => ['agg', 'aut'],
            176 => ['nur', 'aff'], 177 => ['chg', 'aff'], 178 => ['end', 'aff'], 179 => ['het', 'aff'], 180 => ['agg', 'aff'],
            181 => ['nur', 'int'], 182 => ['chg', 'int'], 183 => ['end', 'int'], 184 => ['het', 'int'], 185 => ['agg', 'int'],
            186 => ['nur', 'suc'], 187 => ['chg', 'suc'], 188 => ['end', 'suc'], 189 => ['het', 'suc'], 190 => ['agg', 'suc'],
            191 => ['nur', 'dom'], 192 => ['chg', 'dom'], 193 => ['end', 'dom'], 194 => ['het', 'dom'], 195 => ['agg', 'dom'],
            196 => ['nur', 'aba'], 197 => ['chg', 'aba'], 198 => ['end', 'aba'], 199 => ['het', 'aba'], 200 => ['agg', 'aba'],
            201 => [null, null], 202 => ['chg', 'nur'], 203 => ['end', 'nur'], 204 => ['het', 'nur'], 205 => ['agg', 'nur'],
            206 => ['nur', 'chg'], 207 => [null, null], 208 => ['end', 'chg'], 209 => ['het', 'chg'], 210 => ['agg', 'chg'],
            211 => ['nur', 'end'], 212 => ['chg', 'end'], 213 => [null, null], 214 => ['het', 'end'], 215 => ['agg', 'end'],
            216 => ['nur', 'het'], 217 => ['chg', 'het'], 218 => ['end', 'het'], 219 => [null, null], 220 => ['agg', 'het'],
            221 => ['nur', 'agg'], 222 => ['chg', 'agg'], 223 => ['end', 'agg'], 224 => ['het', 'agg'], 225 => [null, null],
    ];

    /** Pasangan [soal asli, soal ulangan] untuk skor konsistensi (con), 15 pasang. */
    public const PASANGAN_KONSISTENSI = [
        [1, 151], [7, 157], [13, 163], [19, 169], [25, 175],
        [26, 101], [32, 107], [38, 113], [44, 119], [50, 125],
        [51, 201], [57, 207], [63, 213], [69, 219], [75, 225],
    ];

    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'EPPS')->first();
        if (!$alatTes) {
            $this->command?->error('AlatTes EPPS tidak ditemukan.');
            return;
        }

        $dimensiMap = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->pluck('id', 'kode_dimensi');

        $required = ['ach','def','ord','exh','aut','aff','int','suc','dom','aba','nur','chg','end','het','agg'];
        foreach ($required as $kode) {
            if (!isset($dimensiMap[$kode])) {
                $this->command?->error("Dimensi '{$kode}' tidak ditemukan.");
                return;
            }
        }

        $teks = $this->getTeks();

        foreach (self::KUNCI as $nomor => $dimPerOpsi) {
            $soal = Soal::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'nomor' => $nomor],
                [
                    'teks_soal'             => 'Pilih salah satu pernyataan yang paling menggambarkan diri Anda.',
                    'tipe_format'           => 'forced_choice',
                    'urutan'                => $nomor,
                    'duplikat_dari_soal_id' => null,
                ]
            );

            foreach ([1, 2] as $urutan) {
                $opsi = OpsiJawaban::updateOrCreate(
                    ['soal_id' => $soal->id, 'urutan' => $urutan],
                    ['teks_opsi' => $teks[$nomor][$urutan - 1] ?? 'Pernyataan ' . ($urutan === 1 ? 'A' : 'B') . " nomor {$nomor}"]
                );

                BobotOpsiDimensi::where('opsi_jawaban_id', $opsi->id)->delete();
                $dim = $dimPerOpsi[$urutan - 1];
                if ($dim) {
                    BobotOpsiDimensi::create([
                        'opsi_jawaban_id' => $opsi->id,
                        'dimensi_id'      => $dimensiMap[$dim],
                        'bobot'           => 1,
                        'is_reverse'      => false,
                    ]);
                }

                // Bersihkan opsi ganda dengan urutan sama (sisa seeder konsistensi lama):
                // pindahkan jawaban peserta ke opsi resmi, lalu hapus opsi gandanya.
                $ganda = OpsiJawaban::where('soal_id', $soal->id)->where('urutan', $urutan)
                    ->where('id', '!=', $opsi->id)->pluck('id');
                if ($ganda->isNotEmpty()) {
                    JawabanPeserta::whereIn('opsi_dipilih_id', $ganda)->update(['opsi_dipilih_id' => $opsi->id]);
                    BobotOpsiDimensi::whereIn('opsi_jawaban_id', $ganda)->delete();
                    OpsiJawaban::whereIn('id', $ganda)->delete();
                }
            }
        }

        $soalIdByNomor = Soal::where('alat_tes_id', $alatTes->id)->pluck('id', 'nomor');
        foreach (self::PASANGAN_KONSISTENSI as [$asli, $ulangan]) {
            Soal::where('id', $soalIdByNomor[$ulangan])->update(['duplikat_dari_soal_id' => $soalIdByNomor[$asli]]);
        }

        $this->command?->info('EppsSoalSeeder selesai: 225 soal, kunci sesuai EPPS.xls, 15 pasangan konsistensi.');
    }

    private function getTeks(): array
    {
        return [
            1 => ["Saya ingin menolong teman-teman saya, bila mereka berada dalam kesulitan.", "Saya ingin berkarya dan bekerja sebaik mungkin."],
            2 => ["Saya ingin mengetahui pandangan tokoh-tokoh dan para ahli mengenai berbagai masalah yang menarik perhatian saya.", "Saya ingin ahli dalam suatu pekerjaan, jabatan, atau bidang khusus."],
            3 => ["Saya ingin setiap pekerjaan, tulisan saya, teliti, rapi dan tersusun dengan baik.", "Saya ingin ahli dalam suatu pekerjaan, jabatan atau bidang khusus."],
            4 => ["Saya suka menceritakan hal-hal yang lucu waktu pesta.", "Saya ingin menulis roman, atau sandiwara yang hebat."],
            5 => ["Saya ingin dapat berbuat sekehendak hati.", "Saya ingin dapat menyatakan bahwa saya telah menyelesaikan dengan baik suatu pekerjaan yang memang sulit."],
            6 => ["Saya ingin memecahkan teka-teki dan persoalan yang memang sukar bagi orang lain.", "Saya suka mengikuti petunjuk-petunjuk dan melakukan hal-hal yang orang harapkan dari diri saya."],
            7 => ["Saya ingin mengalami hal-hal yang baru dan perubahan-perubahan dalam kehidupan saya sehari-hari.", "Saya suka menyatakan pada atasan saya bahwa mereka telah melakukan suatu pekerjaan dengan baik, bila memang demikian halnya menurut pikiran saya."],
            8 => ["Saya terbiasa merencanakan dan mengatur detail-detail dari setiap pekerjaan yang harus saya lakukan.", "Saya suka mengikuti petunjuk-petunjuk dan melakukan hal-hal yang orang harapkan dari diri saya."],
            9 => ["Saya ingin orang memperhatikan dan berkomentar mengenai penampilan saya di depan umum.", "Saya suka membaca riwayat hidup orang-orang besar."],
            10 => ["Saya suka mengelakkan keadaan untuk berlaku secara konvensional (kebiasaan umum).", "Saya suka membaca riwayat hidup orang-orang besar."],
            11 => ["Saya ingin ahli dalam suatu pekerjaan, jabatan atau bidang khusus.", "Saya ingin pekerjaan saya diatur dan direncanakan sebelum dimulai."],
            12 => ["Saya ingin mengetahui pandangan tokoh-tokoh dan para ahli mengenai berbagai masalah yang menarik perhatian saya.", "Jika saya harus bepergian, maka saya ingin segala sesuatunya telah direncanakan terlebih dahulu."],
            13 => ["Saya ingin mengerjakan sampai benar-benar selesai setiap pekerjaan ataupun tugas yang telah saya mulai.", "Saya ingin perlengkapan keperluan saya tersusun rapi dan teratur di tempat yang semestinya atau di dalam ruang kerja saya."],
            14 => ["Saya suka bercerita kepada orang-orang lain tentang petualangan dan hal-hal aneh yang pernah saya alami.", "Saya suka makan saya teratur dan ada waktu tertentu untuk makan."],
            15 => ["Saya tak ingin tergantung orang lain saat menentukan hal yang akan saya lakukan.", "Saya ingin perlengkapan keperluan saya tersusun rapi dan teratur di tempat yang semestinya atau di dalam ruang kerja saya."],
            16 => ["Saya ingin mengerjakan segala sesuatu lebih baik daripada orang lain.", "Saya suka menceritakan hal-hal yang lucu waktu pesta."],
            17 => ["Saya suka mengikuti adat istiadat dan menghindarkan hal-hal yang mungkin dianggap tak wajar oleh orang-orang yang saya hormati.", "Saya suka berbicara tentang hal-hal yang telah saya capai."],
            18 => ["Saya ingin agar hidup saya teratur sedemikian rupa sehingga berjalan lancar dan tanpa banyak perubahan rencana.", "Saya suka bercerita kepada orang-orang lain tentang petualangan dan hal-hal aneh yang pernah saya alami."],
            19 => ["Saya suka membaca buku-buku atau sandiwara-sandiwara yang terutama berkisar sekitar soal-soal seks.", "Saya suka menjadi pusat perhatian dalam kelompok."],
            20 => ["Saya suka mengecam orang-orang yang dianggap berwenang.", "Saya suka menggunakan kata-kata yang maknanya sering tak diketahui orang lain."],
            21 => ["Saya ingin menyelesaikan tugas yang memang membutuhkan ketrampilan serta usaha.", "Saya ingin dapat berbuat sekehendak hati."],
            22 => ["Saya suka memuji seseorang yang saya kagumi.", "Saya ingin merasa bebas melakukan yang saya kehendaki."],
            23 => ["Saya suka menyimpan surat, bon, dan kertas-kertas lain secara rapi dan menurut sistem tertentu.", "Saya tak ingin tergantung orang lain saat menentukan hal yang akan saya lakukan."],
            24 => ["Saya suka mengajukan pertanyaan yang setahu saya tak seorangpun akan bisa menjawabnya.", "Saya suka mengecam orang-orang yang dianggap berwenang."],
            25 => ["Saya menjadi sedemikian marah, rasanya ingin melemparkan, dan merusak barang-barang.", "Saya tak ingin memikul tanggungjawab dan kewajiban yang ditetapkan orang lain."],
            26 => ["Saya ingin berhasil dalam setiap hal yang saya lakukan.", "Saya suka memperoleh teman-teman baru."],
            27 => ["Saya suka mengikuti petunjuk-petunjuk dan melakukan hal-hal yang orang harapkan dari diri saya.", "Saya ingin keterikatan perasaan bersama yang kuat dengan teman-teman saya."],
            28 => ["Saya ingin setiap pekerjaan, tulisan saya, teliti, rapi dan tersusun dengan baik.", "Saya ingin memperoleh teman sebanyak mungkin."],
            29 => ["Saya suka menceritakan hal-hal yang lucu waktu pesta.", "Saya suka berkirim surat kepada teman-teman saya."],
            30 => ["Saya ingin dapat berbuat sekehendak hati.", "Saya suka melakukan atau menjalani sesuatu bersama teman-teman saya."],
            31 => ["Saya ingin memecahkan teka-teki dan persoalan yang memang sukar bagi orang lain.", "Saya suka menilai orang berdasarkan alasan mereka melakukan sesuatu dan bukan atas dasar yang sesungguhnya mereka lakukan."],
            32 => ["Saya suka dipimpin orang-orang yang saya kagumi.", "Saya ingin memahami perasaan teman-teman saya dalam menghadapi berbagai masalah."],
            33 => ["Saya suka makan saya teratur dan ada waktu tertentu untuk makan.", "Saya suka mempelajari dan menganalisa tingkah laku orang-orang lain."],
            34 => ["Saya ingin mengatakan hal-hal yang dianggap lucu dan cerdas oleh orang-orang lain.", "Saya suka menempatkan diri saya ke dalam kedudukan orang lain dan membayangkan bagaimana perasaan saya bila berada dalam keadaan yang sama."],
            35 => ["Saya ingin merasa bebas melakukan yang saya kehendaki.", "Saya suka mengamati perasaan orang lain dalam suatu keadaan tertentu."],
            36 => ["Saya ingin menyelesaikan tugas yang memang membutuhkan ketrampilan serta usaha.", "Saya ingin teman-teman memberi dorongan kepada saya bila saya menghadapi kegagalan."],
            37 => ["Dalam merencanakan sesuatu, saya ingin mendapat saran-saran dari orang-orang yang pendapatnya saya hormati.", "Saya ingin diperlakukan dengan ramah oleh teman-teman saya."],
            38 => ["Saya ingin agar hidup saya teratur sedemikian rupa sehingga berjalan lancar dan tanpa banyak perubahan rencana.", "Saya ingin teman-teman saya merasa kasihan pada saya apabila saya sakit."],
            39 => ["Saya suka menjadi pusat perhatian dalam kelompok.", "Saya ingin teman-teman saya meributkan tentang diri saya bila saya mendapat cedera atau sakit."],
            40 => ["Saya suka mengelakkan keadaan untuk berlaku secara konvensional (kebiasaan umum).", "Saya ingin teman-teman saya bersimpati terhadap saya dan menghibur saya bila saya bersusah hati."],
            41 => ["Saya ingin menulis roman, atau sandiwara yang hebat.", "Bila masuk dalam kepanitiaan, saya ingin ditunjuk atau dipilih sebagai ketuanya."],
            42 => ["Bila saya berada dalam suatu kelompok, saya suka menerima pimpinan orang lain dalam memutuskan hal-hal yang akan dilakukan.", "Saya ingin mengawasi dan mengarahkan tindakan-tindakan orang lain bila keadaan memungkinkan."],
            43 => ["Saya suka menyimpan surat, bon, dan kertas-kertas lain secara rapi dan menurut sistem tertentu.", "Saya ingin menjadi salah seorang pemimpin dalam organisasi atau kelompok dimana saya menjadi anggotanya."],
            44 => ["Saya suka mengajukan pertanyaan yang setahu saya tak seorangpun akan bisa menjawabnya.", "Saya suka mengarahkan orang lain bagaimana mereka melakukan pekerjaan mereka."],
            45 => ["Saya tak ingin memikul tanggung jawab dan kewajiban yang ditetapkan orang lain.", "Saya ingin diminta menyelesaikan perdebatan atau perselisihan di antara orang lain."],
            46 => ["Saya ingin ahli dalam suatu pekerjaan, jabatan atau bidang khusus.", "Saya merasa bersalah apabila melakukan sesuatu yang saya ketahui tidak baik."],
            47 => ["Saya suka membaca riwayat hidup orang-orang besar.", "Saya merasa harus mengakui hal-hal tidak baik yang telah saya lakukan."],
            48 => ["Saya terbiasa merencanakan dan mengatur detail-detail dari setiap pekerjaan yang harus saya lakukan.", "Bila keadaan kurang menguntungkan, saya merasa harus lebih disalahkan daripada orang lain."],
            49 => ["Saya suka menggunakan kata-kata yang maknanya sering tak diketahui orang lain.", "Dalam banyak hal saya merasa kalah dibandingkan dengan orang-orang lain."],
            50 => ["Saya suka mengecam orang-orang yang dianggap berwenang.", "Saya merasa canggung berada diantara orang-orang yang saya anggap sebagai atasan saya."],
            51 => ["Saya ingin berkarya dan bekerja sebaik mungkin.", "Saya suka menolong orang-orang lain yang tidak begitu beruntung seperti saya."],
            52 => ["Saya ingin mengetahui pandangan tokoh-tokoh dan para ahli mengenai berbagai masalah yang menarik perhatian saya.", "Saya suka bermurah hati kepada teman-teman saya."],
            53 => ["Saya suka membuat perencanaan sebelum memulai pekerjaan yang sulit.", "Saya suka memberi bantuan-bantuan kecil kepada teman-teman saya."],
            54 => ["Saya suka bercerita kepada orang-orang lain tentang petualangan dan hal-hal aneh yang pernah saya alami.", "Saya ingin teman-teman saya mempercayai saya dan menceritakan kesulitan-kesulitan mereka kepada saya."],
            55 => ["Saya suka menyatakan pendapat saya tentang berbagai hal.", "Saya suka memaafkan teman-teman saya yang kadang-kadang mungkin menyakiti hati saya."],
            56 => ["Saya ingin mengerjakan segala sesuatu lebih baik daripada orang lain.", "Saya suka makan di restoran-restoran baru atau asing."],
            57 => ["Saya suka mengikuti adat istiadat dan menghindarkan hal-hal yang mungkin dianggap tak wajar oleh orang-orang yang saya hormati.", "Saya suka mengikuti mode atau cara baru."],
            58 => ["Saya ingin pekerjaan saya diatur dan direncanakan sebelum dimulai.", "Saya suka bepergian melihat-lihat daerah pedalaman."],
            59 => ["Saya ingin orang memperhatikan dan berkomentar mengenai penampilan saya di depan umum.", "Saya suka menjelajahi pedalaman dan tinggal di berbagai tempat."],
            60 => ["Saya tak ingin tergantung orang lain saat menentukan hal yang akan saya lakukan.", "Saya suka melakukan hal-hal baru dan berbeda dari biasanya."],
            61 => ["Saya ingin dapat menyatakan bahwa saya telah menyelesaikan dengan baik suatu pekerjaan yang memang sulit.", "Saya suka bekerja keras pada tiap pekerjaan yang saya hadapi."],
            62 => ["Saya suka menyatakan pada atasan saya bahwa mereka telah melakukan suatu pekerjaan dengan baik, bila memang demikian halnya menurut pikiran saya.", "Saya ingin menyelesaikan pekerjaan satu-persatu, sebelum memulai yang lainnya."],
            63 => ["Jika saya harus bepergian, maka saya ingin segala sesuatunya telah direncanakan terlebih dahulu.", "Saya ingin mengerjakan teka-teki atau memecahkan persoalan-persoalan sampai selesai."],
            64 => ["Saya kadang-kadang suka melakukan hal-hal semata-mata untuk melihat reaksi orang lain.", "Saya suka bertahan menghadapi suatu pekerjaan atau masalah sekalipun tampaknya seolah-olah saya tak akan berhasil."],
            65 => ["Saya suka melakukan hal yang dianggap tak sesuai dengan adat kebiasaan.", "Saya ingin bekerja berjam-jam tanpa gangguan."],
            66 => ["Saya ingin mengerjakan sesuatu yang berarti.", "Saya suka mencium lawan jenis saya yang menarik."],
            67 => ["Saya suka memuji seseorang yang saya kagumi.", "Saya ingin dianggap punya daya tarik fisik oleh lawan jenis saya."],
            68 => ["Saya ingin perlengkapan keperluan saya tersusun rapi dan teratur di tempat yang semestinya atau di dalam ruang kerja saya.", "Saya suka jatuh cinta kepada seseorang dari lawan jenis saya."],
            69 => ["Saya suka berbicara tentang hal-hal yang telah saya capai.", "Saya suka mendengarkan atau menceritakan sejumlah lelucon yang berkisar sekitar soal seks."],
            70 => ["Saya ingin melakukan setiap hal dengan cara saya sendiri tanpa menghiraukan pemikiran orang lain.", "Saya suka buku, sandiwara (film) yang berkisar soal seks."],
            71 => ["Saya ingin menulis roman, atau sandiwara yang hebat.", "Saya suka menyerang pendirian yang bertentangan dengan pendirian saya."],
            72 => ["Bila saya berada dalam suatu kelompok, saya suka menerima pimpinan orang lain dalam memutuskan hal-hal yang akan dilakukan.", "Ingin rasanya saya mengecam seseorang di muka umum bila dia memang patut menerimanya."],
            73 => ["Saya ingin agar hidup saya teratur sedemikian rupa sehingga berjalan lancar dan tanpa banyak perubahan rencana.", "Saya menjadi sedemikian marah, ingin rasanya melemparkan, dan merusak barang-barang."],
            74 => ["Saya suka mengajukan pertanyaan yang setahu saya tak seorangpun akan bisa menjawabnya.", "Saya suka mengatakan kepada orang lain pendapat saya mengenai mereka."],
            75 => ["Saya tak ingin memikul tanggung jawab dan kewajiban yang ditetapkan orang lain.", "Ingin rasanya saya memperolok orang-orang yang melakukan hal-hal yang saya anggap bodoh."],
            76 => ["Saya ingin setia terhadap teman-teman saya.", "Saya ingin berkarya dan bekerja sebaik mungkin."],
            77 => ["Saya suka mengamati perasaan orang lain dalam suatu keadaan tertentu.", "Saya ingin dapat menyatakan bahwa saya telah menyelesaikan dengan baik suatu pekerjaan yang memang sulit."],
            78 => ["Saya ingin teman-teman memberi dorongan kepada saya bila saya menghadapi kegagalan.", "Saya ingin berhasil dalam setiap hal yang saya lakukan."],
            79 => ["Saya ingin menjadi salah seorang pemimpin dalam organisasi atau kelompok dimana saya menjadi anggotanya.", "Saya ingin mengerjakan segala sesuatu lebih baik daripada orang lain."],
            80 => ["Bila keadaan kurang menguntungkan, saya merasa harus lebih disalahkan daripada orang lain.", "Saya ingin memecahkan teka-teki dan persoalan yang memang sukar bagi orang lain."],
            81 => ["Saya suka melakukan sesuatu untuk kepentingan teman-teman saya.", "Dalam merencanakan sesuatu, saya ingin mendapat saran-saran dari orang-orang yang pendapatnya saya hormati."],
            82 => ["Saya suka menempatkan diri saya ke dalam kedudukan orang lain dan membayangkan bagaimana perasaan saya bila berada dalam keadaan yang sama.", "Saya suka menyatakan pada atasan saya bahwa mereka telah melakukan suatu pekerjaan dengan baik, bila memang demikian halnya menurut pikiran saya."],
            83 => ["Saya ingin teman-teman saya menunjukkan simpati dan pengertian bila saya mengalami kesukaran.", "Saya suka dipimpin orang-orang yang saya kagumi."],
            84 => ["Bila masuk dalam kepanitiaan, saya ingin ditunjuk atau dipilih sebagai ketuanya.", "Bila saya berada dalam suatu kelompok, saya suka menerima pimpinan orang lain dalam memutuskan hal-hal yang akan dilakukan."],
            85 => ["Bila saya melakukan kesalahan, saya merasa harus dihukum.", "Saya suka mengikuti adat-istiadat dan menghindarkan hal-hal yang mungkin dianggap tak wajar oleh orang-orang yang saya hormati."],
            86 => ["Saya suka melakukan atau menjalani sesuatu bersama teman-teman saya.", "Saya suka membuat perencanaan sebelum memulai pekerjaan yang sulit."],
            87 => ["Saya ingin memahami perasaan teman-teman saya dalam menghadapi berbagai masalah.", "Jika saya harus bepergian, maka saya ingin segala sesuatunya telah direncanakan terlebih dahulu."],
            88 => ["Saya ingin diperlakukan dengan ramah oleh teman-teman saya.", "Saya ingin pekerjaan saya diatur dan direncanakan sebelum dimulai."],
            89 => ["Saya ingin dianggap pemimpin oleh orang lain.", "Saya suka menyimpan surat, bon, dan kertas-kertas lain secara rapi dan menurut sistem tertentu."],
            90 => ["Bagi saya kesedihan dan kesusahan saya, lebih banyak membawa kebaikan daripada kerugian.", "Saya ingin agar hidup saya teratur sedemikian rupa sehingga berjalan lancar dan tanpa banyak perubahan rencana."],
            91 => ["Saya ingin keterikatan perasaan bersama yang kuat dengan teman-teman saya.", "Saya ingin mengatakan hal-hal yang dianggap lucu dan cerdas oleh orang-orang lain."],
            92 => ["Saya suka merenungkan kepribadian teman-teman saya dan mencoba mengerti hal-hal yang menjadikan mereka sebagaimana terlihat.", "Saya kadang-kadang suka melakukan hal-hal semata-mata untuk melihat reaksi orang lain."],
            93 => ["Saya ingin teman-teman saya meributkan tentang diri saya bila saya mendapat cedera atau sakit.", "Saya suka berbicara tentang hal-hal yang telah saya capai."],
            94 => ["Saya suka mengarahkan orang lain bagaimana mereka melakukan pekerjaan mereka.", "Saya suka menjadi pusat perhatian dalam kelompok."],
            95 => ["Saya merasa canggung berada diantara orang-orang yang saya anggap sebagai atasan saya.", "Saya suka menggunakan kata-kata yang maknanya sering tak diketahui orang lain."],
            96 => ["Saya lebih suka mengerjakan sesuatu bersama teman-teman saya daripada sendirian.", "Saya suka menyatakan pendapat saya tentang berbagai hal."],
            97 => ["Saya suka mempelajari dan menganalisa tingkah laku orang-orang lain.", "Saya suka melakukan hal yang dianggap tak sesuai dengan adat kebiasaan."],
            98 => ["Saya ingin teman-teman saya merasa kasihan pada saya apabila saya sakit.", "Saya suka mengelakkan keadaan untuk berlaku secara konvensional (kebiasaan umum)."],
            99 => ["Saya ingin mengawasi dan mengarahkan tindakan-tindakan orang lain bila keadaan memungkinkan.", "Saya ingin melakukan setiap hal dengan cara saya sendiri tanpa menghiraukan pemikiran orang lain."],
            100 => ["Dalam banyak hal saya merasa kalah dibandingkan dengan orang-orang lain.", "Saya tak ingin memikul tanggung jawab dan kewajiban yang ditetapkan orang lain."],
            101 => ["Saya ingin berhasil dalam setiap hal yang saya lakukan.", "Saya suka memperoleh teman-teman baru."],
            102 => ["Saya suka menganalisa perasaan dan alasan saya melakukan sesuatu.", "Saya ingin memperoleh teman sebanyak mungkin."],
            103 => ["Saya ingin agar teman-teman saya membantu saya bila saya mengalami kesulitan.", "Saya suka melakukan sesuatu untuk kepentingan teman-teman saya."],
            104 => ["Saya suka memperdebatkan pendirian saya bila diserang orang lain.", "Saya suka berkirim surat kepada teman-teman saya."],
            105 => ["Saya merasa bersalah apabila melakukan sesuatu yang saya ketahui tidak baik.", "Saya ingin keterikatan perasaan bersama yang kuat dengan teman-teman saya."],
            106 => ["Saya suka melakukan atau menjalani sesuatu bersama teman-teman saya.", "Saya suka menganalisa perasaan dan alasan saya melakukan sesuatu."],
            107 => ["Saya suka menerima pimpinan orang-orang yang saya kagumi.", "Saya ingin memahami perasaan teman-teman saya dalam menghadapi berbagai masalah."],
            108 => ["Saya ingin teman-teman saya dengan gembira memberikan bantuan-bantuan kecil kepada saya.", "Saya suka menilai orang berdasarkan alasan mereka melakukan sesuatu dan bukan atas dasar yang sesungguhnya mereka lakukan."],
            109 => ["Bila berada dalam suatu kelompok, saya ingin menentukan hal-hal yang akan dilakukan.", "Saya suka meramalkan bagaimana teman-teman saya akan bertindak dalam berbagai situasi."],
            110 => ["Saya lebih suka mengalah dan menghindarkan pertengkaran daripada memaksakan kemauan saya.", "Saya suka menganalisa perasaan orang lain dan alasan mereka melakukan sesuatu."],
            111 => ["Saya suka memperoleh teman-teman baru.", "Saya ingin agar teman-teman saya membantu saya bila saya mengalami kesulitan."],
            112 => ["Saya suka menilai orang berdasarkan alasan mereka melakukan sesuatu dan bukan atas dasar yang sesungguhnya mereka lakukan.", "Saya ingin teman-teman saya banyak menunjukkan rasa sayang mereka terhadap saya."],
            113 => ["Saya ingin agar hidup saya teratur sedemikian rupa sehingga berjalan lancar dan tanpa banyak perubahan-perubahan dalam rencana-rencana saya.", "Saya ingin teman-teman saya merasa kasihan pada saya apabila saya sakit."],
            114 => ["Saya ingin diminta menyelesaikan perdebatan atas perselisihan di antara orang lain.", "Saya ingin teman-teman saya dengan gembira memberikan bantuan-bantuan kecil kepada saya."],
            115 => ["Saya merasa harus mengakui hal-hal tidak baik yang telah saya lakukan.", "Saya ingin teman-teman saya bersimpati terhadap saya dan menghibur saya bila saya bersusah hati."],
            116 => ["Saya lebih suka mengerjakan sesuatu bersama teman-teman saya daripada sendirian.", "Saya suka memperdebatkan pendirian saya bila diserang orang lain."],
            117 => ["Saya suka merenungkan kepribadian teman-teman saya dan mencoba mengerti hal-hal yang menjadikan mereka sebagaimana terlihat.", "Saya ingin mampu membujuk dan mempengaruhi orang lain melakukan hal-hal yang saya kehendaki."],
            118 => ["Saya ingin teman-teman saya bersimpati terhadap saya dan menghibur saya bila saya bersusah hati.", "Bila berada dalam suatu kelompok, saya ingin menentukan hal-hal yang akan dilakukan."],
            119 => ["Saya suka mengajukan pertanyaan yang setahu saya tak seorangpun akan bisa menjawabnya.", "Saya suka mengarahkan orang lain bagaimana mereka melakukan pekerjaan mereka."],
            120 => ["Saya merasa canggung berada diantara orang-orang yang saya anggap sebagai atasan saya.", "Saya ingin mengawasi dan mengarahkan tindakan-tindakan orang lain bila keadaan memungkinkan."],
            121 => ["Saya suka bergaul dalam lingkungan yang mempunyai perasaan akrab satu sama lain.", "Saya merasa bersalah apabila melakukan sesuatu yang saya ketahui tidak baik."],
            122 => ["Saya suka menganalisa perasaan orang lain dan alasan mereka melakukan sesuatu.", "Saya merasa sedih atas ketidakmampuan saya menghadapi berbagai macam keadaan."],
            123 => ["Saya ingin teman-teman saya merasa kasihan pada saya apabila saya sakit.", "Saya lebih suka mengalah dan menghindarkan pertengkaran daripada memaksakan kemauan saya."],
            124 => ["Saya ingin mampu membujuk dan mempengaruhi orang lain melakukan hal-hal yang saya kehendaki.", "Saya merasa sedih atas ketidakmampuan saya menghadapi berbagai macam keadaan."],
            125 => ["Saya suka mengecam orang-orang yang dianggap berwenang.", "Saya merasa canggung berada diantara orang-orang yang saya anggap sebagai atasan saya."],
            126 => ["Saya suka bergaul dalam lingkungan yang mempunyai perasaan akrab satu sama lain.", "Saya suka menolong teman-teman saya bila mereka berada dalam kesulitan."],
            127 => ["Saya suka menganalisa perasaan dan alasan saya melakukan sesuatu.", "Saya suka menunjukkan simpati saya kepada teman-teman saya bila mereka mendapat cedera atau sakit."],
            128 => ["Saya ingin agar teman-teman saya membantu saya bila saya mengalami kesulitan.", "Saya suka memperlakukan orang lain dengan ramah dan simpatik."],
            129 => ["Saya ingin menjadi salah seorang pemimpin dalam organisasi atau kelompok dimana saya menjadi anggotanya.", "Saya suka menunjukkan simpati saya kepada teman-teman saya bila mereka mendapat cedera atau sakit."],
            130 => ["Bagi saya kesedihan dan kesusahan saya, lebih banyak membawa kebaikan daripada kerugian.", "Saya suka berlaku sangat ramah terhadap teman-teman saya."],
            131 => ["Saya lebih suka mengerjakan sesuatu bersama teman-teman saya daripada sendirian.", "Saya suka bereksperimen, dan mencoba hal-hal baru."],
            132 => ["Saya suka merenungkan kepribadian teman-teman saya dan mencoba memahami hal-hal yang membuat mereka sebagaimana terlihat.", "Saya lebih menyukai mencoba pekerjaan baru daripada melakukan pekerjaan tetap."],
            133 => ["Saya ingin teman-teman saya menunjukkan simpati dan pengertian bila saya mengalami kesukaran.", "Saya suka bertemu dengan orang-orang baru."],
            134 => ["Saya suka memperdebatkan pendirian saya bila diserang orang lain.", "Saya ingin mengalami hal-hal baru dan perubahan dalam kehidupan saya sehari-hari."],
            135 => ["Saya suka bergaul dalam lingkungan yang mempunyai perasaan akrab satu sama lain.", "Saya suka menjelajahi pedalaman dan tinggal di berbagai tempat."],
            136 => ["Saya suka melakukan sesuatu untuk kepentingan teman-teman saya.", "Bila saya melakukan suatu tugas, saya ingin mengerjakannya sampai benar-benar selesai."],
            137 => ["Saya suka menganalisa perasaan orang lain dan alasan mereka melakukan sesuatu.", "Saya ingin menghindarkan gangguan bila saya sedang bekerja."],
            138 => ["Saya ingin teman-teman saya dengan gembira memberikan bantuan-bantuan kecil kepada saya.", "Saya suka bekerja sampai jauh malam menyelesaikan suatu pekerjaan."],
            139 => ["Saya ingin dianggap pemimpin oleh orang lain.", "Saya ingin bekerja berjam-jam tanpa gangguan."],
            140 => ["Bila saya melakukan kesalahan, saya merasa harus dihukum.", "Saya suka bertahan menghadapi suatu pekerjaan atau masalah sekalipun tampaknya seolah-olah saya tak akan berhasil."],
            141 => ["Saya ingin setia terhadap teman-teman saya.", "Saya suka bepergian dengan lawan jenis yang menarik."],
            142 => ["Saya suka meramalkan bagaimana teman-teman saya akan bertindak dalam berbagai situasi.", "Saya suka ikut serta dalam diskusi tentang seks dan aktivitas seksual."],
            143 => ["Saya ingin agar teman-teman saya banyak menunjukkan rasa sayang mereka terhadap saya.", "Saya suka nafsu saya terangsang."],
            144 => ["Bila berada dalam suatu kelompok, saya ingin menentukan hal-hal yang akan dilakukan.", "Saya suka bersibuk dalam aktivitas sosial bersama orang-orang dari lawan jenis saya."],
            145 => ["Saya merasa sedih atas ketidakmampuan saya menghadapi berbagai macam keadaan.", "Saya suka buku, sandiwara (film) yang berkisar soal seks."],
            146 => ["Saya suka berkirim surat kepada teman-teman saya.", "Saya suka membaca berita kabar tentang pembunuhan, dan perbuatan kekerasan lain."],
            147 => ["Saya suka meramalkan bagaimana teman-teman saya akan bertindak dalam berbagai situasi.", "Saya suka menyerang pendirian yang bertentangan dengan pendirian saya."],
            148 => ["Saya ingin teman-teman saya meributkan tentang diri saya bila saya mendapat cedera atau sakit.", "Bila keadaan kurang menguntungkan, ingin rasanya saya menyalahkan orang lain."],
            149 => ["Saya suka mengarahkan orang lain bagaimana mereka melakukan pekerjaan mereka.", "Saya ingin membalas dendam terhadap orang yang menghina saya."],
            150 => ["Dalam banyak hal saya merasa kalah dibandingkan dengan orang-orang lain.", "Ingin rasanya saya menghardik orang lain bila berbeda pendapat dengan mereka."],
            151 => ["Saya suka menolong teman-teman saya bila mereka berada dalam kesulitan.", "Saya ingin berkarya dan bekerja sebaik mungkin."],
            152 => ["Saya suka bepergian melihat-lihat daerah pedalaman.", "Saya ingin menyelesaikan tugas yang memang membutuhkan keterampilan serta usaha."],
            153 => ["Saya suka bekerja keras pada tiap pekerjaan yang saya hadapi.", "Saya ingin mengerjakan sesuatu yang berarti."],
            154 => ["Saya suka bepergian dengan lawan jenis yang menarik.", "Saya ingin berhasil dalam setiap hal yang saya lakukan."],
            155 => ["Saya suka membaca berita kabar tentang pembunuhan, dan perbuatan kekerasan lain.", "Saya ingin menulis roman, atau sandiwara yang hebat."],
            156 => ["Saya suka memberi bantuan-bantuan kecil kepada teman-teman saya.", "Dalam merencanakan sesuatu, saya ingin mendapat saran-saran dari orang-orang yang pendapatnya saya hormati."],
            157 => ["Saya ingin mengalami hal-hal baru dan perubahan dalam kehidupan saya sehari-hari.", "Saya suka menyatakan pada atasan saya bahwa mereka telah melakukan suatu pekerjaan dengan baik, bila memang demikian halnya menurut pikiran saya."],
            158 => ["Saya suka bekerja sampai jauh malam menyelesaikan suatu pekerjaan.", "Saya suka memuji seseorang yang saya kagumi."],
            159 => ["Saya suka nafsu saya terangsang.", "Saya suka dipimpin orang-orang yang saya kagumi."],
            160 => ["Saya ingin membalas dendam terhadap orang yang menghina saya.", "Bila saya berada dalam suatu kelompok, saya suka menerima pimpinan orang lain dalam memutuskan hal-hal yang akan dilakukan."],
            161 => ["Saya suka bermurah hati kepada teman-teman saya.", "Saya suka membuat perencanaan sebelum memulai pekerjaan yang sulit."],
            162 => ["Saya suka bertemu dengan orang-orang baru.", "Saya ingin setiap pekerjaan, tulisan saya, teliti, rapi dan tersusun dengan baik."],
            163 => ["Saya ingin mengerjakan sampai benar-benar selesai setiap pekerjaan atau tugas yang telah saya mulai.", "Saya ingin perlengkapan keperluan saya tersusun rapi dan teratur di tempat yang semestinya atau di dalam ruang kerja saya."],
            164 => ["Saya ingin dianggap punya daya tarik fisik oleh lawan jenis saya.", "Saya terbiasa merencanakan dan mengatur detail-detail dari setiap pekerjaan yang harus saya lakukan."],
            165 => ["Saya suka mengatakan kepada orang lain pendapat saya mengenai mereka.", "Saya suka makan saya teratur dan ada waktu tertentu untuk makan."],
            166 => ["Saya suka berlaku sangat ramah terhadap teman-teman saya.", "Saya ingin mengatakan hal-hal yang dianggap lucu dan cerdas oleh orang-orang lain."],
            167 => ["Saya lebih menyukai mencoba pekerjaan baru daripada melakukan pekerjaan tetap.", "Saya kadang-kadang suka melakukan hal-hal semata-mata untuk melihat reaksi orang lain."],
            168 => ["Saya suka bertahan menghadapi suatu pekerjaan atau masalah sekalipun tampaknya seolah-olah saya tak akan berhasil.", "Saya ingin orang memperhatikan dan berkomentar mengenai penampilan saya di depan umum."],
            169 => ["Saya suka buku, sandiwara (film) yang berkisar soal seks.", "Saya suka menjadi pusat perhatian dalam kelompok."],
            170 => ["Bila keadaan kurang menguntungkan, ingin rasanya saya menyalahkan orang lain.", "Saya suka mengajukan pertanyaan yang setahu saya tak seorangpun akan bisa menjawabnya."],
            171 => ["Saya suka bertahan menghadapi suatu pekerjaan atau masalah sekalipun tampaknya seolah-olah saya tak akan berhasil.", "Saya suka mengatakan pendapat saya tentang berbagai hal."],
            172 => ["Saya suka makan di restoran-restoran baru atau asing.", "Saya suka melakukan hal yang dianggap tak sesuai dengan adat kebiasaan."],
            173 => ["Saya ingin menyelesaikan pekerjaan satu-persatu, sebelum memulai yang lain.", "Saya ingin merasa bebas melakukan yang saya kehendaki."],
            174 => ["Saya suka ikut serta dalam diskusi tentang seks dan aktivitas seksual.", "Saya ingin melakukan setiap hal dengan cara saya sendiri tanpa menghiraukan pemikiran orang lain."],
            175 => ["Saya menjadi sedemikian marah, ingin rasanya melemparkan, dan merusak barang-barang.", "Saya tak ingin memikul tanggung jawab dan kewajiban yang ditetapkan orang lain."],
            176 => ["Saya suka menolong teman-teman saya bila mereka berada dalam kesulitan.", "Saya ingin setia terhadap teman-teman saya."],
            177 => ["Saya suka melakukan hal-hal baru dan berbeda dari biasanya.", "Saya suka memperoleh teman-teman baru."],
            178 => ["Bila saya melakukan suatu tugas, saya ingin mengerjakannya sampai benar-benar selesai.", "Saya suka bergaul dalam lingkungan yang mempunyai perasaan akrab satu sama lain."],
            179 => ["Saya suka bepergian dengan lawan jenis yang menarik.", "Saya ingin memperoleh teman sebanyak mungkin."],
            180 => ["Saya suka menyerang pendirian yang bertentangan dengan pendirian saya.", "Saya suka berkirim surat kepada teman-teman saya."],
            181 => ["Saya suka bermurah hati kepada teman-teman saya.", "Saya suka mengamati perasaan orang lain dalam suatu keadaan tertentu."],
            182 => ["Saya suka makan di restoran-restoran baru atau asing.", "Saya suka menempatkan diri saya ke dalam kedudukan orang lain dan membayangkan bagaimana perasaan saya bila berada dalam keadaan yang sama."],
            183 => ["Saya suka bekerja sampai jauh malam menyelesaikan suatu pekerjaan.", "Saya ingin memahami perasaan teman-teman saya dalam menghadapi berbagai masalah."],
            184 => ["Saya suka nafsu saya terangsang.", "Saya suka mempelajari dan menganalisa tingkah laku orang-orang lain."],
            185 => ["Ingin rasanya saya memperolok orang-orang yang melakukan hal-hal yang saya anggap bodoh.", "Saya suka meramalkan bagaimana teman-teman saya akan bertindak dalam berbagai situasi."],
            186 => ["Saya suka memaafkan teman-teman saya yang kadang-kadang mungkin menyakiti hati saya.", "Saya ingin teman-teman memberi dorongan kepada saya bila saya menghadapi kegagalan."],
            187 => ["Saya suka bereksperimen, dan mencoba hal-hal baru.", "Saya ingin teman-teman saya menunjukkan simpati dan pengertian bila saya mengalami kesukaran."],
            188 => ["Saya ingin mengerjakan teka-teki atau memecahkan persoalan-persoalan sampai selesai.", "Saya ingin diperlakukan dengan ramah oleh teman-teman saya."],
            189 => ["Saya ingin dianggap punya daya tarik fisik oleh lawan jenis saya.", "Saya ingin teman-teman saya banyak menunjukkan rasa sayang mereka terhadap saya."],
            190 => ["Ingin rasanya saya mengecam seseorang di muka umum bila dia memang patut menerimanya.", "Saya ingin teman-teman saya meributkan tentang diri saya bila saya mendapat cedera atau sakit."],
            191 => ["Saya suka berlaku sangat ramah terhadap teman-teman saya.", "Saya ingin dianggap pemimpin oleh orang lain."],
            192 => ["Saya lebih menyukai mencoba pekerjaan baru daripada melakukan pekerjaan tetap.", "Bila masuk dalam kepanitiaan, saya ingin ditunjuk atau dipilih sebagai ketua."],
            193 => ["Saya ingin mengerjakan sampai benar-benar selesai setiap pekerjaan atau tugas yang telah saya mulai.", "Saya ingin mampu membujuk dan mempengaruhi orang lain melakukan hal-hal yang saya kehendaki."],
            194 => ["Saya suka ikut serta dalam diskusi tentang seks dan aktivitas seksual.", "Saya ingin diminta menyelesaikan perdebatan atau perselisihan diantara orang lain."],
            195 => ["Saya menjadi sedemikian marah, ingin rasanya melemparkan, dan merusak barang-barang.", "Saya suka mengarahkan orang lain bagaimana mereka melakukan pekerjaan mereka."],
            196 => ["Saya suka berlaku sangat ramah terhadap teman-teman saya.", "Bila keadaan kurang menguntungkan, saya merasa harus lebih disalahkan daripada orang lain."],
            197 => ["Saya suka menjelajahi pedalaman dan tinggal di berbagai tempat.", "Bila saya melakukan kesalahan, saya merasa harus dihukum."],
            198 => ["Saya suka bertahan menghadapi suatu pekerjaan atau masalah sekalipun tampaknya seolah-olah saya tak akan berhasil.", "Bagi saya kesedihan dan kesusahan saya, lebih banyak membawa kebaikan daripada kerugian."],
            199 => ["Saya suka buku, sandiwara (film) yang berkisar soal seks.", "Saya merasa harus mengakui hal-hal tidak baik yang telah saya lakukan."],
            200 => ["Bila keadaan kurang menguntungkan, ingin rasanya saya menyalahkan orang lain.", "Dalam banyak hal saya merasa kalah dibandingkan dengan orang-orang lain."],
            201 => ["Saya ingin melakukan setiap pekerjaan sebaik mungkin.", "Saya suka menolong orang-orang lain yang tidak begitu beruntung seperti saya."],
            202 => ["Saya suka melakukan hal-hal baru dan berbeda dari biasanya.", "Saya suka memperlakukan orang lain dengan ramah dan simpatik."],
            203 => ["Bila saya melakukan suatu tugas, saya ingin mengerjakannya sampai benar-benar selesai.", "Saya suka menolong orang-orang lain yang tidak begitu beruntung seperti saya."],
            204 => ["Saya suka bersibuk dalam aktivitas sosial bersama orang-orang dari lawan jenis saya.", "Saya suka memaafkan teman-teman saya yang kadang-kadang mungkin menyakiti hati saya."],
            205 => ["Saya akan menyerang pendirian yang bertentangan dengan pendirian saya.", "Saya ingin teman-teman saya mempercayai saya dan menceritakan kesulitan-kesulitan mereka kepada saya."],
            206 => ["Saya suka memperlakukan orang lain dengan ramah dan simpatik.", "Saya suka bepergian melihat-lihat daerah pedalaman."],
            207 => ["Saya suka mengikuti adat-istiadat dan menghindarkan melakukan hal-hal yang mungkin dianggap tidak wajar oleh orang-orang yang saya hormati.", "Saya suka mengikuti mode atau cara baru."],
            208 => ["Saya suka bekerja keras pada tiap pekerjaan yang saya hadapi.", "Saya ingin mengalami hal-hal baru dan perubahan dalam kehidupan saya sehari-hari."],
            209 => ["Saya suka mencium lawan jenis saya yang menarik.", "Saya suka bereksperimen, dan mencoba hal-hal baru."],
            210 => ["Ingin rasanya saya menghardik orang lain bila berbeda pendapat dengan mereka.", "Saya suka mengikuti mode atau cara baru."],
            211 => ["Saya ingin menolong teman-teman saya, bila mereka berada dalam kesulitan.", "Saya ingin berkarya dan bekerja sebaik mungkin."],
            212 => ["Saya ingin mengerjakan segala sesuatu lebih baik daripada orang lain.", "Saya suka menceritakan hal-hal yang lucu waktu pesta."],
            213 => ["Saya suka membuat perencanaan sebelum memulai pekerjaan yang sulit.", "Saya suka memberi bantuan-bantuan kecil kepada teman-teman saya."],
            214 => ["Saya suka menceritakan hal-hal yang lucu waktu pesta.", "Saya ingin menulis roman, atau sandiwara yang hebat."],
            215 => ["Saya ingin dapat berbuat sekehendak hati.", "Saya ingin dapat menyatakan bahwa saya telah menyelesaikan dengan baik suatu pekerjaan yang memang sulit."],
            216 => ["Saya ingin memecahkan teka-teki dan persoalan yang memang sukar bagi orang lain.", "Saya suka mengikuti petunjuk-petunjuk dan melakukan hal-hal yang orang harapkan dari diri saya."],
            217 => ["Saya ingin mengalami hal-hal yang baru dan perubahan-perubahan dalam kehidupan saya sehari-hari.", "Saya suka menyatakan pada atasan saya bahwa mereka telah melakukan suatu pekerjaan dengan baik."],
            218 => ["Saya terbiasa merencanakan dan mengatur detail-detail dari setiap pekerjaan yang harus saya lakukan.", "Saya suka mengikuti petunjuk-petunjuk dan melakukan hal-hal yang orang harapkan dari diri saya."],
            219 => ["Saya ingin orang memperhatikan dan berkomentar mengenai penampilan saya di depan umum.", "Saya suka membaca riwayat hidup orang-orang besar."],
            220 => ["Saya suka mengelakkan keadaan untuk berlaku secara konvensional (kebiasaan umum).", "Saya suka membaca riwayat hidup orang-orang besar."],
            221 => ["Saya ingin ahli dalam suatu pekerjaan, jabatan atau bidang khusus.", "Saya ingin pekerjaan saya diatur dan direncanakan sebelum dimulai."],
            222 => ["Saya ingin mengetahui pandangan tokoh-tokoh dan para ahli mengenai berbagai masalah yang menarik perhatian saya.", "Jika saya harus bepergian, maka saya ingin segala sesuatunya telah direncanakan terlebih dahulu."],
            223 => ["Saya ingin mengerjakan sampai benar-benar selesai setiap pekerjaan ataupun tugas yang telah saya mulai.", "Saya ingin perlengkapan keperluan saya tersusun rapi dan teratur di tempat yang semestinya atau di dalam ruang kerja saya."],
            224 => ["Saya suka bercerita kepada orang-orang lain tentang petualangan dan hal-hal aneh yang pernah saya alami.", "Saya suka makan saya teratur dan ada waktu tertentu untuk makan."],
            225 => ["Saya suka mengelakkan tanggung jawab dan kewajiban-kewajiban.", "Ingin rasanya saya memperolok orang-orang yang melakukan hal-hal yang saya anggap bodoh."],
        ];
    }
}