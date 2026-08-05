<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class EppsSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'EPPS')->first();
        if (!$alatTes) {
            $this->command->error('AlatTes EPPS tidak ditemukan. Jalankan EppsSeeder terlebih dahulu.');
            return;
        }

        $dimensiAch = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'ach')
            ->first();
        $dimensiDom = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'dom')
            ->first();

        if (!$dimensiAch || !$dimensiDom) {
            $this->command->error('Dimensi ach/dom pada EPPS tidak ditemukan.');
            return;
        }

        // Peta target dimensi per soal:
        //   Soal 1-3 : opsi A -> 'ach',  opsi B -> 'dom'
        //   Soal 4-6 : opsi A -> 'dom',  opsi B -> 'ach'
        // Desain ini menghasilkan suara seimbang jika peserta pilih A di semua soal
        // (ach = 3, dom = 3) untuk validasi mekanisme skoring.
        $petaSoal = [
            1 => ['A' => $dimensiAch->id, 'B' => $dimensiDom->id],
            2 => ['A' => $dimensiAch->id, 'B' => $dimensiDom->id],
            3 => ['A' => $dimensiAch->id, 'B' => $dimensiDom->id],
            4 => ['A' => $dimensiDom->id, 'B' => $dimensiAch->id],
            5 => ['A' => $dimensiDom->id, 'B' => $dimensiAch->id],
            6 => ['A' => $dimensiDom->id, 'B' => $dimensiAch->id],
        ];

        foreach ($petaSoal as $nomor => $labelKeDimensi) {
            $soal = Soal::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'nomor' => $nomor],
                [
                    'teks_soal' => "Soal EPPS nomor {$nomor} (lihat lembar soal asli)",
                    'tipe_format' => 'forced_choice',
                    'urutan' => $nomor,
                ]
            );

            foreach (['A', 'B'] as $index => $label) {
                $opsi = OpsiJawaban::updateOrCreate(
                    ['soal_id' => $soal->id, 'teks_opsi' => "Pilihan {$label}"],
                    [
                        'urutan' => $index + 1,
                    ]
                );

                BobotOpsiDimensi::updateOrCreate(
                    ['opsi_jawaban_id' => $opsi->id, 'dimensi_id' => $labelKeDimensi[$label]],
                    [
                        'bobot' => 1,
                        'is_reverse' => false,
                    ]
                );
            }

            $this->command->info("Soal EPPS nomor {$nomor} berhasil diseder.");
        }

        $this->command->info('EppsSoalSeeder selesai: 6 soal forced_choice, 12 opsi, peta ach/dom tersimpan di bobot_opsi_dimensi.');
    }
}