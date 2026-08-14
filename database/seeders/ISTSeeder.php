<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\NormaKonversi;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class ISTSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ALAT TES
        $alatTes = AlatTes::updateOrCreate(
            ['kode' => 'IST'],
            [
                'nama'              => 'IST (Intelligenz Struktur Test)',
                'kategori'          => 'kognitif',
                'deskripsi'         => 'Tes intelegensi 9 subtes mengukur struktur kemampuan intelektual.',
                'format_dasar'      => 'Mixed',
                'pola_skoring'      => 'kognitif',
                'durasi_total_menit' => 90,
                'is_aktif'          => true,
            ]
        );

        // 2. DIMENSI SUBTES
        $subtesData = [
            ['SE', 'SE - Satzerganzung (Melengkapi Kalimat)', 1, 420, 'Soal-soal berikut terdiri atas kalimat-kalimat. Pada setiap kalimat satu kata hilang dan disediakan 5 kata pilihan. Pilih kata yang paling tepat.'],
            ['WA', 'WA - Wortauswahl (Pemilihan Kata)', 2, 480, 'Ditentukan 5 kata. Pada 4 dari 5 kata itu terdapat suatu kesamaan. Carilah kata kelima yang TIDAK memiliki kesamaan dengan keempat kata itu.'],
            ['AN', 'AN - Analogien (Analogi Kata)', 3, 420, 'Ditentukan 3 kata. Antara kata pertama dan kata kedua terdapat suatu hubungan tertentu. Antara kata ketiga dan salah satu dari lima pilihan harus terdapat hubungan yang sama. Carilah kata itu.'],
            ['GE', 'GE - Gemeinsamkeiten (Persamaan)', 4, 480, 'Ditentukan dua kata. Carilah satu perkataan yang meliputi pengertian kedua kata tadi. Tuliskan jawaban Anda.'],
            ['RA', 'RA - Rechenaufgaben (Soal Hitung)', 5, 600, 'Kerjakan soal-soal hitungan berikut. Tuliskan jawaban angka pada kolom yang tersedia.'],
            ['ZR', 'ZR - Zahlenreihen (Deret Angka)', 6, 600, 'Pada setiap deret angka terdapat suatu aturan tertentu. Carilah angka berikutnya dan tuliskan pada kolom yang tersedia.'],
            ['FA', 'FA - Figurenauswahl (Pilihan Gambar)', 7, 420, 'Setiap soal memperlihatkan suatu bentuk yang terpotong menjadi beberapa bagian. Carilah di antara bentuk-bentuk pilihan bentuk yang dibangun dengan menyusun potongan-potongan itu.'],
            ['WU', 'WU - Wurfelaufgaben (Kubus)', 8, 540, 'Ditentukan 5 buah kubus. Setiap soal memperlihatkan salah satu kubus dalam kedudukan yang berbeda. Carilah kubus yang dimaksudkan.'],
            ['ME', 'ME - Merkaufgaben (Memori)', 9, 180, 'Hafalkan kata-kata berikut selama waktu yang ditentukan. Setelah waktu habis, Anda akan ditanya mengenai kata-kata tersebut.'],
        ];

        $dimensiIds = [];
        foreach ($subtesData as [$kode, $nama, $urutan, $durasi, $instruksi]) {
            $dim = DimensiAlatTes::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => $kode],
                ['nama_dimensi' => $nama, 'tipe_kategori' => 'psikogram', 'arah_skor' => 'tinggi_baik', 'urutan' => $urutan, 'durasi_detik' => $durasi, 'instruksi_subtes' => $instruksi]
            );
            $dimensiIds[$kode] = $dim->id;
        }
        $dimIQ = DimensiAlatTes::updateOrCreate(
            ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => 'IQ'],
            ['nama_dimensi' => 'IQ Total', 'tipe_kategori' => 'psikogram', 'arah_skor' => 'tinggi_baik', 'urutan' => 10]
        );
        $dimensiIds['IQ'] = $dimIQ->id;
        $this->command->info('Dimensi: ' . count($dimensiIds));

        // 3. NORMA KONVERSI
        NormaKonversi::where('alat_tes_id', $alatTes->id)->delete();
        // Norma SE
        $norma = [
            [0, 'SLTP', 69],
            [0, 'SLTA', 71],
            [0, 'SARJ', 60],
            [1, 'SLTP', 73],
            [1, 'SLTA', 75],
            [1, 'SARJ', 65],
            [10, 'SLTP', 110],
            [10, 'SLTA', 108],
            [10, 'SARJ', 110],
            [100, 'SLTP', 109],
            [100, 'SLTA', 97],
            [100, 'SARJ', 105],
            [101, 'SLTP', 109],
            [101, 'SLTA', 98],
            [101, 'SARJ', 106],
            [102, 'SLTP', 110],
            [102, 'SLTA', 99],
            [102, 'SARJ', 106],
            [103, 'SLTP', 110],
            [103, 'SLTA', 100],
            [103, 'SARJ', 107],
            [104, 'SLTP', 111],
            [104, 'SLTA', 101],
            [104, 'SARJ', 107],
            [105, 'SLTP', 111],
            [105, 'SLTA', 102],
            [105, 'SARJ', 108],
            [106, 'SLTP', 112],
            [106, 'SLTA', 103],
            [106, 'SARJ', 108],
            [107, 'SLTP', 112],
            [107, 'SLTA', 104],
            [107, 'SARJ', 109],
            [108, 'SLTP', 113],
            [108, 'SLTA', 105],
            [108, 'SARJ', 109],
            [109, 'SLTP', 113],
            [109, 'SLTA', 106],
            [109, 'SARJ', 110],
            [11, 'SLTP', 114],
            [11, 'SLTA', 112],
            [11, 'SARJ', 115],
            [110, 'SLTP', 114],
            [110, 'SLTA', 107],
            [110, 'SARJ', 110],
            [111, 'SLTP', 114],
            [111, 'SLTA', 108],
            [111, 'SARJ', 111],
            [112, 'SLTP', 114],
            [112, 'SLTA', 109],
            [112, 'SARJ', 111],
            [113, 'SLTP', 115],
            [113, 'SLTA', 110],
            [113, 'SARJ', 112],
            [114, 'SLTP', 115],
            [114, 'SLTA', 111],
            [114, 'SARJ', 112],
            [115, 'SLTP', 116],
            [115, 'SLTA', 112],
            [115, 'SARJ', 113],
            [116, 'SLTP', 116],
            [116, 'SLTA', 113],
            [116, 'SARJ', 113],
            [117, 'SLTP', 117],
            [117, 'SLTA', 114],
            [117, 'SARJ', 114],
            [118, 'SLTP', 117],
            [118, 'SLTA', 115],
            [118, 'SARJ', 114],
            [119, 'SLTP', 118],
            [119, 'SLTA', 116],
            [119, 'SARJ', 115],
            [12, 'SLTP', 118],
            [12, 'SLTA', 116],
            [12, 'SARJ', 120],
            [120, 'SLTP', 118],
            [120, 'SLTA', 117],
            [120, 'SARJ', 115],
            [121, 'SLTP', 118],
            [121, 'SLTA', 118],
            [121, 'SARJ', 116],
            [122, 'SLTP', 119],
            [122, 'SLTA', 119],
            [122, 'SARJ', 116],
            [123, 'SLTP', 119],
            [123, 'SLTA', 120],
            [123, 'SARJ', 117],
            [124, 'SLTP', 120],
            [124, 'SLTA', 121],
            [124, 'SARJ', 117],
            [125, 'SLTP', 120],
            [125, 'SLTA', 122],
            [125, 'SARJ', 118],
            [126, 'SLTP', 121],
            [126, 'SLTA', 123],
            [126, 'SARJ', 118],
            [127, 'SLTP', 121],
            [127, 'SLTA', 124],
            [127, 'SARJ', 118],
            [128, 'SLTP', 122],
            [128, 'SLTA', 125],
            [128, 'SARJ', 119],
            [129, 'SLTP', 122],
            [129, 'SLTA', 126],
            [129, 'SARJ', 119],
            [13, 'SLTP', 122],
            [13, 'SLTA', 120],
            [13, 'SARJ', 125],
            [130, 'SLTP', 123],
            [130, 'SLTA', 127],
            [130, 'SARJ', 120],
            [131, 'SLTP', 123],
            [131, 'SLTA', 128],
            [131, 'SARJ', 120],
            [132, 'SLTP', 123],
            [132, 'SLTA', 129],
            [132, 'SARJ', 121],
            [133, 'SLTP', 124],
            [133, 'SLTA', 130],
            [133, 'SARJ', 121],
            [134, 'SLTP', 124],
            [134, 'SLTA', 131],
            [134, 'SARJ', 122],
            [135, 'SLTP', 125],
            [135, 'SLTA', 132],
            [135, 'SARJ', 122],
            [136, 'SLTP', 125],
            [136, 'SLTA', 133],
            [136, 'SARJ', 123],
            [137, 'SLTP', 126],
            [137, 'SLTA', 134],
            [137, 'SARJ', 123],
            [138, 'SLTP', 126],
            [138, 'SLTA', 135],
            [138, 'SARJ', 124],
            [139, 'SLTP', 127],
            [139, 'SLTA', 136],
            [139, 'SARJ', 124],
            [14, 'SLTP', 126],
            [14, 'SLTA', 123],
            [14, 'SARJ', 130],
            [140, 'SLTP', 127],
            [140, 'SLTA', 137],
            [140, 'SARJ', 125],
            [141, 'SLTP', 127],
            [141, 'SLTA', 138],
            [141, 'SARJ', 125],
            [142, 'SLTP', 128],
            [142, 'SLTA', 139],
            [142, 'SARJ', 126],
            [143, 'SLTP', 128],
            [143, 'SLTA', 140],
            [143, 'SARJ', 126],
            [144, 'SLTP', 129],
            [144, 'SLTA', 141],
            [144, 'SARJ', 127],
            [145, 'SLTP', 129],
            [145, 'SLTA', 142],
            [145, 'SARJ', 127],
            [146, 'SLTP', 130],
            [146, 'SLTA', 143],
            [146, 'SARJ', 128],
            [147, 'SLTP', 130],
            [147, 'SLTA', 144],
            [147, 'SARJ', 128],
            [148, 'SLTP', 131],
            [148, 'SLTA', 145],
            [148, 'SARJ', 129],
            [149, 'SLTP', 131],
            [149, 'SLTA', 146],
            [149, 'SARJ', 129],
            [15, 'SLTP', 130],
            [15, 'SLTA', 127],
            [15, 'SARJ', 135],
            [150, 'SLTP', 132],
            [150, 'SLTA', 147],
            [150, 'SARJ', 130],
            [16, 'SLTP', 134],
            [16, 'SLTA', 131],
            [16, 'SARJ', 140],
            [17, 'SLTP', 139],
            [17, 'SLTA', 135],
            [17, 'SARJ', 145],
            [18, 'SLTP', 143],
            [18, 'SLTA', 138],
            [18, 'SARJ', 150],
            [19, 'SLTP', 147],
            [19, 'SLTA', 142],
            [19, 'SARJ', 155],
            [2, 'SLTP', 77],
            [2, 'SLTA', 78],
            [2, 'SARJ', 70],
            [20, 'SLTP', 151],
            [20, 'SLTA', 146],
            [20, 'SARJ', 160],
            [28, 'SLTP', 77],
            [28, 'SLTA', 25],
            [28, 'SARJ', 70],
            [29, 'SLTP', 77],
            [29, 'SLTA', 26],
            [29, 'SARJ', 70],
            [3, 'SLTP', 82],
            [3, 'SLTA', 82],
            [3, 'SARJ', 75],
            [30, 'SLTP', 78],
            [30, 'SLTA', 27],
            [30, 'SARJ', 71],
            [31, 'SLTP', 78],
            [31, 'SLTA', 28],
            [31, 'SARJ', 71],
            [32, 'SLTP', 78],
            [32, 'SLTA', 29],
            [32, 'SARJ', 72],
            [33, 'SLTP', 79],
            [33, 'SLTA', 30],
            [33, 'SARJ', 72],
            [34, 'SLTP', 79],
            [34, 'SLTA', 31],
            [34, 'SARJ', 73],
            [35, 'SLTP', 80],
            [35, 'SLTA', 32],
            [35, 'SARJ', 73],
            [36, 'SLTP', 80],
            [36, 'SLTA', 33],
            [36, 'SARJ', 74],
            [37, 'SLTP', 81],
            [37, 'SLTA', 34],
            [37, 'SARJ', 74],
            [38, 'SLTP', 81],
            [38, 'SLTA', 35],
            [38, 'SARJ', 75],
            [39, 'SLTP', 82],
            [39, 'SLTA', 36],
            [39, 'SARJ', 75],
            [4, 'SLTP', 86],
            [4, 'SLTA', 86],
            [4, 'SARJ', 80],
            [40, 'SLTP', 82],
            [40, 'SLTA', 37],
            [40, 'SARJ', 76],
            [41, 'SLTP', 82],
            [41, 'SLTA', 38],
            [41, 'SARJ', 76],
            [42, 'SLTP', 83],
            [42, 'SLTA', 39],
            [42, 'SARJ', 77],
            [43, 'SLTP', 83],
            [43, 'SLTA', 40],
            [43, 'SARJ', 77],
            [44, 'SLTP', 84],
            [44, 'SLTA', 41],
            [44, 'SARJ', 78],
            [45, 'SLTP', 84],
            [45, 'SLTA', 42],
            [45, 'SARJ', 78],
            [46, 'SLTP', 85],
            [46, 'SLTA', 43],
            [46, 'SARJ', 79],
            [47, 'SLTP', 85],
            [47, 'SLTA', 44],
            [47, 'SARJ', 79],
            [48, 'SLTP', 86],
            [48, 'SLTA', 45],
            [48, 'SARJ', 80],
            [49, 'SLTP', 86],
            [49, 'SLTA', 46],
            [49, 'SARJ', 80],
            [5, 'SLTP', 90],
            [5, 'SLTA', 90],
            [5, 'SARJ', 85],
            [50, 'SLTP', 87],
            [50, 'SLTA', 47],
            [50, 'SARJ', 81],
            [51, 'SLTP', 87],
            [51, 'SLTA', 48],
            [51, 'SARJ', 81],
            [52, 'SLTP', 87],
            [52, 'SLTA', 49],
            [52, 'SARJ', 82],
            [53, 'SLTP', 88],
            [53, 'SLTA', 50],
            [53, 'SARJ', 82],
            [54, 'SLTP', 88],
            [54, 'SLTA', 51],
            [54, 'SARJ', 83],
            [55, 'SLTP', 89],
            [55, 'SLTA', 52],
            [55, 'SARJ', 83],
            [56, 'SLTP', 89],
            [56, 'SLTA', 53],
            [56, 'SARJ', 84],
            [57, 'SLTP', 90],
            [57, 'SLTA', 54],
            [57, 'SARJ', 84],
            [58, 'SLTP', 90],
            [58, 'SLTA', 55],
            [58, 'SARJ', 85],
            [59, 'SLTP', 91],
            [59, 'SLTA', 56],
            [59, 'SARJ', 85],
            [6, 'SLTP', 94],
            [6, 'SLTA', 93],
            [6, 'SARJ', 90],
            [60, 'SLTP', 91],
            [60, 'SLTA', 57],
            [60, 'SARJ', 85],
            [61, 'SLTP', 91],
            [61, 'SLTA', 58],
            [61, 'SARJ', 86],
            [62, 'SLTP', 92],
            [62, 'SLTA', 59],
            [62, 'SARJ', 86],
            [63, 'SLTP', 92],
            [63, 'SLTA', 60],
            [63, 'SARJ', 87],
            [64, 'SLTP', 93],
            [64, 'SLTA', 61],
            [64, 'SARJ', 87],
            [65, 'SLTP', 93],
            [65, 'SLTA', 62],
            [65, 'SARJ', 88],
            [66, 'SLTP', 94],
            [66, 'SLTA', 63],
            [66, 'SARJ', 88],
            [67, 'SLTP', 94],
            [67, 'SLTA', 64],
            [67, 'SARJ', 89],
            [68, 'SLTP', 95],
            [68, 'SLTA', 65],
            [68, 'SARJ', 89],
            [69, 'SLTP', 95],
            [69, 'SLTA', 66],
            [69, 'SARJ', 90],
            [7, 'SLTP', 98],
            [7, 'SLTA', 97],
            [7, 'SARJ', 95],
            [70, 'SLTP', 96],
            [70, 'SLTA', 67],
            [70, 'SARJ', 90],
            [71, 'SLTP', 96],
            [71, 'SLTA', 68],
            [71, 'SARJ', 91],
            [72, 'SLTP', 96],
            [72, 'SLTA', 69],
            [72, 'SARJ', 91],
            [73, 'SLTP', 97],
            [73, 'SLTA', 70],
            [73, 'SARJ', 92],
            [74, 'SLTP', 97],
            [74, 'SLTA', 71],
            [74, 'SARJ', 92],
            [75, 'SLTP', 98],
            [75, 'SLTA', 72],
            [75, 'SARJ', 93],
            [76, 'SLTP', 98],
            [76, 'SLTA', 73],
            [76, 'SARJ', 93],
            [77, 'SLTP', 99],
            [77, 'SLTA', 74],
            [77, 'SARJ', 94],
            [78, 'SLTP', 99],
            [78, 'SLTA', 75],
            [78, 'SARJ', 94],
            [79, 'SLTP', 100],
            [79, 'SLTA', 76],
            [79, 'SARJ', 95],
            [8, 'SLTP', 102],
            [8, 'SLTA', 101],
            [8, 'SARJ', 100],
            [80, 'SLTP', 100],
            [80, 'SLTA', 77],
            [80, 'SARJ', 95],
            [81, 'SLTP', 100],
            [81, 'SLTA', 78],
            [81, 'SARJ', 96],
            [82, 'SLTP', 101],
            [82, 'SLTA', 79],
            [82, 'SARJ', 96],
            [83, 'SLTP', 101],
            [83, 'SLTA', 80],
            [83, 'SARJ', 97],
            [84, 'SLTP', 102],
            [84, 'SLTA', 81],
            [84, 'SARJ', 98],
            [85, 'SLTP', 102],
            [85, 'SLTA', 82],
            [85, 'SARJ', 98],
            [86, 'SLTP', 103],
            [86, 'SLTA', 83],
            [86, 'SARJ', 98],
            [87, 'SLTP', 103],
            [87, 'SLTA', 84],
            [87, 'SARJ', 99],
            [88, 'SLTP', 104],
            [88, 'SLTA', 85],
            [88, 'SARJ', 99],
            [89, 'SLTP', 104],
            [89, 'SLTA', 86],
            [89, 'SARJ', 100],
            [9, 'SLTP', 106],
            [9, 'SLTA', 105],
            [9, 'SARJ', 105],
            [90, 'SLTP', 105],
            [90, 'SLTA', 87],
            [90, 'SARJ', 100],
            [91, 'SLTP', 105],
            [91, 'SLTA', 88],
            [91, 'SARJ', 101],
            [92, 'SLTP', 105],
            [92, 'SLTA', 89],
            [92, 'SARJ', 101],
            [93, 'SLTP', 106],
            [93, 'SLTA', 90],
            [93, 'SARJ', 102],
            [94, 'SLTP', 106],
            [94, 'SLTA', 91],
            [94, 'SARJ', 102],
            [95, 'SLTP', 107],
            [95, 'SLTA', 92],
            [95, 'SARJ', 103],
            [96, 'SLTP', 107],
            [96, 'SLTA', 93],
            [96, 'SARJ', 103],
            [97, 'SLTP', 108],
            [97, 'SLTA', 94],
            [97, 'SARJ', 104],
            [98, 'SLTP', 108],
            [98, 'SLTA', 95],
            [98, 'SARJ', 104],
            [99, 'SLTP', 109],
            [99, 'SLTA', 96],
            [99, 'SARJ', 105],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['SE'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma WA
        $norma = [
            [0, 'SLTP', 68],
            [0, 'SLTA', 67],
            [0, 'SARJ', 35],
            [1, 'SLTP', 72],
            [1, 'SLTA', 71],
            [1, 'SARJ', 40],
            [10, 'SLTP', 107],
            [10, 'SLTA', 102],
            [10, 'SARJ', 85],
            [11, 'SLTP', 111],
            [11, 'SLTA', 105],
            [11, 'SARJ', 90],
            [12, 'SLTP', 114],
            [12, 'SLTA', 109],
            [12, 'SARJ', 95],
            [13, 'SLTP', 118],
            [13, 'SLTA', 112],
            [13, 'SARJ', 100],
            [14, 'SLTP', 122],
            [14, 'SLTA', 116],
            [14, 'SARJ', 105],
            [15, 'SLTP', 126],
            [15, 'SLTA', 119],
            [15, 'SARJ', 110],
            [16, 'SLTP', 130],
            [16, 'SLTA', 123],
            [16, 'SARJ', 115],
            [17, 'SLTP', 134],
            [17, 'SLTA', 126],
            [17, 'SARJ', 120],
            [18, 'SLTP', 138],
            [18, 'SLTA', 130],
            [18, 'SARJ', 125],
            [19, 'SLTP', 141],
            [19, 'SLTA', 133],
            [19, 'SARJ', 130],
            [2, 'SLTP', 76],
            [2, 'SLTA', 74],
            [2, 'SARJ', 45],
            [20, 'SLTP', 145],
            [20, 'SLTA', 137],
            [20, 'SARJ', 135],
            [3, 'SLTP', 80],
            [3, 'SLTA', 78],
            [3, 'SARJ', 50],
            [4, 'SLTP', 84],
            [4, 'SLTA', 81],
            [4, 'SARJ', 55],
            [5, 'SLTP', 87],
            [5, 'SLTA', 84],
            [5, 'SARJ', 60],
            [6, 'SLTP', 91],
            [6, 'SLTA', 88],
            [6, 'SARJ', 65],
            [7, 'SLTP', 95],
            [7, 'SLTA', 92],
            [7, 'SARJ', 70],
            [8, 'SLTP', 99],
            [8, 'SLTA', 95],
            [8, 'SARJ', 75],
            [9, 'SLTP', 103],
            [9, 'SLTA', 98],
            [9, 'SARJ', 80],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['WA'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma AN
        $norma = [
            [0, 'SLTP', 76],
            [0, 'SLTA', 79],
            [0, 'SARJ', 34],
            [1, 'SLTP', 80],
            [1, 'SLTA', 83],
            [1, 'SARJ', 42],
            [10, 'SLTP', 112],
            [10, 'SLTA', 112],
            [10, 'SARJ', 124],
            [11, 'SLTP', 115],
            [11, 'SLTA', 115],
            [11, 'SARJ', 132],
            [12, 'SLTP', 119],
            [12, 'SLTA', 119],
            [12, 'SARJ', 140],
            [13, 'SLTP', 122],
            [13, 'SLTA', 122],
            [13, 'SARJ', 148],
            [14, 'SLTP', 126],
            [14, 'SLTA', 125],
            [14, 'SARJ', 156],
            [15, 'SLTP', 130],
            [15, 'SLTA', 129],
            [15, 'SARJ', 164],
            [16, 'SLTP', 133],
            [16, 'SLTA', 132],
            [16, 'SARJ', 172],
            [17, 'SLTP', 137],
            [17, 'SLTA', 135],
            [17, 'SARJ', 180],
            [18, 'SLTP', 140],
            [18, 'SLTA', 138],
            [18, 'SARJ', 188],
            [19, 'SLTP', 144],
            [19, 'SLTA', 142],
            [19, 'SARJ', 196],
            [2, 'SLTP', 83],
            [2, 'SLTA', 86],
            [2, 'SARJ', 60],
            [20, 'SLTP', 147],
            [20, 'SLTA', 145],
            [20, 'SARJ', 204],
            [3, 'SLTP', 87],
            [3, 'SLTA', 89],
            [3, 'SARJ', 68],
            [4, 'SLTP', 90],
            [4, 'SLTA', 92],
            [4, 'SARJ', 76],
            [5, 'SLTP', 94],
            [5, 'SLTA', 96],
            [5, 'SARJ', 84],
            [6, 'SLTP', 98],
            [6, 'SLTA', 99],
            [6, 'SARJ', 92],
            [7, 'SLTP', 101],
            [7, 'SLTA', 102],
            [7, 'SARJ', 100],
            [8, 'SLTP', 105],
            [8, 'SLTA', 106],
            [8, 'SARJ', 108],
            [9, 'SLTP', 108],
            [9, 'SLTA', 109],
            [9, 'SARJ', 116],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['AN'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma GE
        $norma = [
            [0, 'SLTP', 78],
            [0, 'SLTA', 70],
            [0, 'SARJ', 57],
            [1, 'SLTP', 80],
            [1, 'SLTA', 72],
            [1, 'SARJ', 60],
            [10, 'SLTP', 97],
            [10, 'SLTA', 93],
            [10, 'SARJ', 84],
            [11, 'SLTP', 99],
            [11, 'SLTA', 95],
            [11, 'SARJ', 87],
            [12, 'SLTP', 101],
            [12, 'SLTA', 97],
            [12, 'SARJ', 90],
            [13, 'SLTP', 103],
            [13, 'SLTA', 99],
            [13, 'SARJ', 93],
            [14, 'SLTP', 105],
            [14, 'SLTA', 101],
            [14, 'SARJ', 96],
            [15, 'SLTP', 107],
            [15, 'SLTA', 104],
            [15, 'SARJ', 97],
            [16, 'SLTP', 109],
            [16, 'SLTA', 106],
            [16, 'SARJ', 100],
            [17, 'SLTP', 111],
            [17, 'SLTA', 108],
            [17, 'SARJ', 103],
            [18, 'SLTP', 113],
            [18, 'SLTA', 110],
            [18, 'SARJ', 106],
            [19, 'SLTP', 115],
            [19, 'SLTA', 113],
            [19, 'SARJ', 109],
            [2, 'SLTP', 82],
            [2, 'SLTA', 75],
            [2, 'SARJ', 63],
            [20, 'SLTP', 117],
            [20, 'SLTA', 115],
            [20, 'SARJ', 111],
            [21, 'SLTP', 119],
            [21, 'SLTA', 117],
            [21, 'SARJ', 114],
            [22, 'SLTP', 121],
            [22, 'SLTA', 119],
            [22, 'SARJ', 117],
            [23, 'SLTP', 123],
            [23, 'SLTA', 122],
            [23, 'SARJ', 120],
            [24, 'SLTP', 125],
            [24, 'SLTA', 124],
            [24, 'SARJ', 123],
            [25, 'SLTP', 127],
            [25, 'SLTA', 126],
            [25, 'SARJ', 126],
            [26, 'SLTP', 129],
            [26, 'SLTA', 129],
            [26, 'SARJ', 129],
            [27, 'SLTP', 131],
            [27, 'SLTA', 131],
            [27, 'SARJ', 131],
            [28, 'SLTP', 133],
            [28, 'SLTA', 133],
            [28, 'SARJ', 134],
            [29, 'SLTP', 135],
            [29, 'SLTA', 135],
            [29, 'SARJ', 137],
            [3, 'SLTP', 84],
            [3, 'SLTA', 77],
            [3, 'SARJ', 64],
            [30, 'SLTP', 137],
            [30, 'SLTA', 137],
            [30, 'SARJ', 140],
            [31, 'SLTP', 139],
            [31, 'SLTA', 140],
            [31, 'SARJ', 143],
            [32, 'SLTP', 141],
            [32, 'SLTA', 142],
            [32, 'SARJ', 146],
            [4, 'SLTP', 86],
            [4, 'SLTA', 79],
            [4, 'SARJ', 67],
            [5, 'SLTP', 88],
            [5, 'SLTA', 81],
            [5, 'SARJ', 70],
            [6, 'SLTP', 90],
            [6, 'SLTA', 84],
            [6, 'SARJ', 73],
            [7, 'SLTP', 92],
            [7, 'SLTA', 86],
            [7, 'SARJ', 76],
            [8, 'SLTP', 94],
            [8, 'SLTA', 88],
            [8, 'SARJ', 79],
            [9, 'SLTP', 96],
            [9, 'SLTA', 90],
            [9, 'SARJ', 81],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['GE'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma ME
        $norma = [
            [0, 'SLTP', 71],
            [0, 'SLTA', 74],
            [0, 'SARJ', 52],
            [1, 'SLTP', 74],
            [1, 'SLTA', 76],
            [1, 'SARJ', 56],
            [10, 'SLTP', 95],
            [10, 'SLTA', 95],
            [10, 'SARJ', 92],
            [11, 'SLTP', 98],
            [11, 'SLTA', 97],
            [11, 'SARJ', 96],
            [12, 'SLTP', 100],
            [12, 'SLTA', 100],
            [12, 'SARJ', 100],
            [13, 'SLTP', 103],
            [13, 'SLTA', 102],
            [13, 'SARJ', 104],
            [14, 'SLTP', 105],
            [14, 'SLTA', 104],
            [14, 'SARJ', 108],
            [15, 'SLTP', 108],
            [15, 'SLTA', 106],
            [15, 'SARJ', 112],
            [16, 'SLTP', 110],
            [16, 'SLTA', 108],
            [16, 'SARJ', 116],
            [17, 'SLTP', 113],
            [17, 'SLTA', 110],
            [17, 'SARJ', 120],
            [18, 'SLTP', 115],
            [18, 'SLTA', 112],
            [18, 'SARJ', 124],
            [19, 'SLTP', 117],
            [19, 'SLTA', 115],
            [19, 'SARJ', 128],
            [2, 'SLTP', 76],
            [2, 'SLTA', 78],
            [2, 'SARJ', 60],
            [20, 'SLTP', 120],
            [20, 'SLTA', 117],
            [20, 'SARJ', 132],
            [3, 'SLTP', 78],
            [3, 'SLTA', 80],
            [3, 'SARJ', 64],
            [4, 'SLTP', 81],
            [4, 'SLTA', 82],
            [4, 'SARJ', 68],
            [5, 'SLTP', 83],
            [5, 'SLTA', 84],
            [5, 'SARJ', 72],
            [6, 'SLTP', 86],
            [6, 'SLTA', 87],
            [6, 'SARJ', 76],
            [7, 'SLTP', 88],
            [7, 'SLTA', 89],
            [7, 'SARJ', 80],
            [8, 'SLTP', 91],
            [8, 'SLTA', 91],
            [8, 'SARJ', 84],
            [9, 'SLTP', 93],
            [9, 'SLTA', 93],
            [9, 'SARJ', 88],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['ME'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma RA
        $norma = [
            [0, 'SLTP', 79],
            [0, 'SLTA', 79],
            [0, 'SARJ', 60],
            [1, 'SLTP', 82],
            [1, 'SLTA', 82],
            [1, 'SARJ', 65],
            [10, 'SLTP', 106],
            [10, 'SLTA', 105],
            [10, 'SARJ', 110],
            [11, 'SLTP', 108],
            [11, 'SLTA', 107],
            [11, 'SARJ', 115],
            [12, 'SLTP', 111],
            [12, 'SLTA', 110],
            [12, 'SARJ', 120],
            [13, 'SLTP', 114],
            [13, 'SLTA', 112],
            [13, 'SARJ', 125],
            [14, 'SLTP', 116],
            [14, 'SLTA', 115],
            [14, 'SARJ', 130],
            [15, 'SLTP', 119],
            [15, 'SLTA', 118],
            [15, 'SARJ', 135],
            [16, 'SLTP', 122],
            [16, 'SLTA', 120],
            [16, 'SARJ', 140],
            [17, 'SLTP', 124],
            [17, 'SLTA', 123],
            [17, 'SARJ', 145],
            [18, 'SLTP', 127],
            [18, 'SLTA', 125],
            [18, 'SARJ', 150],
            [19, 'SLTP', 130],
            [19, 'SLTA', 128],
            [19, 'SARJ', 155],
            [2, 'SLTP', 84],
            [2, 'SLTA', 84],
            [2, 'SARJ', 70],
            [20, 'SLTP', 132],
            [20, 'SLTA', 130],
            [20, 'SARJ', 160],
            [3, 'SLTP', 87],
            [3, 'SLTA', 87],
            [3, 'SARJ', 75],
            [4, 'SLTP', 90],
            [4, 'SLTA', 89],
            [4, 'SARJ', 80],
            [5, 'SLTP', 92],
            [5, 'SLTA', 92],
            [5, 'SARJ', 85],
            [6, 'SLTP', 95],
            [6, 'SLTA', 95],
            [6, 'SARJ', 90],
            [7, 'SLTP', 98],
            [7, 'SLTA', 97],
            [7, 'SARJ', 95],
            [8, 'SLTP', 100],
            [8, 'SLTA', 100],
            [8, 'SARJ', 100],
            [9, 'SLTP', 103],
            [9, 'SLTA', 102],
            [9, 'SARJ', 105],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['RA'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma ZR
        $norma = [
            [0, 'SLTP', 79],
            [0, 'SLTA', 85],
            [0, 'SARJ', 60],
            [1, 'SLTP', 81],
            [1, 'SLTA', 87],
            [1, 'SARJ', 64],
            [10, 'SLTP', 102],
            [10, 'SLTA', 104],
            [10, 'SARJ', 100],
            [11, 'SLTP', 105],
            [11, 'SLTA', 106],
            [11, 'SARJ', 104],
            [12, 'SLTP', 107],
            [12, 'SLTA', 108],
            [12, 'SARJ', 108],
            [13, 'SLTP', 109],
            [13, 'SLTA', 109],
            [13, 'SARJ', 112],
            [14, 'SLTP', 112],
            [14, 'SLTA', 111],
            [14, 'SARJ', 116],
            [15, 'SLTP', 114],
            [15, 'SLTA', 113],
            [15, 'SARJ', 120],
            [16, 'SLTP', 116],
            [16, 'SLTA', 115],
            [16, 'SARJ', 124],
            [17, 'SLTP', 119],
            [17, 'SLTA', 117],
            [17, 'SARJ', 128],
            [18, 'SLTP', 121],
            [18, 'SLTA', 119],
            [18, 'SARJ', 132],
            [19, 'SLTP', 123],
            [19, 'SLTA', 121],
            [19, 'SARJ', 136],
            [2, 'SLTP', 84],
            [2, 'SLTA', 89],
            [2, 'SARJ', 68],
            [20, 'SLTP', 126],
            [20, 'SLTA', 122],
            [20, 'SARJ', 140],
            [3, 'SLTP', 86],
            [3, 'SLTA', 91],
            [3, 'SARJ', 72],
            [4, 'SLTP', 88],
            [4, 'SLTA', 93],
            [4, 'SARJ', 76],
            [5, 'SLTP', 91],
            [5, 'SLTA', 95],
            [5, 'SARJ', 80],
            [6, 'SLTP', 93],
            [6, 'SLTA', 96],
            [6, 'SARJ', 84],
            [7, 'SLTP', 95],
            [7, 'SLTA', 98],
            [7, 'SARJ', 88],
            [8, 'SLTP', 98],
            [8, 'SLTA', 100],
            [8, 'SARJ', 92],
            [9, 'SLTP', 100],
            [9, 'SLTA', 102],
            [9, 'SARJ', 96],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['ZR'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma FA
        $norma = [
            [0, 'SLTP', 66],
            [0, 'SLTA', 69],
            [0, 'SARJ', 45],
            [1, 'SLTP', 69],
            [1, 'SLTA', 73],
            [1, 'SARJ', 50],
            [10, 'SLTP', 99],
            [10, 'SLTA', 100],
            [10, 'SARJ', 95],
            [11, 'SLTP', 102],
            [11, 'SLTA', 103],
            [11, 'SARJ', 100],
            [12, 'SLTP', 105],
            [12, 'SLTA', 106],
            [12, 'SARJ', 105],
            [13, 'SLTP', 108],
            [13, 'SLTA', 109],
            [13, 'SARJ', 110],
            [14, 'SLTP', 112],
            [14, 'SLTA', 112],
            [14, 'SARJ', 115],
            [15, 'SLTP', 115],
            [15, 'SLTA', 115],
            [15, 'SARJ', 120],
            [16, 'SLTP', 118],
            [16, 'SLTA', 118],
            [16, 'SARJ', 125],
            [17, 'SLTP', 121],
            [17, 'SLTA', 121],
            [17, 'SARJ', 130],
            [18, 'SLTP', 124],
            [18, 'SLTA', 124],
            [18, 'SARJ', 135],
            [19, 'SLTP', 128],
            [19, 'SLTA', 127],
            [19, 'SARJ', 140],
            [2, 'SLTP', 73],
            [2, 'SLTA', 75],
            [2, 'SARJ', 55],
            [20, 'SLTP', 131],
            [20, 'SLTA', 130],
            [20, 'SARJ', 145],
            [3, 'SLTP', 76],
            [3, 'SLTA', 79],
            [3, 'SARJ', 60],
            [4, 'SLTP', 79],
            [4, 'SLTA', 82],
            [4, 'SARJ', 65],
            [5, 'SLTP', 82],
            [5, 'SLTA', 85],
            [5, 'SARJ', 70],
            [6, 'SLTP', 86],
            [6, 'SLTA', 88],
            [6, 'SARJ', 75],
            [7, 'SLTP', 89],
            [7, 'SLTA', 91],
            [7, 'SARJ', 80],
            [8, 'SLTP', 92],
            [8, 'SLTA', 94],
            [8, 'SARJ', 85],
            [9, 'SLTP', 95],
            [9, 'SLTA', 97],
            [9, 'SARJ', 90],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['FA'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma WU
        $norma = [
            [0, 'SLTP', 74],
            [0, 'SLTA', 75],
            [0, 'SARJ', 55],
            [1, 'SLTP', 76],
            [1, 'SLTA', 78],
            [1, 'SARJ', 60],
            [10, 'SLTP', 103],
            [10, 'SLTA', 105],
            [10, 'SARJ', 105],
            [11, 'SLTP', 106],
            [11, 'SLTA', 108],
            [11, 'SARJ', 110],
            [12, 'SLTP', 109],
            [12, 'SLTA', 111],
            [12, 'SARJ', 115],
            [13, 'SLTP', 112],
            [13, 'SLTA', 114],
            [13, 'SARJ', 120],
            [14, 'SLTP', 115],
            [14, 'SLTA', 117],
            [14, 'SARJ', 125],
            [15, 'SLTP', 118],
            [15, 'SLTA', 120],
            [15, 'SARJ', 130],
            [16, 'SLTP', 121],
            [16, 'SLTA', 123],
            [16, 'SARJ', 135],
            [17, 'SLTP', 124],
            [17, 'SLTA', 126],
            [17, 'SARJ', 140],
            [18, 'SLTP', 127],
            [18, 'SLTA', 129],
            [18, 'SARJ', 145],
            [19, 'SLTP', 130],
            [19, 'SLTA', 131],
            [19, 'SARJ', 150],
            [2, 'SLTP', 79],
            [2, 'SLTA', 81],
            [2, 'SARJ', 65],
            [20, 'SLTP', 133],
            [20, 'SLTA', 134],
            [20, 'SARJ', 155],
            [3, 'SLTP', 82],
            [3, 'SLTA', 84],
            [3, 'SARJ', 70],
            [4, 'SLTP', 85],
            [4, 'SLTA', 87],
            [4, 'SARJ', 75],
            [5, 'SLTP', 88],
            [5, 'SLTA', 90],
            [5, 'SARJ', 80],
            [6, 'SLTP', 91],
            [6, 'SLTA', 93],
            [6, 'SARJ', 85],
            [7, 'SLTP', 94],
            [7, 'SLTA', 96],
            [7, 'SARJ', 90],
            [8, 'SLTP', 97],
            [8, 'SLTA', 99],
            [8, 'SARJ', 95],
            [9, 'SLTP', 100],
            [9, 'SLTA', 102],
            [9, 'SARJ', 100],
        ];
        foreach ($norma as [$rw, $kel, $skor]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['WU'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $skor]);
        }
        // Norma IQ SLTP
        $normaIQ = [
            [100, 'SLTP', 109],
            [101, 'SLTP', 109],
            [102, 'SLTP', 110],
            [103, 'SLTP', 110],
            [104, 'SLTP', 111],
            [105, 'SLTP', 111],
            [106, 'SLTP', 112],
            [107, 'SLTP', 112],
            [108, 'SLTP', 113],
            [109, 'SLTP', 113],
            [110, 'SLTP', 114],
            [111, 'SLTP', 114],
            [112, 'SLTP', 114],
            [113, 'SLTP', 115],
            [114, 'SLTP', 115],
            [115, 'SLTP', 116],
            [116, 'SLTP', 116],
            [117, 'SLTP', 117],
            [118, 'SLTP', 117],
            [119, 'SLTP', 118],
            [120, 'SLTP', 118],
            [121, 'SLTP', 118],
            [122, 'SLTP', 119],
            [123, 'SLTP', 119],
            [124, 'SLTP', 120],
            [125, 'SLTP', 120],
            [126, 'SLTP', 121],
            [127, 'SLTP', 121],
            [128, 'SLTP', 122],
            [129, 'SLTP', 122],
            [130, 'SLTP', 123],
            [131, 'SLTP', 123],
            [132, 'SLTP', 123],
            [133, 'SLTP', 124],
            [134, 'SLTP', 124],
            [135, 'SLTP', 125],
            [136, 'SLTP', 125],
            [137, 'SLTP', 126],
            [138, 'SLTP', 126],
            [139, 'SLTP', 127],
            [140, 'SLTP', 127],
            [141, 'SLTP', 127],
            [142, 'SLTP', 128],
            [143, 'SLTP', 128],
            [144, 'SLTP', 129],
            [145, 'SLTP', 129],
            [146, 'SLTP', 130],
            [147, 'SLTP', 130],
            [148, 'SLTP', 131],
            [149, 'SLTP', 131],
            [150, 'SLTP', 132],
            [28, 'SLTP', 77],
            [29, 'SLTP', 77],
            [30, 'SLTP', 78],
            [31, 'SLTP', 78],
            [32, 'SLTP', 78],
            [33, 'SLTP', 79],
            [34, 'SLTP', 79],
            [35, 'SLTP', 80],
            [36, 'SLTP', 80],
            [37, 'SLTP', 81],
            [38, 'SLTP', 81],
            [39, 'SLTP', 82],
            [40, 'SLTP', 82],
            [41, 'SLTP', 82],
            [42, 'SLTP', 83],
            [43, 'SLTP', 83],
            [44, 'SLTP', 84],
            [45, 'SLTP', 84],
            [46, 'SLTP', 85],
            [47, 'SLTP', 85],
            [48, 'SLTP', 86],
            [49, 'SLTP', 86],
            [50, 'SLTP', 87],
            [51, 'SLTP', 87],
            [52, 'SLTP', 87],
            [53, 'SLTP', 88],
            [54, 'SLTP', 88],
            [55, 'SLTP', 89],
            [56, 'SLTP', 89],
            [57, 'SLTP', 90],
            [58, 'SLTP', 90],
            [59, 'SLTP', 91],
            [60, 'SLTP', 91],
            [61, 'SLTP', 91],
            [62, 'SLTP', 92],
            [63, 'SLTP', 92],
            [64, 'SLTP', 93],
            [65, 'SLTP', 93],
            [66, 'SLTP', 94],
            [67, 'SLTP', 94],
            [68, 'SLTP', 95],
            [69, 'SLTP', 95],
            [70, 'SLTP', 96],
            [71, 'SLTP', 96],
            [72, 'SLTP', 96],
            [73, 'SLTP', 97],
            [74, 'SLTP', 97],
            [75, 'SLTP', 98],
            [76, 'SLTP', 98],
            [77, 'SLTP', 99],
            [78, 'SLTP', 99],
            [79, 'SLTP', 100],
            [80, 'SLTP', 100],
            [81, 'SLTP', 100],
            [82, 'SLTP', 101],
            [83, 'SLTP', 101],
            [84, 'SLTP', 102],
            [85, 'SLTP', 102],
            [86, 'SLTP', 103],
            [87, 'SLTP', 103],
            [88, 'SLTP', 104],
            [89, 'SLTP', 104],
            [90, 'SLTP', 105],
            [91, 'SLTP', 105],
            [92, 'SLTP', 105],
            [93, 'SLTP', 106],
            [94, 'SLTP', 106],
            [95, 'SLTP', 107],
            [96, 'SLTP', 107],
            [97, 'SLTP', 108],
            [98, 'SLTP', 108],
            [99, 'SLTP', 109],
            [100, 'GTSLTP', 107],
            [101, 'GTSLTP', 107],
            [102, 'GTSLTP', 108],
            [103, 'GTSLTP', 108],
            [104, 'GTSLTP', 109],
            [105, 'GTSLTP', 109],
            [106, 'GTSLTP', 110],
            [107, 'GTSLTP', 110],
            [108, 'GTSLTP', 111],
            [109, 'GTSLTP', 111],
            [110, 'GTSLTP', 112],
            [111, 'GTSLTP', 112],
            [112, 'GTSLTP', 113],
            [113, 'GTSLTP', 113],
            [114, 'GTSLTP', 114],
            [115, 'GTSLTP', 114],
            [116, 'GTSLTP', 115],
            [117, 'GTSLTP', 115],
            [118, 'GTSLTP', 116],
            [119, 'GTSLTP', 116],
            [120, 'GTSLTP', 117],
            [121, 'GTSLTP', 117],
            [122, 'GTSLTP', 118],
            [123, 'GTSLTP', 118],
            [124, 'GTSLTP', 118],
            [125, 'GTSLTP', 119],
            [126, 'GTSLTP', 119],
            [127, 'GTSLTP', 120],
            [128, 'GTSLTP', 120],
            [129, 'GTSLTP', 121],
            [130, 'GTSLTP', 121],
            [131, 'GTSLTP', 122],
            [132, 'GTSLTP', 122],
            [133, 'GTSLTP', 123],
            [134, 'GTSLTP', 123],
            [135, 'GTSLTP', 124],
            [136, 'GTSLTP', 124],
            [137, 'GTSLTP', 125],
            [138, 'GTSLTP', 125],
            [139, 'GTSLTP', 126],
            [140, 'GTSLTP', 126],
            [141, 'GTSLTP', 127],
            [142, 'GTSLTP', 127],
            [143, 'GTSLTP', 128],
            [144, 'GTSLTP', 128],
            [145, 'GTSLTP', 129],
            [146, 'GTSLTP', 129],
            [147, 'GTSLTP', 130],
            [25, 'GTSLTP', 70],
            [26, 'GTSLTP', 70],
            [27, 'GTSLTP', 71],
            [28, 'GTSLTP', 71],
            [29, 'GTSLTP', 72],
            [30, 'GTSLTP', 72],
            [31, 'GTSLTP', 73],
            [32, 'GTSLTP', 73],
            [33, 'GTSLTP', 74],
            [34, 'GTSLTP', 74],
            [35, 'GTSLTP', 75],
            [36, 'GTSLTP', 75],
            [37, 'GTSLTP', 76],
            [38, 'GTSLTP', 76],
            [39, 'GTSLTP', 77],
            [40, 'GTSLTP', 77],
            [41, 'GTSLTP', 78],
            [42, 'GTSLTP', 78],
            [43, 'GTSLTP', 79],
            [44, 'GTSLTP', 79],
            [45, 'GTSLTP', 80],
            [46, 'GTSLTP', 80],
            [47, 'GTSLTP', 81],
            [48, 'GTSLTP', 81],
            [49, 'GTSLTP', 82],
            [50, 'GTSLTP', 82],
            [51, 'GTSLTP', 83],
            [52, 'GTSLTP', 83],
            [53, 'GTSLTP', 84],
            [54, 'GTSLTP', 84],
            [55, 'GTSLTP', 85],
            [56, 'GTSLTP', 85],
            [57, 'GTSLTP', 85],
            [58, 'GTSLTP', 86],
            [59, 'GTSLTP', 86],
            [60, 'GTSLTP', 87],
            [61, 'GTSLTP', 87],
            [62, 'GTSLTP', 88],
            [63, 'GTSLTP', 88],
            [64, 'GTSLTP', 89],
            [65, 'GTSLTP', 89],
            [66, 'GTSLTP', 90],
            [67, 'GTSLTP', 90],
            [68, 'GTSLTP', 91],
            [69, 'GTSLTP', 91],
            [70, 'GTSLTP', 92],
            [71, 'GTSLTP', 92],
            [72, 'GTSLTP', 93],
            [73, 'GTSLTP', 93],
            [74, 'GTSLTP', 94],
            [75, 'GTSLTP', 94],
            [76, 'GTSLTP', 95],
            [77, 'GTSLTP', 95],
            [78, 'GTSLTP', 96],
            [79, 'GTSLTP', 96],
            [80, 'GTSLTP', 97],
            [81, 'GTSLTP', 98],
            [82, 'GTSLTP', 98],
            [83, 'GTSLTP', 98],
            [84, 'GTSLTP', 99],
            [85, 'GTSLTP', 99],
            [86, 'GTSLTP', 100],
            [87, 'GTSLTP', 100],
            [88, 'GTSLTP', 101],
            [89, 'GTSLTP', 101],
            [90, 'GTSLTP', 102],
            [91, 'GTSLTP', 102],
            [92, 'GTSLTP', 103],
            [93, 'GTSLTP', 103],
            [94, 'GTSLTP', 104],
            [95, 'GTSLTP', 104],
            [96, 'GTSLTP', 105],
            [97, 'GTSLTP', 105],
            [98, 'GTSLTP', 106],
            [99, 'GTSLTP', 106],
        ];
        foreach ($normaIQ as [$rw, $kel, $iq]) {
            NormaKonversi::create(['alat_tes_id' => $alatTes->id, 'dimensi_id' => $dimensiIds['IQ'], 'kelompok_segmen' => $kel, 'tahap' => 1, 'skor_mentah_min' => $rw, 'skor_mentah_max' => $rw, 'skor_hasil' => $iq]);
        }
        $this->command->info('Norma selesai');

        // 4. SOAL - Hapus lama
        $soalIds = Soal::where('alat_tes_id', $alatTes->id)->pluck('id');
        BobotOpsiDimensi::whereIn('opsi_jawaban_id', OpsiJawaban::whereIn('soal_id', $soalIds)->pluck('id'))->delete();
        OpsiJawaban::whereIn('soal_id', $soalIds)->delete();
        Soal::where('alat_tes_id', $alatTes->id)->delete();
        // SE
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 1, 'teks_soal' => 'Pengaruh seseorang terhadap orang lain seharusnya bergantung pada .....', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kekuasaan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bujukan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kekayaan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'keberanian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kewibawaan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 2, 'teks_soal' => 'Lawannya hemat ialah ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'murah', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kikir', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'boros', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bernilai', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kaya', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 3, 'teks_soal' => '........ tidak termasuk cuaca', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'angin puyuh', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'halilintar', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'salju', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gempa bumi', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kabut', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 4, 'teks_soal' => 'Lawannya setia ialah ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'cinta', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'benci', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'persahabatan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'khianat', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'permusuhan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 5, 'teks_soal' => 'Seekor kuda selalu mempunyai ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 5]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kandang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ladam', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pelana', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kuku', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'surai', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 6, 'teks_soal' => 'Seorang paman ...... lebih tua dari kemenakannya.', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 6]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jarang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'biasanya', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'selalu', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tidak pernah', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kadang-kadang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 7, 'teks_soal' => 'Pada jumlah yang sama, nilai kalori yang tertinggi terdapat pada ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 7]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ikan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'daging', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lemak', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tahu', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sayuran', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 8, 'teks_soal' => 'Pada suatu pertandingan selalu terdapat ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 8]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lawan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'wasit', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'penonton', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sorak', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kemenangan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 9, 'teks_soal' => 'Suatu pernyataan yang belum dipastikan dikatakan sebagai pernyataan yang .....', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 9]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'paradoks', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tergesa-gesa', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mempunyai arti rangkap', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menyesatkan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'hipotesis', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 10, 'teks_soal' => 'Pada sepatu selalu terdapat ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 10]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kulit', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sol', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tali sepatu', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gesper', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lidah', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 11, 'teks_soal' => 'Suatu ...... tidak menyangkut persoalan pencegahan kecelakaan.', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 11]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lampu lalu lintas', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kacamata pelindung', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kotak PPPK', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tanda peringatan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'palang kereta api', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 12, 'teks_soal' => 'Mata uang logam Rp 50 tahun 1991, garis tengahnya ialah ...... mm.', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 12]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '17', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '29', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '25', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '20', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '15', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 13, 'teks_soal' => 'Seseorang yang bersikap menyangsikan setiap kemajuan ialah seorang yang .....', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 13]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'demokratis', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'radikal', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'liberal', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'konservatif', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'anarkis', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 14, 'teks_soal' => 'Lawannya tidak pernah ialah ......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 14]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sering', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kadang-kadang', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jarang', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kerap kali', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'selalu', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 15, 'teks_soal' => 'Jarak antara Jakarta dan Surabaya kira-kira ...... Km', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 15]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '650', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '1000', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '800', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '600', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '950', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 16, 'teks_soal' => 'Untuk dapat membuat nada yang rendah dan mendalam, kita memerlukan banyak .....', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 16]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kekuatan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'peranan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ayunan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berat', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'suara', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 17, 'teks_soal' => 'Ayah ...... lebih berpengalaman dari pada anaknya', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 17]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'selalu', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'biasanya', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jauh', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jarang', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pada dasarnya', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 18, 'teks_soal' => 'Diantara kota-kota berikut ini, maka kota ..... letaknya paling selatan.', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 18]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'Jakarta', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'Bandung', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'Cirebon', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'Semarang', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'Surabaya', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 19, 'teks_soal' => 'Jika kita mengetahui jumlah presentase nomor-nomor lotere yang tidak menang, maka kita dapat menghitung .....', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 19]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jumlah nomor yang menang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pajak lotere', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kemungkinan menang', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jumlah pengikut', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tinggi keuntungan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 20, 'teks_soal' => 'Seorang anak yang berumur 10 tahun tingginya rata-rata ...... cm', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 20]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '150', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '130', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '110', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '105', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => '115', 'urutan' => 5]);
        // WA
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 21, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan dengan keempat kata lainnya:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 21]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lingkungan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'panah', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'elips', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'busur', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lengkungan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 22, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 22]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengetuk', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'memaki', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menjahit', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menggergaji', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'memukul', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 23, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 23]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lebar', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'keliling', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'luas', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'isi', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'panjang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 24, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 24]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengikat', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menyatukan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'melepaskan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengaitkan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'melekatkan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 25, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 25]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'arah', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'timur', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perjalanan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tujuan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'selatan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 26, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 26]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jarak', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perpisahan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tugas', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'batas', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perceraian', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 27, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 27]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'saringan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kelambu', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'payung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tapisan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jala', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 28, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 28]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'putih', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pucat', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'buram', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kasar', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berkilauan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 29, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 29]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'otobis', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pesawat terbang', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sepeda motor', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sepeda', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kapal api', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 30, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 30]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'biola', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'seruling', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'klarinet', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'terompet', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'saxophon', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 31, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 31]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bergelombang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kasar', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berduri', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'licin', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lurus', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 32, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 32]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jam', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kompas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'penunjuk jalan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bintang pari', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'arah', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 33, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 33]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kebijaksanaan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pendidikan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perencanaan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'penempatan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pengerahan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 34, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 34]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bermotor', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berjalan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berlayar', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bersepeda', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berkuda', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 35, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 35]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gambar', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lukisan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'potret', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'patung', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ukiran', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 36, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 36]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'panjang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lonjong', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'runcing', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bulat', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bersudut', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 37, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 37]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kunci', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'palang pintu', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gerendel', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gunting', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'obeng', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 38, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 38]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jembatan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'batas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkawinan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pagar', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'masyarakat', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 39, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 39]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengetam', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menasehati', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengasah', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'melicinkan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menggosok', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 40, 'teks_soal' => 'Pilih kata yang TIDAK memiliki kesamaan:', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 40]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'batu', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'baja', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bulu', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'karet', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kayu', 'urutan' => 5]);
        // AN
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 41, 'teks_soal' => 'Menemukan : menghilangkan = Mengingat : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 41]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menghapal', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengenai', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'melupakan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berpikir', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'memimpikan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 42, 'teks_soal' => 'Bunga : jambangan = Burung : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 42]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sarang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'langit', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pagar', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pohon', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sangkar', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 43, 'teks_soal' => 'Kereta api : rel = Otobis : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 43]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'roda', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'poros', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ban', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jalan raya', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kecepatan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 44, 'teks_soal' => 'Perak : emas = Cincin : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 44]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'arloji', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'berlian', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'permata', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gelang', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'platina', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 45, 'teks_soal' => 'Lingkaran : bola = Bujur sangkar : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 45]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bentuk', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'gambar', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'segi empat', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kubus', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'piramida', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 46, 'teks_soal' => 'Saran : kepustakaan = Merundingkan : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 46]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menawarkan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menentukan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menilai', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'menimbang', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'merenungkan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 47, 'teks_soal' => 'Lidah : asam = Hidung : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 47]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mencium', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bernapas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mengecap', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tengik', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'asin', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 48, 'teks_soal' => 'Darah : pembuluh = Air : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 48]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pintu air', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sungai', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'talang', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'hujan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ember', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 49, 'teks_soal' => 'Saraf : penyalur = Pupil : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 49]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'penyinaran', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mata', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'melihat', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'cahaya', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pelindung', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 50, 'teks_soal' => 'Pengantar surat : pengantar telegram = Pandai besi : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 50]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'palu godam', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pedagang besi', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'api', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tukang emas', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'besi tempa', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 51, 'teks_soal' => 'Buta : warna = Tuli : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 51]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pendengaran', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mendengar', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'nada', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kata', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'telinga', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 52, 'teks_soal' => 'Makanan : bumbu = Ceramah : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 52]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'penghinaan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pidato', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kelakar', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesan', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ayat', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 53, 'teks_soal' => 'Marah : emosi = Duka cita : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 53]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'suka cita', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sakit hati', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'suasana hati', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sedih', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'rindu', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 54, 'teks_soal' => 'Mantel : jubah = Wool : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 54]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bahan sandang', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'domba', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sutra', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'jas', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'tekstil', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 55, 'teks_soal' => 'Ketinggian puncak : tekanan udara = Ketinggian nada : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 55]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'garpu tala', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'sopran', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'nyanyian', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'panjang senar', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'suara', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 56, 'teks_soal' => 'Negara : revolusi = Hidup : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 56]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'biologi', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'keturunan', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mutasi', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'seleksi', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ilmu hewan', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 57, 'teks_soal' => 'Kekurangan : penemuan = Panas : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 57]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'haus', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'khatulistiwa', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'es', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'matahari', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'dingin', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 58, 'teks_soal' => 'Kayu : diketam = Besi : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 58]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'dipalu', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'digergaji', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'dituang', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'dikikir', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'ditempa', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 59, 'teks_soal' => 'Olahragawan : lembing = Cendekiawan : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 59]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perpustakaan', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'penelitian', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'karya', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'studi', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'mikroskop', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 60, 'teks_soal' => 'Keledai : kuda pacuan = Pembakaran : ?', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 60]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'pemadam api', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'obor', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'letupan', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'korek api', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'lautan api', 'urutan' => 5]);
        // GE
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 61, 'teks_soal' => 'mawar - melati', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'bunga', 'urutan' => 61]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 62, 'teks_soal' => 'mata - telinga', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'indera', 'urutan' => 62]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 63, 'teks_soal' => 'gula - intan', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'benda keras', 'urutan' => 63]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 64, 'teks_soal' => 'hujan - salju', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'curah', 'urutan' => 64]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 65, 'teks_soal' => 'pengantar surat - telepon', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'alat komunikasi', 'urutan' => 65]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 66, 'teks_soal' => 'kamera - kacamata', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'alat optik', 'urutan' => 66]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 67, 'teks_soal' => 'lambung - usus', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'organ pencernaan', 'urutan' => 67]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 68, 'teks_soal' => 'banyak - sedikit', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'jumlah', 'urutan' => 68]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 69, 'teks_soal' => 'telur - benih', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'bibit', 'urutan' => 69]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 70, 'teks_soal' => 'bendera - lencana', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'lambang', 'urutan' => 70]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 71, 'teks_soal' => 'rumput - gajah', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'makanan', 'urutan' => 71]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 72, 'teks_soal' => 'ember - kantong', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'wadah', 'urutan' => 72]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 73, 'teks_soal' => 'awal - akhir', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'batas', 'urutan' => 73]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 74, 'teks_soal' => 'kikir - boros', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'sifat', 'urutan' => 74]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 75, 'teks_soal' => 'penawaran - permintaan', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'transaksi', 'urutan' => 75]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 76, 'teks_soal' => 'atas - bawah', 'tipe_format' => 'isian_teks', 'kunci_jawaban' => 'posisi', 'urutan' => 76]);
        // RA
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 77, 'teks_soal' => 'Jika seorang anak memiliki 50 rupiah dan memberikan 15 rupiah kepada orang lain, berapa rupiahkah yang masih tinggal padanya?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '35', 'urutan' => 77]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 78, 'teks_soal' => 'Berapa km-kah yang dapat ditempuh oleh kereta api dalam waktu 7 jam, jika kecepatannya 40 km/jam?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '280', 'urutan' => 78]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 79, 'teks_soal' => '15 peti buah-buahan beratnya 250 kg dan setiap peti kosong beratnya 3 kg, berapakah berat buah-buahan itu?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '205', 'urutan' => 79]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 80, 'teks_soal' => 'Seseorang mempunyai persediaan rumput yang cukup untuk 7 ekor kuda selama 78 hari. Berapa harikah persediaan itu cukup untuk 21 ekor kuda?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '26', 'urutan' => 80]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 81, 'teks_soal' => '3 batang coklat harganya Rp 5. Berapa batangkah yang dapat kita beli dengan Rp 50?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '30', 'urutan' => 81]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 82, 'teks_soal' => 'Seseorang dapat berjalan 1,75 m dalam waktu 1/4 detik. Berapakah meter yang dapat ia tempuh dalam waktu 10 detik?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '70', 'urutan' => 82]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 83, 'teks_soal' => 'Jika sebuah batu terletak 15 m di sebelah selatan dari sebatang pohon dan pohon itu berada 30 m di sebelah selatan dari sebuah rumah, berapa meterkah jarak antara batu dan rumah itu?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '45', 'urutan' => 83]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 84, 'teks_soal' => 'Jika 4,5 m bahan sandang harganya Rp 90, berapakah rupiahkah harganya 2,5 m?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '50', 'urutan' => 84]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 85, 'teks_soal' => '7 orang dapat menyelesaikan sesuatu pekerjaan dalam 6 hari. Berapa orangkah yang diperlukan untuk menyelesaikan pekerjaan itu dalam setengah hari?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '84', 'urutan' => 85]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 86, 'teks_soal' => 'Karena dipanaskan, kawat yang panjangnya 48 cm akan mengembang menjadi 52 cm. Setelah pemanasan, berapakah panjangnya kawat yang berukuran 72 cm?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '78', 'urutan' => 86]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 87, 'teks_soal' => 'Suatu pabrik dapat menghasilkan 304 batang pensil dalam waktu 8 jam. Berapa batangkah dihasilkan dalam waktu setengah jam?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '19', 'urutan' => 87]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 88, 'teks_soal' => 'Untuk suatu campuran diperlukan 2 bagian perak dan 3 bagian timah. Berapa gramkah perak yang diperlukan untuk mendapatkan campuran itu yang beratnya 15 gram?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '6', 'urutan' => 88]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 89, 'teks_soal' => 'Untuk setiap Rp 3 yang dimiliki Sidin, Hamid memiliki Rp 5. Jika mereka bersama mempunyai Rp 120, berapa rupiahkah yang dimiliki Hamid?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '75', 'urutan' => 89]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 90, 'teks_soal' => 'Mesin A menenun 60 m kain, sedangkan mesin B menenun 40 m. Berapa meterkah yang ditenun mesin A, jika mesin B menenun 60 m?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '90', 'urutan' => 90]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 91, 'teks_soal' => 'Seseorang membelikan 1/10 dari uangnya untuk perangko dan 4 kali jumlah itu untuk alat tulis. Sisa uangnya masih Rp 60. Berapa rupiahkah uang semula?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '100', 'urutan' => 91]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 92, 'teks_soal' => 'Di dalam dua peti terdapat 43 piring. Di dalam peti yang satu terdapat 9 piring lebih banyak dari pada di dalam peti yang lain. Berapa buah piring terdapat di dalam peti yang lebih kecil?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '17', 'urutan' => 92]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 93, 'teks_soal' => 'Suatu lembaran kain yang panjangnya 60 cm harus dibagikan sehingga panjangnya satu bagian ialah 2/3 dari bagian yang lain. Berapa panjangnya bagian yang terpendek?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '24', 'urutan' => 93]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 94, 'teks_soal' => 'Suatu perusahaan mengekspor 3/4 dari hasil produksinya dan menjual 4/5 dari sisa itu dalam negeri. Berapa persen hasil produksi yang masih tinggal?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '5', 'urutan' => 94]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 95, 'teks_soal' => 'Jika suatu botol berisi anggur hanya 7/8 bagian dan harganya ialah Rp 84, berapakah harga anggur itu jika botol itu hanya terisi 1/2 penuh?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '48', 'urutan' => 95]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 96, 'teks_soal' => 'Di dalam suatu keluarga setiap anak perempuan mempunyai jumlah saudara laki-laki yang sama dengan jumlah saudara perempuan dan setiap anak laki-laki mempunyai dua kali lebih banyak saudara perempuan dari pada saudara laki-laki. Berapa anak laki-lakikah yang terdapat di dalam keluarga tersebut?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '3', 'urutan' => 96]);
        // ZR
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 97, 'teks_soal' => '6 9 12 15 18 21 24 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '27', 'urutan' => 97]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 98, 'teks_soal' => '15 16 18 19 21 22 24 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '25', 'urutan' => 98]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 99, 'teks_soal' => '19 18 22 21 25 24 28 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '27', 'urutan' => 99]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 100, 'teks_soal' => '16 12 17 13 18 14 19 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '15', 'urutan' => 100]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 101, 'teks_soal' => '2 4 8 10 20 22 44 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '46', 'urutan' => 101]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 102, 'teks_soal' => '15 13 16 12 17 11 18 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '10', 'urutan' => 102]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 103, 'teks_soal' => '25 22 11 33 30 15 45 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '42', 'urutan' => 103]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 104, 'teks_soal' => '49 51 54 27 9 11 14 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '7', 'urutan' => 104]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 105, 'teks_soal' => '2 3 1 3 4 2 4 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '5', 'urutan' => 105]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 106, 'teks_soal' => '19 17 20 16 21 15 22 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '14', 'urutan' => 106]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 107, 'teks_soal' => '94 92 46 44 22 20 10 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '8', 'urutan' => 107]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 108, 'teks_soal' => '5 8 9 8 11 12 11 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '14', 'urutan' => 108]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 109, 'teks_soal' => '12 15 19 23 28 33 39 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '46', 'urutan' => 109]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 110, 'teks_soal' => '7 5 10 7 21 17 68 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '63', 'urutan' => 110]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 111, 'teks_soal' => '11 15 18 9 13 16 8 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '12', 'urutan' => 111]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 112, 'teks_soal' => '3 8 15 24 35 48 63 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '80', 'urutan' => 112]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 113, 'teks_soal' => '4 5 7 4 8 13 7 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '20', 'urutan' => 113]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 114, 'teks_soal' => '8 5 15 18 6 3 9 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '12', 'urutan' => 114]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 115, 'teks_soal' => '15 6 18 10 30 23 69 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '62', 'urutan' => 115]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 116, 'teks_soal' => '5 35 28 4 11 77 70 ?', 'tipe_format' => 'isian_angka', 'kunci_jawaban' => '10', 'urutan' => 116]);
        // FA (gambar diupload HR)
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 117, 'teks_soal' => 'Soal FA nomor 117', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 117]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 118, 'teks_soal' => 'Soal FA nomor 118', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 118]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 119, 'teks_soal' => 'Soal FA nomor 119', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 119]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 120, 'teks_soal' => 'Soal FA nomor 120', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 120]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 121, 'teks_soal' => 'Soal FA nomor 121', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 121]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 122, 'teks_soal' => 'Soal FA nomor 122', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 122]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 123, 'teks_soal' => 'Soal FA nomor 123', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 123]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 124, 'teks_soal' => 'Soal FA nomor 124', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 124]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 125, 'teks_soal' => 'Soal FA nomor 125', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 125]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 126, 'teks_soal' => 'Soal FA nomor 126', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 126]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 127, 'teks_soal' => 'Soal FA nomor 127', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 127]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 128, 'teks_soal' => 'Soal FA nomor 128', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '1', 'urutan' => 128]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 129, 'teks_soal' => 'Soal FA nomor 129', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 129]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 130, 'teks_soal' => 'Soal FA nomor 130', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 130]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 131, 'teks_soal' => 'Soal FA nomor 131', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 131]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 132, 'teks_soal' => 'Soal FA nomor 132', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 132]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 133, 'teks_soal' => 'Soal FA nomor 133', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 133]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 134, 'teks_soal' => 'Soal FA nomor 134', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 134]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 135, 'teks_soal' => 'Soal FA nomor 135', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 135]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 136, 'teks_soal' => 'Soal FA nomor 136', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'set_opsi' => '2', 'urutan' => 136]);
        // WU (gambar diupload HR)
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 137, 'teks_soal' => 'Soal WU nomor 137', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 137]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 138, 'teks_soal' => 'Soal WU nomor 138', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 138]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 139, 'teks_soal' => 'Soal WU nomor 139', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 139]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 140, 'teks_soal' => 'Soal WU nomor 140', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 140]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 141, 'teks_soal' => 'Soal WU nomor 141', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 141]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 142, 'teks_soal' => 'Soal WU nomor 142', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 142]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 143, 'teks_soal' => 'Soal WU nomor 143', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 143]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 144, 'teks_soal' => 'Soal WU nomor 144', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 144]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 145, 'teks_soal' => 'Soal WU nomor 145', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 145]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 146, 'teks_soal' => 'Soal WU nomor 146', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 146]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 147, 'teks_soal' => 'Soal WU nomor 147', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 147]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 148, 'teks_soal' => 'Soal WU nomor 148', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 148]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 149, 'teks_soal' => 'Soal WU nomor 149', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 149]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 150, 'teks_soal' => 'Soal WU nomor 150', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 150]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 151, 'teks_soal' => 'Soal WU nomor 151', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 151]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 152, 'teks_soal' => 'Soal WU nomor 152', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 152]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 153, 'teks_soal' => 'Soal WU nomor 153', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 153]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 154, 'teks_soal' => 'Soal WU nomor 154', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 154]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 155, 'teks_soal' => 'Soal WU nomor 155', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 155]);
        Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 156, 'teks_soal' => 'Soal WU nomor 156', 'tipe_format' => 'pilihan_gambar', 'kunci_jawaban' => null, 'urutan' => 156]);
        // ME
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 157, 'teks_soal' => 'Kata yang mempunyai huruf permulaan A adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 157]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 158, 'teks_soal' => 'Kata yang mempunyai huruf permulaan B adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 158]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 159, 'teks_soal' => 'Kata yang mempunyai huruf permulaan C adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 159]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 160, 'teks_soal' => 'Kata yang mempunyai huruf permulaan D adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 160]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 161, 'teks_soal' => 'Kata yang mempunyai huruf permulaan E adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 161]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 162, 'teks_soal' => 'Kata yang mempunyai huruf permulaan F adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 162]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 163, 'teks_soal' => 'Kata yang mempunyai huruf permulaan G adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 163]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 164, 'teks_soal' => 'Kata yang mempunyai huruf permulaan H adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 164]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 165, 'teks_soal' => 'Kata yang mempunyai huruf permulaan I adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 165]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 166, 'teks_soal' => 'Kata yang mempunyai huruf permulaan J adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 166]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 167, 'teks_soal' => 'Kata yang mempunyai huruf permulaan K adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 167]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 168, 'teks_soal' => 'Kata yang mempunyai huruf permulaan L adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 168]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 169, 'teks_soal' => 'Kata yang mempunyai huruf permulaan M adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 169]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 170, 'teks_soal' => 'Kata yang mempunyai huruf permulaan N adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 170]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 171, 'teks_soal' => 'Kata yang mempunyai huruf permulaan O adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 171]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 172, 'teks_soal' => 'Kata yang mempunyai huruf permulaan P adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'b', 'urutan' => 172]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 173, 'teks_soal' => 'Kata yang mempunyai huruf permulaan R adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'e', 'urutan' => 173]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 174, 'teks_soal' => 'Kata yang mempunyai huruf permulaan S adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'a', 'urutan' => 174]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 175, 'teks_soal' => 'Kata yang mempunyai huruf permulaan T adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'c', 'urutan' => 175]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $soal = Soal::create(['alat_tes_id' => $alatTes->id, 'nomor' => 176, 'teks_soal' => 'Kata yang mempunyai huruf permulaan U adalah .......', 'tipe_format' => 'pilihan_ganda', 'kunci_jawaban' => 'd', 'urutan' => 176]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'bunga', 'urutan' => 1]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'perkakas', 'urutan' => 2]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'burung', 'urutan' => 3]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'kesenian', 'urutan' => 4]);
        OpsiJawaban::create(['soal_id' => $soal->id, 'teks_opsi' => 'binatang', 'urutan' => 5]);
        $this->command->info('Soal IST selesai');
    }
}