<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AlatTesSesiTes extends Model
{
    use HasFactory;

    protected $table = 'alat_tes_sesi_tes';

    public $timestamps = false;

    protected $fillable = [
        'sesi_tes_id',
        'alat_tes_id',
    ];

    public function sesiTes(): BelongsTo
    {
        return $this->belongsTo(SesiTes::class, 'sesi_tes_id');
    }

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }
}
