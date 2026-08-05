<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BobotOpsiDimensi extends Model
{
    use HasFactory;

    protected $table = 'bobot_opsi_dimensi';

    protected $fillable = [
        'opsi_jawaban_id',
        'dimensi_id',
        'bobot',
        'is_reverse',
    ];

    protected $casts = [
        'is_reverse' => 'boolean',
    ];

    public function opsiJawaban(): BelongsTo
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_jawaban_id');
    }

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }
}
