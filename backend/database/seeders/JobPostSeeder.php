<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Models\Category;
use App\Domains\Users\Models\User;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get employers and categories by simple ID
        $employers = User::where('type', 'employer')->orderBy('id')->get();
        $categories = Category::orderBy('id')->get();



        // Use simple array indexing - category_id 1, 2, 3, etc. and employer_id from employers collection
        $jobs = [
            [
                'title' => 'Senior PHP Developer',
                'experience' => '5+ years',
                'description' => 'We are looking for an experienced PHP Developer to join our team. You will be responsible for building and maintaining backend services using Laravel framework. Experience with RESTful APIs, MySQL, and Git is required.',
                'salary_min' => 2000,
                'salary_max' => 5000,
                'type' => 'full-time',
                // 'location' => 'Remote',
                'deadline' => now()->addDays(30),
                'is_active' => true,
                'category_id' => $categories[0]->id, // Software Development
                'employer_id' => $employers[0]->id,
            ],
            [
                'title' => 'Frontend Engineer (Vue/React)',
                'experience' => '3+ years',
                'description' => 'Join our frontend team to develop modern single-page applications. You will work with Vue.js or React to create responsive and interactive user interfaces. Knowledge of TypeScript and state management is a plus.',
                'salary_min' => 1500,
                'salary_max' => 4000,
                'type' => 'remote',
                // 'location' => 'Cairo, Egypt',
                'deadline' => now()->addDays(20),
                'is_active' => true,
                'category_id' => $categories[1]->id, // Web Development
                'employer_id' => $employers[1]->id,
            ],
            [
                'title' => 'DevOps Engineer',
                'experience' => '4+ years',
                'description' => 'We need a DevOps Engineer to automate our deployment processes and maintain CI/CD pipelines. Experience with Docker, Kubernetes, AWS, and monitoring tools is essential.',
                'salary_min' => 2500,
                'salary_max' => 6000,
                'type' => 'full-time',
                // 'location' => 'Alexandria, Egypt',
                'deadline' => now()->addDays(25),
                'is_active' => true,
                'category_id' => $categories[4]->id, // DevOps
                'employer_id' => $employers[2]->id,
            ],
            [
                'title' => 'UI/UX Designer',
                'experience' => '2+ years',
                'description' => 'Creative UI/UX Designer needed to design beautiful and intuitive user interfaces. You will work closely with our development team to create wireframes, prototypes, and design systems.',
                'salary_min' => 1200,
                'salary_max' => 3500,
                'type' => 'full-time',
                // 'location' => 'Remote',
                'deadline' => now()->addDays(15),
                'is_active' => true,
                'category_id' => $categories[5]->id, // UI/UX Design
                'employer_id' => $employers[3]->id,
            ],
            [
                'title' => 'Data Scientist',
                'experience' => '3+ years',
                'description' => 'Looking for a Data Scientist to analyze large datasets and build machine learning models. Experience with Python, R, SQL, and data visualization tools is required.',
                'salary_min' => 3000,
                'salary_max' => 7000,
                'type' => 'full-time',
                // 'location' => 'Cairo, Egypt',
                'deadline' => now()->addDays(40),
                'is_active' => true,
                'category_id' => $categories[3]->id, // Data Science
                'employer_id' => $employers[0]->id,
            ],
            [
                'title' => 'Digital Marketing Specialist',
                'experience' => '2+ years',
                'description' => 'We are seeking a Digital Marketing Specialist to manage our online marketing campaigns. You will handle SEO, social media marketing, content creation, and analyze marketing metrics.',
                'salary_min' => 1000,
                'salary_max' => 3000,
                'type' => 'part-time',
                // 'location' => 'Remote',
                'deadline' => now()->addDays(18),
                'is_active' => true,
                'category_id' => $categories[7]->id, // Digital Marketing
                'employer_id' => $employers[1]->id,
            ],
            [
                'title' => 'Full Stack Developer',
                'experience' => '4+ years',
                'description' => 'Full Stack Developer position for someone who can work on both frontend and backend. Must have experience with Laravel, Vue.js, MySQL, and modern development practices.',
                'salary_min' => 2200,
                'salary_max' => 5500,
                'type' => 'full-time',
                // 'location' => 'Cairo, Egypt',
                'deadline' => now()->addDays(35),
                'is_active' => true,
                'category_id' => $categories[1]->id, // Web Development
                'employer_id' => $employers[2]->id,
            ],
            [
                'title' => 'Mobile App Developer (React Native)',
                'experience' => '3+ years',
                'description' => 'Join our mobile development team to build cross-platform mobile applications using React Native. Experience with iOS and Android app deployment is required.',
                'salary_min' => 1800,
                'salary_max' => 4500,
                'type' => 'full-time',
                // 'location' => 'Remote',
                'deadline' => now()->addDays(28),
                'is_active' => true,
                'category_id' => $categories[2]->id, // Mobile Development
                'employer_id' => $employers[3]->id,
            ],
        ];

        // Create job posts
        foreach ($jobs as $jobData) {
            JobPost::firstOrCreate(
                [
                    'title' => $jobData['title'],
                    'employer_id' => $jobData['employer_id'],
                ],
                $jobData
            );
        }
    }
}
