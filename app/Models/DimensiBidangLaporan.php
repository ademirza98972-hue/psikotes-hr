<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DimensiBidangLaporan extends Model
{
    use HasFactory;

    protected $table = 'dimensi_bidang_laporan';

    protected $fillable = [
        'dimensi_id',
        'bidang_laporan_id',
    ];

    public function dimensi(): BelongsTo
    {
        return $this->belongsTo(DimensiAlatTes::class, 'dimensi_id', 'id');
    }

    public function bidangLaporan(): BelongsTo
    {
        return $this->belongsTo(BidangLaporan::class, 'bidang_laporan_id');
    }
}
