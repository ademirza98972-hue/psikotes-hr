<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = 2;
$sesiId = 2;

// Ambil semua soal EPPS
$alatTesId = \App\Models\AlatTes::where('kode', 'EPPS')->value('id');
$soalIds = \App\Models\Soal::where('alat_tes_id', $alatTesId)->pluck('id');

// Hapus jawaban lama
\App\Models\JawabanPeserta::where('user_id', $userId)->where('sesi_tes_id', $sesiId)->delete();
\App\Models\HasilSkorPeserta::where('user_id', $userId)->where('sesi_tes_id', $sesiId)->delete();

// Isi jawaban dummy - pilih opsi A untuk semua soal
foreach ($soalIds as $soalId) {
    $opsiId = \App\Models\OpsiJawaban::where('soal_id', $soalId)->orderBy('urutan')->value('id');
    if (!$opsiId) continue;
    \App\Models\JawabanPeserta::create([
        'user_id' => $userId,
        'sesi_tes_id' => $sesiId,
        'soal_id' => $soalId,
        'opsi_dipilih_id' => $opsiId,
        'waktu_jawab' => now(),
    ]);
}

// Jalankan scoring
$service = app(\App\Services\ScoringEngineService::class);
$result = $service->dispatch($userId, $sesiId, $alatTesId, '4');

$count = \App\Models\HasilSkorPeserta::where('user_id', $userId)->where('sesi_tes_id', $sesiId)->count();
echo "Scoring selesai. Dimensi tersimpan: {$count}\n";

$selesai = \App\Models\PesertaSesiTes::where('user_id', $userId)->where('sesi_tes_id', $sesiId)
    ->update(['status_pengerjaan' => 'Selesai', 'tanggal_pengerjaan' => now()]);
echo "Done\n";