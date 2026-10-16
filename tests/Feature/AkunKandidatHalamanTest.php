<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AkunKandidatHalamanTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
    }

    public function test_ringkasan_status_sesuai_data(): void
    {
        $ringkasan = $this->actingAs($this->admin())->get('/admin/data-kandidat')->assertOk()->viewData('ringkasan');
        $kandidat = User::where('tipe_akun', 'kandidat');

        $this->assertSame((clone $kandidat)->count(), (int) $ringkasan->total);
        $this->assertSame((clone $kandidat)->where('status', 'menunggu_verifikasi')->count(), (int) $ringkasan->menunggu);
        $this->assertSame((clone $kandidat)->where('status', 'ditolak')->count(), (int) $ringkasan->ditolak);
    }

    public function test_tautan_tambah_lama_membuka_modal_di_index(): void
    {
        $this->actingAs($this->admin())->get('/admin/data-kandidat/tambah')
            ->assertRedirect(route('admin.data-kandidat.index', ['tambah' => 1]));
        $this->assertTrue($this->actingAs($this->admin())->get('/admin/data-kandidat?tambah=1')->assertOk()->viewData('bukaTambah'));
    }

    public function test_tambah_gagal_validasi_membawa_isian_kembali(): void
    {
        $ada = User::where('tipe_akun', 'kandidat')->whereHas('profilKandidat.posisi')->with('profilKandidat.posisi')->firstOrFail();

        $this->actingAs($this->admin())->from('/admin/data-kandidat')
            ->post('/admin/data-kandidat', ['_form' => 'tambah', 'nama_kandidat' => 'Uji Kandidat Duplikat', 'email' => $ada->email,
                'nik_kandidat' => '1234567890123456', 'no_hp' => '0811', 'jenis_kelamin' => 'P', 'pendidikan_terakhir' => 'S1',
                'departemen' => $ada->profilKandidat->posisi->departemen_id, 'posisi_dilamar' => $ada->profilKandidat->posisi_id,
                'password' => 'rahasia123', 'password_confirmation' => 'beda12345'])
            ->assertRedirect('/admin/data-kandidat')
            ->assertSessionHasErrors(['email', 'password']);

        $this->actingAs($this->admin())->get('/admin/data-kandidat')->assertOk()->assertSee('Uji Kandidat Duplikat');
    }
}
