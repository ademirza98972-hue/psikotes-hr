<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpsiJawaban extends Model
{
    use HasFactory;

    protected $table = 'opsi_jawaban';

    protected $fillable = [
        'soal_id',
        'teks_opsi',
        'urutan',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function bobotOpsiDimensi(): HasMany
    {
        return $this->hasMany(BobotOpsiDimensi::class, 'opsi_jawaban_id');
    }

    public function jawabanPeserta(): HasMany
    {
        return $this->hasMany(JawabanPeserta::class, 'opsi_dipilih_id');
    }
}
