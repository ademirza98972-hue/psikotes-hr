<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlatTes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alat_tes';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'deskripsi',
        'format_dasar',
        'pola_skoring',
        'durasi_total_menit',
        'batas_waktu_per_soal_aktif',
        'batas_waktu_per_soal_detik',
        'is_sensitif',
        'jumlah_soal',
        'is_aktif',
    ];

    protected $casts = [
        'batas_waktu_per_soal_aktif' => 'boolean',
        'is_sensitif' => 'boolean',
        'is_aktif' => 'boolean',
    ];

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'alat_tes_id');
    }

    public function dimensiAlatTes(): HasMany
    {
        return $this->hasMany(DimensiAlatTes::class, 'alat_tes_id');
    }

    public function normaKonversi(): HasMany
    {
        return $this->hasMany(NormaKonversi::class, 'alat_tes_id');
    }

    public function levelDimensi(): HasMany
    {
        return $this->hasMany(LevelDimensi::class, 'alat_tes_id');
    }

    public function gridInputPeserta(): HasMany
    {
        return $this->hasMany(GridInputPeserta::class, 'alat_tes_id');
    }

    public function hasilKolomGrid(): HasMany
    {
        return $this->hasMany(HasilKolomGrid::class, 'alat_tes_id');
    }

    public function hasilSkorPeserta(): HasMany
    {
        return $this->hasMany(HasilSkorPeserta::class, 'alat_tes_id');
    }

    public function pesertaSesiTes()
    {
        return $this->belongsToMany(SesiTes::class, 'alat_tes_sesi_tes', 'alat_tes_id', 'sesi_tes_id');
    }
}
