<?php

namespace Tests\Feature\Api;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // Use appropriate trait, usually RefreshDatabase for testing

    public function test_candidate_can_register()
    {
        \Spatie\Permission\Models\Role::create(['name' => 'candidate', 'guard_name' => 'web']);
        $response = $this->postJson('/api/auth/candidate/register', [
            'name' => 'Test Candidate',
            'email' => 'candidate-test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201) // Assuming 201 Created or 200 OK
                 ->assertJsonStructure(['token', 'user']); // Adjust based on actual response
    }

    public function test_candidate_can_login()
    {
        $user = User::factory()->create([
            'email' => 'candidate@test.com',
            'password' => bcrypt('password'),
            'type' => 'candidate',
            'status' => 'active'
        ]);

        $response = $this->postJson('/api/auth/candidate/login', [
            'email' => 'candidate@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']); 
    }

    public function test_candidate_can_get_info()
    {
        $user = User::factory()->create(['type' => 'candidate']);
        \App\Domains\Candidates\Models\CandidateInfo::create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->getJson('/api/auth/candidate/info');

        $response->assertStatus(200);
    }
}
