<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GridInputPeserta extends Model
{
    use HasFactory;

    protected $table = 'grid_input_peserta';

    protected $fillable = [
        'user_id',
        'sesi_tes_id',
        'alat_tes_id',
        'kolom_ke',
        'baris_ke',
        'jawaban_peserta',
        'jawaban_benar',
        'is_benar',
        'waktu_input',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
        'waktu_input' => 'datetime',
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
}
