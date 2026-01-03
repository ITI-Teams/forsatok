<?php

namespace Tests\Feature\Dashboard;

use App\Domains\CompanyReviews\Models\CompanyReview;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setupEmployer()
    {
        $role = Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer', 'status' => 'approved']);
        $employer->assignRole($role);
        return $employer;
    }

    public function test_employer_can_list_reviews_for_their_company()
    {
        $employer = $this->setupEmployer();
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 5,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->getJson('/api/dashboard/company-reviews');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_employer_can_approve_review()
    {
        $employer = $this->setupEmployer();
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $review = CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 5,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->postJson("/api/dashboard/company-reviews/{$review->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('company_reviews', [
            'id' => $review->id,
            'status' => 'approved'
        ]);
    }

    public function test_employer_can_reject_review()
    {
        $employer = $this->setupEmployer();
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $review = CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 1,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($employer, 'sanctum')->postJson("/api/dashboard/company-reviews/{$review->id}/reject");

        $response->assertStatus(200);
        $this->assertDatabaseHas('company_reviews', [
            'id' => $review->id,
            'status' => 'rejected'
        ]);
    }

    public function test_employer_can_soft_delete_review()
    {
        $employer = $this->setupEmployer();
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $review = CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 5
        ]);

        $response = $this->actingAs($employer, 'sanctum')->deleteJson("/api/dashboard/company-reviews/{$review->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($review);
    }

    public function test_employer_can_restore_review()
    {
        $employer = $this->setupEmployer();
        $candidate = User::factory()->create(['type' => 'candidate']);
        
        $review = CompanyReview::create([
            'candidate_id' => $candidate->id,
            'company_id' => $employer->id,
            'rating' => 5
        ]);
        $review->delete();

        $response = $this->actingAs($employer, 'sanctum')->postJson("/api/dashboard/company-reviews/{$review->id}/restore");

        $response->assertStatus(200);
        $this->assertDatabaseHas('company_reviews', [
            'id' => $review->id,
            'deleted_at' => null
        ]);
    }
}
