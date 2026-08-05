<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\NormaKonversi;
use Illuminate\Database\Seeder;

class EppsNormaSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'EPPS')->first();

        if (! $alatTes) {
            $this->command->error('AlatTes EPPS belum ada. Jalankan EppsSeeder terlebih dahulu.');
            return;
        }

        $dimensiMap = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->pluck('id', 'kode_dimensi');

        $kolomDimensi = [
            'ach', 'def', 'ord', 'exh', 'aut', 'aff', 'int', 'suc',
            'dom', 'aba', 'nur', 'chg', 'end', 'het', 'agg', 'con',
        ];

        $missing = array_diff($kolomDimensi, $dimensiMap->keys()->all());
        if (! empty($missing)) {
            $this->command->error('Dimensi EPPS belum lengkap: ' . implode(', ', $missing));
            return;
        }

        $csvPath = database_path('seeders/data/norma_epps_full.csv');

        if (! is_readable($csvPath)) {
            $this->command->error("File CSV tidak bisa dibaca: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);

        $rowsToInsert = [];
        $inserted = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $kodeKategori = trim((string) ($data['kode_kategori'] ?? ''));
            $rawScore = $data['raw_score'] ?? null;

            if ($kodeKategori === '' || $rawScore === null || $rawScore === '') {
                continue;
            }

            foreach ($kolomDimensi as $kode) {
                $value = $data[$kode] ?? null;

                if ($value === null || $value === '') {
                    $skipped++;
                    continue;
                }

                $rowsToInsert[] = [
                    'alat_tes_id' => $alatTes->id,
                    'dimensi_id' => $dimensiMap[$kode],
                    'sumber_dimensi_id' => null,
                    'kelompok_segmen' => $kodeKategori,
                    'tahap' => 1,
                    'skor_mentah_min' => $rawScore,
                    'skor_mentah_max' => $rawScore,
                    'skor_hasil' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $inserted++;

                if (count($rowsToInsert) >= 500) {
                    NormaKonversi::insert($rowsToInsert);
                    $rowsToInsert = [];
                }
            }
        }

        fclose($handle);

        if (! empty($rowsToInsert)) {
            NormaKonversi::insert($rowsToInsert);
        }

        $this->command->info("EPPS norma_konversi: {$inserted} baris diinsert, {$skipped} sel kosong dilewati.");
    }
}