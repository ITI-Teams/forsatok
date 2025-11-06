<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@jobboard.com',
                'type' => 'admin',
                'password' => Hash::make('password'),
            ],
        ];

        // Create Employer Users
        $employers = [
            [
                'name' => 'Tech Solutions Inc.',
                'email' => 'employer@techsolutions.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Digital Innovations',
                'email' => 'hr@digitalinnovations.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Global Systems',
                'email' => 'careers@globalsystems.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Startup Hub',
                'email' => 'jobs@startuphub.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
        ];

        // Create Candidate Users
        $candidates = [
            [
                'name' => 'Ahmed Mohamed',
                'email' => 'ahmed.mohamed@example.com',
                'type' => 'candidate',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'type' => 'candidate',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mohammed Ali',
                'email' => 'mohammed.ali@example.com',
                'type' => 'candidate',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'type' => 'candidate',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Omar Hassan',
                'email' => 'omar.hassan@example.com',
                'type' => 'candidate',
                'password' => Hash::make('password'),
            ],
        ];

        // Seed Admins
        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }

        // Seed Employers
        foreach ($employers as $employer) {
            User::firstOrCreate(
                ['email' => $employer['email']],
                $employer
            );
        }

        // Seed Candidates
        foreach ($candidates as $candidate) {
            User::firstOrCreate(
                ['email' => $candidate['email']],
                $candidate
            );
        }

    }
}
