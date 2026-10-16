<?php

namespace Tests\Feature;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\NormaKonversi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AlatTesHalamanTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
    }

    public function test_halaman_index_dimensi_norma_render_dan_tautan_lama_dialihkan(): void
    {
        $alat = AlatTes::firstOrFail();
        $this->actingAs($this->admin());

        $this->get('/admin/alat-tes')->assertOk()->assertSee($alat->nama);
        $this->get("/admin/alat-tes/{$alat->id}/dimensi")->assertOk();
        $this->get("/admin/alat-tes/{$alat->id}/norma")->assertOk();
        $this->get('/admin/alat-tes/tambah')->assertRedirect(route('admin.alat-tes.index', ['tambah' => 1]));
        $this->get("/admin/alat-tes/{$alat->id}/edit")->assertRedirect(route('admin.alat-tes.index', ['ubah' => $alat->id]));
    }

    public function test_matriks_norma_memuat_setiap_baris_tanpa_mengubah_nilai(): void
    {
        $this->actingAs($this->admin());

        foreach (NormaKonversi::whereIn('alat_tes_id', AlatTes::select('id'))->select('alat_tes_id', 'dimensi_id')->distinct()->get() as $pasangan) {
            $matriks = $this->get("/admin/alat-tes/{$pasangan->alat_tes_id}/norma?dimensi={$pasangan->dimensi_id}")
                ->assertOk()->viewData('matriks');
            $baris = NormaKonversi::where('alat_tes_id', $pasangan->alat_tes_id)->where('dimensi_id', $pasangan->dimensi_id)->get();

            $jumlahSel = collect($matriks)->sum(fn ($m) => collect($m['sel'])->sum(fn ($isi) => count($isi)));
            $this->assertSame($baris->count(), $jumlahSel, "dimensi {$pasangan->dimensi_id}");
            foreach ($baris as $n) {
                $this->assertEquals($n->skor_hasil, $matriks[$n->tahap]['sel'][$n->skor_mentah_min . '|' . $n->skor_mentah_max][$n->kelompok_segmen]);
            }
        }
    }

    public function test_deskripsi_alat_tes_tersimpan(): void
    {
        $alat = AlatTes::firstOrFail();

        $this->actingAs($this->admin())->put("/admin/alat-tes/{$alat->id}", [
            'kode' => $alat->kode, 'nama' => $alat->nama, 'deskripsi' => 'Deskripsi uji',
            'format_dasar' => $alat->format_dasar, 'pola_skoring' => $alat->pola_skoring,
            'durasi_total_menit' => $alat->durasi_total_menit, 'batas_waktu_per_soal_aktif' => 0, 'is_sensitif' => 0,
        ])->assertRedirect(route('admin.alat-tes.index'));

        $this->assertSame('Deskripsi uji', $alat->fresh()->deskripsi);
    }

    public function test_dimensi_baru_tanpa_urutan_ditaruh_paling_akhir(): void
    {
        $alat = AlatTes::firstOrFail();
        $maks = (int) DimensiAlatTes::where('alat_tes_id', $alat->id)->max('urutan');

        $this->actingAs($this->admin())->post("/admin/alat-tes/{$alat->id}/dimensi", [
            'kode_dimensi' => 'UJI', 'nama_dimensi' => 'Dimensi Uji', 'tipe_kategori' => 'psikogram',
            'arah_skor' => 'tinggi_baik', 'urutan' => '',
        ])->assertRedirect(route('admin.alat-tes.dimensi', $alat->id));

        $this->assertSame($maks + 1, (int) DimensiAlatTes::where('alat_tes_id', $alat->id)->where('kode_dimensi', 'UJI')->value('urutan'));
    }
}
