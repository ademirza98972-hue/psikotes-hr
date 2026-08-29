<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataKaryawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_karyawan';

    protected $fillable = [
        'nik_karyawan',
        'tanggal_lahir',
        'pendidikan_terakhir',
        'departemen_id',
        'posisi_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function posisi(): BelongsTo
    {
        return $this->belongsTo(Posisi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}