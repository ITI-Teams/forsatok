<?php

namespace Tests\Feature\Api;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setupCandidate()
    {
        Role::create(['name' => 'candidate', 'guard_name' => 'web']);
        $candidate = User::factory()->create(['type' => 'candidate']);
        $candidate->assignRole('candidate');
        return $candidate;
    }

    protected function setupEmployer()
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer']);
        $employer->assignRole('employer');
        return $employer;
    }

    public function test_candidate_can_apply_to_job()
    {
        Notification::fake();
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        
        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Test Job',
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

        $response = $this->actingAs($candidate, 'sanctum')->postJson('/api/applications', [
            'job_post_id' => $job->id,
            'cover_letter' => 'I am the best candidate.',
            'resume' => UploadedFile::fake()->create('resume.pdf', 100),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('job_applications', [
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'pending'
        ]);
        Notification::assertSentTo($employer, \App\Notifications\JobApplicationReceived::class);
    }

    public function test_candidate_cannot_apply_twice_to_same_job()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $job = JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Test Job', 'category_id' => $category->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote',
        ]);

        JobApplication::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $job->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($candidate, 'sanctum')->postJson('/api/applications', [
            'job_post_id' => $job->id,
            'cover_letter' => 'Again...',
        ]);

        $response->assertStatus(422);
    }

    public function test_candidate_can_get_application_stats()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $job = JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Test Job', 'category_id' => $category->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote',
        ]);

        JobApplication::create(['candidate_id' => $candidate->id, 'job_post_id' => $job->id, 'status' => 'pending']);

        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/applications/stats');

        $response->assertStatus(200);
        $response->assertJsonFragment(['total' => 1, 'pending' => 1]);
    }

    public function test_candidate_can_list_own_applications()
    {
        $candidate = $this->setupCandidate();
        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/applications');

        $response->assertStatus(200);
    }

    public function test_candidate_can_view_single_application()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $job = JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Test Job', 'category_id' => $category->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote',
        ]);
        $app = JobApplication::create(['candidate_id' => $candidate->id, 'job_post_id' => $job->id, 'status' => 'pending']);

        $response = $this->actingAs($candidate, 'sanctum')->getJson("/api/applications/{$app->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $app->id]);
    }

    public function test_can_view_available_jobs()
    {
        $candidate = $this->setupCandidate();
        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/applications/available-jobs');

        $response->assertStatus(200);
    }
}
