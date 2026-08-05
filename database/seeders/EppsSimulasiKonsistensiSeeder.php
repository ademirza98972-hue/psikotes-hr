<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\JawabanPeserta;
use App\Models\OpsiJawaban;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Database\Seeder;

class EppsSimulasiKonsistensiSeeder extends Seeder
{
    public function run(): void
    {
        $alatTesEpps = AlatTes::where('kode', 'EPPS')->first();
        if (!$alatTesEpps) {
            $this->command->error('AlatTes EPPS tidak ditemukan. Jalankan EppsSeeder terlebih dahulu.');
            return;
        }

        $user = User::where('email', 'kandidat1@test.com')->first();
        if (!$user) {
            $this->command->error('User kandidat1@test.com tidak ditemukan.');
            return;
        }

        $sesi = SesiTes::where('nama_sesi', 'Pilot Validasi EPPS')->first();
        if (!$sesi) {
            $this->command->error('Sesi "Pilot Validasi EPPS" tidak ditemukan. Jalankan EppsSimulasiSeeder terlebih dahulu.');
            return;
        }

        $pesertaSesi = PesertaSesiTes::where('user_id', $user->id)
            ->where('sesi_tes_id', $sesi->id)
            ->first();
        if (!$pesertaSesi) {
            $this->command->error("PesertaSesiTes untuk user kandidat1@test.com pada sesi \"Pilot Validasi EPPS\" (id={$sesi->id}) tidak ditemukan. Jalankan EppsSimulasiSeeder terlebih dahulu.");
            return;
        }

        for ($nomorBaru = 7; $nomorBaru <= 12; $nomorBaru++) {
            $soal = Soal::where('alat_tes_id', $alatTesEpps->id)
                ->where('nomor', $nomorBaru)
                ->first();

            if (!$soal) {
                $this->command->error("Soal EPPS nomor {$nomorBaru} tidak ditemukan. Jalankan EppsKonsistensiSoalSeeder terlebih dahulu.");
                continue;
            }

            $opsi = OpsiJawaban::where('soal_id', $soal->id)
                ->where('teks_opsi', 'Pilihan A')
                ->first();

            if (!$opsi) {
                $this->command->error("Opsi 'Pilihan A' untuk soal nomor {$nomorBaru} tidak ditemukan.");
                continue;
            }

            JawabanPeserta::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sesi_tes_id' => $pesertaSesi->sesi_tes_id,
                    'soal_id' => $soal->id,
                ],
                [
                    'opsi_dipilih_id' => $opsi->id,
                    'jawaban_teks' => 'A',
                    'nilai_input' => null,
                    'waktu_jawab' => now(),
                ]
            );

            $this->command->info("Jawaban soal EPPS nomor {$nomorBaru} (konsistensi): Pilihan A.");
        }

        $this->command->info('EppsSimulasiKonsistensiSeeder selesai: 6 jawaban (semua pilih A) untuk soal pengulangan 7-12, agar jawaban konsisten dengan soal 1-6.');
    }
}