<?php
require __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

\$c = new DB;
\$c->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'utf8'),
    'prefix' => env('DB_PREFIX', ''),
]);
\$c->setAsGlobal();
\$c->bootEloquent();

// Cek izin
\$izin = DB::table('izin')->where('kode_izin', 'hasil_tes.lihat_sensitif')->first();
echo "Izin hasil_tes.lihat_sensitif: ".(\$izin ? "Ada (ID: \$izin->id, Deskripsi: \$izin->deskripsi)" : "Tidak Ada")."\n";

// Cek Admin HR izinya
\$adminHrIzins = DB::table('izin')
    ->join('peran_izin', 'izin.id', '=', 'peran_izin.izin_id')
    ->join('peran', 'peran.id', '=', 'peran_izin.peran_id')
    ->where('peran.nama_peran', 'Admin HR')
    ->pluck('izin.kode_izin');

echo "\nAdmin HR Jumlah Izin: ".count(\$adminHrIzins)."\n";
echo "Ada hasil_tes.lihat_sensitif? ".(\$adminHrIzins->contains('hasil_tes.lihat_sensitif') ? 'YA' : 'TIDAK')."\n";
echo "Daftar izinyakemudian: ".implode(', ', \$adminHrIzins->take(5)->toArray())." ...".(count(\$adminHrIzins) > 5 ? '...' : "")."\n";

// Cek HR Viewer
\$hrViewerIzins = DB::table('izin')
    ->join('peran_izin', 'izin.id', '=', 'peran_izin.izin_id')
    ->join('peran', 'peran.id', '=', 'peran_izin.peran_id')
    ->where('peran.nama_peran', 'HR Viewer')
    ->pluck('izin.kode_ihin');

echo "\n\nHR Viewer Jumlah Izin: ".count(\$hrViewerIzins)."\n";
echo "Ada hasil_tes.lihat_sensitif? ".(\$hrViewerIzins->contains('hasil_tes.lihat_sensitif') ? 'YA' : 'TIDAK')."\n";
echo "Daftar izinya: ".implode(', ', \$hrViewerIzins->toArray())."\n";