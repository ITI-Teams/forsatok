<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setupAdmin()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        return $admin;
    }

    // ═══════════════════════════════════════════════════════════════════
    // COUNTRIES
    // ═══════════════════════════════════════════════════════════════════

    public function test_admin_can_create_country()
    {
        $admin = $this->setupAdmin();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/dashboard/countries', [
            'name' => 'Egypt',
            'code' => 'EG'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('countries', ['name' => 'Egypt', 'code' => 'EG']);
    }

    public function test_admin_can_list_countries()
    {
        $admin = $this->setupAdmin();
        Country::create(['name' => 'Egypt', 'code' => 'EG']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/countries');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Egypt']);
    }

    public function test_admin_can_update_country()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'Old Name', 'code' => 'OL']);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/dashboard/countries/{$country->id}", [
            'name' => 'New Name',
            'code' => 'NW'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('countries', ['id' => $country->id, 'name' => 'New Name']);
    }

    public function test_admin_can_soft_delete_country()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'To Delete', 'code' => 'TD']);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/countries/{$country->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($country);
    }

    public function test_admin_can_list_trashed_countries()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'Trashed', 'code' => 'TR']);
        $country->delete();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/countries/trashed');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Trashed']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // CITIES
    // ═══════════════════════════════════════════════════════════════════

    public function test_admin_can_create_city()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'Egypt', 'code' => 'EG']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/dashboard/cities', [
            'name' => 'Cairo',
            'country_id' => $country->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cities', ['name' => 'Cairo', 'country_id' => $country->id]);
    }

    public function test_admin_can_list_cities()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'Egypt', 'code' => 'EG']);
        City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/cities');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Cairo']);
    }

    public function test_admin_can_update_city()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'Egypt', 'code' => 'EG']);
        $city = City::create(['name' => 'Old City', 'country_id' => $country->id]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/dashboard/cities/{$city->id}", [
            'name' => 'New City',
            'country_id' => $country->id
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cities', ['id' => $city->id, 'name' => 'New City']);
    }

    public function test_admin_can_soft_delete_city()
    {
        $admin = $this->setupAdmin();
        $country = Country::create(['name' => 'Egypt', 'code' => 'EG']);
        $city = City::create(['name' => 'To Delete', 'country_id' => $country->id]);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/cities/{$city->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($city);
    }
}
