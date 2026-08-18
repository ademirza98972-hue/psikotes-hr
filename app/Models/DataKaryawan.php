<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataKaryawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_karyawan';

    protected $fillable = [
        'nik_karyawan',
        'nama_karyawan',
        'jenis_kelamin',
        'departemen',
        'jabatan',
        'status',
        'departemen_id',
        'posisi_id',
    ];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function posisi(): BelongsTo
    {
        return $this->belongsTo(Posisi::class);
    }

    public function pengguna(): HasMany
    {
        return $this->hasMany(ProfilKaryawan::class, 'data_karyawan_id', 'id');
    }
}