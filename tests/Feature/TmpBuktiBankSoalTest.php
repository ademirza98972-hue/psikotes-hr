<?php

namespace Tests\Feature;

use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TmpBuktiBankSoalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_bukti(): void
    {
        $admin = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $this->actingAs($admin);
        $hasil = [];

        foreach ([14 => 'EPPS', 239 => 'PAPI', 329 => 'KRP', 379 => 'IST pg', 439 => 'IST isian_teks', 455 => 'IST isian_angka', 495 => 'IST gambar'] as $id => $label) {
            $hasil["edit $label"] = $this->get("/admin/bank-soal/$id/edit")->status();
        }
        $hasil['index IST'] = $this->get('/admin/bank-soal?alat_tes_id=5')->status();

        // simpan ulang soal isian angka apa adanya, seperti form edit lama mengirimnya
        $s = Soal::find(455);
        $r = $this->from("/admin/bank-soal/455/edit")->put('/admin/bank-soal/455', [
            'tipe_format' => 'isian_angka', 'teks_soal' => $s->teks_soal, 'kunci_jawaban' => $s->kunci_jawaban,
        ]);
        $hasil['update isian_angka'] = $r->status() . ' ' . json_encode(session('errors')?->all());

        $s = Soal::find(439);
        $r = $this->from("/admin/bank-soal/439/edit")->put('/admin/bank-soal/439', [
            'tipe_format' => 'isian_teks', 'teks_soal' => $s->teks_soal, 'kunci_jawaban' => $s->kunci_jawaban,
        ]);
        $hasil['update isian_teks'] = $r->status() . ' ' . json_encode(session('errors')?->all());

        fwrite(STDERR, json_encode($hasil, JSON_PRETTY_PRINT));
        $this->assertTrue(true);
    }
}
