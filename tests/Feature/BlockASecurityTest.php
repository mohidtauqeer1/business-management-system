<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

use PHPUnit\Framework\Attributes\Test;

class BlockASecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_and_logout_flow()
    {
        $user = User::create([
            'name' => 'Active Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Login submit
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/login');
        $this->assertGuest();
    }

    #[Test]
    public function inactive_user_cannot_login()
    {
        User::create([
            'name' => 'Inactive Staff',
            'email' => 'inactive@test.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
            'status' => 'inactive',
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function role_based_access_control_and_403_protection()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        $cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
            'status' => 'active',
        ]);

        // Cashier attempts Admin-only endpoints -> 403
        $this->actingAs($cashier);
        $this->get('/users')->assertStatus(403);
        $this->get('/settings')->assertStatus(403);
        $this->get('/activity-logs')->assertStatus(403);
        $this->get('/expenses')->assertStatus(403);
        $this->get('/reports/profit')->assertStatus(403);

        // Manager attempts Admin-only endpoints -> 403
        $this->actingAs($manager);
        $this->get('/users')->assertStatus(403);
        $this->get('/settings')->assertStatus(403);
        $this->get('/activity-logs')->assertStatus(403);

        // Admin accesses Admin-only endpoints -> 200
        $this->actingAs($admin);
        $this->get('/users')->assertStatus(200);
        $this->get('/settings')->assertStatus(200);
        $this->get('/activity-logs')->assertStatus(200);
    }
}
