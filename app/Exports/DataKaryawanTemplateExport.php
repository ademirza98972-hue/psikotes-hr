<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataKaryawanTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return ['NIK', 'Nama Karyawan', 'Jenis Kelamin', 'Departemen', 'Jabatan'];
    }

    public function array(): array
    {
        return [
            ['1234567890123456', 'Budi Santoso', 'L', 'IT', 'Backend Developer'],
            ['6543210987654321', 'Siti Rahayu', 'P', 'HR', 'HR Specialist'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 30,
            'C' => 16,
            'D' => 24,
            'E' => 28,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2C5F6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Sample rows: lighter background
        $sheet->getStyle('A2:E3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F7F9']],
        ]);

        // Note row below sample
        $sheet->setCellValue('A5', 'Catatan:');
        $sheet->setCellValue('A6', '• Kolom NIK, Nama Karyawan, dan Departemen wajib diisi.');
        $sheet->setCellValue('A7', '• Jenis Kelamin: isi L (Laki-laki) atau P (Perempuan).');
        $sheet->setCellValue('A8', '• Departemen/Jabatan baru akan otomatis dibuat di sistem.');
        $sheet->setCellValue('A9', '• Hapus baris contoh (baris 2-3) sebelum mengimpor.');

        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF2C5F6F']],
        ]);

        $sheet->getStyle('A6:A9')->applyFromArray([
            'font' => ['color' => ['argb' => 'FF555555'], 'italic' => true, 'size' => 10],
        ]);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(false);
        }

        return [];
    }
}
