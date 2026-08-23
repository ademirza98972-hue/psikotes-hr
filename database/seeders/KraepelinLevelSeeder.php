<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\LevelDimensi;
use Illuminate\Database\Seeder;

class KraepelinLevelSeeder extends Seeder
{
    /**
     * Norma berdasarkan kisaran skor nyata dari data simulasi Kraepelin (n=117).
     *
     * Panker (Kecepatan)     : rata-rata ~13.1, range ~0–26   → tinggi_baik
     * Janker (Keajegan)      : rata-rata ~1.1,  range ~0–6    → tinggi_baik (smaller = steadier)
     * Hanker (Ketahanan)     : range ~−5 sampai +5           → tinggi_baik (delta positif = improving)
     * Tianker (Ketelitian)   : range ~0–200+                  → lebih kecil lebih baik
     */
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'KRP')->first();
        if (! $alatTes) {
            $this->command->error('AlatTes KRP belum ada. Jalankan KraepelinSeeder terlebih dahulu.');
            return;
        }

        $dimensiById = DimensiAlatTes::whereIn('kode_dimensi', ['KECEPATAN','KEAJEGAN','KETAHANAN','KETELITIAN'])
            ->get(['id','kode_dimensi'])
            ->keyBy('kode_dimensi');

        if ($dimensiById->count() !== 4) {
            $this->command->error('Dimensi KRP belum lengkap. Jalankan KraepelinSeeder terlebih dahulu.');
            return;
        }

        // ── Level definitions per dimensi ──────────────────────────────────────
        // Each level: ['label' => ..., 'skor_min' => ..., 'skor_max' => ..., 'urutan' => ...]
        //
        // For "tinggi_baik" dims (Panker, Janker, Hanker):
        //   skor_min = lower bound, skor_max = upper bound
        //   Higher scores → higher levels.
        //
        // For Tianker (error count), the *same* structure works because we store
        // the inverted category logic at display time; here we just define the
        // numeric bands. The raw skor_mentah = total errors, so:
        //   skor_min=0, skor_max=60  → Rendah (few errors = good)
        //   skor_min=61, skor_max=100 → Kurang
        //   skor_min=101, skor_max=150 → Cukup
        //   skor_min=151, skor_max=200 → Baik
        //   skor_min=201, skor_max=9999 → Baik Sekali
        // However our sistem uses skor_max as the upper threshold checked by
        // cariLevelId (skor_min <= skor <= skor_max), so the bands must cover
        // the full score range contiguously.

        $levels = [
            ['label' => 'Rendah',         'skor_min' => 0,   'skor_max' => 8,   'urutan' => 1],
            ['label' => 'Kurang',         'skor_min' => 9,   'skor_max' => 14,  'urutan' => 2],
            ['label' => 'Cukup',          'skor_min' => 15,  'skor_max' => 19,  'urutan' => 3],
            ['label' => 'Baik',           'skor_min' => 20,  'skor_max' => 25,  'urutan' => 4],
            ['label' => 'Baik Sekali',    'skor_min' => 26,  'skor_max' => 999, 'urutan' => 5],
        ];

        // ── Panker (KECEPATAN): avg ≈ 13, range 0–26 ──────────────────────────
        $pankerLevels = [
            ['label' => 'Rendah',         'skor_min' => 0,  'skor_max' => 8,  'urutan' => 1],
            ['label' => 'Kurang',         'skor_min' => 9,  'skor_max' => 13, 'urutan' => 2],
            ['label' => 'Cukup',          'skor_min' => 14, 'skor_max' => 18, 'urutan' => 3],
            ['label' => 'Baik',           'skor_min' => 19, 'skor_max' => 23, 'urutan' => 4],
            ['label' => 'Baik Sekali',    'skor_min' => 24, 'skor_max' => 999,'urutan' => 5],
        ];

        // ── Janker (KEAJEGAN): avg ≈ 1.1, range 0–6 ───────────────────────────
        // Smaller deviation = steadier rhythm = better.
        // We assign higher levels to LOWER scores to match arah_skor = tinggi_baik.
        $jankerLevels = [
            ['label' => 'Baik Sekali',    'skor_min' => 0,   'skor_max' => 0.5,  'urutan' => 1],
            ['label' => 'Baik',           'skor_min' => 0.51,'skor_max' => 1.2,  'urutan' => 2],
            ['label' => 'Cukup',          'skor_min' => 1.21,'skor_max' => 2.0,  'urutan' => 3],
            ['label' => 'Kurang',         'skor_min' => 2.01,'skor_max' => 3.5,  'urutan' => 4],
            ['label' => 'Rendah',         'skor_min' => 3.51,'skor_max' => 999,  'urutan' => 5],
        ];

        // ── Hanker (KETAHANAN): range roughly −5 to +5 ────────────────────────
        // Positive delta = improving (good). Negative = declining (bad).
        $hankerLevels = [
            ['label' => 'Rendah',         'skor_min' => -99, 'skor_max' => -2,   'urutan' => 1],
            ['label' => 'Kurang',         'skor_min' => -1.99,'skor_max' => -0.5,'urutan' => 2],
            ['label' => 'Cukup',          'skor_min' => -0.49,'skor_max' => 0.5, 'urutan' => 3],
            ['label' => 'Baik',           'skor_min' => 0.51, 'skor_max' => 2,   'urutan' => 4],
            ['label' => 'Baik Sekali',    'skor_min' => 2.01, 'skor_max' => 999, 'urutan' => 5],
        ];

        // ── Tianker (KETELITIAN): total errors, lower = better ────────────────
        // arah_skor = tinggi_baik, but higher error count gets lower category.
        // We invert by making "Rendah" band the HIGHEST (large errors), and
        // "Baik Sekali" the LOWEST (few errors). The logic in cariLevelId uses
        // skor_min ≤ skor ≤ skor_max, so we flip the bands manually.
        $tiankerLevels = [
            ['label' => 'Baik Sekali',    'skor_min' => 0,   'skor_max' => 50,  'urutan' => 1],
            ['label' => 'Baik',           'skor_min' => 51,  'skor_max' => 100, 'urutan' => 2],
            ['label' => 'Cukup',          'skor_min' => 101, 'skor_max' => 150, 'urutan' => 3],
            ['label' => 'Kurang',         'skor_min' => 151, 'skor_max' => 200, 'urutan' => 4],
            ['label' => 'Rendah',         'skor_min' => 201, 'skor_max' => 9999,'urutan' => 5],
        ];

        $dimMap = [
            'KECEPATAN' => $dimensiById->get('KECEPATAN'),
            'KEAJEGAN'  => $dimensiById->get('KEAJEGAN'),
            'KETAHANAN' => $dimensiById->get('KETAHANAN'),
            'KETELITIAN'=> $dimensiById->get('KETELITIAN'),
        ];

        $mapping = [
            $dimMap['KECEPATAN']->id => $pankerLevels,
            $dimMap['KEAJEGAN']->id  => $jankerLevels,
            $dimMap['KETAHANAN']->id => $hankerLevels,
            $dimMap['KETELITIAN']->id=> $tiankerLevels,
        ];

        foreach ($mapping as $dimensiId => $lvlList) {
            foreach ($lvlList as $level) {
                LevelDimensi::updateOrCreate(
                    [
                        'alat_tes_id' => $alatTes->id,
                        'dimensi_id'  => $dimensiId,
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

        $this->command->info('KraepelinLevelSeeder selesai: 4 dimensi × 5 level = 20 baris inserted.');
    }
}
