<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = 2;
$sesiId = 2;
$soalIds = range(99, 118);

// Ambil semua opsi jawaban per soal, pilih opsi pertama (A)
foreach ($soalIds as $soalId) {
    $opsiId = \App\Models\OpsiJawaban::where('soal_id', $soalId)->orderBy('urutan')->value('id');
    if (!$opsiId) continue;

    \App\Models\JawabanPeserta::updateOrCreate(
        ['user_id' => $userId, 'sesi_tes_id' => $sesiId, 'soal_id' => $soalId],
        ['opsi_dipilih_id' => $opsiId, 'waktu_jawab' => now()]
    );
}

// Jalankan scoring
$alatTesId = \App\Models\AlatTes::where('kode', 'EPPS')->value('id');
$service = app(\App\Services\ScoringEngineService::class);
$result = $service->dispatch($userId, $sesiId, $alatTesId, '4');
echo "Scoring selesai. HasilSkorPeserta: " . \App\Models\HasilSkorPeserta::where('user_id', $userId)->where('sesi_tes_id', $sesiId)->count() . " dimensi\n";

// Update status
\App\Models\PesertaSesiTes::where('user_id', $userId)->where('sesi_tes_id', $sesiId)->update(['status_pengerjaan' => 'Selesai', 'tanggal_pengerjaan' => now()]);
echo "Done\n";
