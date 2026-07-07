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
        'posisi_dilamar',
        'pendidikan_terakhir',
        'no_ktp',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}