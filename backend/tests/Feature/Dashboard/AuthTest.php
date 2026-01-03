<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_register()
    {
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $response = $this->postJson('/api/dashboard/auth/register', [
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'type' => 'admin'
        ]);

        $response->assertStatus(201); // or 200
    }

    public function test_admin_can_login()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'status' => 'approved'
        ]);
        $user->assignRole($role);

        $response = $this->postJson('/api/dashboard/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['token']]);
    }
}
