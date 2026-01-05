<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_job()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $permission = Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'IT']);
        $employer = User::factory()->create(['type' => 'employer']);
        
        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Pending Job',
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'pending',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/dashboard/jobs/' . $job->id . '/approve');

        $response->assertStatus(200);
        $this->assertDatabaseHas('job_posts', ['id' => $job->id, 'status' => 'approved']);
    }

    public function test_admin_can_reject_job()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']));
        
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'IT']);
        $job = JobPost::create([
            'employer_id' => User::factory()->create(['type' => 'employer'])->id,
            'title' => 'Job to Reject',
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'pending',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/jobs/{$job->id}/reject", [
            'reason' => 'Invalid content'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('job_posts', ['id' => $job->id, 'status' => 'rejected']);
    }

    public function test_admin_can_list_all_jobs()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.view', 'guard_name' => 'web']));
        
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/jobs');

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_job()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']));
        
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $job = JobPost::create([
            'employer_id' => User::factory()->create(['type' => 'employer'])->id,
            'title' => 'To Delete',
            'category_id' => Category::create(['name' => 'IT'])->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/jobs/{$job->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($job);
    }

    public function test_admin_can_show_job()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.view', 'guard_name' => 'web']));
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $job = JobPost::create([
            'employer_id' => User::factory()->create(['type' => 'employer'])->id,
            'title' => 'Job Details',
            'category_id' => Category::create(['name' => 'IT'])->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/dashboard/jobs/{$job->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Job Details']);
    }

    public function test_admin_can_list_trashed_jobs()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.view', 'guard_name' => 'web']));
        $role->givePermissionTo(Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']));
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $job = JobPost::create([
            'employer_id' => User::factory()->create(['type' => 'employer'])->id,
            'title' => 'Trashed Job',
            'category_id' => Category::create(['name' => 'IT'])->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);
        $job->delete();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/jobs/trashed');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Trashed Job']);
    }

    public function test_admin_can_restore_job()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']));
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $job = JobPost::create([
            'employer_id' => User::factory()->create(['type' => 'employer'])->id,
            'title' => 'To Restore',
            'category_id' => Category::create(['name' => 'IT'])->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);
        $job->delete();

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/jobs/{$job->id}/restore");

        $response->assertStatus(200);
        $this->assertDatabaseHas('job_posts', ['id' => $job->id, 'deleted_at' => null]);
    }

    public function test_admin_can_force_delete_job()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']));
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $job = JobPost::create([
            'employer_id' => User::factory()->create(['type' => 'employer'])->id,
            'title' => 'To Force Delete',
            'category_id' => Category::create(['name' => 'IT'])->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
        ]);
        $job->delete();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/jobs/{$job->id}/force");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('job_posts', ['id' => $job->id]);
    }
}
