<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::create(['name' => 'employer', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::create(['name' => 'candidate', 'guard_name' => 'web']); // Seeding candidate too just in case

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }
}
