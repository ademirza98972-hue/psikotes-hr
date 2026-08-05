<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandarKompetensiPosisi extends Model
{
    use HasFactory;

    protected $table = 'standar_kompetensi_posisi';

    protected $fillable = [
        'posisi_id',
        'dimensi_id',
        'level_id_diharapkan',
    ];

    public function posisi(): BelongsTo
    {
        return $this->belongsTo(Posisi::class, 'posisi_id');
    }

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }

    public function levelDiharapkan(): BelongsTo
    {
        return $this->belongsTo(LevelDimensi::class, 'level_id_diharapkan', 'id');
    }
}
