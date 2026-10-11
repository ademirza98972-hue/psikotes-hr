<?php

namespace Tests\Feature;

use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardAdminTest extends TestCase
{
    use DatabaseTransactions;

    private function statusDashboard(): array
    {
        $admin = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $res = $this->actingAs($admin)->get('/admin/dashboard')->assertOk();

        return collect(['pesertaTotal', 'pesertaSelesai', 'pesertaSedang', 'pesertaBelum', 'pesertaTidakSelesai'])
            ->mapWithKeys(fn ($k) => [$k => $res->viewData($k)])->all();
    }

    public function test_status_peserta_mengikuti_periode_sesi_dan_abaikan_draft(): void
    {
        $sebelum = $this->statusDashboard();
        $peserta = User::where('tipe_akun', 'kandidat')->firstOrFail();

        $buat = function (string $status, string $mulai, string $selesai, ?string $waktuMulai) use ($peserta) {
            $sesi = SesiTes::create(['nama_sesi' => 'TES_DASHBOARD', 'status' => $status, 'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $selesai, 'jumlah_peserta' => 1, 'jumlah_selesai' => 0]);
            PesertaSesiTes::create(['user_id' => $peserta->id, 'sesi_tes_id' => $sesi->id,
                'status_pengerjaan' => 'Belum Mengerjakan', 'waktu_mulai' => $waktuMulai]);
        };

        $buat('Aktif', today()->subDay(), today()->addDay(), now());       // sedang
        $buat('Aktif', today()->addDay(), today()->addDays(3), null);      // belum mulai (mendatang)
        $buat('Aktif', today()->subDays(5), today()->subDay(), now());     // tidak selesai (lewat tanggal)
        $buat('Selesai', today()->subDay(), today()->addDay(), null);      // tidak selesai (ditutup)
        $buat('Draft', today(), today()->addDay(), null);                  // diabaikan

        $sesudah = $this->statusDashboard();

        $this->assertSame($sebelum['pesertaTotal'] + 4, $sesudah['pesertaTotal']);
        $this->assertSame($sebelum['pesertaSelesai'], $sesudah['pesertaSelesai']);
        $this->assertSame($sebelum['pesertaSedang'] + 1, $sesudah['pesertaSedang']);
        $this->assertSame($sebelum['pesertaBelum'] + 1, $sesudah['pesertaBelum']);
        $this->assertSame($sebelum['pesertaTidakSelesai'] + 2, $sesudah['pesertaTidakSelesai']);
    }

    public function test_hr_viewer_tidak_melihat_tombol_verifikasi_kandidat(): void
    {
        $viewer = User::whereHas('peran', fn ($q) => $q->where('nama_peran', 'HR Viewer'))->firstOrFail();
        $kandidat = User::where('tipe_akun', 'kandidat')->firstOrFail();
        $kandidat->update(['status' => 'menunggu_verifikasi']);

        $this->assertFalse($viewer->hasIzin('pengguna.verifikasi'));
        $this->actingAs($viewer)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee($kandidat->name)
            ->assertDontSee(route('admin.data-kandidat.approve', $kandidat->id));
    }
}
