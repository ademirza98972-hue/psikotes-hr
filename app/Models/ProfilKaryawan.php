<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilKaryawan extends Model
{
    use HasFactory;

    protected $table = 'profil_karyawan';

    protected $fillable = [
        'user_id',
        'data_karyawan_id',
        'nama_karyawan',
        'nik_karyawan',
        'departemen',
        'jabatan',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function dataKaryawan(): BelongsTo
    {
        return $this->belongsTo(DataKaryawan::class, 'data_karyawan_id', 'id');
    }
}