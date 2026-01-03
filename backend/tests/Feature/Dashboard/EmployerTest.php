<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmployerTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_view_profile()
    {
        Role::create(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer']);
        $employer->assignRole('employer');
        
        // Ensure EmployerInfo exists, assuming it's related or needs manual creation
        if (class_exists(\App\Domains\Employers\Models\EmployerInfo::class)) {
             \App\Domains\Employers\Models\EmployerInfo::create([
                 'user_id' => $employer->id,
                 'company_name' => 'Test Company',
                 // Add other required fields if known or broad permissive defaults
             ]);
        }
        
        // Based on dashboardApi.php: Route::prefix('employer')->name('employer.')->group(... Route::get('/profile'...
        // Full path: /api/dashboard/employer/profile
        $response = $this->actingAs($employer)->getJson('/api/dashboard/employer/profile');

        $response->assertStatus(200);
    }

    public function test_employer_can_update_profile()
    {
        $role = Role::create(['name' => 'employer', 'guard_name' => 'web']);
        $employer = User::factory()->create(['type' => 'employer']);
        $employer->assignRole('employer');

        $country = \App\Domains\Location\Models\Country::create(['name' => 'USA', 'code' => 'US']);
        $city = \App\Domains\Location\Models\City::create(['name' => 'NYC', 'country_id' => $country->id]);

        \Illuminate\Support\Facades\Storage::fake('public');

        $response = $this->actingAs($employer, 'sanctum')->putJson('/api/dashboard/employer/profile', [
            'name' => 'New Management',
            'email' => 'new@employer.com',
            'company_name' => 'Tech Corp',
            'industry' => 'Software',
            'description' => 'A great tech company.',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'address' => '5th Ave',
            'avatar' => \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $employer->id, 'email' => 'new@employer.com']);
        $this->assertDatabaseHas('employer_infos', ['user_id' => $employer->id, 'company_name' => 'Tech Corp']);
        $this->assertDatabaseHas('locationables', [
            'locationable_id' => $response->json('data.id'),
            'locationable_type' => \App\Domains\Employers\Models\EmployerInfo::class,
            'city_id' => $city->id
        ]);
    }
}
