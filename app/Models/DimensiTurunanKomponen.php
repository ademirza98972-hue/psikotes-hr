<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DimensiTurunanKomponen extends Model
{
    use HasFactory;

    protected $table = 'dimensi_turunan_komponen';

    protected $fillable = [
        'dimensi_turunan_id',
        'dimensi_komponen_id',
        'bobot',
    ];

    public function dimensiTurunan(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_turunan_id', 'id');
    }

    public function dimensiKomponen(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_komponen_id', 'id');
    }
}
