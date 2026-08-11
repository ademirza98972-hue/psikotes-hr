<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use Illuminate\Database\Seeder;

class CfitSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::updateOrCreate(
            ['kode' => 'CFIT'],
            [
                'nama' => 'CFIT (Culture Fair Intelligence Test) - Skala 3A & 3B',
                'kategori' => null,
                'deskripsi' => null,
                'format_dasar' => 'Pilihan Ganda',
                'durasi_total_menit' => null,
                'batas_waktu_per_soal_aktif' => false,
                'batas_waktu_per_soal_detik' => null,
                'is_sensitif' => false,
                'jumlah_soal' => 50,
                'pola_skoring' => 'kognitif',
                'is_aktif' => true,
            ]
        );

        DimensiAlatTes::updateOrCreate(
            ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => 'IQ'],
            [
                'nama_dimensi' => 'Skor CFIT',
                'deskripsi_aspek' => null,
                'tipe_kategori' => 'psikogram',
                'arah_skor' => 'tinggi_baik',
                'urutan' => 0,
            ]
        );

        $this->command->info('CFIT alat_tes & dimensi_alat_tes berhasil diseder.');
    }
}
