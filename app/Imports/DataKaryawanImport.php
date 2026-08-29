<?php

namespace App\Imports;

use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataKaryawanImport implements ToCollection, WithHeadingRow
{
    public array $duplikat = [];
    public int   $berhasil = 0;
    public int   $dilewati = 0;

    private ?int $peranId = null;

    public function __construct()
    {
        $this->peranId = Peran::where('nama_peran', 'Karyawan')->value('id');
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nik        = trim((string) ($row['nik'] ?? ''));
            $nama       = trim((string) ($row['nama_karyawan'] ?? ''));
            $namaDept   = trim((string) ($row['departemen'] ?? ''));
            $namaJabatan = trim((string) ($row['jabatan'] ?? ''));
            $jenisKelamin = $this->normalizeJenisKelamin($row['jenis_kelamin'] ?? null);

            if ($nik === '' || $nama === '' || $namaDept === '' || $namaJabatan === '') {
                $this->dilewati++;
                continue;
            }

            if (DataKaryawan::where('nik_karyawan', $nik)->exists()) {
                $this->duplikat[] = $nik;
                continue;
            }

            DB::transaction(function () use ($nik, $nama, $namaDept, $namaJabatan, $jenisKelamin) {
                $departemen = Departemen::firstOrCreate(['nama_departemen' => $namaDept]);
                $posisi     = Posisi::firstOrCreate(['nama_posisi' => $namaJabatan, 'departemen_id' => $departemen->id]);

                $idKaryawan = $this->generateIdKaryawan();

                $user = User::create([
                    'name'                 => $nama,
                    'email'                => null,
                    'id_karyawan'          => $idKaryawan,
                    'password'             => Hash::make($idKaryawan),
                    'jenis_kelamin'        => $jenisKelamin,
                    'tipe_akun'            => 'karyawan',
                    'peran_id'             => $this->peranId,
                    'status'               => 'aktif',
                    'must_change_password' => true,
                ]);

                DataKaryawan::create([
                    'nik_karyawan'  => $nik,
                    'departemen_id' => $departemen->id,
                    'posisi_id'     => $posisi->id,
                    'user_id'       => $user->id,
                ]);
            });

            $this->berhasil++;
        }
    }

    private function generateIdKaryawan(): string
    {
        $year = date('Y');
        $last = User::where('id_karyawan', 'like', "{$year}%")
            ->orderBy('id_karyawan', 'desc')
            ->value('id_karyawan');

        $seq = $last ? (int) substr($last, 4) + 1 : 1;

        return $year . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    private function normalizeJenisKelamin(mixed $nilai): ?string
    {
        if ($nilai === null || trim((string) $nilai) === '') {
            return null;
        }

        $val = strtoupper(trim((string) $nilai));

        if (in_array($val, ['L', 'LAKI', 'LAKI-LAKI', 'LAKILAKI', 'MALE', 'M'], true)) {
            return 'L';
        }

        if (in_array($val, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE', 'F'], true)) {
            return 'P';
        }

        return null;
    }
}
