<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SesiTes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sesi_tes';

    protected $fillable = [
        'nama_sesi',
        'departemen_terkait_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'jumlah_peserta',
        'jumlah_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected function statusDisplay(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->status === 'Draft') return 'Draft';
            if ($this->status === 'Selesai') return 'Selesai';
            $today = now()->toDateString();
            if ($today > $this->tanggal_selesai?->toDateString()) return 'Kedaluwarsa';
            if ($today >= $this->tanggal_mulai?->toDateString()) return 'Aktif';
            return 'Belum Dimulai';
        });
    }

    public function departemenTerkait(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_terkait_id');
    }

    public function alatTes(): BelongsToMany
    {
        return $this->belongsToMany(AlatTes::class, 'alat_tes_sesi_tes', 'sesi_tes_id', 'alat_tes_id');
    }

    public function pesertaSesiTes()
    {
        return $this->belongsToMany(User::class, 'peserta_sesi_tes', 'sesi_tes_id', 'user_id');
    }

    public function pesertaSesiTesRecords()
    {
        return $this->hasMany(PesertaSesiTes::class, 'sesi_tes_id');
    }

    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'sesi_tes_id');
    }

    public function gridInputPeserta()
    {
        return $this->hasMany(GridInputPeserta::class, 'sesi_tes_id');
    }

    public function hasilKolomGrid()
    {
        return $this->hasMany(HasilKolomGrid::class, 'sesi_tes_id');
    }

    public function hasilSkorPeserta()
    {
        return $this->hasMany(HasilSkorPeserta::class, 'sesi_tes_id');
    }
}
