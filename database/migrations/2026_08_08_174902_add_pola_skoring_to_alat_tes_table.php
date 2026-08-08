<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->string('pola_skoring', 30)->nullable()->after('format_dasar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->dropColumn('pola_skoring');
        });
    }
};
