<?php

namespace Database\Seeders;

use App\Domains\Applications\Models\JobApplication;
use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Support\Str;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $candidates = User::where("type", "candidate")->get();
        $jobs = JobPost::all();

        if ($candidates->isEmpty() || $jobs->isEmpty()) {
            $this->command?->warn('JobApplicationSeeder: No candidates or jobs available.');
            return;
        }

        $faker = fake();

        $noAppsPercentage = 20; // مثال: 20%

        foreach ($jobs as $index => $job) {

            if (rand(1, 100) <= $noAppsPercentage) {
                continue;
            }

            $applicationsCount = rand(1, 5);

            for ($i = 0; $i < $applicationsCount; $i++) {
                $candidate = $candidates->random();

                $statuses = ['pending', 'accepted', 'rejected'];
                $status = strtolower(trim($faker->randomElement($statuses)));

                JobApplication::firstOrCreate(
                    [
                        'candidate_id' => $candidate->id,
                        'job_post_id' => $job->id,
                    ],
                    [
                        'cover_letter' => $faker->paragraphs(rand(2, 4), true),
                        'resume_path' => 'resumes/' . Str::slug($candidate->name) . '-cv.pdf',
                        'status' => $status,
                        'created_at' => now()->subDays(rand(1, 40)),
                    ]
                );
            }
        }

        $this->command?->info("JobApplicationSeeder: Applications created successfully.");
    }
}
