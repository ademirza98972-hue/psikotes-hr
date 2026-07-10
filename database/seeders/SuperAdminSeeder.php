<?php

namespace Database\Seeders;

use App\Models\Peran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $peranSuperAdmin = Peran::where('nama_peran', 'Super Admin')->first();

        if (! $peranSuperAdmin) {
            $this->command->error('Peran Super Admin tidak ditemukan. Jalankan PeranSeeder terlebih dahulu.');
            return;
        }

        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@psikotes-hr.test');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'no_hp' => null,
                'tipe_akun' => 'custom',
                'peran_id' => $peranSuperAdmin->id,
                'status' => 'aktif',
            ]
        );

        $this->command->info("Akun Super Admin siap: {$email}");
    }
}