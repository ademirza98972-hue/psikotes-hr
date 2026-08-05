<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\DimensiTurunanKomponen;
use Illuminate\Database\Seeder;

class PapikostikSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::updateOrCreate(
            ['kode' => 'PAP'],
            [
                'nama' => 'Papikostik (PAPI Kostick)',
                'kategori' => null,
                'deskripsi' => 'Inventori kepribadian berbasis 90 pasang pernyataan pilihan A/B, 20 skala dasar yang di-rollup ke 7 kategori besar.',
                'format_dasar' => 'Forced Choice',
                'durasi_total_menit' => 30,
                'batas_waktu_per_soal_aktif' => false,
                'batas_waktu_per_soal_detik' => null,
                'is_sensitif' => false,
                'jumlah_soal' => 90,
                'is_aktif' => true,
            ]
        );

        $skalaDasar = [
            ['kode' => 'TGS_PRIBADI', 'nama' => 'Penyelesaian tugas secara pribadi',           'urutan' => 0],
            ['kode' => 'KERJA_KERAS', 'nama' => 'Peranan sebagai pekerja keras',               'urutan' => 1],
            ['kode' => 'BERPRESTASI', 'nama' => 'Hasrat untuk berprestasi',                    'urutan' => 2],
            ['kode' => 'PIMPINAN',    'nama' => 'Peran sebagai Pimpinan',                      'urutan' => 3],
            ['kode' => 'KENDALI_ORG', 'nama' => 'Mengendalikan orang lain',                   'urutan' => 4],
            ['kode' => 'PUTUSAN',     'nama' => 'Mudah dalam membuat keputusan',               'urutan' => 5],
            ['kode' => 'TAAT_ATURAN', 'nama' => 'Kebutuhan taat pada aturan & pengarahan',    'urutan' => 6],
            ['kode' => 'DUKUNGAN_ATASAN', 'nama' => 'Dukungan dari atasan',                  'urutan' => 7],
            ['kode' => 'AGRESI',      'nama' => 'Aggresi',                                     'urutan' => 8],
            ['kode' => 'KENDALI_EMOSI', 'nama' => 'Pengendalian emosi',                      'urutan' => 9],
            ['kode' => 'HASRAT_BERUBAH', 'nama' => 'Hasrat untuk berubah',                   'urutan' => 10],
            ['kode' => 'SIBUK',       'nama' => 'Type selalu sibuk',                         'urutan' => 11],
            ['kode' => 'SEMANGAT',    'nama' => 'Type orang yang semangat',                  'urutan' => 12],
            ['kode' => 'PERHATIAN',   'nama' => 'Untuk mendapat perhatian',                  'urutan' => 13],
            ['kode' => 'PERGAULAN_LUAS', 'nama' => 'Pergaulan luas',                        'urutan' => 14],
            ['kode' => 'BETAH_KELOMPOK', 'nama' => 'Kebutuhan betah terhadap kelompok',     'urutan' => 15],
            ['kode' => 'DEKAT_SAYANG', 'nama' => 'Kebutuhan untuk mendekati & menyayangi', 'urutan' => 16],
            ['kode' => 'TYPE_PENGATUR', 'nama' => 'Type pengatur',                           'urutan' => 17],
            ['kode' => 'DETAIL_KERJA', 'nama' => 'Suka pekerjaan yang terperinci',          'urutan' => 18],
            ['kode' => 'TEORITIS',    'nama' => 'Type Teoritical',                           'urutan' => 19],
        ];

        $kodeKeId = [];
        foreach ($skalaDasar as $d) {
            $dim = DimensiAlatTes::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => $d['kode']],
                [
                    'nama_dimensi'    => $d['nama'],
                    'deskripsi_aspek' => null,
                    'tipe_kategori'   => 'psikogram',
                    'arah_skor'       => 'tinggi_baik',
                    'urutan'          => $d['urutan'],
                ]
            );
            $kodeKeId[$d['kode']] = $dim->id;
        }

        $kategoriBesar = [
            ['kode' => 'ARAH_KERJA', 'nama' => 'Arah Kerja', 'urutan' => 0, 'komponen' => ['TGS_PRIBADI', 'KERJA_KERAS', 'BERPRESTASI']],
            ['kode' => 'KEPEMIMPINAN', 'nama' => 'Kepemimpinan', 'urutan' => 1, 'komponen' => ['PIMPINAN', 'KENDALI_ORG', 'PUTUSAN']],
            ['kode' => 'KETAATAN',   'nama' => 'Ketaatan',   'urutan' => 2, 'komponen' => ['TAAT_ATURAN', 'DUKUNGAN_ATASAN']],
            ['kode' => 'SIFAT',      'nama' => 'Sifat',      'urutan' => 3, 'komponen' => ['AGRESI', 'KENDALI_EMOSI', 'HASRAT_BERUBAH']],
            ['kode' => 'AKTIVITAS',  'nama' => 'Aktivitas',  'urutan' => 4, 'komponen' => ['SIBUK', 'SEMANGAT']],
            ['kode' => 'PERGAULAN',  'nama' => 'Pergaulan',  'urutan' => 5, 'komponen' => ['PERHATIAN', 'PERGAULAN_LUAS', 'BETAH_KELOMPOK', 'DEKAT_SAYANG']],
            ['kode' => 'GAYA_KERJA', 'nama' => 'Gaya Kerja', 'urutan' => 6, 'komponen' => ['TYPE_PENGATUR', 'DETAIL_KERJA', 'TEORITIS']],
        ];

        foreach ($kategoriBesar as $kat) {
            $turunan = DimensiAlatTes::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => $kat['kode']],
                [
                    'nama_dimensi'    => $kat['nama'],
                    'deskripsi_aspek' => null,
                    'tipe_kategori'   => 'psikogram',
                    'arah_skor'       => 'tinggi_baik',
                    'urutan'          => $kat['urutan'],
                ]
            );

            foreach ($kat['komponen'] as $kodeKomponen) {
                if (!isset($kodeKeId[$kodeKomponen])) {
                    continue;
                }
                DimensiTurunanKomponen::updateOrCreate(
                    [
                        'dimensi_turunan_id'  => $turunan->id,
                        'dimensi_komponen_id' => $kodeKeId[$kodeKomponen],
                    ],
                    ['bobot' => 1]
                );
            }
        }

        $this->command->info('Papikostik alat_tes, 20 skala dasar & 7 rollup kategori berhasil diseder.');
    }
}
