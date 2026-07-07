<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'no_hp', 'tipe_akun', 'peran_id', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'peran_id', 'id');
    }

    public function profilKandidat(): HasOne
    {
        return $this->hasOne(ProfilKandidat::class, 'user_id', 'id');
    }

    public function profilKaryawan(): HasOne
    {
        return $this->hasOne(ProfilKaryawan::class, 'user_id', 'id');
    }

    public function hasIzin(string $kodeIzin): bool
    {
        if (! $this->peran_id) {
            return false;
        }

        return $this->peran()
            ->whereHas('izin', function ($query) use ($kodeIzin) {
                $query->where('kode_izin', $kodeIzin);
            })
            ->exists();
    }
}