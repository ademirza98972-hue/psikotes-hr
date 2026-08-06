<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\AlatTesSesiTes;
use App\Models\Departemen;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EppsSimulasiBaruSeeder extends Seeder
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

        $departemen = Departemen::first();
        if (!$departemen) {
            $this->command->error('Tabel departemen kosong.');
            return;
        }

        $sesi = SesiTes::updateOrCreate(
            ['nama_sesi' => 'Test Pengerjaan EPPS'],
            [
                'departemen_terkait_id' => $departemen->id,
                'tanggal_mulai' => Carbon::today(),
                'tanggal_selesai' => Carbon::today()->addDays(7),
                'status' => 'Aktif',
                'jumlah_peserta' => 1,
                'jumlah_selesai' => 0,
            ]
        );

        AlatTesSesiTes::updateOrCreate(
            ['sesi_tes_id' => $sesi->id, 'alat_tes_id' => $alatTesEpps->id],
            []
        );

        PesertaSesiTes::updateOrCreate(
            ['user_id' => $user->id, 'sesi_tes_id' => $sesi->id],
            [
                'status_pengerjaan' => 'Belum Mengerjakan',
            ]
        );

        $this->command->info("EppsSimulasiBaruSeeder selesai:");
        $this->command->info("- sesi_tes id={$sesi->id} nama='{$sesi->nama_sesi}'");
        $this->command->info("- alat_tes_sesi_tes: sesi {$sesi->id} <-> EPPS (alat_tes_id={$alatTesEpps->id})");
        $this->command->info("- peserta_sesi_tes: user id={$user->id} email={$user->email} status='Belum Mengerjakan'");
    }
}
