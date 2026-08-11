<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$rows = \App\Models\GridInputPeserta::select('user_id','sesi_tes_id','alat_tes_id','kolom_ke')
    ->distinct()->get();

foreach ($rows as $r) {
    \App\Models\HasilKolomGrid::updateOrCreate(
        ['user_id'=>$r->user_id,'sesi_tes_id'=>$r->sesi_tes_id,'alat_tes_id'=>$r->alat_tes_id,'kolom_ke'=>$r->kolom_ke],
        [
            'jumlah_benar'     => \App\Models\GridInputPeserta::where('user_id',$r->user_id)->where('sesi_tes_id',$r->sesi_tes_id)->where('alat_tes_id',$r->alat_tes_id)->where('kolom_ke',$r->kolom_ke)->where('is_benar',true)->count(),
            'jumlah_salah'     => \App\Models\GridInputPeserta::where('user_id',$r->user_id)->where('sesi_tes_id',$r->sesi_tes_id)->where('alat_tes_id',$r->alat_tes_id)->where('kolom_ke',$r->kolom_ke)->where('is_benar',false)->where('jawaban_peserta','!=',0)->count(),
            'jumlah_kelewat'   => \App\Models\GridInputPeserta::where('user_id',$r->user_id)->where('sesi_tes_id',$r->sesi_tes_id)->where('alat_tes_id',$r->alat_tes_id)->where('kolom_ke',$r->kolom_ke)->where('jawaban_peserta',0)->count(),
            'waktu_pakai_detik'=> 0,
        ]
    );
}
echo "Done: " . \App\Models\HasilKolomGrid::count() . " rows\n";
