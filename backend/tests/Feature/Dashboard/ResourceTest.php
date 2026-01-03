<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/dashboard/users');

        $response->assertStatus(200);
    }
    
    public function test_admin_can_list_roles()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/api/dashboard/roles');

        $response->assertStatus(200);
    }

    public function test_admin_can_list_permissions()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin']);
        $admin->assignRole('admin');
        
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->getJson('/api/dashboard/permissions');

        $response->assertStatus(200);
    }
}
