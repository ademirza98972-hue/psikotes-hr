<?php

namespace Tests\Feature;

use App\Models\AlatTes;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BankSoalHalamanTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
    }

    public function test_index_form_tambah_dan_ubah_render_untuk_setiap_tipe_soal(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin/bank-soal')->assertOk();

        foreach (AlatTes::all() as $alat) {
            $this->get("/admin/bank-soal?alat_tes_id={$alat->id}")->assertOk()->assertSee($alat->nama);
            $this->get("/admin/bank-soal/tambah/{$alat->id}")->assertOk();
        }
        foreach (Soal::whereIn('alat_tes_id', AlatTes::select('id'))->get()->unique('tipe_format') as $soal) {
            $this->get("/admin/bank-soal/{$soal->id}/edit")->assertOk();
        }

        $ist = AlatTes::where('format_dasar', 'Mixed')->firstOrFail();
        $daftar = $this->get("/admin/bank-soal?alat_tes_id={$ist->id}&subtes=GE")->viewData('daftarSoal');
        $this->assertEquals(range(61, 76), $daftar->pluck('nomor')->sort()->values()->all());
    }

    public function test_simpan_ulang_soal_ist_tanpa_perubahan_tidak_mengubah_data(): void
    {
        $this->actingAs($this->admin());
        $ist = AlatTes::where('format_dasar', 'Mixed')->firstOrFail();

        foreach (['isian_teks', 'isian_angka', 'pilihan_ganda'] as $tipe) {
            $soal = Soal::with('opsiJawaban')->where('alat_tes_id', $ist->id)->where('tipe_format', $tipe)->firstOrFail();
            $data = ['tipe_format' => $tipe, 'teks_soal' => $soal->teks_soal, 'kunci_jawaban' => $soal->kunci_jawaban];
            if ($tipe === 'pilihan_ganda') {
                $data['opsi'] = $soal->opsiJawaban->sortBy('urutan')->values()
                    ->mapWithKeys(fn ($o, $i) => [chr(97 + $i) => $o->teks_opsi])->all();
            }

            $this->put("/admin/bank-soal/{$soal->id}", $data)
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('admin.bank-soal.index', ['alat_tes_id' => $ist->id]));

            $baru = $soal->fresh('opsiJawaban');
            $this->assertSame($soal->teks_soal, $baru->teks_soal, "teks $tipe");
            $this->assertSame($soal->kunci_jawaban, $baru->kunci_jawaban, "kunci $tipe");
            $this->assertSame($soal->opsiJawaban->count(), $baru->opsiJawaban->count(), "opsi $tipe");
        }
    }

    public function test_hapus_soal(): void
    {
        $this->actingAs($this->admin());
        $soal = Soal::whereIn('alat_tes_id', AlatTes::select('id'))->firstOrFail();

        $this->delete("/admin/bank-soal/{$soal->id}")->assertRedirect();
        $this->assertSoftDeleted($soal);
    }
}
