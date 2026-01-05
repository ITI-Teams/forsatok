<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setupAdmin()
    {
        Mail::fake();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        return $admin;
    }

    public function test_admin_can_approve_employer()
    {
        $admin = $this->setupAdmin();
        $employer = User::factory()->create(['type' => 'employer', 'status' => 'pending']);

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/users/{$employer->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $employer->id, 'status' => 'approved']);
        Mail::assertQueued(\App\Mail\AccountApproved::class);
    }

    public function test_admin_can_reject_user()
    {
        $admin = $this->setupAdmin();
        $employer = User::factory()->create(['type' => 'employer', 'status' => 'pending']);

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/users/{$employer->id}/reject", [
            'reason' => 'Missing documentation for your company.'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $employer->id]);
        $this->assertDatabaseHas('rejected_users', ['email' => $employer->email]);
        Mail::assertQueued(\App\Mail\AccountRejected::class);
    }

    public function test_admin_can_ban_and_unban_user()
    {
        $admin = $this->setupAdmin();
        $user = User::factory()->create(['type' => 'candidate', 'status' => 'approved']);

        // Ban
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/users/{$user->id}/ban", [
            'reason' => 'Violating terms of service.'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'banned']);
        Mail::assertQueued(\App\Mail\AccountBanned::class);

        // Unban
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/users/{$user->id}/unban");
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'approved']);
    }

    public function test_admin_can_soft_delete_and_restore_user()
    {
        $admin = $this->setupAdmin();
        $user = User::factory()->create(['type' => 'candidate']);

        // Delete
        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/users/{$user->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted($user);

        // Restore
        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/users/{$user->id}/restore");
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    public function test_admin_can_force_delete_user()
    {
        $admin = $this->setupAdmin();
        $user = User::factory()->create(['type' => 'candidate']);
        $user->delete();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/users/{$user->id}/force");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_update_user()
    {
        $admin = $this->setupAdmin();
        $user = User::factory()->create(['type' => 'candidate', 'name' => 'Old Name']);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/dashboard/users/{$user->id}", [
            'name' => 'New Name',
            'email' => $user->email,
            'type' => 'candidate',
            'status' => 'approved'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_admin_can_list_rejected_users()
    {
        $admin = $this->setupAdmin();
        \Illuminate\Support\Facades\DB::table('rejected_users')->insert([
            'email' => 'rejected@test.com',
            'name' => 'Rejected Guy',
            'type' => 'employer',
            'rejection_reason' => 'Bad docs',
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/users/rejected');

        $response->assertStatus(200);
        $response->assertJsonFragment(['email' => 'rejected@test.com']);
    }

    public function test_admin_can_view_rejection_history()
    {
        $admin = $this->setupAdmin();
        \Illuminate\Support\Facades\DB::table('rejected_users')->insert([
            'email' => 'rejected@test.com',
            'name' => 'Rejected Guy',
            'type' => 'employer',
            'rejection_reason' => 'Bad docs',
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/users/rejection-history/rejected@test.com');

        $response->assertStatus(200);
        $response->assertJsonFragment(['rejection_reason' => 'Bad docs']);
    }

    public function test_admin_can_view_user_status_history()
    {
        $admin = $this->setupAdmin();
        $user = User::factory()->create(['type' => 'candidate']);
        
        \Illuminate\Support\Facades\DB::table('user_status_history')->insert([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'action' => 'banned',
            'reason' => 'Spam',
            'actioned_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/dashboard/users/{$user->id}/status-history");

        $response->assertStatus(200);
        $response->assertJsonFragment(['action' => 'banned', 'reason' => 'Spam']);
    }
}
