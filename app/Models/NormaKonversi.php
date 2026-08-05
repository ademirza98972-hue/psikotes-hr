<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NormaKonversi extends Model
{
    use HasFactory;

    protected $table = 'norma_konversi';

    protected $fillable = [
        'alat_tes_id',
        'dimensi_id',
        'sumber_dimensi_id',
        'kelompok_segmen',
        'tahap',
        'skor_mentah_min',
        'skor_mentah_max',
        'skor_hasil',
    ];

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }

    public function sumberDimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'sumber_dimensi_id', 'id');
    }
}
