<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posisi';

    protected $fillable = ['nama_posisi', 'departemen_id'];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function karyawan(): HasMany
    {
        return $this->hasMany(DataKaryawan::class, 'posisi_id');
    }
}