<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Soal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'soal';

    protected $fillable = [
        'alat_tes_id',
        'nomor',
        'teks_soal',
        'tipe_format',
        'kunci_jawaban',
        'urutan',
        'duplikat_dari_soal_id',
    ];

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class, 'soal_id');
    }

    public function jawabanPeserta(): HasMany
    {
        return $this->hasMany(JawabanPeserta::class, 'soal_id');
    }
}
