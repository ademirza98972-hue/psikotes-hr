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

class CfitSimulasiSeeder extends Seeder
{
    public function run(): void
    {
        $alatTesCfit = AlatTes::where('kode', 'CFIT')->first();
        if (!$alatTesCfit) {
            $this->command->error('AlatTes CFIT tidak ditemukan. Jalankan CfitSeeder terlebih dahulu.');
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
            'nama_sesi' => 'Pilot Validasi CFIT',
            'departemen_terkait_id' => $departemen->id,
            'tanggal_mulai' => Carbon::today(),
            'tanggal_selesai' => Carbon::today(),
            'status' => 'Aktif',
            'jumlah_peserta' => 1,
            'jumlah_selesai' => 1,
        ]);

        AlatTesSesiTes::create([
            'sesi_tes_id' => $sesi->id,
            'alat_tes_id' => $alatTesCfit->id,
        ]);

        PesertaSesiTes::create([
            'user_id' => $user->id,
            'sesi_tes_id' => $sesi->id,
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => Carbon::today(),
        ]);

        $kunci = [
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

        foreach ($kunci as $nomor => $labelOpsi) {
            $soal = Soal::where('alat_tes_id', $alatTesCfit->id)
                ->where('nomor', $nomor)
                ->first();

            if (!$soal) {
                $this->command->error("Soal CFIT nomor {$nomor} tidak ditemukan. Jalankan CfitSoalSeeder terlebih dahulu.");
                continue;
            }

            $opsi = OpsiJawaban::where('soal_id', $soal->id)
                ->where('teks_opsi', "Pilihan {$labelOpsi}")
                ->first();

            if (!$opsi) {
                $this->command->error("Opsi 'Pilihan {$labelOpsi}' untuk soal nomor {$nomor} tidak ditemukan.");
                continue;
            }

            JawabanPeserta::create([
                'user_id' => $user->id,
                'sesi_tes_id' => $sesi->id,
                'soal_id' => $soal->id,
                'opsi_dipilih_id' => $opsi->id,
                'jawaban_teks' => $labelOpsi,
                'nilai_input' => null,
                'waktu_jawab' => now(),
            ]);
        }

        $this->command->info("CfitSimulasiSeeder selesai:");
        $this->command->info("- sesi_tes id={$sesi->id} nama='{$sesi->nama_sesi}' departemen_id={$departemen->id}");
        $this->command->info("- alat_tes_sesi_tes: sesi {$sesi->id} <-> CFIT (alat_tes_id={$alatTesCfit->id})");
        $this->command->info("- peserta_sesi_tes: user id={$user->id} email={$user->email} (Ahmad Fauzan)");
        $this->command->info("- jawaban_peserta: 13 baris (semua benar, skor_mentah harus = 13)");
    }
}