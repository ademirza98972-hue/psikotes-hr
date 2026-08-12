<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class EppsSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'EPPS')->first();
        if (!$alatTes) {
            $this->command->error('AlatTes EPPS tidak ditemukan.');
            return;
        }

        // Muat semua dimensi
        $dimensiMap = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->pluck('id', 'kode_dimensi');

        $required = ['ach','def','ord','exh','aut','aff','int','suc','dom','aba','nur','chg','end','het','agg'];
        foreach ($required as $kode) {
            if (!isset($dimensiMap[$kode])) {
                $this->command->error("Dimensi '{$kode}' tidak ditemukan.");
                return;
            }
        }

        // Hapus soal lama beserta semua dependensinya
        $soalLama = Soal::where('alat_tes_id', $alatTes->id)->get();
        $soalIds = $soalLama->pluck('id');
        $opsiIds = OpsiJawaban::whereIn('soal_id', $soalIds)->pluck('id');

        // Hapus jawaban peserta yang mereferensi opsi lama
        \App\Models\JawabanPeserta::whereIn('opsi_dipilih_id', $opsiIds)->delete();
        \App\Models\JawabanPeserta::whereIn('soal_id', $soalIds)->delete();

        // Hapus bobot, opsi, soal
        BobotOpsiDimensi::whereIn('opsi_jawaban_id', $opsiIds)->delete();
        OpsiJawaban::whereIn('soal_id', $soalIds)->delete();
        Soal::whereIn('id', $soalIds)->delete();

        // Kunci dimensi per soal (210 soal utama + 15 konsistensi)
        $kunci = $this->getKunci();

        // Teks soal
        $teks = $this->getTeks();

        foreach ($kunci as $nomor => [$dimA, $dimB]) {
            $teksA = $teks[$nomor][0] ?? "Pernyataan A nomor {$nomor}";
            $teksB = $teks[$nomor][1] ?? "Pernyataan B nomor {$nomor}";

            $soal = Soal::create([
                'alat_tes_id' => $alatTes->id,
                'nomor'       => $nomor,
                'teks_soal'   => "Pilih salah satu pernyataan yang paling menggambarkan diri Anda.",
                'tipe_format' => 'forced_choice',
                'urutan'      => $nomor,
            ]);

            // Opsi A
            $opsiA = OpsiJawaban::create([
                'soal_id'  => $soal->id,
                'teks_opsi' => $teksA,
                'urutan'   => 1,
            ]);
            BobotOpsiDimensi::create([
                'opsi_jawaban_id' => $opsiA->id,
                'dimensi_id'      => $dimensiMap[$dimA],
                'bobot'           => 1,
                'is_reverse'      => false,
            ]);

            // Opsi B
            $opsiB = OpsiJawaban::create([
                'soal_id'  => $soal->id,
                'teks_opsi' => $teksB,
                'urutan'   => 2,
            ]);
            BobotOpsiDimensi::create([
                'opsi_jawaban_id' => $opsiB->id,
                'dimensi_id'      => $dimensiMap[$dimB],
                'bobot'           => 1,
                'is_reverse'      => false,
            ]);

            if ($nomor % 25 === 0) {
                $this->command->info("Soal {$nomor}/225 selesai...");
            }
        }

        $this->command->info('EppsSoalSeeder selesai: 225 soal, 15 dimensi, distribusi seimbang 28x per dimensi.');
    }

    private function getKunci(): array
    {
        $dimensi = ['ach','def','ord','exh','aut','aff','int','suc','dom','aba','nur','chg','end','het','agg'];
        $kunci = [];
        $n = 1;
        for ($i = 0; $i < 15; $i++) {
            foreach ($dimensi as $j => $dim2) {
                if ($dimensi[$i] !== $dim2) {
                    $kunci[$n++] = [$dimensi[$i], $dim2];
                }
            }
        }
        // 15 soal konsistensi (211-225): duplikat soal 1-15
        $konSources = [1,16,53,4,5,6,7,8,9,10,11,12,13,14,15];
        foreach ($konSources as $idx => $src) {
            $kunci[211 + $idx] = $kunci[$src];
        }
        return $kunci;
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