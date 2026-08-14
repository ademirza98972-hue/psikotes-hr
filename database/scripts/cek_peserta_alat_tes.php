<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$data = \App\Models\PesertaAlatTes::with(['alatTes','pesertaSesiTes.user'])
    ->whereIn('alat_tes_id', [1, 3])
    ->get()
    ->map(fn($p) => [
        'user'  => $p->pesertaSesiTes->user->name,
        'kode'  => $p->alatTes->kode,
        'sesi'  => $p->pesertaSesiTes->sesi_tes_id,
    ]);

foreach ($data as $d) {
    echo "{$d['user']} | {$d['kode']} | sesi {$d['sesi']}\n";
}
