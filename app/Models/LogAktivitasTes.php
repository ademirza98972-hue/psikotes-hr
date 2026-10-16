<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAktivitasTes extends Model
{
    public $timestamps = false;

    protected $table = 'log_aktivitas_tes';

    protected $fillable = [
        'peserta_sesi_tes_id',
        'tipe_kejadian',
        'mencurigakan',
        'keterangan',
        'created_at',
    ];

    protected $casts = [
        'mencurigakan' => 'boolean',
        'created_at'   => 'datetime',
    ];

    public function pesertaSesiTes(): BelongsTo
    {
        return $this->belongsTo(PesertaSesiTes::class, 'peserta_sesi_tes_id');
    }
}
