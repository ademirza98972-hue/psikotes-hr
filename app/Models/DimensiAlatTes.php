<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimensiAlatTes extends Model
{
    use HasFactory;

    protected $table = 'dimensi_alat_tes';

    protected $fillable = [
        'alat_tes_id',
        'kode_dimensi',
        'nama_dimensi',
        'deskripsi_aspek',
        'tipe_kategori',
        'arah_skor',
        'urutan',
    ];

    public function alatTes(): BelongsTo
    {
        return $this->belongsTo(AlatTes::class, 'alat_tes_id');
    }

    public function bobotOpsiDimensi(): HasMany
    {
        return $this->hasMany(BobotOpsiDimensi::class, 'dimensi_id');
    }

    public function normaKonversi(): HasMany
    {
        return $this->hasMany(NormaKonversi::class, 'dimensi_id');
    }

    public function komponenPenyusun(): HasMany
    {
        return $this->hasMany(DimensiTurunanKomponen::class, 'dimensi_turunan_id');
    }

    public function bagianDariTurunan(): HasMany
    {
        return $this->hasMany(DimensiTurunanKomponen::class, 'dimensi_komponen_id');
    }

    public function levelDimensi(): HasMany
    {
        return $this->hasMany(LevelDimensi::class, 'dimensi_id');
    }

    public function interpretasiTeks(): HasMany
    {
        return $this->hasMany(InterpretasiTeks::class, 'dimensi_id');
    }

    public function bidangLaporan(): HasMany
    {
        return $this->hasMany(DimensiBidangLaporan::class, 'dimensi_id');
    }

    public function hasilSkorPeserta(): HasMany
    {
        return $this->hasMany(HasilSkorPeserta::class, 'dimensi_id');
    }

    public function standarKompetensiPosisi(): HasMany
    {
        return $this->hasMany(StandarKompetensiPosisi::class, 'dimensi_id');
    }
}
