<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\OpsiJawaban;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class PapikostikSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'PAP')->first();
        if (!$alatTes) {
            $this->command->error('AlatTes PAP tidak ditemukan. Jalankan PapikostikSeeder terlebih dahulu.');
            return;
        }

        $dimensiTgs = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'TGS_PRIBADI')
            ->first();
        $dimensiKerja = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'KERJA_KERAS')
            ->first();
        $dimensiBerprestasi = DimensiAlatTes::where('alat_tes_id', $alatTes->id)
            ->where('kode_dimensi', 'BERPRESTASI')
            ->first();

        if (!$dimensiTgs || !$dimensiKerja || !$dimensiBerprestasi) {
            $this->command->error('Dimensi TGS_PRIBADI / KERJA_KERAS / BERPRESTASI tidak ditemukan.');
            return;
        }

        $petaSoal = [
            1 => $dimensiTgs->id,
            2 => $dimensiKerja->id,
            3 => $dimensiBerprestasi->id,
        ];

        foreach ($petaSoal as $nomor => $dimensiIdUntukOpsiA) {
            $soal = Soal::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'nomor' => $nomor],
                [
                    'teks_soal' => "Soal PAP nomor {$nomor} (lihat lembar soal asli)",
                    'tipe_format' => 'forced_choice',
                    'urutan' => $nomor,
                ]
            );

            $opsiA = OpsiJawaban::updateOrCreate(
                ['soal_id' => $soal->id, 'teks_opsi' => 'Pilihan A'],
                ['urutan' => 1]
            );

            OpsiJawaban::updateOrCreate(
                ['soal_id' => $soal->id, 'teks_opsi' => 'Pilihan B'],
                ['urutan' => 2]
            );

            BobotOpsiDimensi::updateOrCreate(
                ['opsi_jawaban_id' => $opsiA->id, 'dimensi_id' => $dimensiIdUntukOpsiA],
                ['bobot' => 1, 'is_reverse' => false]
            );

            $this->command->info("Soal PAP nomor {$nomor} berhasil diseder.");
        }

        $this->command->info('PapikostikSoalSeeder selesai: 3 soal forced_choice, opsi A beri bobot dimensi, opsi B netral.');
    }
}