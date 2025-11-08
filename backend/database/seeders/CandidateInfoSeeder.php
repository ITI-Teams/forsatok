<?php

namespace Database\Seeders;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only candidates
        $candidates = User::where('type', 'candidate')->get();

        // loop over each candidate
        foreach ($candidates as $user) {
            CandidateInfo::create([
                'user_id' => $user->id,
                'phone' => 15778974546,
                'resume' => 'resumes',
                'education' =>
                    'Bachelor in Computer Science
                    Master in Business Administration
                    Bachelor in Information Technology
                    Diploma in Web Design',
                'experience' => 'years experience',
                'bio' => fake()->paragraph(2),
            ]);
        }
    }
}
