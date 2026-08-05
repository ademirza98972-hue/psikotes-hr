<?php

namespace Database\Seeders;

use App\Models\NormaKonversi;
use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use Illuminate\Database\Seeder;

class CfitNormaSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'CFIT')->first();
        if (!$alatTes) {
            $this->command->error('AlatTes kode CFIT tidak ditemukan. Jalankan CfitSeeder terlebih dahulu.');
            return;
        }

        $dimensiIQ = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'IQ')
            ->first();
        if (!$dimensiIQ) {
            $this->command->error('DimensiAlatTes IQ untuk CFIT tidak ditemukan. Jalankan CfitSeeder terlebih dahulu.');
            return;
        }

        $csvPath = database_path('seeders/data/norma_cfit.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        $created = 0;
        $header = true;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if ($header) {
                $header = false;
                continue;
            }

            if (count($row) < 2) {
                continue;
            }

            $rawScore = trim($row[0]);
            $iq = trim($row[1]);

            if ($rawScore === '' || $iq === '') {
                continue;
            }

            NormaKonversi::updateOrCreate(
                [
                    'alat_tes_id' => $alatTes->id,
                    'dimensi_id' => $dimensiIQ->id,
                    'kelompok_segmen' => 'default',
                    'tahap' => 1,
                    'skor_mentah_min' => (float) $rawScore,
                    'skor_mentah_max' => (float) $rawScore,
                    'skor_hasil' => (float) $iq,
                ],
                [
                    'alat_tes_id' => $alatTes->id,
                    'dimensi_id' => $dimensiIQ->id,
                    'kelompok_segmen' => 'default',
                    'tahap' => 1,
                    'skor_mentah_min' => (float) $rawScore,
                    'skor_mentah_max' => (float) $rawScore,
                    'skor_hasil' => (float) $iq,
                ]
            );
            $created++;
        }
        fclose($handle);

        $this->command->info("CfitNormaSeeder: {$created} norma_konversi CFIT-INT berhasil diseder.");
    }
}
