<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilKandidat extends Model
{
    use HasFactory;

    protected $table = 'profil_kandidat';

    protected $fillable = [
        'user_id',
        'no_hp',
        'jenis_kelamin',
        'posisi_id',
        'pendidikan_terakhir',
        'nik_kandidat',
        'tanggal_lahir',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function posisi(): BelongsTo
    {
        return $this->belongsTo(Posisi::class, 'posisi_id');
    }
}