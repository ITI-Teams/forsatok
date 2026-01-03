<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setupEmployer()
    {
        $role = Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer', 'status' => 'approved']);
        $employer->assignRole($role);
        return $employer;
    }

    protected function createJob($employerId)
    {
        $category = Category::create(['name' => 'IT']);
        return JobPost::create([
            'employer_id' => $employerId,
            'title' => 'Job Title',
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Desc',
            'responsibilities' => 'Resp',
            'qualification' => 'Quals',
            'benefits' => 'Benefits',
            'experience' => '1-3 years',
            'work_type' => 'full-time',
            'work_place' => 'remote',
        ]);
    }

    public function test_employer_can_list_applications_for_their_jobs()
    {
        $employer = $this->setupEmployer();
        $job = $this->createJob($employer->id);
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        JobApplication::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->getJson('/api/dashboard/applications');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_employer_can_view_single_application()
    {
        $employer = $this->setupEmployer();
        $job = $this->createJob($employer->id);
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $app = JobApplication::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->getJson("/api/dashboard/applications/{$app->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'pending']);
    }

    public function test_employer_can_update_application_status()
    {
        $employer = $this->setupEmployer();
        $job = $this->createJob($employer->id);
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $app = JobApplication::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->putJson("/api/dashboard/applications/{$app->id}", [
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'accepted'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('job_applications', [
            'id' => $app->id,
            'status' => 'accepted'
        ]);
    }

    public function test_employer_cannot_view_others_applications()
    {
        $employer1 = $this->setupEmployer();
        $employer2 = $this->setupEmployer();
        $job = $this->createJob($employer2->id);
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $app = JobApplication::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer1, 'sanctum')->getJson("/api/dashboard/applications/{$app->id}");

        $response->assertStatus(403);
    }
}
