<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DummyDataSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SeederCekTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dummy_seeder_jalan_dan_karyawan_bisa_login(): void
    {
        // Kosongkan id/email bentrok (di-rollback) agar seeder benar-benar membuat data baru
        User::withTrashed()->whereNotNull('id_karyawan')->update(['id_karyawan' => \Illuminate\Support\Facades\DB::raw("concat('x', id)")]);
        User::withTrashed()->where('email', 'like', 'karyawan%@test.com')->update(['email' => null]);
        $this->seed(DummyDataSeeder::class);

        $u = User::where('id_karyawan', date('Y') . '001')->firstOrFail();
        $this->assertNotNull($u->dataKaryawan?->posisi);
        $this->assertSame($u->dataKaryawan->departemen_id, $u->dataKaryawan->posisi->departemen_id);

        $this->post('/login', ['identifier' => $u->id_karyawan, 'password' => 'password'])
            ->assertRedirect(route('peserta.dashboard'));
    }

    public function test_halaman_kandidat_dan_detail_hasil_render(): void
    {
        $admin = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $kandidat = User::where('tipe_akun', 'kandidat')->whereHas('profilKandidat')->firstOrFail();

        $this->actingAs($admin)->get('/admin/data-kandidat')->assertOk();
        $this->actingAs($admin)->get("/admin/data-kandidat/{$kandidat->id}/ubah")->assertOk();
        $this->actingAs($admin)->get('/admin/hasil-tes')->assertOk();
    }
}
