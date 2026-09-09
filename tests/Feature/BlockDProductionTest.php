<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

use PHPUnit\Framework\Attributes\Test;

class BlockDProductionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dashboard_and_reports_routes_return_200_ok()
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'ui_admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin);

        // Dashboard
        $this->get('/dashboard')->assertStatus(200);

        // Reports
        $this->get('/reports/sales')->assertStatus(200);
        $this->get('/reports/purchases')->assertStatus(200);
        $this->get('/reports/inventory')->assertStatus(200);
        $this->get('/reports/profit')->assertStatus(200);

        // Activity Logs
        $this->get('/activity-logs')->assertStatus(200);
    }
}
