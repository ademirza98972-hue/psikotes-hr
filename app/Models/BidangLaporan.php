<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangLaporan extends Model
{
    use HasFactory;

    protected $table = 'bidang_laporan';

    protected $fillable = [
        'nama',
        'urutan',
    ];
}
