<?php

namespace Tests\Feature;

use App\Models\AlatTes;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PenjadwalanTesHalamanTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
    }

    public function test_index_detail_render_dan_tautan_lama_dialihkan(): void
    {
        $this->actingAs($this->admin());
        $sesi = SesiTes::firstOrFail();

        $this->get('/admin/penjadwalan-tes')->assertOk();
        foreach (['Aktif', 'Belum Dimulai', 'Draft', 'Selesai'] as $status) {
            $daftar = $this->get('/admin/penjadwalan-tes?per_page=100&status=' . urlencode($status))->assertOk()->viewData('penjadwalan');
            $this->assertTrue($daftar->every(fn ($s) => $s->status_display === $status), $status);
        }
        $this->get("/admin/penjadwalan-tes/{$sesi->id}")->assertOk()->assertSee($sesi->nama_sesi);
        $this->get('/admin/penjadwalan-tes/tambah')->assertRedirect(route('admin.penjadwalan-tes.index', ['tambah' => 1]));
        $this->get("/admin/penjadwalan-tes/{$sesi->id}/edit")->assertRedirect(route('admin.penjadwalan-tes.detail', ['id' => $sesi->id, 'ubah' => 1]));
    }

    public function test_calon_peserta_hanya_akun_aktif(): void
    {
        $this->actingAs($this->admin());
        $sesi = SesiTes::firstOrFail();

        $calon = $this->get("/admin/penjadwalan-tes/{$sesi->id}")->viewData('daftarUser');
        $this->assertNotEmpty($calon);
        $this->assertSame(0, User::whereIn('id', $calon->pluck('id'))->where('status', '!=', 'aktif')->count());
    }

    public function test_tambah_dan_keluarkan_peserta_menyinkronkan_jumlah(): void
    {
        $this->actingAs($this->admin());
        $alat = AlatTes::firstOrFail();
        $user = User::where('tipe_akun', 'karyawan')->where('status', 'aktif')->firstOrFail();
        $sesi = SesiTes::create(['nama_sesi' => 'Uji', 'tanggal_mulai' => now(), 'tanggal_selesai' => now(), 'status' => 'Draft', 'jumlah_peserta' => 99]);
        $sesi->alatTes()->sync([$alat->id]);

        $this->post("/admin/penjadwalan-tes/{$sesi->id}/peserta", ['user_ids' => [$user->id], 'alat_tes_ids' => [$alat->id]])->assertSessionHasNoErrors();
        $this->assertSame(1, $sesi->fresh()->jumlah_peserta);

        // alat tes di luar sesi ditolak
        $lain = AlatTes::where('id', '!=', $alat->id)->firstOrFail();
        $this->post("/admin/penjadwalan-tes/{$sesi->id}/peserta", ['user_ids' => [$user->id], 'alat_tes_ids' => [$lain->id]])->assertSessionHasErrors('alat_tes_ids');

        $this->delete("/admin/penjadwalan-tes/{$sesi->id}/peserta/{$user->id}")->assertRedirect();
        $this->assertSame(0, $sesi->fresh()->jumlah_peserta);
    }

    public function test_peserta_yang_sudah_selesai_tidak_bisa_dikeluarkan(): void
    {
        $this->actingAs($this->admin());
        $peserta = PesertaSesiTes::where('status_pengerjaan', 'Selesai')->firstOrFail();

        $this->delete("/admin/penjadwalan-tes/{$peserta->sesi_tes_id}/peserta/{$peserta->user_id}")->assertSessionHas('error');
        $this->assertModelExists($peserta);
    }

    public function test_ubah_sesi_wajib_alat_tes(): void
    {
        $this->actingAs($this->admin());
        $sesi = SesiTes::firstOrFail();

        $this->put("/admin/penjadwalan-tes/{$sesi->id}", [
            'nama_sesi' => $sesi->nama_sesi, 'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => '2026-01-02', 'status' => 'Draft',
        ])->assertSessionHasErrors('alat_tes_ids');
    }
}
