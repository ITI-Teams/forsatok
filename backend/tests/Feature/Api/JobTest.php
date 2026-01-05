<?php

namespace Tests\Feature\Api;

use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_jobs()
    {
        $category = Category::create(['name' => 'IT']);
        $employer = User::factory()->create(['type' => 'employer']);
        
        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Software Engineer',
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Test Description',
            'responsibilities' => 'Test Responsibilities',
            'qualification' => 'Test Qualifications',
            'benefits' => 'Test Benefits',
            'experience' => '1-3 years',
            // Add other required fields if any, based on database constraints
            // Checking model fillable: 'experience', 'location', 'salary_min', ... 
            // Assuming nullable or provide defaults if strict mode.
        ]);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/jobs');

        $response->assertStatus(200);
    }

    public function test_can_show_job()
    {
        $category = Category::create(['name' => 'IT']);
        $employer = User::factory()->create(['type' => 'employer']);
        
        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Software Engineer',
            'category_id' => $category->id,
            'is_active' => true,
            'status' => 'approved',
            'deadline' => now()->addDays(10),
            'description' => 'Test Description',
            'responsibilities' => 'Test Responsibilities',
            'qualification' => 'Test Qualifications',
            'benefits' => 'Test Benefits',
            'experience' => '1-3 years',
            'work_type' => 'full-time',
            'work_place' => 'remote',
        ]);

        $response = $this->getJson('/api/jobs/' . $job->id);

        $response->assertStatus(200);
    }

    public function test_jobs_can_be_filtered_by_category()
    {
        $cat1 = Category::create(['name' => 'IT']);
        $cat2 = Category::create(['name' => 'Marketing']);
        $employer = User::factory()->create(['type' => 'employer']);

        JobPost::create([
            'employer_id' => $employer->id, 'title' => 'IT Job', 'category_id' => $cat1->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote'
        ]);

        JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Marketing Job', 'category_id' => $cat2->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->getJson("/api/jobs?category_id={$cat1->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.data');
        $response->assertJsonFragment(['title' => 'IT Job']);
    }

    public function test_jobs_can_be_searched_by_keyword()
    {
        $cat = Category::create(['name' => 'IT']);
        $employer = User::factory()->create(['type' => 'employer']);

        JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Laravel Developer', 'category_id' => $cat->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote'
        ]);

        JobPost::create([
            'employer_id' => $employer->id, 'title' => 'React Developer', 'category_id' => $cat->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->getJson("/api/jobs?search=Laravel");

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Laravel Developer']);
        $response->assertJsonMissing(['title' => 'React Developer']);
    }

    public function test_candidate_does_not_see_already_applied_jobs()
    {
        $cat = Category::create(['name' => 'IT']);
        $employer = User::factory()->create(['type' => 'employer']);
        $candidate = User::factory()->create(['type' => 'candidate']);

        $job1 = JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Applied Job', 'category_id' => $cat->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote'
        ]);

        $job2 = JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Available Job', 'category_id' => $cat->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote'
        ]);

        \App\Domains\Applications\Models\JobApplication::create([
            'candidate_id' => $candidate->id,
            'job_post_id' => $job1->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/jobs');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Available Job']);
        $response->assertJsonMissing(['title' => 'Applied Job']);
    }

    public function test_can_get_job_filter_options()
    {
        $response = $this->getJson('/api/jobs/filter-options');
        $response->assertStatus(200);
    }

    public function test_candidate_can_list_saved_jobs()
    {
        $candidate = User::factory()->create(['type' => 'candidate', 'status' => 'approved']);
        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/jobs/saved');

        $response->assertStatus(200);
    }
}
