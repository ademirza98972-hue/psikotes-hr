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
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'no_hp', 'jenis_kelamin', 'tipe_akun', 'peran_id', 'status'])]
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

    public function profilKandidat(): HasOne
    {
        return $this->hasOne(ProfilKandidat::class, 'user_id', 'id');
    }

    public function profilKaryawan(): HasOne
    {
        return $this->hasOne(ProfilKaryawan::class, 'user_id', 'id');
    }

    public function dataKaryawan(): HasOneThrough
    {
        return $this->hasOneThrough(DataKaryawan::class, ProfilKaryawan::class, 'user_id', 'id', 'id', 'data_karyawan_id');
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

    public function gridInputPeserta(): HasMany
    {
        return $this->hasMany(GridInputPeserta::class, 'user_id');
    }

    public function hasilKolomGrid(): HasMany
    {
        return $this->hasMany(HasilKolomGrid::class, 'user_id');
    }

    public function hasilSkorPeserta(): HasMany
    {
        return $this->hasMany(HasilSkorPeserta::class, 'user_id');
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