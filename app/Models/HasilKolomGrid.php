<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilKolomGrid extends Model
{
    use HasFactory;

    protected $table = 'hasil_kolom_grid';

    protected $fillable = [
        'user_id',
        'sesi_tes_id',
        'alat_tes_id',
        'kolom_ke',
        'jumlah_benar',
        'jumlah_salah',
        'jumlah_kelewat',
        'waktu_pakai_detik',
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
