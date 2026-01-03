<?php

namespace Tests\Feature\Api;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_candidates()
    {
        // Seeding some candidates
        $user = User::factory()->create(['type' => 'candidate']);
        CandidateInfo::create(['user_id' => $user->id, 'job_title' => 'Developer', 'bio' => 'Test Bio']);


        $response = $this->getJson('/api/candidates/search?keyword=test');

        $response->assertStatus(200);
    }

    public function test_can_list_candidates()
    {
        $user = User::factory()->create(['type' => 'candidate']);
        CandidateInfo::create(['user_id' => $user->id, 'job_title' => 'Developer', 'bio' => 'Test Bio']);

        $response = $this->getJson('/api/candidates');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'meta']);
    }

    public function test_can_show_candidate()
    {
        $user = User::factory()->create(['type' => 'candidate']);
        $candidate = CandidateInfo::create(['user_id' => $user->id, 'job_title' => 'Developer', 'bio' => 'Test Bio']);

        $response = $this->getJson('/api/candidates/' . $candidate->id);

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $candidate->id);
    }
}
