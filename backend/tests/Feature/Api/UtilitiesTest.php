<?php

namespace Tests\Feature\Api;

use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Location\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_skills()
    {
        $category = Category::create(['name' => 'IT']);
        Skill::create(['name' => 'PHP', 'category_id' => $category->id]);
        Skill::create(['name' => 'Laravel', 'category_id' => $category->id]);

        $response = $this->getJson('/api/skills');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_can_list_categories()
    {
        Category::create(['name' => 'IT']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
    }

    public function test_can_list_countries()
    {
        Country::create(['name' => 'Egypt', 'code' => 'EG']);

        $response = $this->getJson('/api/locations/countries');

        $response->assertStatus(200);
    }

    public function test_can_list_cities()
    {
        $country = Country::create(['name' => 'Egypt', 'code' => 'EG']);
        \App\Domains\Location\Models\City::create(['name' => 'Cairo', 'country_id' => $country->id]);

        $response = $this->getJson('/api/locations/cities');

        $response->assertStatus(200);
    }
}
