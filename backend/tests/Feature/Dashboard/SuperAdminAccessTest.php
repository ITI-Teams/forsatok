<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_jobs_without_admin_role()
    {
        // 1. Create super-admin role and permissions
        $saRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::create(['name' => 'jobs.view', 'guard_name' => 'web']);
        $saRole->givePermissionTo('jobs.view');
        
        // 2. Create user and ONLY assign super-admin role
        $user = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $user->assignRole($saRole);

        // 3. Attempt to access jobs list
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/dashboard/jobs');

        // 4. Assert success
        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_contact_messages_without_admin_role()
    {
        // 1. Create super-admin role
        $saRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        
        // 2. Create user and ONLY assign super-admin role
        $user = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $user->assignRole($saRole);

        // 3. Attempt to access shared contact messages
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/dashboard/contact-messages');

        // 4. Assert success
        $response->assertStatus(200);
    }
}
