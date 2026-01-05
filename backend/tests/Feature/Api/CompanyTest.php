<?php

namespace Tests\Feature\Api;

use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_companies()
    {
        $employer = User::factory()->create(['type' => 'employer', 'name' => 'Awesome Corp']);
        
        $response = $this->getJson('/api/companies/search?keyword=Awesome');

        $response->assertStatus(200);
    }

    public function test_can_get_company_filter_options()
    {
        $response = $this->getJson('/api/companies/filter-options');
        $response->assertStatus(200);
    }
}
