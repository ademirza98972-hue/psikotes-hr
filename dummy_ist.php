<?php
// Script dummy jawaban IST untuk testing scoring
// Jalankan: php artisan tinker < dummy_ist.php
// Atau copy paste ke tinker

use App\Models\AlatTes;
use App\Models\Soal;
use App\Models\JawabanPeserta;

$userId = 2;
$sesiId = 5;

$at = AlatTes::where('kode', 'IST')->first();
$soals = Soal::where('alat_tes_id', $at->id)->get();

foreach ($soals as $s) {
    $opsi = $s->opsiJawaban->first();
    JawabanPeserta::updateOrCreate(
        ['user_id' => $userId, 'sesi_tes_id' => $sesiId, 'soal_id' => $s->id],
        [
            'opsi_dipilih_id' => $opsi?->id ?? null,
            'jawaban_teks'    => $opsi ? null : '1',
            'waktu_jawab'     => now(),
        ]
    );
}

echo "Done: " . $soals->count() . " jawaban inserted\n";