<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_view_login_form()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.admin-login');
    }

    #[Test]
    public function admin_can_login_with_valid_credentials()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    #[Test]
    public function admin_cannot_login_with_invalid_credentials()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    #[Test]
    public function authenticated_admins_are_redirected_from_login_page()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/login');

        $response->assertRedirect('/admin/dashboard');
    }
}