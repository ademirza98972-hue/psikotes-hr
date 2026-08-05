<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelDimensi extends Model
{
    use HasFactory;

    protected $table = 'level_dimensi';

    protected $fillable = [
        'alat_tes_id',
        'dimensi_id',
        'label',
        'skor_min',
        'skor_max',
        'urutan',
    ];

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }

    public function interpretasiTeks(): HasMany
    {
        return $this->hasMany(InterpretasiTeks::class, 'level_id');
    }

    public function hasilSkorPeserta(): HasMany
    {
        return $this->hasMany(HasilSkorPeserta::class, 'level_id');
    }

    public function standarKompetensiPosisi(): HasMany
    {
        return $this->hasMany(StandarKompetensiPosisi::class, 'level_id_diharapkan');
    }
}
