<?php

namespace App\Console\Commands;

use App\Models\AlatTes;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\DimensiAlatTes;
use App\Models\LevelDimensi;
use App\Models\NormaKonversi;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeSoftDeletes extends Command
{
    protected $signature = 'purge:soft-deletes {--days=30 : Minimum days before permanent deletion}';

    protected $description = 'Permanently delete soft-trashed records older than N days (default: 30)';

    /**
     * Cascade chain: child tables must be purged before their parent.
     * Models with SoftDeletes support are tracked via deleted_at;
     * leaf tables (no SoftDeletes) are cleaned by FK lookup.
     */
    private array $models = [
        // Leaves first — no SoftDeletes, must be cleared before parents
        NormaKonversi::class,
        DimensiAlatTes::class,
        LevelDimensi::class,
        Soal::class,
        // Parents with SoftDeletes
        AlatTes::class,
        User::class,
        Departemen::class,
        Posisi::class,
        DataKaryawan::class,
        Peran::class,
    ];

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("Purge records trashed > {$days} hari (cutoff: {$cutoff->format('Y-m-d H:i:s')})");

        $grandTotal = 0;

        foreach ($this->models as $model) {
            $table = (new $model)->getTable();

            if (in_array($model, [
                NormaKonversi::class, DimensiAlatTes::class,
                LevelDimensi::class, Soal::class,
            ])) {
                // These tables don't have SoftDeletes — find via FK to trashed parents
                $parentIdCol = match ($model) {
                    NormaKonversi::class => 'alat_tes_id',
                    DimensiAlatTes::class => 'alat_tes_id',
                    LevelDimensi::class => 'alat_tes_id',
                    Soal::class => 'alat_tes_id',
                    default => null,
                };

                if ($parentIdCol) {
                    // Find parent IDs that are trashed and past cutoff
                    $parentIds = DB::table('alat_tes')
                        ->whereNotNull('deleted_at')
                        ->where('deleted_at', '<', $cutoff)
                        ->pluck('id');

                    if ($parentIds->isEmpty()) {
                        continue;
                    }

                    // Delete matching children by FK
                    $deleted = DB::table($table)
                        ->whereIn($parentIdCol, $parentIds)
                        ->delete();

                    if ($deleted > 0) {
                        $this->info("  [{$table}] {$deleted} child records (FK cleanup)");
                        $grandTotal += $deleted;
                    }
                }
                continue;
            }

            // SoftDeletes models — use whereNotTrashed to count, then forceDelete each
            $count = $model::onlyTrashed()
                ->where('deleted_at', '<', $cutoff)
                ->count();

            if ($count === 0) {
                continue;
            }

            $purged = 0;
            $model::onlyTrashed()
                ->where('deleted_at', '<', $cutoff)
                ->each(function ($record) use (&$purged, $model) {
                    try {
                        $record->forceDelete();
                        $purged++;
                    } catch (\Exception $e) {
                        $this->warn("  Gagal menghapus {$model} (ID {$record->id}): " . $e->getMessage());
                    }
                });

            if ($purged > 0) {
                $this->info("  [{$model}] {$purged} record dihapus permanen");
                $grandTotal += $purged;
            }
        }

        $this->info("Selesai. Total dihapus permanen: {$grandTotal} record");
        return Command::SUCCESS;
    }
}
