<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->id();
            $table->string('nama_departemen', 100)->unique();
            $table->timestamps();
        });

        Schema::create('posisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posisi', 100)->unique();
            $table->foreignId('departemen_id')->constrained('departemen')->onDelete('cascade');
            $table->timestamps();
        });

        DB::table('departemen')->insert([
            ['id' => 1, 'nama_departemen' => 'HR & GA', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama_departemen' => 'IT', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama_departemen' => 'Finance', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama_departemen' => 'Operations', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama_departemen' => 'Marketing', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama_departemen' => 'Legal', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('posisi')->insert([
            // HR & GA
            ['id' => 1, 'departemen_id' => 1, 'nama_posisi' => 'Chief HR & GA Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'departemen_id' => 1, 'nama_posisi' => 'Head of HR', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'departemen_id' => 1, 'nama_posisi' => 'HR Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'departemen_id' => 1, 'nama_posisi' => 'HR Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'departemen_id' => 1, 'nama_posisi' => 'Admin Staff', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'departemen_id' => 1, 'nama_posisi' => 'General Affairs Staff', 'created_at' => now(), 'updated_at' => now()],
            // IT
            ['id' => 7, 'departemen_id' => 2, 'nama_posisi' => 'Chief Technology Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'departemen_id' => 2, 'nama_posisi' => 'Head of IT', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'departemen_id' => 2, 'nama_posisi' => 'IT Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'departemen_id' => 2, 'nama_posisi' => 'Software Engineer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'departemen_id' => 2, 'nama_posisi' => 'System Analyst', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'departemen_id' => 2, 'nama_posisi' => 'IT Support Staff', 'created_at' => now(), 'updated_at' => now()],
            // Finance
            ['id' => 13, 'departemen_id' => 3, 'nama_posisi' => 'Chief Financial Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'departemen_id' => 3, 'nama_posisi' => 'Finance Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'departemen_id' => 3, 'nama_posisi' => 'Accounting Staff', 'created_at' => now(), 'updated_at' => now()],
            // Operations
            ['id' => 16, 'departemen_id' => 4, 'nama_posisi' => 'Chief Operation Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'departemen_id' => 4, 'nama_posisi' => 'Operation Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'departemen_id' => 4, 'nama_posisi' => 'Operation Supervisor', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'departemen_id' => 4, 'nama_posisi' => 'Warehouse Staff', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'departemen_id' => 4, 'nama_posisi' => 'Driver Staff', 'created_at' => now(), 'updated_at' => now()],
            // Marketing
            ['id' => 21, 'departemen_id' => 5, 'nama_posisi' => 'Chief Marketing Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'departemen_id' => 5, 'nama_posisi' => 'Marketing Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'departemen_id' => 5, 'nama_posisi' => 'Marketing Staff', 'created_at' => now(), 'updated_at' => now()],
            // Legal
            ['id' => 24, 'departemen_id' => 6, 'nama_posisi' => 'Chief Legal Officer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'departemen_id' => 6, 'nama_posisi' => 'Legal Manager', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'departemen_id' => 6, 'nama_posisi' => 'Legal Staff', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('posisi');
        Schema::dropIfExists('departemen');
    }
};
