<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Jobs\Models\Skill;
use App\Domains\Jobs\Models\Category;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    protected function setupAdmin()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        return $admin;
    }

    public function test_admin_can_create_skill()
    {
        $admin = $this->setupAdmin();
        $category = Category::create(['name' => 'Tech']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/dashboard/skills', [
            'name' => 'PHP',
            'category_id' => $category->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('skills', ['name' => 'PHP']);
    }

    public function test_admin_can_list_skills()
    {
        $admin = $this->setupAdmin();
        $category = Category::create(['name' => 'Tech']);
        Skill::create(['name' => 'Laravel', 'category_id' => $category->id]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/skills');

        $response->assertStatus(200);
    }

    public function test_admin_can_update_skill()
    {
        $admin = $this->setupAdmin();
        $category = Category::create(['name' => 'Tech']);
        $skill = Skill::create(['name' => 'Old Skill', 'category_id' => $category->id]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/dashboard/skills/{$skill->id}", [
            'name' => 'New Skill',
            'category_id' => $category->id
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'New Skill']);
    }

    public function test_admin_can_soft_delete_skill()
    {
        $admin = $this->setupAdmin();
        $category = Category::create(['name' => 'Tech']);
        $skill = Skill::create(['name' => 'To Delete', 'category_id' => $category->id]);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/skills/{$skill->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($skill);
    }

    public function test_admin_can_restore_skill()
    {
        $admin = $this->setupAdmin();
        $category = Category::create(['name' => 'Tech']);
        $skill = Skill::create(['name' => 'To Restore', 'category_id' => $category->id]);
        $skill->delete();

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/skills/{$skill->id}/restore");

        $response->assertStatus(200);
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'deleted_at' => null]);
    }
}
