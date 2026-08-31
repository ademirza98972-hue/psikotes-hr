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
        return ['ID Karyawan', 'NIK', 'Nama Karyawan', 'Jenis Kelamin', 'Departemen', 'Jabatan'];
    }

    public function array(): array
    {
        return [
            ['KRY-001', '1234567890123456', 'Budi Santoso', 'L', 'IT', 'Backend Developer'],
            ['KRY-002', '6543210987654321', 'Siti Rahayu', 'P', 'HR', 'HR Specialist'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 22,
            'C' => 30,
            'D' => 16,
            'E' => 24,
            'F' => 28,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2C5F6F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Sample rows: lighter background
        $sheet->getStyle('A2:F3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F7F9']],
        ]);

        // Note row below sample
        $sheet->setCellValue('A5', 'Catatan:');
        $sheet->setCellValue('A6', '• Kolom ID Karyawan, NIK, Nama Karyawan, dan Departemen wajib diisi.');
        $sheet->setCellValue('A7', '• Jenis Kelamin: isi L (Laki-laki) atau P (Perempuan).');
        $sheet->setCellValue('A8', '• Departemen/Jabatan baru akan otomatis dibuat di sistem.');
        $sheet->setCellValue('A9', '• Hapus baris contoh (baris 2-3) sebelum mengimpor.');

        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF2C5F6F']],
        ]);

        $sheet->getStyle('A6:A9')->applyFromArray([
            'font' => ['color' => ['argb' => 'FF555555'], 'italic' => true, 'size' => 10],
        ]);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(false);
        }

        return [];
    }
}
