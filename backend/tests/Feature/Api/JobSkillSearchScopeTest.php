<?php

namespace Tests\Feature\Api;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JobSkillSearchScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_only_sees_approved_jobs_in_skill_search()
    {
        // 0. Setup Category
        $category = \App\Domains\Jobs\Models\Category::create(['name' => 'IT', 'slug' => 'it']);

        // 1. Setup skills
        $skill = Skill::create(['name' => 'PHP', 'category_id' => $category->id]);
        
        // 2. Setup Jobs
        $approvedJob = JobPost::create([
            'employer_id' => User::factory()->create()->id,
            'title' => 'Approved PHP Job',
            'status' => 'approved',
            'is_active' => true,
            'category_id' => $category->id,
            'deadline' => now()->addDays(10),
            'experience' => '1 year',
            'salary_min' => 1000,
            'salary_max' => 2000,
            'description' => 'Test',
            'responsibilities' => 'Test',
            'qualification' => 'Test',
            'benefits' => 'Test',
            'work_type' => 'full-time',
            'work_place' => 'remote'
        ]);
        $approvedJob->skills()->attach($skill->id);

        $pendingJob = JobPost::create([
            'employer_id' => User::factory()->create()->id,
            'title' => 'Pending PHP Job',
            'status' => 'pending',
            'is_active' => true,
            'category_id' => $category->id,
            'deadline' => now()->addDays(10),
            'experience' => '1 year',
            'salary_min' => 1000,
            'salary_max' => 2000,
            'description' => 'Test',
            'responsibilities' => 'Test',
            'qualification' => 'Test',
            'benefits' => 'Test',
            'work_type' => 'full-time',
            'work_place' => 'remote'
        ]);
        $pendingJob->skills()->attach($skill->id);

        // 3. Search as Guest (shoud behave like candidate)
        $response = $this->postJson('/api/dashboard/jobs/search-by-skills', [
            'skill_ids' => [$skill->id]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'Approved PHP Job']);
        $response->assertJsonMissing(['title' => 'Pending PHP Job']);

        // 4. Search as Admin
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/dashboard/jobs/search-by-skills', [
            'skill_ids' => [$skill->id]
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }
}
