<?php

namespace Database\Seeders;

use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\ProfilKandidat;
use App\Models\ProfilKaryawan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedDataKaryawan();
            $this->seedAkunKaryawan();
            $this->seedKandidat();
            $this->seedStaffAdmin();
        });
    }

    /**
     * 1. DATA KARYAWAN (master data NIK) - 10 records
     *  - 5 status 'belum_terpakai', 5 status 'sudah_terpakai'
     *  - Posisi harus sesuai dengan departemen-nya (FK valid)
     */
    private function seedDataKaryawan(): void
    {
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
            $nomorUrut = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            // NIK 16 digit: 3201 + DDMMYY(tahun pakai 1990-1995) + 4 digit urut + 4 digit random
            $tahunLahir = str_pad((string) (1990 + $index), 2, '0', STR_PAD_LEFT);
            $bulan = str_pad((string) (1 + ($index % 12)), 2, '0', STR_PAD_LEFT);
            $tanggal = str_pad((string) (1 + ($index % 28)), 2, '0', STR_PAD_LEFT);
            $acak = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            $nik = $prefixNik.$tanggal.$bulan.$tahunLahir.$nomorUrut.$acak;

            // Pilih departemen random, lalu pilih posisi yang sesuai departemen-nya
            $departemen = Departemen::inRandomOrder()->first();
            $posisi = Posisi::where('departemen_id', $departemen->id)
                ->inRandomOrder()
                ->first();

            $status = $index < 5 ? 'belum_terpakai' : 'sudah_terpakai';

            DataKaryawan::create([
                'nik_karyawan' => $nik,
                'nama_karyawan' => $nama,
                'departemen' => $departemen->nama_departemen,
                'jabatan' => $posisi->nama_posisi,
                'status' => $status,
                'departemen_id' => $departemen->id,
                'posisi_id' => $posisi->id,
            ]);

            $created++;
        }

        $this->command->info("[1] Data Karyawan: {$created} record dibuat (5 belum_terpakai, 5 sudah_terpakai).");
    }

    /**
     * 2. AKUN KARYAWAN (users + profil_karyawan) - 5 akun
     *  - Mengambil 5 data_karyawan berstatus 'sudah_terpakai' yang belum punya akun.
     *  - Nama & NIK harus MATCH PERSIS dengan data_karyawan (agar lolos validasi sistem).
     */
    private function seedAkunKaryawan(): void
    {
        $peranKaryawan = Peran::where('nama_peran', 'Karyawan')->first();

        if (! $peranKaryawan) {
            $this->command->error('Peran "Karyawan" tidak ditemukan.');
            return;
        }

        // Ambil data_karyawan sudah_terpakai yang belum punya profil_karyawan
        $kandidatMaster = DataKaryawan::where('status', 'sudah_terpakai')
            ->whereDoesntHave('pengguna')
            ->orderBy('id')
            ->limit(5)
            ->get();

        if ($kandidatMaster->count() < 5) {
            $this->command->warn("Akun Karyawan: hanya menemukan {$kandidatMaster->count()} data_karyawan 'sudah_terpakai' yang tersedia.");
        }

        $noHpSamples = [
            '081234567890',
            '081298765432',
            '085711223344',
            '087812345678',
            '081377889900',
        ];

        $created = 0;
        foreach ($kandidatMaster as $index => $master) {
            $email = 'karyawan'.($index + 1).'@test.com';

            // Skip jika email sudah ada (idempotent-ish)
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $user = User::create([
                'name' => $master->nama_karyawan,
                'email' => $email,
                'password' => Hash::make('password'),
                'no_hp' => $noHpSamples[$index] ?? '081200000000',
                'tipe_akun' => 'karyawan',
                'peran_id' => $peranKaryawan->id,
                'status' => 'aktif',
            ]);

            ProfilKaryawan::create([
                'user_id' => $user->id,
                'data_karyawan_id' => $master->id,
                'nama_karyawan' => $master->nama_karyawan,
                'nik_karyawan' => $master->nik_karyawan,
                'departemen' => $master->departemen,
                'jabatan' => $master->jabatan,
            ]);

            $created++;
        }

        $this->command->info("[2] Akun Karyawan: {$created} user + profil_karyawan dibuat.");
    }

    /**
     * 3. DATA KANDIDAT (users + profil_kandidat) - 8 akun
     *  - 3 aktif, 3 menunggu_verifikasi, 2 ditolak
     *  - posisi_dilamar = ID dari tabel posisi
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
                'no_hp' => '0857'.str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'tipe_akun' => 'kandidat',
                'peran_id' => $peranKandidat->id,
                'status' => $distribusiStatus[$index],
            ]);

            ProfilKandidat::create([
                'user_id' => $user->id,
                'nama_kandidat' => $nama,
                'posisi_dilamar' => $posisi->id,
                'pendidikan_terakhir' => $listPendidikan[$index],
                'nik_kandidat' => $nik,
            ]);

            $created++;
        }

        $this->command->info("[3] Kandidat: {$created} user + profil_kandidat (3 aktif, 3 menunggu_verifikasi, 2 ditolak).");
    }

    /**
     * 4. AKUN STAFF/ADMIN tambahan (tipe_akun = custom)
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
                'no_hp' => '0813'.str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'tipe_akun' => 'custom',
                'peran_id' => $row['peran_id'],
                'status' => 'aktif',
            ]);

            $created++;
        }

        $this->command->info("[4] Staff/Admin: {$created} user (2 Admin HR, 1 HR Viewer).");
    }
}
