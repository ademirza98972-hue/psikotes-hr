<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanPeserta extends Model
{
    use HasFactory;

    protected $table = 'jawaban_peserta';

    protected $fillable = [
        'user_id',
        'sesi_tes_id',
        'soal_id',
        'opsi_dipilih_id',
        'jawaban_teks',
        'nilai_input',
        'waktu_jawab',
    ];

    protected $casts = [
        'nilai_input' => 'decimal:2',
        'waktu_jawab' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesiTes(): BelongsTo
    {
        return $this->belongsTo(SesiTes::class, 'sesi_tes_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function opsiDipilih(): BelongsTo
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_dipilih_id');
    }
}
