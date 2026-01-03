<?php

namespace Tests\Feature\Dashboard;

use App\Domains\Jobs\Models\Category;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin']);
        $admin->assignRole($role);

        $response = $this->actingAs($admin)->postJson('/api/dashboard/categories', [
            'name' => 'New Category'
        ]);

        $response->assertStatus(201); // or 200
        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    public function test_admin_can_list_categories()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        Category::create(['name' => 'Test Cat']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/categories');

        $response->assertStatus(200);
    }

    public function test_admin_can_update_category()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'Old Name']);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/dashboard/categories/{$category->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_soft_delete_category()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'To Delete']);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted($category);
    }

    public function test_admin_can_list_trashed_categories()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'Trashed Cat']);
        $category->delete();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/categories/trashed');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Trashed Cat']);
    }

    public function test_admin_can_restore_category()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'To Restore']);
        $category->delete();

        $response = $this->actingAs($admin, 'sanctum')->postJson("/api/dashboard/categories/{$category->id}/restore");

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    public function test_admin_can_force_delete_category()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['type' => 'admin', 'status' => 'approved']);
        $admin->assignRole($role);
        
        $category = Category::create(['name' => 'Force Delete']);
        $category->delete();

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/dashboard/categories/{$category->id}/force");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
