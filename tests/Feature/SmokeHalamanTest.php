<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SmokeHalamanTest extends TestCase
{
    // Semua route GET tanpa parameter harus render tanpa error 5xx
    public function test_semua_halaman_admin_render_tanpa_error(): void
    {
        $admin = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $gagal = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! in_array('GET', $route->methods()) || str_contains($uri, '{') || ! str_starts_with($uri, 'admin/') || str_ends_with($uri, '/login')) {
                continue;
            }
            $status = $this->actingAs($admin)->get('/' . $uri)->getStatusCode();
            if ($status >= 500) {
                $gagal[] = "$uri → $status";
            }
        }

        $this->assertSame([], $gagal);
    }

    public function test_halaman_portal_peserta_render_dengan_sidebar_yang_sama(): void
    {
        $peserta = User::where('tipe_akun', 'kandidat')->where('status', 'aktif')->firstOrFail();

        foreach (['/peserta/dashboard', '/peserta/panduan', '/profil'] as $uri) {
            $this->actingAs($peserta)->get($uri)
                ->assertOk()
                ->assertSee('Portal Peserta')
                ->assertSee('class="sb-item', false);
        }
    }
}
