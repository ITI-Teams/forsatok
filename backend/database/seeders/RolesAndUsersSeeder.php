<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Users\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'employer', 'candidate'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@jobboard.com'],
            [
                'name' => 'Super Admin',
                'type' => 'admin',
                'password' => Hash::make('admin123'),
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $employer = User::firstOrCreate(
            ['email' => 'employer@jobboard.com'],
            [
                'name' => 'Demo Employer',
                'type' => 'employer',
                'password' => Hash::make('employer123'),
                'status' => 'pending', // For testing approval
                'email_verified_at' => now(),
            ]
        );
        $employer->assignRole('employer');

        $candidate = User::firstOrCreate(
            ['email' => 'candidate@jobboard.com'],
            [
                'name' => 'Demo Candidate',
                'type' => 'candidate',
                'password' => Hash::make('candidate123'),
                'status' => 'approved',
                'email_verified_at' => now(),
            ]
        );
        $candidate->assignRole('candidate');
    }
}
