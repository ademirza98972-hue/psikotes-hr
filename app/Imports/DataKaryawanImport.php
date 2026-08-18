<?php

namespace App\Imports;

use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Posisi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DataKaryawanImport implements ToCollection, WithHeadingRow
{
    public array $duplikat = [];
    public int $berhasil = 0;
    public int $dilewati = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nik = trim((string) ($row['nik'] ?? ''));
            $nama = trim((string) ($row['nama_karyawan'] ?? ''));
            $namaDept = trim((string) ($row['departemen'] ?? ''));

            if ($nik === '' || $nama === '' || $namaDept === '') {
                $this->dilewati++;
                continue;
            }

            if (DataKaryawan::where('nik_karyawan', $nik)->exists()) {
                $this->duplikat[] = $nik;
                continue;
            }

            $jenisKelamin = $this->normalizeJenisKelamin($row['jenis_kelamin'] ?? null);
            $namaJabatan = trim((string) ($row['jabatan'] ?? ''));

            $departemen = Departemen::firstOrCreate(
                ['nama_departemen' => $namaDept]
            );

            $posisi = null;
            if ($namaJabatan !== '') {
                $posisi = Posisi::firstOrCreate(
                    ['nama_posisi' => $namaJabatan, 'departemen_id' => $departemen->id]
                );
            }

            DataKaryawan::create([
                'nik_karyawan'  => $nik,
                'nama_karyawan' => $nama,
                'jenis_kelamin' => $jenisKelamin,
                'departemen'    => $namaDept,
                'jabatan'       => $namaJabatan ?: null,
                'departemen_id' => $departemen->id,
                'posisi_id'     => $posisi?->id,
                'status'        => 'belum_terpakai',
            ]);

            $this->berhasil++;
        }
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
