<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use Illuminate\Database\Seeder;

class KraepelinSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::updateOrCreate(
            ['kode' => 'KRP'],
            [
                'nama' => 'Kraepelin',
                'kategori' => null,
                'deskripsi' => 'Tes penjumlahan pasangan angka berurutan dalam kolom (50 kolom). 4 faktor otomatis: Kecepatan, Ketelitian, Ketahanan, Keajegan.',
                'format_dasar' => 'Grid',
                'durasi_total_menit' => null,
                'batas_waktu_per_soal_aktif' => false,
                'batas_waktu_per_soal_detik' => null,
                'is_sensitif' => false,
                'jumlah_soal' => null,
                'is_aktif' => true,
            ]
        );

        $dimensi = [
            ['kode' => 'KECEPATAN', 'nama' => 'Kecepatan',  'urutan' => 0],
            ['kode' => 'KETELITIAN','nama' => 'Ketelitian', 'urutan' => 1],
            ['kode' => 'KETAHANAN', 'nama' => 'Ketahanan',  'urutan' => 2],
            ['kode' => 'KEAJEGAN',  'nama' => 'Keajegan',   'urutan' => 3],
        ];

        foreach ($dimensi as $d) {
            DimensiAlatTes::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => $d['kode']],
                [
                    'nama_dimensi'    => $d['nama'],
                    'deskripsi_aspek' => null,
                    'tipe_kategori'   => 'psikogram',
                    'arah_skor'       => 'tinggi_baik',
                    'urutan'          => $d['urutan'],
                ]
            );
        }

        $this->command->info('Kraepelin alat_tes & 4 dimensi (KECEPATAN, KETELITIAN, KETAHANAN, KEAJEGAN) berhasil diseder.');
    }
}