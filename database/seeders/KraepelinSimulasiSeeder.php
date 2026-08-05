<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\AlatTesSesiTes;
use App\Models\Departemen;
use App\Models\HasilKolomGrid;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KraepelinSimulasiSeeder extends Seeder
{
    public function run(): void
    {
        $alatTesKrp = AlatTes::where('kode', 'KRP')->first();
        if (!$alatTesKrp) {
            $this->command->error('AlatTes KRP tidak ditemukan. Jalankan KraepelinSeeder terlebih dahulu.');
            return;
        }

        $user = User::where('email', 'kandidat1@test.com')->first();
        if (!$user) {
            $this->command->error('User kandidat1@test.com tidak ditemukan.');
            return;
        }

        $departemen = Departemen::first();
        if (!$departemen) {
            $this->command->error('Tabel departemen kosong. Seed departemen terlebih dahulu.');
            return;
        }

        $sesi = SesiTes::updateOrCreate(
            ['nama_sesi' => 'Pilot Validasi Kraepelin'],
            [
                'departemen_terkait_id' => $departemen->id,
                'tanggal_mulai' => Carbon::today(),
                'tanggal_selesai' => Carbon::today(),
                'status' => 'Aktif',
                'jumlah_peserta' => 1,
                'jumlah_selesai' => 1,
            ]
        );

        AlatTesSesiTes::updateOrCreate(
            ['sesi_tes_id' => $sesi->id, 'alat_tes_id' => $alatTesKrp->id],
            []
        );

        PesertaSesiTes::updateOrCreate(
            ['user_id' => $user->id, 'sesi_tes_id' => $sesi->id],
            [
                'status_pengerjaan' => 'Selesai',
                'tanggal_pengerjaan' => Carbon::today(),
            ]
        );

        $dataKolom = [
            ['kolom_ke' => 1, 'jumlah_benar' => 8,  'jumlah_salah' => 1, 'jumlah_kelewat' => 1],
            ['kolom_ke' => 2, 'jumlah_benar' => 9,  'jumlah_salah' => 0, 'jumlah_kelewat' => 1],
            ['kolom_ke' => 3, 'jumlah_benar' => 10, 'jumlah_salah' => 0, 'jumlah_kelewat' => 0],
            ['kolom_ke' => 4, 'jumlah_benar' => 11, 'jumlah_salah' => 1, 'jumlah_kelewat' => 0],
            ['kolom_ke' => 5, 'jumlah_benar' => 12, 'jumlah_salah' => 0, 'jumlah_kelewat' => 0],
        ];

        foreach ($dataKolom as $k) {
            HasilKolomGrid::updateOrCreate(
                [
                    'user_id'      => $user->id,
                    'sesi_tes_id'  => $sesi->id,
                    'alat_tes_id'  => $alatTesKrp->id,
                    'kolom_ke'     => $k['kolom_ke'],
                ],
                [
                    'jumlah_benar'   => $k['jumlah_benar'],
                    'jumlah_salah'   => $k['jumlah_salah'],
                    'jumlah_kelewat' => $k['jumlah_kelewat'],
                    'waktu_pakai_detik' => null,
                ]
            );
        }

        $this->command->info("KraepelinSimulasiSeeder selesai:");
        $this->command->info("- sesi_tes id={$sesi->id} nama='{$sesi->nama_sesi}'");
        $this->command->info("- alat_tes_sesi_tes: sesi {$sesi->id} <-> KRP (alat_tes_id={$alatTesKrp->id})");
        $this->command->info("- peserta_sesi_tes: user id={$user->id} email={$user->email} status=Selesai");
        $this->command->info('- hasil_kolom_grid: 5 baris (kolom 1-5, langsung agregat — tanpa grid_input_peserta)');
    }
}