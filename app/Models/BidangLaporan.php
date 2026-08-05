<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BidangLaporan extends Model
{
    use HasFactory;

    protected $table = 'bidang_laporan';

    protected $fillable = [
        'nama',
        'urutan',
    ];

    public function dimensiBidangLaporan(): HasMany
    {
        return $this->hasMany(DimensiBidangLaporan::class, 'bidang_laporan_id');
    }
}
