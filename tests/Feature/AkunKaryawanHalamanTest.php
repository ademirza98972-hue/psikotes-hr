<?php

namespace Tests\Feature;

use App\Models\DataKaryawan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AkunKaryawanHalamanTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_menampilkan_ringkasan_dan_import(): void
    {
        $admin = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $res = $this->actingAs($admin)->get('/admin/akun-karyawan')->assertOk();

        $ringkasan = $res->viewData('ringkasan');
        $karyawan = User::where('tipe_akun', 'karyawan');
        $this->assertSame((clone $karyawan)->count(), (int) $ringkasan->total);
        $this->assertSame((clone $karyawan)->where('status', 'aktif')->count(), (int) $ringkasan->aktif);
        $res->assertSee(route('admin.akun-karyawan.import'))->assertSee(route('admin.akun-karyawan.template'));
    }

    public function test_tambah_gagal_validasi_membuka_ulang_form_dengan_isian(): void
    {
        $admin = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $ada = DataKaryawan::with('user')->whereHas('user')->firstOrFail();

        $this->actingAs($admin)->from('/admin/akun-karyawan')
            ->post('/admin/akun-karyawan', ['_form' => 'tambah', 'nama_karyawan' => 'Uji Duplikat',
                'id_karyawan' => $ada->user->id_karyawan, 'nik_karyawan' => 'UJI-' . uniqid(), 'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2000-01-01', 'departemen_id' => $ada->departemen_id, 'posisi_id' => $ada->posisi_id])
            ->assertRedirect('/admin/akun-karyawan')
            ->assertSessionHasErrors('id_karyawan');

        // halaman berikutnya membawa isian lama ke Alpine agar modal terbuka lagi
        $this->actingAs($admin)->get('/admin/akun-karyawan')->assertOk()->assertSee('Uji Duplikat');
    }
}
