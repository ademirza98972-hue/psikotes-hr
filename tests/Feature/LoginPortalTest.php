<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginPortalTest extends TestCase
{
    use DatabaseTransactions;

    private function superAdmin(): User
    {
        $user = User::where('email', 'superadmin@psikotes-hr.test')->firstOrFail();
        $user->update(['password' => Hash::make('rahasia-tes')]); // di-rollback

        return $user;
    }

    public function test_super_admin_tidak_bisa_login_lewat_portal_peserta(): void
    {
        $this->post('/login', [
            'identifier' => $this->superAdmin()->email,
            'password'   => 'rahasia-tes',
        ])->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_login_admin_abaikan_intended_peserta(): void
    {
        $this->withSession(['url.intended' => url('/peserta/dashboard')])
            ->post('/admin/login', [
                'email'    => $this->superAdmin()->email,
                'password' => 'rahasia-tes',
            ])->assertRedirect(route('admin.dashboard'));
    }
}
