<?php

namespace Tests\Feature;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::create(['name' => 'employer', 'guard_name' => 'web']);
        Role::create(['name' => 'candidate', 'guard_name' => 'web']);
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'super-admin', 'guard_name' => 'api']);
        Role::create(['name' => 'employer', 'guard_name' => 'api']);
        Role::create(['name' => 'candidate', 'guard_name' => 'api']);
    }

    /** @test */
    public function dashboard_api_prefix_requires_authentication()
    {
        $response = $this->getJson('/api/dashboard/profile');
        $response->assertStatus(401);
    }

    /** @test */
    public function admin_only_dashboard_routes_reject_candidates()
    {
        $candidate = User::factory()->create(['type' => 'candidate']);
        $candidate->assignRole('candidate');

        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/dashboard/stats/admin');
        
        // Spatie returns 403 for unauthorized roles
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_only_dashboard_routes_reject_employers()
    {
        $employer = User::factory()->create(['type' => 'employer']);
        $employer->assignRole('employer');

        $response = $this->actingAs($employer, 'sanctum')->getJson('/api/dashboard/stats/admin');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_only_dashboard_routes_allow_admins()
    {
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/stats/admin');
        $response->assertStatus(200);
    }

    /** @test */
    public function employer_only_dashboard_routes_reject_candidates()
    {
        $candidate = User::factory()->create(['type' => 'candidate']);
        $candidate->assignRole('candidate');

        $response = $this->actingAs($candidate, 'sanctum')->getJson('/api/dashboard/stats/employer');
        $response->assertStatus(403);
    }

    /** @test */
    public function employer_only_dashboard_routes_allow_employers()
    {
        $employer = User::factory()->create(['type' => 'employer', 'status' => 'approved']);
        $employer->assignRole('employer');

        $response = $this->actingAs($employer, 'sanctum')->getJson('/api/dashboard/stats/employer');
        $response->assertStatus(200);
    }

    /** @test */
    public function super_admin_inherits_admin_permissions()
    {
        $superAdmin = User::factory()->create(['type' => 'super-admin', 'status' => 'approved']);
        $superAdmin->assignRole('super-admin');

        $response = $this->actingAs($superAdmin, 'sanctum')->getJson('/api/dashboard/stats/admin');
        
        // Super admin should have access.
        $response->assertStatus(200); 
    }

    /** @test */
    public function candidate_can_register_via_api()
    {
        $response = $this->postJson('/api/auth/candidate/register', [
            'name' => 'Test Candidate',
            'email' => 'candidate@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'candidate@gmail.com', 'type' => 'candidate']);
    }

    /** @test */
    public function candidate_can_login_via_api()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
            'type' => 'candidate',
            'status' => 'approved'
        ]);

        $response = $this->postJson('/api/auth/candidate/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'user']);
    }

    /** @test */
    public function google_redirect_works()
    {
        \Laravel\Socialite\Facades\Socialite::shouldReceive('driver->with->redirect')
            ->once()
            ->andReturn(redirect('https://google.com/auth'));

        $response = $this->get('/auth/google');
        $response->assertRedirect('https://google.com/auth');
    }

    /** @test */
    public function linkedin_redirect_works()
    {
        \Laravel\Socialite\Facades\Socialite::shouldReceive('driver->redirect')
            ->once()
            ->andReturn(redirect('https://linkedin.com/auth'));

        $response = $this->get('/api/auth/linkedin/redirect');
        $response->assertRedirect('https://linkedin.com/auth');
    }

    /** @test */
    public function candidate_can_forgot_password_request()
    {
        User::factory()->create([
            'email' => 'forgot@gmail.com',
            'type' => 'candidate'
        ]);

        $response = $this->postJson('/api/auth/candidate/forgot-password', [
            'email' => 'forgot@gmail.com',
        ]);

        $response->assertStatus(200);
    }
}
