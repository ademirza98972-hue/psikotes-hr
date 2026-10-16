<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('psikotes:hitung-ulang {--sesi= : Hanya sesi tes dengan id ini}', function (\App\Services\ScoringEngineService $engine) {
    $sesi = $this->option('sesi');

    // 1. Kraepelin: rekap ulang benar/salah/terlampaui per kolom dari input mentah
    $kolom = \Illuminate\Support\Facades\DB::table('grid_input_peserta')
        ->when($sesi, fn ($q) => $q->where('sesi_tes_id', $sesi))
        ->select('user_id', 'sesi_tes_id', 'alat_tes_id', 'kolom_ke')->distinct()->get();
    foreach ($kolom as $k) {
        $engine->rekapKolomGrid($k->user_id, $k->sesi_tes_id, $k->alat_tes_id, $k->kolom_ke);
    }

    // 2. Hitung ulang semua hasil yang pernah diskor
    $target = \Illuminate\Support\Facades\DB::table('hasil_skor_peserta as h')
        ->leftJoin('dimensi_alat_tes as d', 'd.id', '=', 'h.dimensi_id')
        ->when($sesi, fn ($q) => $q->where('h.sesi_tes_id', $sesi))
        ->selectRaw('DISTINCT h.user_id, h.sesi_tes_id, COALESCE(h.alat_tes_id, d.alat_tes_id) as alat_tes_id')
        ->get();

    $gagal = 0;
    foreach ($target as $t) {
        $user = \App\Models\User::withTrashed()->find($t->user_id);
        $alat = \App\Models\AlatTes::withTrashed()->find($t->alat_tes_id);
        if (! $user || ! $alat) {
            continue;
        }
        $tanggalTes = \App\Models\PesertaSesiTes::where('user_id', $t->user_id)
            ->where('sesi_tes_id', $t->sesi_tes_id)->value('tanggal_pengerjaan');
        try {
            $engine->dispatch($t->user_id, $t->sesi_tes_id, $alat->id,
                $engine->kelompokSegmen($user, $alat, $tanggalTes ? \Carbon\Carbon::parse($tanggalTes) : null));
        } catch (\Throwable $e) {
            $gagal++;
            $this->warn("Lewati user {$t->user_id} sesi {$t->sesi_tes_id} {$alat->kode}: {$e->getMessage()}");
        }
    }

    $this->info("Kolom Kraepelin direkap: {$kolom->count()}. Hasil dihitung ulang: " . ($target->count() - $gagal) . ", gagal: {$gagal}.");
})->purpose('Hitung ulang skor semua peserta dengan aturan skoring terbaru');
