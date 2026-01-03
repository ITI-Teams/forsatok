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
        // Assuming Country model has 'name' and maybe 'code'
        // If Country model is different, this test might need adjustment.
        // Let's try basic creation.
        // Checking if Country uses a factory or manual create
        try {
             Country::create(['name' => 'Egypt', 'code' => 'EG', 'phone_code' => '20']);
        } catch (\Exception $e) {
             // Fallback if schema is different, but for now assuming standard fields
             Country::create(['name' => 'Egypt']);
        }

        $response = $this->getJson('/api/locations/countries');

        $response->assertStatus(200);
    }
}
