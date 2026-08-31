<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah no_hp dan jenis_kelamin ke data_karyawan
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->string('no_hp', 30)->nullable()->after('nik_karyawan');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('no_hp');
        });

        // 2. Tambah no_hp dan jenis_kelamin ke profil_kandidat
        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->string('no_hp', 30)->nullable()->after('user_id');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('no_hp');
        });

        // 3. Migrasi data karyawan: users → data_karyawan
        DB::statement('
            UPDATE data_karyawan dk
            JOIN users u ON u.id = dk.user_id
            SET dk.no_hp = u.no_hp, dk.jenis_kelamin = u.jenis_kelamin
            WHERE u.tipe_akun = "karyawan"
        ');

        // 4. Migrasi data kandidat: users → profil_kandidat
        DB::statement('
            UPDATE profil_kandidat pk
            JOIN users u ON u.id = pk.user_id
            SET pk.no_hp = u.no_hp, pk.jenis_kelamin = u.jenis_kelamin
            WHERE u.tipe_akun = "kandidat"
        ');

        // 5. Hapus dari users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'jenis_kelamin']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp', 30)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
        });

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'jenis_kelamin']);
        });

        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'jenis_kelamin']);
        });
    }
};
