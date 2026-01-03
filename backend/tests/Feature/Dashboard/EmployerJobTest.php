<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmployerJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Notification::fake();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }

    protected function setupEmployer()
    {
        $role = Role::create(['name' => 'employer', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'jobs.manage', 'guard_name' => 'web']));
        $role->givePermissionTo(Permission::create(['name' => 'jobs.view', 'guard_name' => 'web']));
        
        $employer = User::factory()->create(['type' => 'employer', 'status' => 'approved']);
        $employer->assignRole($role);
        return $employer;
    }

    public function test_employer_can_create_job()
    {
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $country = \App\Domains\Location\Models\Country::create(['name' => 'Egypt']);
        $city = \App\Domains\Location\Models\City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        $response = $this->actingAs($employer, 'sanctum')->postJson('/api/dashboard/jobs', [
            'title' => 'New Awesome Job',
            'category_id' => $category->id,
            'description' => 'A great job description.',
            'responsibilities' => 'Do great things.',
            'qualification' => 'Be awesome.',
            'benefits' => 'Free coffee.',
            'experience' => '3+ years',
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'work_type' => 'full-time',
            'work_place' => 'remote',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'is_active' => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('job_posts', ['title' => 'New Awesome Job', 'employer_id' => $employer->id]);
    }

    public function test_employer_can_list_own_jobs()
    {
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $country = \App\Domains\Location\Models\Country::create(['name' => 'Egypt']);
        $city = \App\Domains\Location\Models\City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'My Job',
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
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($employer, 'sanctum')->getJson('/api/dashboard/jobs');

        $response->assertStatus(200);
        // Ensure the job is in the response (depending on implementation, it might only show their jobs)
        $response->assertJsonFragment(['title' => 'My Job']);
    }

    public function test_employer_can_update_own_job()
    {
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $country = \App\Domains\Location\Models\Country::create(['name' => 'Egypt']);
        $city = \App\Domains\Location\Models\City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Old Title',
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
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($employer, 'sanctum')->putJson("/api/dashboard/jobs/{$job->id}", [
            'title' => 'New Title',
            'category_id' => $category->id,
            'description' => 'Updated desc',
            'responsibilities' => 'Updated resp',
            'qualification' => 'Updated quals',
            'benefits' => 'Updated benefits',
            'experience' => '5+ years',
            'deadline' => now()->addDays(15)->format('Y-m-d'),
            'work_type' => 'part-time',
            'work_place' => 'hybrid',
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('job_posts', ['id' => $job->id, 'title' => 'New Title']);
    }

    public function test_employer_cannot_update_others_job()
    {
        $employer = $this->setupEmployer();
        $otherEmployer = User::factory()->create(['type' => 'employer']);
        $category = Category::create(['name' => 'IT']);
        $country = \App\Domains\Location\Models\Country::create(['name' => 'Egypt']);
        $city = \App\Domains\Location\Models\City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        $job = JobPost::create([
            'employer_id' => $otherEmployer->id,
            'title' => 'Other Job',
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
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($employer, 'sanctum')->putJson("/api/dashboard/jobs/{$job->id}", [
            'title' => 'Trying to Hack',
            'category_id' => $category->id,
            'description' => 'Updated desc',
            'responsibilities' => 'Updated resp',
            'qualification' => 'Updated quals',
            'benefits' => 'Updated benefits',
            'experience' => '5+ years',
            'deadline' => now()->addDays(15)->format('Y-m-d'),
            'work_type' => 'part-time',
            'work_place' => 'hybrid',
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_employer_can_delete_own_job()
    {
        $employer = $this->setupEmployer();
        $category = Category::create(['name' => 'IT']);
        $country = \App\Domains\Location\Models\Country::create(['name' => 'Egypt']);
        $city = \App\Domains\Location\Models\City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        $job = JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'To Delete',
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
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);

        $response = $this->actingAs($employer, 'sanctum')->deleteJson("/api/dashboard/jobs/{$job->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($job);
    }
}
