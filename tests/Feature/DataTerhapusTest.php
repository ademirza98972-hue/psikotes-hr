<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DataTerhapusTest extends TestCase
{
    private function findSuperAdmin(): ?User
    {
        return User::where('email', 'superadmin@psikotes-hr.test')->first();
    }

    public function test_trash_page_loads_for_super_admin(): void
    {
        $user = $this->findSuperAdmin();
        $this->assertNotNull($user, 'Super Admin user not found.');

        $response = $this->actingAs($user)->get('/admin/data-terhapus');
        $response->assertStatus(200)
            ->assertSee('Data Terhapus')
            ->assertSee('Akun Karyawan')
            ->assertSee('Akun Kandidat');
    }

    public function test_departemen_tab_returns_200(): void
    {
        $user = $this->findSuperAdmin();
        $this->assertNotNull($user, 'Super Admin user not found.');

        // Ensure at least one soft-deleted departemen exists for valid testing
        $deletedCount = \App\Models\Departemen::onlyTrashed()->count();
        if ($deletedCount === 0) {
            $d = \App\Models\Departemen::create(['nama_departemen' => 'TEST_TRASH_DEPT']);
            $d->delete();
        }

        $response = $this->actingAs($user)->get('/admin/data-terhapus?jenis=departemen');
        $response->assertStatus(200);
    }

    public function test_trash_page_renders_all_7_tabs_in_sidebar(): void
    {
        $user = $this->findSuperAdmin();
        $this->assertNotNull($user, 'Super Admin user not found.');

        $response = $this->actingAs($user)->get('/admin/data-terhapus');
        $response->assertStatus(200);

        $tabLabels = [
            'Akun Karyawan',
            'Akun Kandidat',
            'Akun Admin/Staff',
            'Data Karyawan',
            'Departemen',
            'Posisi',
            'Peran',
        ];

        foreach ($tabLabels as $label) {
            $response->assertSee($label);
        }
    }

    public function test_trash_page_partial_tables_render_without_view_errors(): void
    {
        $user = $this->findSuperAdmin();
        $this->assertNotNull($user, 'Super Admin user not found.');

        $jenisUrls = [
            '/admin/data-terhapus?jenis=karyawan',
            '/admin/data-terhapus?jenis=kandidat',
            '/admin/data-terhapus?jenis=admin',
            '/admin/data-terhapus?jenis=data_karyawan',
            '/admin/data-terhapus?jenis=departemen',
            '/admin/data-terhapus?jenis=posisi',
            '/admin/data-terhapus?jenis=peran',
        ];

        foreach ($jenisUrls as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertStatus(200, "Failed loading $url");
        }
    }

    public function test_non_authenticated_gets_redirect(): void
    {
        $response = $this->get('/admin/data-terhapus');
        $response->assertRedirect('/login');
    }
}
