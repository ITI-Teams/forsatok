<?php

namespace Tests\Feature;

use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\SavedJob;
use App\Domains\Shared\Models\AuditLog;
use App\Domains\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecondaryFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setupUser($roleName, $type)
    {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create(['type' => $type, 'status' => 'approved']);
        $user->assignRole($role);
        return $user;
    }

    // ═══════════════════════════════════════════════════════════════════
    // NOTIFICATIONS
    // ═══════════════════════════════════════════════════════════════════
    public function test_user_can_manage_notifications()
    {
        $user = $this->setupUser('candidate', 'candidate');
        
        // Mock a notification (Laravel database notifications)
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'test'],
            'read_at' => null,
        ]);

        // List
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/notifications');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        // Mark as read
        $id = $response->json('data.0.id');
        $this->actingAs($user, 'sanctum')->postJson("/api/notifications/{$id}/read")->assertStatus(200);
        $this->assertNotNull($user->notifications()->find($id)->read_at);

        // Delete
        $this->actingAs($user, 'sanctum')->deleteJson("/api/notifications/{$id}")->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['id' => $id]);

        // Mark all as read
        $this->actingAs($user, 'sanctum')->postJson('/api/notifications/read-all')->assertStatus(200);

        // List unread
        $this->actingAs($user, 'sanctum')->getJson('/api/notifications/unread')->assertStatus(200);

        // Clear all
        $this->actingAs($user, 'sanctum')->deleteJson('/api/notifications')->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════════
    // CONTACT MESSAGES
    // ═══════════════════════════════════════════════════════════════════
    public function test_guest_can_submit_contact_message()
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $response = $this->postJson('/api/contact', [
            'full_name' => 'John Doe',
            'email' => 'john@gmail.com',
            'subject' => 'Inquiry',
            'message' => 'I would like to know more about your services.'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('contact_messages', ['email' => 'john@gmail.com']);
    }

    public function test_admin_can_list_contact_messages()
    {
        $admin = $this->setupUser('admin', 'admin');
        ContactMessage::create([
            'full_name' => 'Jane', 'email' => 'jane@gmail.com', 'subject' => 'Hi', 'message' => 'Hello there.'
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/contact-messages');
        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════════
    // SAVED JOBS
    // ═══════════════════════════════════════════════════════════════════
    public function test_candidate_can_save_and_unsave_job()
    {
        $candidate = $this->setupUser('candidate', 'candidate');
        $employer = $this->setupUser('employer', 'employer');
        $category = Category::create(['name' => 'IT']);
        $job = JobPost::create([
            'employer_id' => $employer->id, 'title' => 'Test Job', 'category_id' => $category->id,
            'is_active' => true, 'status' => 'approved', 'deadline' => now()->addDays(10),
            'description' => 'Desc', 'responsibilities' => 'Resp', 'qualification' => 'Quals', 'benefits' => 'Benefits', 'experience' => '1-3 years',
            'work_type' => 'full-time', 'work_place' => 'remote',
        ]);

        // Save
        $response = $this->actingAs($candidate, 'sanctum')->postJson('/api/jobs/save', ['job_post_id' => $job->id]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('saved_jobs', ['candidate_id' => $candidate->id, 'job_post_id' => $job->id]);

        // Unsave (toggle)
        $this->actingAs($candidate, 'sanctum')->postJson('/api/jobs/save', ['job_post_id' => $job->id])->assertStatus(200);
        $this->assertDatabaseMissing('saved_jobs', ['candidate_id' => $candidate->id, 'job_post_id' => $job->id]);
    }

    // ═══════════════════════════════════════════════════════════════════
    // STATS & AUDIT LOGS
    // ═══════════════════════════════════════════════════════════════════
    public function test_admin_can_view_stats_and_audit_logs()
    {
        $admin = $this->setupUser('admin', 'admin');
        // AdminStats needs employer role count
        Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);
        
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'Login',
            'model_type' => 'User',
            'model_id' => $admin->id,
            'changes' => 'Admin logged in'
        ]);

        $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/stats/admin')->assertStatus(200);
        $this->actingAs($admin, 'sanctum')->getJson('/api/dashboard/audit-logs')->assertStatus(200);
    }

    public function test_employer_can_view_stats()
    {
        $employer = $this->setupUser('employer', 'employer');
        $this->actingAs($employer, 'sanctum')->getJson('/api/dashboard/stats/employer')->assertStatus(200);
    }
}
