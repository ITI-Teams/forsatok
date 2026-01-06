<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDetailAndSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_details()
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('admin');
        
        $user = User::factory()->create(['name' => 'Target User']);

        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/dashboard/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Target User');
    }

    public function test_admin_can_search_users_by_role()
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('admin');

        $role = Role::create(['name' => 'special-role', 'guard_name' => 'web']);
        $user = User::factory()->create(['name' => 'Role Search User']);
        $user->assignRole($role);

        // Search for 'special-role'
        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/dashboard/users?search=special-role");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Role Search User']);
    }

    public function test_admin_can_search_users_by_type()
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('admin');

        User::factory()->create(['name' => 'Employer User', 'type' => 'employer']);

        // Search for 'employer'
        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/dashboard/users?search=employer");

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Employer User']);
    }
}
