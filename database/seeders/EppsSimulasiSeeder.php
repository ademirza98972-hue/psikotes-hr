<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\AlatTesSesiTes;
use App\Models\Departemen;
use App\Models\JawabanPeserta;
use App\Models\OpsiJawaban;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\Soal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EppsSimulasiSeeder extends Seeder
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
            $this->command->error('User kandidat1@test.com (id=7) tidak ditemukan.');
            return;
        }

        $departemen = Departemen::first();
        if (!$departemen) {
            $this->command->error('Tabel departemen kosong. Seed departemen terlebih dahulu.');
            return;
        }

        $sesi = SesiTes::create([
            'nama_sesi' => 'Pilot Validasi EPPS',
            'departemen_terkait_id' => $departemen->id,
            'tanggal_mulai' => Carbon::today(),
            'tanggal_selesai' => Carbon::today(),
            'status' => 'Aktif',
            'jumlah_peserta' => 1,
            'jumlah_selesai' => 1,
        ]);

        AlatTesSesiTes::create([
            'sesi_tes_id' => $sesi->id,
            'alat_tes_id' => $alatTesEpps->id,
        ]);

        PesertaSesiTes::create([
            'user_id' => $user->id,
            'sesi_tes_id' => $sesi->id,
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => Carbon::today(),
        ]);

        $nomorSoal = [1, 2, 3, 4, 5, 6];

        foreach ($nomorSoal as $nomor) {
            $soal = Soal::where('alat_tes_id', $alatTesEpps->id)
                ->where('nomor', $nomor)
                ->first();

            if (!$soal) {
                $this->command->error("Soal EPPS nomor {$nomor} tidak ditemukan. Jalankan EppsSoalSeeder terlebih dahulu.");
                continue;
            }

            $opsi = OpsiJawaban::where('soal_id', $soal->id)
                ->where('teks_opsi', 'Pilihan A')
                ->first();

            if (!$opsi) {
                $this->command->error("Opsi 'Pilihan A' untuk soal nomor {$nomor} tidak ditemukan.");
                continue;
            }

            JawabanPeserta::create([
                'user_id' => $user->id,
                'sesi_tes_id' => $sesi->id,
                'soal_id' => $soal->id,
                'opsi_dipilih_id' => $opsi->id,
                'jawaban_teks' => 'A',
                'nilai_input' => null,
                'waktu_jawab' => now(),
            ]);
        }

        $this->command->info("EppsSimulasiSeeder selesai:");
        $this->command->info("- sesi_tes id={$sesi->id} nama='{$sesi->nama_sesi}' departemen_id={$departemen->id}");
        $this->command->info("- alat_tes_sesi_tes: sesi {$sesi->id} <-> EPPS (alat_tes_id={$alatTesEpps->id})");
        $this->command->info("- peserta_sesi_tes: user id={$user->id} email={$user->email}");
        $this->command->info("- jawaban_peserta: 6 baris (semua pilih A; tally ach=3, dom=3 sesuai peta EppsSoalSeeder)");
    }
}