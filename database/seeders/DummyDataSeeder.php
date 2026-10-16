<?php

namespace Database\Seeders;

use App\Models\DataKaryawan;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\ProfilKandidat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedKaryawan();
            $this->seedKandidat();
            $this->seedStaffAdmin();
        });
    }

    /**
     * 1. KARYAWAN - 10 akun (users + data_karyawan), sama seperti alur tambah karyawan oleh HR
     *  - Login pakai id_karyawan (format tahun+urut), password "password"
     *  - Posisi harus sesuai dengan departemen-nya (FK valid)
     */
    private function seedKaryawan(): void
    {
        $peranKaryawan = Peran::where('nama_peran', 'Karyawan')->first();

        if (! $peranKaryawan) {
            $this->command->error('Peran "Karyawan" tidak ditemukan.');
            return;
        }

        $namaKaryawan = [
            'Budi Santoso',
            'Siti Rahayu',
            'Andi Wijaya',
            'Dewi Lestari',
            'Rizky Pratama',
            'Fitriani Ananda',
            'Hendra Kusuma',
            'Maya Sari',
            'Bagas Pramudya',
            'Intan Permata',
        ];

        $prefixNik = '3201'; // Kode area Jawa Barat (dummy awalan tetap)
        $created = 0;

        foreach ($namaKaryawan as $index => $nama) {
            $idKaryawan = date('Y').str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);

            if (User::where('id_karyawan', $idKaryawan)->exists()) {
                continue;
            }

            $nomorUrut = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            // NIK 16 digit: 3201 + DDMMYY(tahun pakai 1990-1999) + 4 digit urut
            $tahunLahir = substr((string) (1990 + $index), -2);
            $bulan = str_pad((string) (1 + ($index % 12)), 2, '0', STR_PAD_LEFT);
            $tanggal = str_pad((string) (1 + ($index % 28)), 2, '0', STR_PAD_LEFT);
            $nik = $prefixNik.$tanggal.$bulan.$tahunLahir.$nomorUrut;

            // Posisi random; departemen ikut departemen posisi tersebut
            $posisi = Posisi::whereNotNull('departemen_id')->inRandomOrder()->first();

            $user = User::create([
                'name' => $nama,
                'email' => 'karyawan'.($index + 1).'@test.com',
                'id_karyawan' => $idKaryawan,
                'password' => Hash::make('password'),
                'tipe_akun' => 'karyawan',
                'peran_id' => $peranKaryawan->id,
                'status' => 'aktif',
            ]);

            DataKaryawan::create([
                'user_id' => $user->id,
                'nik_karyawan' => $nik,
                'jenis_kelamin' => $index % 2 ? 'P' : 'L',
                'tanggal_lahir' => (1990 + $index)."-{$bulan}-{$tanggal}",
                'departemen_id' => $posisi->departemen_id,
                'posisi_id' => $posisi->id,
            ]);

            $created++;
        }

        $this->command->info("[1] Karyawan: {$created} user + data_karyawan dibuat.");
    }

    /**
     * 2. DATA KANDIDAT (users + profil_kandidat) - 8 akun
     *  - 3 aktif, 3 menunggu_verifikasi, 2 ditolak
     *  - posisi_dilamar = nama posisi (string)
     */
    private function seedKandidat(): void
    {
        $peranKandidat = Peran::where('nama_peran', 'Kandidat')->first();

        if (! $peranKandidat) {
            $this->command->error('Peran "Kandidat" tidak ditemukan.');
            return;
        }

        $namaKandidat = [
            'Ahmad Fauzan',
            'Nur Halimah',
            'Yusuf Ramadhan',
            'Anisa Putri',
            'Dimas Prayoga',
            'Salsabila Rahma',
            'Fajar Nugroho',
            'Kirana Mahesa',
        ];

        // Pendidikan bervariasi
        $listPendidikan = [
            'SMA/SMK',
            'D3',
            'S1',
            'S2',
            'SMA/SMK',
            'D3',
            'S1',
            'S2',
        ];

        $distribusiStatus = [
            'aktif',
            'aktif',
            'aktif',
            'menunggu_verifikasi',
            'menunggu_verifikasi',
            'menunggu_verifikasi',
            'ditolak',
            'ditolak',
        ];

        $created = 0;
        foreach ($namaKandidat as $index => $nama) {
            $email = 'kandidat'.($index + 1).'@test.com';

            if (User::where('email', $email)->exists()) {
                continue;
            }

            // NIK KTP 16 digit dummy unik
            $nik = '3175' . str_pad((string) (1995 + $index), 2, '0', STR_PAD_LEFT)
                . str_pad((string) (1 + ($index % 12)), 2, '0', STR_PAD_LEFT)
                . str_pad((string) (1 + ($index % 28)), 2, '0', STR_PAD_LEFT)
                . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

            $posisi = Posisi::inRandomOrder()->first();

            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make('password'),
                'tipe_akun' => 'kandidat',
                'peran_id' => $peranKandidat->id,
                'status' => $distribusiStatus[$index],
            ]);

            ProfilKandidat::create([
                'user_id' => $user->id,
                'posisi_id' => $posisi->id,
                'pendidikan_terakhir' => $listPendidikan[$index],
                'nik_kandidat' => $nik,
            ]);

            $created++;
        }

        $this->command->info("[2] Kandidat: {$created} user + profil_kandidat (3 aktif, 3 menunggu_verifikasi, 2 ditolak).");
    }

    /**
     * 3. AKUN STAFF/ADMIN tambahan (tipe_akun = custom)
     *  - 1 Admin HR, 1 HR Viewer, 1 Admin HR lagi
     *  - TIDAK membuat akun Super Admin baru
     */
    private function seedStaffAdmin(): void
    {
        $peranAdminHr = Peran::where('nama_peran', 'Admin HR')->first();
        $peranHrViewer = Peran::where('nama_peran', 'HR Viewer')->first();

        if (! $peranAdminHr || ! $peranHrViewer) {
            $this->command->error('Peran Admin HR / HR Viewer tidak ditemukan.');
            return;
        }

        $akun = [
            [
                'name' => 'Rina Astuti',
                'email' => 'adminhr1@test.com',
                'peran_id' => $peranAdminHr->id,
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'hrviewer1@test.com',
                'peran_id' => $peranHrViewer->id,
            ],
            [
                'name' => 'Lina Mardiana',
                'email' => 'adminhr2@test.com',
                'peran_id' => $peranAdminHr->id,
            ],
        ];

        $created = 0;
        foreach ($akun as $row) {
            if (User::where('email', $row['email'])->exists()) {
                continue;
            }

            User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make('password'),
                'tipe_akun' => 'custom',
                'peran_id' => $row['peran_id'],
                'status' => 'aktif',
            ]);

            $created++;
        }

        $this->command->info("[3] Staff/Admin: {$created} user (2 Admin HR, 1 HR Viewer).");
    }
}
