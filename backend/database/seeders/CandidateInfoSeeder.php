<?php

namespace Database\Seeders;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\Locationable;
use App\Domains\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CandidateInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 candidate users if they don't exist
        $existingCandidates = User::where('type', 'candidate')->count();
        $neededCandidates = 50 - $existingCandidates;

        if ($neededCandidates > 0) {
            for ($i = 1; $i <= $neededCandidates; $i++) {
                User::create([
                    'name' => fake()->name(),
                    'email' => 'candidate' . ($existingCandidates + $i) . '@example.com',
                    'type' => 'candidate',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
            }
        }

        // Get all candidates
        $candidates = User::where('type', 'candidate')->get();

        // Get all skills
        $skills = Skill::all();
        
        // Get all countries and cities
        $countries = Country::all();
        $cities = City::all();

        // Education levels
        $educationLevels = [
            'High School',
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'PhD',
            'Diploma',
            'Certificate'
        ];

        // Experience levels
        $experienceLevels = [
            'Entry Level',
            '1-3 years',
            '3-5 years',
            '5+ years',
            'Senior'
        ];

        // Loop over each candidate
        foreach ($candidates as $user) {
            // Check if candidate info already exists
            $candidateInfo = CandidateInfo::where('user_id', $user->id)->first();
            
            if (!$candidateInfo) {
                $candidateInfo = CandidateInfo::create([
                    'user_id' => $user->id,
                    'phone' => fake()->phoneNumber(),
                    'job_title' => fake()->jobTitle(),
                    'education' => fake()->randomElement($educationLevels),
                    'experience' => fake()->randomElement($experienceLevels),
                    'bio' => fake()->paragraph(3),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
                ]);

                // Attach random skills (1-5 skills per candidate)
                if ($skills->isNotEmpty()) {
                    $randomSkills = $skills->random(rand(1, min(5, $skills->count())));
                    $candidateInfo->skills()->attach($randomSkills->pluck('id')->toArray());
                }

                // Create location (random city and country)
                if ($cities->isNotEmpty()) {
                    $randomCity = $cities->random();
                    $country = $randomCity->country ?? $countries->random();
                    
                    Locationable::create([
                        'locationable_id' => $candidateInfo->id,
                        'locationable_type' => CandidateInfo::class,
                        'city_id' => $randomCity->id,
                        'country_id' => $country->id,
                        'address' => fake()->address(),
                    ]);
                }
            } else {
                // If candidate info exists but doesn't have skills, add them
                if ($candidateInfo->skills()->count() === 0 && $skills->isNotEmpty()) {
                    $randomSkills = $skills->random(rand(1, min(5, $skills->count())));
                    $candidateInfo->skills()->attach($randomSkills->pluck('id')->toArray());
                }

                // If candidate info exists but doesn't have location, add it
                if (!$candidateInfo->locationable && $cities->isNotEmpty()) {
                    $randomCity = $cities->random();
                    $country = $randomCity->country ?? $countries->random();
                    
                    Locationable::create([
                        'locationable_id' => $candidateInfo->id,
                        'locationable_type' => CandidateInfo::class,
                        'city_id' => $randomCity->id,
                        'country_id' => $country->id,
                        'address' => fake()->address(),
                    ]);
                }
            }
        }
    }
}
