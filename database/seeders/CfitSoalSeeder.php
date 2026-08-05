<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\OpsiJawaban;
use App\Models\BobotOpsiDimensi;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class CfitSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'CFIT')->first();
        if (!$alatTes) {
            $this->command->error('AlatTes CFIT tidak ditemukan. Jalankan CfitSeeder terlebih dahulu.');
            return;
        }

        $dimensiIQ = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'IQ')
            ->first();

        if (!$dimensiIQ) {
            $this->command->error('Dimensi IQ pada CFIT tidak ditemukan.');
            return;
        }

        $answers = [
            1 => 'b',
            2 => 'c',
            3 => 'b',
            4 => 'd',
            5 => 'e',
            6 => 'b',
            7 => 'd',
            8 => 'b',
            9 => 'f',
            10 => 'c',
            11 => 'b',
            12 => 'b',
            13 => 'e',
        ];

        $opsiLabels = ['a', 'b', 'c', 'd', 'e', 'f'];

        foreach ($answers as $nomor => $opsiBenar) {
            $soal = Soal::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'nomor' => $nomor],
                [
                    'teks_soal' => "Soal CFIT TES 1 nomor {$nomor} (materi visual, lihat lembar soal cetak)",
                    'tipe_format' => 'pilihan_ganda',
                    'urutan' => $nomor,
                ]
            );

            foreach ($opsiLabels as $index => $label) {
                $isCorrect = $label === $opsiBenar;

                $opsi = OpsiJawaban::updateOrCreate(
                    ['soal_id' => $soal->id, 'teks_opsi' => "Pilihan {$label}"],
                    [
                        'urutan' => $index + 1,
                    ]
                );

                if ($isCorrect) {
                    BobotOpsiDimensi::updateOrCreate(
                        ['opsi_jawaban_id' => $opsi->id, 'dimensi_id' => $dimensiIQ->id],
                        [
                            'bobot' => 1,
                            'is_reverse' => false,
                        ]
                    );
                }
            }

            $this->command->info("Soal CFIT nomor {$nomor} berhasil diseder.");
        }

        $this->command->info('CfitSoalSeeder selesai: 13 soal, 78 opsi, kunci jawaban tersimpan di bobot_opsi_dimensi.');
    }
}
