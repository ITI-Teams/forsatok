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
    
    public function test_super_admin_can_list_roles()
    {
        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->getJson('/api/dashboard/roles');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_list_permissions()
    {
        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('super-admin');
        
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->getJson('/api/dashboard/permissions');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_manage_role_permissions()
    {
        $role_sa = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role_sa);
        
        $role_test = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        $perm = Permission::create(['name' => 'test-perm', 'guard_name' => 'web']);

        // View
        $this->actingAs($admin)->getJson('/api/dashboard/role-permissions?role_id='.$role_test->id)->assertStatus(200);

        // Update
        $this->actingAs($admin)->postJson('/api/dashboard/role-permissions', [
            'role_id' => $role_test->id,
            'permissions' => ['test-perm']
        ])->assertStatus(200);

        $this->assertTrue($role_test->hasPermissionTo('test-perm'));
    }

    public function test_super_admin_can_manage_user_access()
    {
        $role_sa = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role_sa);
        
        $user = User::factory()->create();
        Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        Permission::create(['name' => 'test-perm', 'guard_name' => 'web']);

        // View
        $this->actingAs($admin)->getJson('/api/dashboard/user-access?user_id='.$user->id)->assertStatus(200);

        // Update
        $this->actingAs($admin)->postJson('/api/dashboard/user-access', [
            'user_id' => $user->id,
            'roles' => ['test-role'],
            'permissions' => ['test-perm']
        ])->assertStatus(200);

        $user->refresh();
        $this->assertTrue($user->hasRole('test-role'));
        $this->assertTrue($user->hasPermissionTo('test-perm'));
    }
}
