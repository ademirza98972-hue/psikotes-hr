<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PesertaSesiTes extends Model
{
    use HasFactory;

    protected $table = 'peserta_sesi_tes';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'sesi_tes_id',
        'status_pengerjaan',
        'tanggal_pengerjaan',
        'waktu_mulai',
        'catatan_hr',
    ];

    protected $casts = [
        'tanggal_pengerjaan' => 'date',
        'waktu_mulai'        => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesiTes(): BelongsTo
    {
        return $this->belongsTo(SesiTes::class, 'sesi_tes_id');
    }

    public function alatTes(): BelongsToMany
    {
        return $this->belongsToMany(AlatTes::class, 'peserta_alat_tes', 'peserta_sesi_tes_id', 'alat_tes_id');
    }
}
