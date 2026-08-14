<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\JawabanPeserta;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class PapikostikSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'PAP')->firstOrFail();

        $soalIds = Soal::where('alat_tes_id', $alatTes->id)->pluck('id');
        $opsiIds = OpsiJawaban::whereIn('soal_id', $soalIds)->pluck('id');
        JawabanPeserta::whereIn('opsi_dipilih_id', $opsiIds)->delete();
        BobotOpsiDimensi::whereIn('opsi_jawaban_id', $opsiIds)->delete();
        OpsiJawaban::whereIn('soal_id', $soalIds)->delete();
        Soal::where('alat_tes_id', $alatTes->id)->delete();

        $dim = DimensiAlatTes::where('alat_tes_id', $alatTes->id)->pluck('id', 'kode_dimensi');

        $kunciSkoring = [
            'KENDALI_EMOSI'  => [1=>'B',12=>'B',23=>'B',34=>'B',45=>'B',56=>'B',67=>'B',78=>'B',89=>'B'],
            'TYPE_PENGATUR'  => [11=>'B',22=>'B',33=>'B',44=>'B',55=>'B',66=>'B',77=>'B',88=>'B',89=>'A'],
            'DETAIL_KERJA'   => [21=>'B',32=>'B',43=>'B',54=>'B',65=>'B',76=>'B',78=>'A',87=>'B',88=>'A'],
            'TEORITIS'       => [31=>'B',42=>'B',53=>'B',64=>'B',67=>'A',75=>'B',77=>'A',86=>'B',87=>'A'],
            'PERGAULAN_LUAS' => [41=>'B',52=>'B',56=>'A',63=>'B',66=>'A',74=>'B',76=>'A',85=>'B',86=>'A'],
            'SEMANGAT'       => [45=>'A',51=>'B',55=>'A',62=>'B',65=>'A',73=>'B',75=>'A',84=>'B',85=>'A'],
            'SIBUK'          => [34=>'A',44=>'A',54=>'A',61=>'B',64=>'A',72=>'B',74=>'A',83=>'B',84=>'A'],
            'PUTUSAN'        => [23=>'A',33=>'A',43=>'A',53=>'A',63=>'A',71=>'B',73=>'A',82=>'B',83=>'A'],
            'PIMPINAN'       => [12=>'A',22=>'A',32=>'A',42=>'A',52=>'A',62=>'A',72=>'A',81=>'B',82=>'A'],
            'KERJA_KERAS'    => [1=>'A',11=>'A',21=>'A',31=>'A',41=>'A',51=>'A',61=>'A',71=>'A',81=>'A'],
            'TAAT_ATURAN'    => [10=>'A',20=>'A',30=>'A',40=>'A',50=>'A',60=>'A',70=>'A',80=>'A',90=>'A'],
            'DUKUNGAN_ATASAN'=> [9=>'A',10=>'B',19=>'A',29=>'A',39=>'A',49=>'A',59=>'A',69=>'A',79=>'A'],
            'AGRESI'         => [8=>'A',9=>'B',18=>'A',20=>'B',28=>'A',38=>'A',48=>'A',58=>'A',68=>'A'],
            'HASRAT_BERUBAH' => [7=>'A',8=>'B',17=>'A',19=>'B',27=>'A',30=>'B',37=>'A',47=>'A',57=>'A'],
            'DEKAT_SAYANG'   => [6=>'A',7=>'B',16=>'A',18=>'B',26=>'A',29=>'B',36=>'A',40=>'B',46=>'A'],
            'BETAH_KELOMPOK' => [5=>'A',6=>'B',15=>'A',17=>'B',25=>'A',28=>'B',35=>'A',39=>'B',50=>'B'],
            'PERHATIAN'      => [4=>'A',5=>'B',14=>'A',16=>'B',24=>'A',27=>'B',38=>'B',49=>'B',60=>'B'],
            'KENDALI_ORG'    => [3=>'A',4=>'B',13=>'A',15=>'B',26=>'B',37=>'B',48=>'B',59=>'B',70=>'B'],
            'BERPRESTASI'    => [2=>'A',3=>'B',14=>'B',25=>'B',36=>'B',47=>'B',58=>'B',69=>'B',80=>'B'],
            'TGS_PRIBADI'    => [2=>'B',13=>'B',24=>'B',35=>'B',46=>'B',57=>'B',68=>'B',79=>'B',90=>'B'],
        ];

        $soalData = [
            1  => ['a' => 'Saya seorang pekerja "keras"', 'b' => 'Saya bukan seorang pemurung'],
            2  => ['a' => 'Saya suka bekerja lebih baik dari orang lain', 'b' => 'Saya suka mengerjakan apa yang sedang saya kerjakan, sampai selesai'],
            3  => ['a' => 'Saya suka menunjukkan caranya melaksanakan sesuatu hal', 'b' => 'Saya ingin bekerja sebaik mungkin'],
            4  => ['a' => 'Saya suka berkelakar', 'b' => 'Saya senang mengatakan kepada orang lain, apa yang harus dilakukan'],
            5  => ['a' => 'Saya suka menggabungkan diri dengan kelompok-kelompok', 'b' => 'Saya suka diperhatikan oleh kelompok-kelompok'],
            6  => ['a' => 'Saya senang bersahabat intim dengan seseorang', 'b' => 'Saya senang bersahabat dengan sekolompok orang'],
            7  => ['a' => 'Saya cepat berubah bila hal itu diperlukan', 'b' => 'Saya berusaha untuk intim dengan teman-teman'],
            8  => ['a' => 'Saya suka "membalas dendam" bila saya benar-benar disakiti', 'b' => 'Saya suka melakukan hal-hal yang baru dan berbeda'],
            9  => ['a' => 'Saya ingin atasan saya menyukai saya', 'b' => 'Saya suka mengatakan kepada orang lain, bila mereka salah'],
            10 => ['a' => 'Saya suka mengikuti perintah-perintah yang diberikan kepada saya', 'b' => 'Saya suka menyenangkan hati orang yang memimpin saya'],
            11 => ['a' => 'Saya mencoba sekuat tenaga', 'b' => 'Saya seorang yang tertib. Saya meletakkan segala sesuatu pada tempatnya'],
            12 => ['a' => 'Saya membuat orang lain melakukan apa yang saya inginkan', 'b' => 'Saya bukan orang yang cepat gusar'],
            13 => ['a' => 'Saya suka mengatakan kepada kelompok, apa yang harus dilakukan', 'b' => 'Saya menekuni satu pekerjaan sampai selesai'],
            14 => ['a' => 'Saya ingin tampak bersemangat dan menarik', 'b' => 'Saya ingin menjadi sangat sukses'],
            15 => ['a' => 'Saya suka menyelaraskan diri dengan kelompok', 'b' => 'Saya suka membantu orang lain menentukan pendapatnya'],
            16 => ['a' => 'Saya cemas kalau orang lain tidak menyukai saya', 'b' => 'Saya senang kalau orang-orang memperhatikan saya'],
            17 => ['a' => 'Saya suka mencoba sesuatu yang baru', 'b' => 'Saya lebih suka bekerja bersama orang-orang daripada bekerja sendiri'],
            18 => ['a' => 'Kadang-kadang saya menyalahkan orang lain bila terjadi sesuatu kesalahan', 'b' => 'Saya cemas bila seseorang tidak menyukai saya'],
            19 => ['a' => 'Saya suka menyenangkan hati orang yang memimpin saya', 'b' => 'Saya suka mencoba pekerjaan-pekerjaan yang baru dan berbeda'],
            20 => ['a' => 'Saya menyukai petunjuk yang terinci untuk melakukan sesuatu pekerjaan', 'b' => 'Saya suka mengatakan kepada orang lain bila mereka mengganggu saya'],
            21 => ['a' => 'Saya selalu mencoba sekuat tenaga', 'b' => 'Saya senang bekerja dengan sangat cermat dan hati-hati'],
            22 => ['a' => 'Saya adalah seorang pemimpin yang baik', 'b' => 'Saya mengorganisir tugas-tugas secara baik'],
            23 => ['a' => 'Saya mudah menjadi gusar', 'b' => 'Saya seorang yang lambat dalam membuat keputusan'],
            24 => ['a' => 'Saya senang mengerjakan beberapa pekerjaan pada waktu yang bersamaan', 'b' => 'Bila dalam kelompok, saya lebih suka diam'],
            25 => ['a' => 'Saya senang bila diundang', 'b' => 'Saya ingin melakukan sesuatu lebih baik dari orang lain'],
            26 => ['a' => 'Saya suka berteman intim dengan teman-teman saya', 'b' => 'Saya suka memberi nasihat kepada orang lain'],
            27 => ['a' => 'Saya suka melakukan hal-hal yang baru dan berbeda', 'b' => 'Saya suka menceritakan keberhasilan saya dalam mengerjakan tugas'],
            28 => ['a' => 'Bila saya benar, saya suka mempertahankannya "mati-matian"', 'b' => 'Saya suka bergabung ke dalam suatu kelompok'],
            29 => ['a' => 'Saya tidak mau berbeda dengan orang lain', 'b' => 'Saya berusaha untuk sangat intim dengan orang-orang'],
            30 => ['a' => 'Saya suka diajari mengenai caranya mengerjakan suatu pekerjaan', 'b' => 'Saya mudah merasa jemu (bosan)'],
            31 => ['a' => 'Saya bekerja "keras"', 'b' => 'Saya banyak berpikir dan berencana'],
            32 => ['a' => 'Saya memimpin kelompok', 'b' => 'Hal-hal yang kecil (detail) menarik hati saya'],
            33 => ['a' => 'Saya cepat dan mudah mengambil keputusan', 'b' => 'Saya meletakkan segala sesuatu secara rapi dan teratur'],
            34 => ['a' => 'Tugas-tugas saya kerjakan secara cepat', 'b' => 'Saya jarang marah atau sedih'],
            35 => ['a' => 'Saya ingin menjadi bagian dari kelompok', 'b' => 'Pada suatu waktu tertentu, saya hanya ingin mengerjakan satu tugas saja'],
            36 => ['a' => 'Saya berusaha untuk intim dengan teman-teman saya', 'b' => 'Saya berusaha keras untuk menjadi yang terbaik'],
            37 => ['a' => 'Saya menyukai mode baju baru dan tipe-tipe mobil baru', 'b' => 'Saya ingin menjadi penanggung jawab bagi orang-orang lain'],
            38 => ['a' => 'Saya suka berdebat', 'b' => 'Saya ingin diperhatikan'],
            39 => ['a' => 'Saya suka menyenangkan hati orang yang memimpin saya', 'b' => 'Saya tertarik menjadi anggota dari suatu kelompok'],
            40 => ['a' => 'Saya senang mengikuti aturan secara tertib', 'b' => 'Saya suka orang-orang mengenal saya benar-benar'],
            41 => ['a' => 'Saya mencoba sekuat tenaga', 'b' => 'Saya sangat menyenangkan'],
            42 => ['a' => 'Orang lain beranggapan bahwa saya adalah seorang pemimpin yang baik', 'b' => 'Saya berpikir jauh ke depan dan terinci'],
            43 => ['a' => 'Seringkali saya memanfaatkan peluang', 'b' => 'Saya senang memperhatikan hal-hal sampai sekecil-kecilnya'],
            44 => ['a' => 'Orang lain menganggap saya bekerja cepat', 'b' => 'Orang lain menganggap saya dapat melakukan penataan yang rapi dan teratur'],
            45 => ['a' => 'Saya menyukai permainan-permainan dan olahraga', 'b' => 'Saya sangat menyenangkan'],
            46 => ['a' => 'Saya senang bila orang-orang dapat intim dan bersahabat', 'b' => 'Saya selalu berusaha menyelesaikan apa yang telah saya mulai'],
            47 => ['a' => 'Saya suka bereksperimen dan mencoba sesuatu yang baru', 'b' => 'Saya suka mengerjakan pekerjaan-pekerjaan yang sulit dengan baik'],
            48 => ['a' => 'Saya senang diperlakukan secara adil', 'b' => 'Saya senang mengajari orang lain bagaimana caranya mengerjakan sesuatu'],
            49 => ['a' => 'Saya suka mengerjakan apa yang diharapkan dari saya', 'b' => 'Saya suka menarik perhatian'],
            50 => ['a' => 'Saya suka petunjuk-petunjuk terinci dalam melaksanakan pekerjaan', 'b' => 'Saya senang berada bersama dengan orang lain'],
            51 => ['a' => 'Saya selalu berusaha mengerjakan tugas secara sempurna', 'b' => 'Orang lain menganggap, saya tidak mengenal lelah, dalam kerja sehari-hari'],
            52 => ['a' => 'Saya tergolong tipe pemimpin', 'b' => 'Saya mudah berteman'],
            53 => ['a' => 'Saya memanfaatkan peluang-peluang', 'b' => 'Saya banyak berpikir'],
            54 => ['a' => 'Saya bekerja dengan kecepatan yang mantap dan cepat', 'b' => 'Saya senang mengerjakan hal-hal yang detail'],
            55 => ['a' => 'Saya memiliki banyak energi untuk permainan-permainan dan olahraga', 'b' => 'Saya menempatkan segala sesuatunya secara rapi dan teratur'],
            56 => ['a' => 'Saya bergaul baik dengan semua orang', 'b' => 'Saya "pandai mengendalikan diri"'],
            57 => ['a' => 'Saya ingin berkenalan dengan orang-orang baru dan mengerjakan hal-hal baru', 'b' => 'Saya selalu ingin menyelesaikan pekerjaan yang sudah saya mulai'],
            58 => ['a' => 'Biasanya saya bersikeras mengenai apa yang saya yakini', 'b' => 'Biasanya saya suka bekerja "keras"'],
            59 => ['a' => 'Saya menyukai saran-saran dari orang-orang yang saya kagumi', 'b' => 'Saya senang mengatur orang lain'],
            60 => ['a' => 'Saya biarkan orang-orang lain mempengaruhi saya', 'b' => 'Saya suka menerima banyak perhatian'],
            61 => ['a' => 'Biasanya saya bekerja sangat "keras"', 'b' => 'Biasanya saya bekerja cepat'],
            62 => ['a' => 'Bila saya berbicara, kelompok akan mendengarkan', 'b' => 'Saya terampil mempergunakan alat-alat kerja'],
            63 => ['a' => 'Saya lambat membina persahabatan', 'b' => 'Saya lambat dalam mengambil keputusan'],
            64 => ['a' => 'Biasanya saya makan secara cepat', 'b' => 'Saya suka membaca'],
            65 => ['a' => 'Saya menyukai pekerjaan yang memungkinkan saya "berkeliling"', 'b' => 'Saya menyukai pekerjaan yang harus dilakukan secara teliti'],
            66 => ['a' => 'Saya berteman sebanyak mungkin', 'b' => 'Saya dapat menemukan hal-hal yang telah saya pindahkan'],
            67 => ['a' => 'Perencanaan saya jauh ke masa depan', 'b' => 'Saya selalu menyenangkan'],
            68 => ['a' => 'Saya merasa bangga akan nama baik saya', 'b' => 'Saya tetap menekuni satu permasalahan sampai ia terselesaikan'],
            69 => ['a' => 'Saya suka menyenangkan hati orang-orang yang saya kagumi', 'b' => 'Saya suka menjadi seorang yang berhasil'],
            70 => ['a' => 'Saya senang bila orang-orang lain mengambil keputusan untuk kelompok', 'b' => 'Saya suka mengambil keputusan untuk kelompok'],
            71 => ['a' => 'Saya selalu berusaha sangat "keras"', 'b' => 'Saya cepat dan mudah mengambil keputusan'],
            72 => ['a' => 'Biasanya kelompok saya mengerjakan hal-hal yang saya inginkan', 'b' => 'Biasanya saya tergesa-gesa'],
            73 => ['a' => 'Saya seringkali merasa lelah', 'b' => 'Saya lambat di dalam mengambil keputusan'],
            74 => ['a' => 'Saya bekerja secara cepat', 'b' => 'Saya mudah mendapat kawan'],
            75 => ['a' => 'Biasanya saya bersemangat atau bergairah', 'b' => 'Sebagian besar waktu saya untuk berpikir'],
            76 => ['a' => 'Saya sangat hangat kepada orang-orang', 'b' => 'Saya menyukai pekerjaan yang menuntut ketepatan'],
            77 => ['a' => 'Saya banyak berpikir dan merencana', 'b' => 'Saya meletakkan segala sesuatu pada tempatnya'],
            78 => ['a' => 'Saya suka tugas yang perlu ditekuni sampai kepada hal sedetilnya', 'b' => 'Saya tidak cepat marah'],
            79 => ['a' => 'Saya senang mengikuti orang-orang yang saya kagumi', 'b' => 'Saya selalu menyelesaikan pekerjaan yang saya mulai'],
            80 => ['a' => 'Saya menyukai petunjuk-petunjuk yang jelas', 'b' => 'Saya suka bekerja "keras"'],
            81 => ['a' => 'Saya mengejar apa yang saya inginkan', 'b' => 'Saya adalah seorang pemimpin yang baik'],
            82 => ['a' => 'Saya membuat orang lain bekerja keras', 'b' => 'Saya adalah seorang yang "gampangan" (tak banyak pertimbangan)'],
            83 => ['a' => 'Saya membuat keputusan-keputusan secara cepat', 'b' => 'Bicara saya cepat'],
            84 => ['a' => 'Biasanya saya bekerja tergesa-gesa', 'b' => 'Secara teratur saya berolahraga'],
            85 => ['a' => 'Saya tidak suka bertemu dengan orang-orang', 'b' => 'Saya cepat lelah'],
            86 => ['a' => 'Saya mempunyai banyak sekali teman', 'b' => 'Banyak waktu saya untuk berpikir'],
            87 => ['a' => 'Saya suka bekerja dengan teori', 'b' => 'Saya suka bekerja sedetil-detilnya'],
            88 => ['a' => 'Saya suka bekerja sampai sedetil-detilnya', 'b' => 'Saya suka mengorganisir pekerjaan saya'],
            89 => ['a' => 'Saya meletakkan segala sesuatu pada tempatnya', 'b' => 'Saya selalu menyenangkan'],
            90 => ['a' => 'Saya senang diberi petunjuk mengenai apa yang harus saya lakukan', 'b' => 'Saya harus menyelesaikan apa yang sudah saya mulai'],
        ];

        foreach ($soalData as $nomor => $teks) {
            $teksA = $teks['a'];
            $teksB = $teks['b'];
            $soal = Soal::create([
                'alat_tes_id' => $alatTes->id,
                'nomor'       => $nomor,
                'teks_soal'   => 'Pilih salah satu pernyataan yang paling menggambarkan diri Anda.',
                'tipe_format' => 'forced_choice',
                'urutan'      => $nomor,
            ]);

            $opsiA = OpsiJawaban::create([
                'soal_id'   => $soal->id,
                'teks_opsi' => $teksA,
                'urutan'    => 1,
            ]);

            $opsiB = OpsiJawaban::create([
                'soal_id'   => $soal->id,
                'teks_opsi' => $teksB,
                'urutan'    => 2,
            ]);

            foreach ($kunciSkoring as $kodeDimensi => $peta) {
                if (!isset($peta[$nomor])) {
                    continue;
                }
                $opsiTarget = $peta[$nomor] === 'A' ? $opsiA : $opsiB;
                BobotOpsiDimensi::create([
                    'opsi_jawaban_id' => $opsiTarget->id,
                    'dimensi_id'      => $dim[$kodeDimensi],
                    'bobot'           => 1,
                    'is_reverse'      => false,
                ]);
            }

            $this->command->info("Soal PAP nomor {$nomor} berhasil diseder.");
        }

        $this->command->info('PapikostikSoalSeeder selesai: 90 soal, 180 opsi, bobot dimension tersimpan.');
    }
}
