<?php

namespace Database\Seeders;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\Locationable;
use Illuminate\Database\Seeder;
use App\Domains\Users\Models\User;
use App\Domains\Jobs\Models\Skill;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        //-----------------------------------
        // 1) Create Permissions
        //-----------------------------------
        $permissions = [
            'jobs.view',
            'jobs.manage',
            'jobs.approve',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        //-----------------------------------
        // 2) Create Roles
        //-----------------------------------
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $employerRole = Role::firstOrCreate(['name' => 'employer']);
        $candidateRole = Role::firstOrCreate(['name' => 'candidate']);

        // Assign permissions
        $adminRole->syncPermissions($permissions);
        $employerRole->syncPermissions(['jobs.view', 'jobs.manage']);
        $candidateRole->syncPermissions([]); // no permissions for now

        //-----------------------------------
        // 3) Admins
        //-----------------------------------
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin1@jobboard.com',
                'type' => 'admin',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Main Admin',
                'email' => 'admin2@jobboard.com',
                'type' => 'admin',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::firstOrCreate(['email' => $admin['email']], $admin);
            $user->assignRole('admin');
        }

        //-----------------------------------
        // 4) Employers
        //-----------------------------------
        $employers = [
            [
                'name' => 'Tech Solutions Inc.',
                'email' => 'employer1@company.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Digital Innovations',
                'email' => 'employer2@company.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Creative Vision',
                'email' => 'employer3@company.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Global Systems',
                'email' => 'employer4@company.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Startup Hub',
                'email' => 'employer5@company.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'NextGen Software',
                'email' => 'employer6@company.com',
                'type' => 'employer',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($employers as $emp) {
            $user = User::firstOrCreate(['email' => $emp['email']], $emp);
            $user->assignRole('employer');

            // Create Employer Profile
            EmployerInfo::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'company_name' => $emp['name'],
                'industry' => 'Technology',
                'about' => 'This is a seeded employer description.',
                'website' => 'https://example.com',
            ]);
        }

        //-----------------------------------
        // 5) Candidates
        //-----------------------------------
        $countries = Country::with('cities')->get();
        $candidates = [
            ['name' => 'Ahmed Mohamed', 'email' => 'candidate1@mail.com'],
            ['name' => 'Sarah Johnson', 'email' => 'candidate2@mail.com'],
            ['name' => 'Mohammed Ali', 'email' => 'candidate3@mail.com'],
            ['name' => 'Emily Davis', 'email' => 'candidate4@mail.com'],
            ['name' => 'Omar Hassan', 'email' => 'candidate5@mail.com'],
            ['name' => 'Youssef Ibrahim', 'email' => 'candidate6@mail.com'],
            ['name' => 'Mona Adel', 'email' => 'candidate7@mail.com'],
        ];

        foreach ($candidates as $c) {
            $country = $countries->random();
            $city = $country->cities->random();
            $user = User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'type' => 'candidate',
                    'password' => Hash::make('password'),
                ]
            );

            $user->assignRole('candidate');

            // Create Candidate Profile
            $candidate = CandidateInfo::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'job_title' => 'Software Engineer',
                'gender' => 'male',
                'date_of_birth' => '1995-01-01',
                'phone' => '01000000000',
                'resume' => null,
                'education' => 'Bachelor of Computer Science',
                'experience' => '2 Years',
                'bio' => 'This is a test candidate bio.',
                'category_id' => 1,
            ]);
            Locationable::firstOrCreate(
                [
                    'locationable_id' => $candidate->id,
                    'locationable_type' => CandidateInfo::class,
                ],
                [
                    'country_id' => $country->id,
                    'city_id' => $city->id,
                    'address' => "{$city->name}, {$country->name}",
                ]
            );

            // Assign 3-6 random skills to the candidate
            $skills = Skill::inRandomOrder()->take(rand(3, 6))->pluck('id');
            $candidate->skills()->sync($skills);
        }
    }
}
