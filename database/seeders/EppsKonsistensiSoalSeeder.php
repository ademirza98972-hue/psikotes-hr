<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class EppsKonsistensiSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'EPPS')->first();
        if (!$alatTes) {
            $this->command->error('AlatTes EPPS tidak ditemukan. Jalankan EppsSeeder terlebih dahulu.');
            return;
        }

        for ($nomorBaru = 7; $nomorBaru <= 12; $nomorBaru++) {
            $nomorAsli = $nomorBaru - 6;

            $soalAsli = Soal::where('alat_tes_id', $alatTes->id)
                ->where('nomor', $nomorAsli)
                ->first();

            if (!$soalAsli) {
                $this->command->error("Soal EPPS nomor {$nomorAsli} (asal untuk nomor {$nomorBaru}) tidak ditemukan. Jalankan EppsSoalSeeder terlebih dahulu.");
                return;
            }

            $soal = Soal::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'nomor' => $nomorBaru],
                [
                    'teks_soal' => "Soal EPPS nomor {$nomorBaru} (pengulangan dari soal nomor {$nomorAsli})",
                    'tipe_format' => 'forced_choice',
                    'urutan' => $nomorBaru,
                    'duplikat_dari_soal_id' => $soalAsli->id,
                ]
            );

            foreach (['A', 'B'] as $index => $label) {
                OpsiJawaban::updateOrCreate(
                    ['soal_id' => $soal->id, 'teks_opsi' => "Pilihan {$label}"],
                    [
                        'urutan' => $index + 1,
                    ]
                );
            }

            $this->command->info("Soal EPPS nomor {$nomorBaru} (duplikat dari soal nomor {$nomorAsli}) berhasil diseder.");
        }

        $this->command->info('EppsKonsistensiSoalSeeder selesai: 6 soal pengulangan (nomor 7-12), tanpa baris bobot_opsi_dimensi.');
    }
}