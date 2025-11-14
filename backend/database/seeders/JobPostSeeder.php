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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetJobs = 50;

        $employers = $this->ensureEmployers();
        $categories = $this->ensureCategories();
        $cities = $this->ensureCities();
        $skills = $this->ensureSkills();

        if ($employers->isEmpty() || $categories->isEmpty() || $cities->isEmpty()) {
            $this->command?->warn('JobPostSeeder: ensure employers, categories, and cities exist before seeding jobs.');
            return;
        }

        $currentCount = JobPost::count();
        if ($currentCount >= $targetJobs) {
            $this->command?->info("JobPostSeeder: already have {$currentCount} jobs, skipping.");
            return;
        }

        $faker = fake();
        $faker->unique(true);

        $experienceLevels = [
            'Entry Level',
            '1-3 years',
            '3-5 years',
            '5+ years',
        ];

        $workTypes = ['full-time', 'part-time', 'freelance'];
        $workPlaces = ['on-site', 'remote', 'hybrid'];

        for ($index = $currentCount; $index < $targetJobs; $index++) {
            $employer = $employers->random();
            $category = $categories->random();
            $city = $cities->random();

            $title = $this->uniqueJobTitle($faker->jobTitle(), $index + 1);
            while (JobPost::where('title', $title)->exists()) {
                $title = $this->uniqueJobTitle($faker->jobTitle(), $index + 1);
            }

            $salaryMin = $faker->numberBetween(800, 4000);
            $salaryMax = $faker->numberBetween($salaryMin + 200, $salaryMin + 5000);

            $responsibilities = $this->toMultilineText($faker->sentences($faker->numberBetween(4, 6)));
            $qualifications = $this->toMultilineText($faker->sentences($faker->numberBetween(3, 5)));
            $benefits = $this->toMultilineText($faker->sentences($faker->numberBetween(3, 5)));

            $job = JobPost::create([
                'employer_id' => $employer->id,
                'category_id' => $category->id,
                'title' => $title,
                'experience' => $faker->randomElement($experienceLevels),
                'description' => $faker->paragraphs($faker->numberBetween(2, 4), true),
                'responsibilities' => $responsibilities,
                'qualification' => $qualifications,
                'benefits' => $benefits,
                'salary_min' => $salaryMin,
                'salary_max' => $salaryMax,
                'deadline' => now()->addDays($faker->numberBetween(10, 120)),
                'is_active' => $faker->boolean(85),
                'work_type' => $faker->randomElement($workTypes),
                'work_place' => $faker->randomElement($workPlaces),
            ]);

            $job->locationable()->updateOrCreate(
                [],
                [
                    'country_id' => $city->country_id,
                    'city_id' => $city->id,
                    'address' => $faker->streetAddress(),
                ]
            );

            if ($skills->isNotEmpty() && Schema::hasTable('job_skills')) {
                $skillsToAttach = $this->pickRandomSkills($skills);
                $job->skills()->sync($skillsToAttach);
            }
        }
    }

    private function toMultilineText(array $items): string
    {
        return collect($items)
            ->map(fn ($line) => trim($line))
            ->filter()
            ->implode("\n");
    }

    private function uniqueJobTitle(string $baseTitle, int $index): string
    {
        return sprintf('%s #%d-%s', $baseTitle, $index, Str::upper(Str::random(3)));
    }

    private function ensureEmployers(int $minimum = 5): Collection
    {
        $employers = User::query()->where('type', 'employer')->get();

        if ($employers->count() >= $minimum) {
            return $employers;
        }

        $needed = $minimum - $employers->count();

        $existingCount = $employers->count();

        for ($i = 1; $i <= $needed; $i++) {
            $emailIndex = $existingCount + $i;
            $email = sprintf('employer%02d@job-demo.test', $emailIndex);
            $employers->push(User::create([
                'name' => "Employer {$emailIndex}",
                'email' => $email,
                'type' => 'employer',
                'password' => 'password',
            ]));
        }

        return User::query()->where('type', 'employer')->get();
    }

    private function ensureCategories(): Collection
    {
        $defaultCategories = [
            'Software Development',
            'Web Development',
            'Data Science',
            'Digital Marketing',
            'Project Management',
            'Design',
            'Customer Support',
            'Finance',
            'Human Resources',
            'Sales',
        ];

        foreach ($defaultCategories as $categoryName) {
            Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
        }

        return Category::all();
    }

    private function ensureCities(): Collection
    {
        $defaultCountries = [
            ['name' => 'Egypt', 'code' => 'EG'],
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'United Kingdom', 'code' => 'UK'],
        ];

        foreach ($defaultCountries as $countryData) {
            Country::firstOrCreate(['code' => $countryData['code']], $countryData);
        }

        $countries = Country::all()->keyBy('code');

        $defaultCities = [
            ['name' => 'Cairo', 'country_code' => 'EG'],
            ['name' => 'Alexandria', 'country_code' => 'EG'],
            ['name' => 'New York', 'country_code' => 'US'],
            ['name' => 'San Francisco', 'country_code' => 'US'],
            ['name' => 'Berlin', 'country_code' => 'DE'],
            ['name' => 'Munich', 'country_code' => 'DE'],
            ['name' => 'Paris', 'country_code' => 'FR'],
            ['name' => 'London', 'country_code' => 'UK'],
            ['name' => 'Manchester', 'country_code' => 'UK'],
        ];

        foreach ($defaultCities as $cityData) {
            $country = $countries->get($cityData['country_code']);
            if ($country) {
                City::firstOrCreate(
                    ['name' => $cityData['name'], 'country_id' => $country->id],
                    []
                );
            }
        }

        return City::with('country')->get();
    }

    private function ensureSkills(): Collection
    {
        $defaultSkills = [
            'PHP',
            'Laravel',
            'JavaScript',
            'TypeScript',
            'React',
            'Vue.js',
            'DevOps',
            'AWS',
            'Docker',
            'UI/UX Design',
            'SQL',
            'Data Analysis',
            'Agile Methodologies',
            'Project Management',
            'Communication',
        ];

        $categories = $this->ensureCategories();

        foreach ($defaultSkills as $skillName) {
            Skill::firstOrCreate(
                ['name' => $skillName],
                ['category_id' => $categories->random()->id]
            );
        }

        return Skill::all();
    }

    /**
     * @return array<int, int>
     */
    private function pickRandomSkills(Collection $skills): array
    {
        $count = $skills->count();
        if ($count === 0) {
            return [];
        }

        $take = min($count, rand(2, 5));
        $random = $skills->random($take);

        if ($random instanceof Collection) {
            return $random->pluck('id')->all();
        }

        return [$random->id];
    }
}
