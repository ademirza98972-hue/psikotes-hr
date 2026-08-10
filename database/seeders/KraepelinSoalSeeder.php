<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\Soal;
use Illuminate\Database\Seeder;

class KraepelinSoalSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::where('kode', 'KRP')->firstOrFail();

        // Hapus soal lama kalau ada
        Soal::where('alat_tes_id', $alatTes->id)->delete();

        // Generate 50 kolom, masing-masing 27 angka random (0-9)
        for ($kolom = 1; $kolom <= 50; $kolom++) {
            $angka = [];
            for ($baris = 0; $baris < 27; $baris++) {
                $angka[] = rand(0, 9);
            }

            Soal::create([
                'alat_tes_id'  => $alatTes->id,
                'nomor'        => $kolom,
                'teks_soal'    => implode(',', $angka),
                'tipe_format'  => 'grid',
                'urutan'       => $kolom,
            ]);
        }

        $this->command->info('50 kolom soal Kraepelin berhasil dibuat.');
    }
}
