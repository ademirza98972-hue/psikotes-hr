<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterpretasiTeks extends Model
{
    use HasFactory;

    protected $table = 'interpretasi_teks';

    protected $fillable = [
        'dimensi_id',
        'level_id',
        'teks_narasi',
    ];

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelDimensi::class, 'level_id');
    }
}
