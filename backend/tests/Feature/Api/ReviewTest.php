<?php

namespace Tests\Feature\Api;

use App\Domains\CompanyReviews\Models\CompanyReview;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setupCandidate()
    {
        Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'web']);
        $candidate = User::factory()->create(['type' => 'candidate']);
        $candidate->assignRole('candidate');
        return $candidate;
    }

    protected function setupEmployer()
    {
        Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer']);
        $employer->assignRole('employer');
        return $employer;
    }

    public function test_candidate_can_post_review()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();

        $response = $this->actingAs($candidate, 'sanctum')->postJson('/api/company-reviews', [
            'company_id' => $employer->id,
            'rating' => 5,
            'review' => 'Excellent company!'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('company_reviews', [
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 5
        ]);
    }

    public function test_candidate_cannot_post_duplicate_review()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();

        CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 4,
            'review' => 'Original'
        ]);

        $response = $this->actingAs($candidate, 'sanctum')->postJson('/api/company-reviews', [
            'company_id' => $employer->id,
            'rating' => 5,
            'review' => 'Duplicate'
        ]);

        $response->assertStatus(409);
    }

    public function test_candidate_can_update_own_review()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $review = CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 4,
            'review' => 'Original'
        ]);

        $response = $this->actingAs($candidate, 'sanctum')->putJson("/api/company-reviews/{$review->id}", [
            'rating' => 5,
            'review' => 'Updated'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('company_reviews', ['id' => $review->id, 'rating' => 5]);
    }

    public function test_candidate_cannot_update_others_review()
    {
        $candidate1 = $this->setupCandidate();
        $candidate2 = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $review = CompanyReview::create([
            'candidate_id' => $candidate2->id,
            'company_id' => $employer->id,
            'rating' => 4,
            'review' => 'Original'
        ]);

        $response = $this->actingAs($candidate1, 'sanctum')->putJson("/api/company-reviews/{$review->id}", [
            'rating' => 5
        ]);

        $response->assertStatus(403);
    }

    public function test_candidate_can_delete_own_review()
    {
        $candidate = $this->setupCandidate();
        $employer = $this->setupEmployer();
        $review = CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 4
        ]);

        $response = $this->actingAs($candidate, 'sanctum')->deleteJson("/api/company-reviews/{$review->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($review);
    }

    public function test_unauthenticated_cannot_post_review()
    {
        $employer = $this->setupEmployer();

        $response = $this->postJson('/api/company-reviews', [
            'company_id' => $employer->id,
            'rating' => 5
        ]);

        $response->assertStatus(401);
    }
}
