<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Users\Models\User;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class JobPostSeeder extends Seeder
{
    public function run(): void
    {
        $targetJobs = 50;

        $employers = User::where("type", "employer")->get();
        $categories = Category::all();
        $cities = City::with("country")->get();
        $skills = Skill::all();

        if ($employers->isEmpty() || $categories->isEmpty() || $cities->isEmpty()) {
            $this->command?->warn('JobPostSeeder: missing employers/categories/cities.');
            return;
        }

        $faker = fake();

        $experienceLevels = [
            'Entry Level',
            '1-3 years',
            '3-5 years',
            '5+ years',
        ];

        $workTypes = ['full-time', 'part-time', 'freelance'];
        $workPlaces = ['on-site', 'remote', 'hybrid'];

        $createdJobsCount = 0;
        $employerIndex = 0;

        while ($createdJobsCount < $targetJobs) {

            $employer = $employers[$employerIndex];
            $employerIndex = ($employerIndex + 1) % $employers->count();

            $category = $categories->random();
            $city = $cities->random();

            $title = $this->generateUniqueJobTitle($faker);

            $salaryMin = $faker->numberBetween(6000, 15000);
            $salaryMax = $faker->numberBetween($salaryMin + 1000, $salaryMin + 8000);

            $job = JobPost::create([
                'views' => rand(50, 3000),
                'employer_id' => $employer->id,
                'category_id' => $category->id,
                'title' => $title,
                'experience' => $faker->randomElement($experienceLevels),
                'description' => $faker->paragraphs(rand(2, 4), true),
                'salary_min' => $salaryMin,
                'salary_max' => $salaryMax,
                'deadline' => now()->addDays(rand(10, 90)),
                // 80% approved, 10% pending, 5% rejected, 5% expired
                'status' => $faker->randomElement([
                    JobPost::STATUS_APPROVED, JobPost::STATUS_APPROVED, JobPost::STATUS_APPROVED, JobPost::STATUS_APPROVED, // 80%
                    JobPost::STATUS_PENDING,
                    JobPost::STATUS_REJECTED,
                ]),
                // is_active must be false if not approved. If approved, 90% chance of being active
                'is_active' => false,
                'responsibilities' => $this->multi($faker->sentences(rand(4, 7))),
                'qualification' => $this->multi($faker->sentences(rand(4, 7))),
                'benefits' => $this->multi($faker->sentences(rand(3, 5))),
                'work_type' => $faker->randomElement($workTypes),
                'work_place' => $faker->randomElement($workPlaces),
            ]);

            // Correct boolean logic for active state
            if ($job->status === JobPost::STATUS_APPROVED) {
                $job->update(['is_active' => rand(0, 100) > 10]); // 90% active if approved
                
                // Create approval decision
                $job->decisions()->create([
                    'admin_id' => User::role('admin')->inRandomOrder()->first()->id ?? 1,
                    'from_status' => JobPost::STATUS_PENDING,
                    'to_status' => JobPost::STATUS_APPROVED,
                    'reason' => 'Auto-approved by seeder',
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            } elseif ($job->status === JobPost::STATUS_REJECTED) {
                // Create rejection decision
                $job->decisions()->create([
                    'admin_id' => User::role('admin')->inRandomOrder()->first()->id ?? 1,
                    'from_status' => JobPost::STATUS_PENDING,
                    'to_status' => JobPost::STATUS_REJECTED,
                    'reason' => $faker->sentence(10), // Random reason
                    'created_at' => now()->subDays(rand(1, 10))
                ]);
            }

            $job->locationable()->updateOrCreate([], [
                'country_id' => $city->country_id,
                'city_id' => $city->id,
                'address' => $faker->streetAddress(),
            ]);

            // Attach random skills
            if ($skills->isNotEmpty() && Schema::hasTable('job_skills')) {
                $job->skills()->sync($this->pickSkills($skills));
            }

            $createdJobsCount++;
        }

        $this->command?->info("JobPostSeeder: created {$createdJobsCount} job posts.");
    }

    private function multi(array $lines): string
    {
        return implode("\n", array_map(fn ($l) => trim($l), $lines));
    }

    private function generateUniqueJobTitle($faker)
    {
        do {
            $title = $faker->jobTitle() . " - " . Str::upper(Str::random(3));
        } while (JobPost::where('title', $title)->exists());

        return $title;
    }

    private function pickSkills(Collection $skills): array
    {
        $take = rand(2, 5);

        return $skills->shuffle()->take($take)->pluck('id')->toArray();
    }
}
