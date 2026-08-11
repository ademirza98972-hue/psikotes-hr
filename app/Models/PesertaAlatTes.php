<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaAlatTes extends Model
{
    use HasFactory;

    protected $table = 'peserta_alat_tes';

    protected $fillable = [
        'peserta_sesi_tes_id',
        'alat_tes_id',
        'waktu_mulai_kolom',
        'kolom_ke',
    ];

    protected $casts = [
        'waktu_mulai_kolom' => 'datetime',
    ];

    public function pesertaSesiTes(): BelongsTo
    {
        return $this->belongsTo(PesertaSesiTes::class, 'peserta_sesi_tes_id');
    }

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }
}
