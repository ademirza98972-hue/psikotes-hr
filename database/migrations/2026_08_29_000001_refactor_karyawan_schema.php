<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah id_karyawan, must_change_password ke users; buat email nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->nullable()->unique()->after('name');
            $table->boolean('must_change_password')->default(false)->after('status');
            $table->string('email')->nullable()->change();
        });

        // 2. Tambah user_id ke data_karyawan
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // 3. Migrasi data: salin user_id dari profil_karyawan ke data_karyawan
        DB::statement('
            UPDATE data_karyawan dk
            JOIN profil_karyawan pk ON pk.data_karyawan_id = dk.id
            JOIN users u ON u.id = pk.user_id AND u.deleted_at IS NULL
            SET dk.user_id = pk.user_id
        ');

        // 4. Generate id_karyawan untuk karyawan yang sudah punya akun
        $counterByYear = [];
        $karyawanExisting = DB::table('users')
            ->where('tipe_akun', 'karyawan')
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        foreach ($karyawanExisting as $u) {
            $year = date('Y', strtotime($u->created_at));
            $counterByYear[$year] = ($counterByYear[$year] ?? 0) + 1;
            $idKaryawan = $year . str_pad($counterByYear[$year], 3, '0', STR_PAD_LEFT);
            DB::table('users')->where('id', $u->id)->update(['id_karyawan' => $idKaryawan]);
        }

        // 5. Buat akun untuk data_karyawan yang belum punya user
        $peranId = DB::table('peran')->where('nama_peran', 'Karyawan')->value('id');

        $orphaned = DB::table('data_karyawan')
            ->whereNull('user_id')
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        foreach ($orphaned as $dk) {
            $year = date('Y', strtotime($dk->created_at));
            $counterByYear[$year] = ($counterByYear[$year] ?? 0) + 1;
            $idKaryawan = $year . str_pad($counterByYear[$year], 3, '0', STR_PAD_LEFT);

            $userId = DB::table('users')->insertGetId([
                'name'                 => $dk->nama_karyawan,
                'email'                => null,
                'id_karyawan'          => $idKaryawan,
                'password'             => Hash::make($idKaryawan),
                'jenis_kelamin'        => $dk->jenis_kelamin,
                'tipe_akun'            => 'karyawan',
                'peran_id'             => $peranId,
                'status'               => 'aktif',
                'must_change_password' => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            DB::table('data_karyawan')->where('id', $dk->id)->update(['user_id' => $userId]);
        }

        // 6. Hapus tabel profil_karyawan
        Schema::dropIfExists('profil_karyawan');

        // 7. Hapus kolom duplikat dari data_karyawan
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->dropColumn(['nama_karyawan', 'jenis_kelamin', 'departemen', 'jabatan', 'status']);
        });
    }

    public function down(): void
    {
        // Kembalikan dari backup — down() kompleks karena data migration irreversible
    }
};
