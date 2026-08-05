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

class PapikostikSimulasiSeeder extends Seeder
{
    public function run(): void
    {
        $alatTesPap = AlatTes::where('kode', 'PAP')->first();
        if (!$alatTesPap) {
            $this->command->error('AlatTes PAP tidak ditemukan. Jalankan PapikostikSeeder terlebih dahulu.');
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
            ['nama_sesi' => 'Pilot Validasi Papikostik'],
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
            ['sesi_tes_id' => $sesi->id, 'alat_tes_id' => $alatTesPap->id],
            []
        );

        PesertaSesiTes::updateOrCreate(
            ['user_id' => $user->id, 'sesi_tes_id' => $sesi->id],
            [
                'status_pengerjaan' => 'Selesai',
                'tanggal_pengerjaan' => Carbon::today(),
            ]
        );

        foreach ([1, 2, 3] as $nomor) {
            $soal = Soal::where('alat_tes_id', $alatTesPap->id)
                ->where('nomor', $nomor)
                ->first();

            if (!$soal) {
                $this->command->error("Soal PAP nomor {$nomor} tidak ditemukan. Jalankan PapikostikSoalSeeder terlebih dahulu.");
                continue;
            }

            $opsi = OpsiJawaban::where('soal_id', $soal->id)
                ->where('teks_opsi', 'Pilihan A')
                ->first();

            if (!$opsi) {
                $this->command->error("Opsi 'Pilihan A' untuk soal nomor {$nomor} tidak ditemukan.");
                continue;
            }

            JawabanPeserta::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sesi_tes_id' => $sesi->id,
                    'soal_id' => $soal->id,
                ],
                [
                    'opsi_dipilih_id' => $opsi->id,
                    'jawaban_teks' => 'A',
                    'nilai_input' => null,
                    'waktu_jawab' => now(),
                ]
            );
        }

        $sesiPap = SesiTes::where('nama_sesi', 'Pilot Validasi Papikostik')
            ->where('status', 'Aktif')
            ->first();

        $this->command->info("PapikostikSimulasiSeeder selesai:");
        $this->command->info("- sesi_tes id={$sesiPap->id} nama='{$sesiPap->nama_sesi}' (lookup eksplisit by nama_sesi, bukan first())");
        $this->command->info("- alat_tes_sesi_tes: sesi {$sesiPap->id} <-> PAP (alat_tes_id={$alatTesPap->id})");
        $this->command->info("- peserta_sesi_tes: user id={$user->id} email={$user->email} status=Selesai");
        $this->command->info("- jawaban_peserta: 3 baris (semua 'Pilihan A', upsert via user_id+sesi_tes_id+soal_id)");
    }
}
