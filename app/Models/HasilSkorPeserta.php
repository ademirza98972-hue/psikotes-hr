<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilSkorPeserta extends Model
{
    use HasFactory;

    protected $table = 'hasil_skor_peserta';

    protected $fillable = [
        'user_id',
        'sesi_tes_id',
        'alat_tes_id',
        'dimensi_id',
        'skor_mentah',
        'skor_akhir',
        'level_id',
    ];

    protected $casts = [
        'skor_mentah' => 'decimal:2',
        'skor_akhir' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesiTes(): BelongsTo
    {
        return $this->belongsTo(SesiTes::class, 'sesi_tes_id');
    }

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelDimensi::class, 'level_id');
    }
}
