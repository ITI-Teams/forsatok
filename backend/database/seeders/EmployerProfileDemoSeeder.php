<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Users\Models\User;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Employers\Models\CompanyReview;
use Illuminate\Support\Str;

class EmployerProfileDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Pick an employer user or fallback to first user
        $employerUser = User::where('type', 'employer')->first() ?? User::first();
        if (! $employerUser) {
            return; // nothing to seed against
        }

        // Upsert employer info
        $info = EmployerInfo::firstOrCreate(
            ['user_id' => $employerUser->id],
            [
                'company_name' => 'DemoTech Ltd',
                'industry' => 'Technology',
                'location' => 'Cairo, Egypt',
                'about' => 'We build scalable products and love clean code.',
                'website' => 'https://demo.example.com',
            ]
        );

        // Ensure a few candidate users exist
        $candidate1 = User::firstOrCreate(
            ['email' => 'candidate1@example.com'],
            ['name' => 'Candidate One', 'type' => 'candidate', 'password' => bcrypt('password')]
        );
        $candidate2 = User::firstOrCreate(
            ['email' => 'candidate2@example.com'],
            ['name' => 'Candidate Two', 'type' => 'candidate', 'password' => bcrypt('password')]
        );
        $candidate3 = User::firstOrCreate(
            ['email' => 'candidate3@example.com'],
            ['name' => 'Candidate Three', 'type' => 'candidate', 'password' => bcrypt('password')]
        );

        // Seed reviews (unique per candidate per company)
        $reviews = [
            [$candidate1->id, 5, 'Fantastic team and clear requirements.'],
            [$candidate2->id, 4, 'Good experience overall.'],
            [$candidate3->id, 3, 'Average process, could be faster.'],
        ];

        foreach ($reviews as [$candidateId, $rating, $reviewText]) {
            CompanyReview::updateOrCreate(
                ['company_id' => $info->id, 'candidate_id' => $candidateId],
                ['rating' => $rating, 'review' => $reviewText]
            );
        }
    }
}
