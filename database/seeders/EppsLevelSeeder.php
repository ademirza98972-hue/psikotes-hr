<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\LevelDimensi;
use Illuminate\Database\Seeder;

class EppsLevelSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'EPPS')->first();
        if (! $alatTes) {
            $this->command->error('AlatTes EPPS belum ada. Jalankan EppsSeeder terlebih dahulu.');
            return;
        }

        $dimensiList = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->orderBy('urutan')
            ->get();

        if ($dimensiList->isEmpty()) {
            $this->command->error('Dimensi EPPS belum ada. Jalankan EppsSeeder terlebih dahulu.');
            return;
        }

        // Patokan EPPS.xls (kolom Kategori): persentil ≤3 SR, ≤16 R, ≤84 Sedang, ≤96 Tinggi, sisanya ST.
        $levels = [
            ['label' => 'Sangat Rendah', 'skor_min' => 0,  'skor_max' => 3,   'urutan' => 1],
            ['label' => 'Rendah',        'skor_min' => 4,  'skor_max' => 16,  'urutan' => 2],
            ['label' => 'Sedang',        'skor_min' => 17, 'skor_max' => 84,  'urutan' => 3],
            ['label' => 'Tinggi',        'skor_min' => 85, 'skor_max' => 96,  'urutan' => 4],
            ['label' => 'Sangat Tinggi', 'skor_min' => 97, 'skor_max' => 100, 'urutan' => 5],
        ];

        foreach ($dimensiList as $dimensi) {
            foreach ($levels as $level) {
                LevelDimensi::updateOrCreate(
                    [
                        'alat_tes_id' => $alatTes->id,
                        'dimensi_id'  => $dimensi->id,
                        'label'       => $level['label'],
                    ],
                    [
                        'skor_min' => $level['skor_min'],
                        'skor_max' => $level['skor_max'],
                        'urutan'   => $level['urutan'],
                    ]
                );
            }
        }

        $this->command->info(sprintf(
            'EPPS level_dimensi berhasil diseder. %d dimensi × 5 level = %d baris.',
            $dimensiList->count(),
            $dimensiList->count() * 5
        ));
    }
}
