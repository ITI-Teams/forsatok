<?php

namespace Tests\Feature\Api;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Users\Models\User;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CandidateProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setupCandidate()
    {
        Role::create(['name' => 'candidate', 'guard_name' => 'web']);
        $candidate = User::factory()->create(['type' => 'candidate']);
        $candidate->assignRole('candidate');
        return $candidate;
    }

    public function test_candidate_can_view_own_profile()
    {
        $candidate = $this->setupCandidate();
        CandidateInfo::create(['user_id' => $candidate->id]);

        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/auth/candidate/info');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data', 'message']);
    }

    public function test_candidate_can_update_profile_info()
    {
        Storage::fake('public');
        $candidate = $this->setupCandidate();
        $category = Category::create(['name' => 'Design']);
        $skill = Skill::create(['name' => 'Figma', 'category_id' => $category->id]);

        $response = $this->actingAs($candidate, 'sanctum')->postJson('/api/auth/candidate/info', [
            'name' => 'Updated Candidate',
            'email' => 'updated@gmail.com',
            'category_id' => $category->id,
            'skills' => [$skill->id],
            'experience' => '3 years',
            'education' => 'Bachelor of Arts',
            'resume' => UploadedFile::fake()->create('resume.pdf', 100),
            'address' => '123 Test St',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $candidate->id, 'name' => 'Updated Candidate', 'email' => 'updated@gmail.com']);
        $this->assertDatabaseHas('candidate_infos', ['user_id' => $candidate->id, 'experience' => '3 years']);
        Storage::disk('public')->assertExists('resumes/' . $response->json('data.resume_name'));
    }

    public function test_non_candidate_cannot_access_candidate_profile()
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer']);
        $employer->assignRole('employer');

        $response = $this->actingAs($employer, 'sanctum')->getJson('/api/auth/candidate/info');

        $response->assertStatus(403);
    }
}
