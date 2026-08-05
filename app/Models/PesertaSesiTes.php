<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaSesiTes extends Model
{
    use HasFactory;

    protected $table = 'peserta_sesi_tes';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'sesi_tes_id',
        'status_pengerjaan',
        'tanggal_pengerjaan',
    ];

    protected $casts = [
        'tanggal_pengerjaan' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesiTes(): BelongsTo
    {
        return $this->belongsTo(SesiTes::class, 'sesi_tes_id');
    }
}
