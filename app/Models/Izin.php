<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izin';

    protected $fillable = [
        'kode_izin',
        'deskripsi',
    ];

    public function peran(): BelongsToMany
    {
        return $this->belongsToMany(
            Peran::class,
            'peran_izin',
            'izin_id',
            'peran_id'
        )->withTimestamps();
    }
}