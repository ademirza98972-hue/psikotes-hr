<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peran extends Model
{
    use HasFactory;

    protected $table = 'peran';

    protected $fillable = [
        'nama_peran',
        'deskripsi',
    ];

    public function izin(): BelongsToMany
    {
        return $this->belongsToMany(
            Izin::class,
            'peran_izin',
            'peran_id',
            'izin_id'
        )->withTimestamps();
    }

    public function pengguna(): HasMany
    {
        return $this->hasMany(User::class, 'peran_id');
    }
}