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

        $levels = [
            ['label' => 'Sangat Rendah', 'skor_min' => 0,   'skor_max' => 10,  'urutan' => 1],
            ['label' => 'Rendah',        'skor_min' => 11,  'skor_max' => 30,  'urutan' => 2],
            ['label' => 'Sedang',        'skor_min' => 31,  'skor_max' => 70,  'urutan' => 3],
            ['label' => 'Tinggi',        'skor_min' => 71,  'skor_max' => 90,  'urutan' => 4],
            ['label' => 'Sangat Tinggi', 'skor_min' => 91,  'skor_max' => 100, 'urutan' => 5],
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
