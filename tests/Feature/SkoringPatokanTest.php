<?php

namespace Tests\Feature;

use Database\Seeders\EppsSoalSeeder;
use Database\Seeders\ISTSeeder;
use Tests\TestCase;

/** Menjaga kunci & ambang skoring tetap sesuai file patokan (4 ALAT TES.zip). */
class SkoringPatokanTest extends TestCase
{
    public function test_kunci_epps_seimbang_seperti_epps_xls(): void
    {
        $hitung = [];
        foreach (EppsSoalSeeder::KUNCI as [$a, $b]) {
            foreach (array_filter([$a, $b]) as $kebutuhan) {
                $hitung[$kebutuhan] = ($hitung[$kebutuhan] ?? 0) + 1;
            }
        }

        $this->assertCount(225, EppsSoalSeeder::KUNCI);
        $this->assertCount(15, $hitung);
        $this->assertSame(array_fill_keys(array_keys($hitung), 28), $hitung); // 15 × 28 = 420 = 210 soal × 2 opsi
        $this->assertSame(['def', 'ach'], EppsSoalSeeder::KUNCI[2]);
        $this->assertSame([null, null], EppsSoalSeeder::KUNCI[1]);
        $this->assertCount(15, EppsSoalSeeder::PASANGAN_KONSISTENSI);
    }

    public function test_ambang_ist_dan_papi(): void
    {
        $this->assertSame(148.0, (float) ISTSeeder::NORMA_SW_KE_IQ[132]);
        $this->assertSame('Average (Rata-rata)', klasifikasi_iq(110));
        $this->assertSame('High Average (Di Atas Rata-rata)', klasifikasi_iq(110.5));
        $this->assertSame('Genius', klasifikasi_iq(140));
        $this->assertSame('Mentally Defective', klasifikasi_iq(65));

        $this->assertSame('SR', kategori_subtes_ist(80));
        $this->assertSame('R', kategori_subtes_ist(94));
        $this->assertSame('C', kategori_subtes_ist(104));
        $this->assertSame('T', kategori_subtes_ist(118));
        $this->assertSame('ST', kategori_subtes_ist(119));

        $this->assertSame('Mudah menyesuaikan diri', interpretasi_papi('Z', 6));
        $this->assertSame('Kemauan bekerja keras tinggi', interpretasi_papi('G', 5));
    }
}
