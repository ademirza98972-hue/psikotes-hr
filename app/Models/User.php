<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'id_karyawan', 'password', 'tipe_akun', 'peran_id', 'status', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'deleted_by');
    }

    public function profilKandidat(): HasOne
    {
        return $this->hasOne(ProfilKandidat::class, 'user_id', 'id');
    }

    public function dataKaryawan(): HasOne
    {
        return $this->hasOne(DataKaryawan::class, 'user_id', 'id');
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

    public function jawabanPeserta(): HasMany
    {
        return $this->hasMany(JawabanPeserta::class, 'user_id');
    }

    public function pesertaSesiTes(): HasMany
    {
        return $this->hasMany(PesertaSesiTes::class, 'user_id');
    }

    public function sesiTes(): BelongsToMany
    {
        return $this->belongsToMany(SesiTes::class, 'peserta_sesi_tes', 'user_id', 'sesi_tes_id');
    }
}