<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_available_and_dashboard_requires_authentication(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSeeText('Welcome back.');

        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_seeded_administrator_can_log_in_and_log_out(): void
    {
        $this->seed();

        $this->post('/admin/login', [
            'username' => 'admin',
            'password' => '7#iK0x31bo',
        ])->assertRedirect('/admin');

        $this->assertAuthenticated();
        $this->get('/admin')
            ->assertOk()
            ->assertSee('CONTENT OVERVIEW')
            ->assertSee('Page settings');

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'password' => 'correct-password',
            'is_admin' => true,
        ]);

        $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_non_admin_user_cannot_use_the_admin_login(): void
    {
        User::factory()->create([
            'username' => 'editor',
            'password' => 'password',
            'is_admin' => false,
        ]);

        $this->post('/admin/login', [
            'username' => 'editor',
            'password' => 'password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }
}
