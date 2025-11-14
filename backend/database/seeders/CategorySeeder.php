<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\Skill;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categories paired with their skills
        $data = [

            'Software Development' => [
                'PHP', 'Laravel', 'Java', 'Spring Boot', 'C#', '.NET', 'Python', 'C++',
            ],

            'Web Development' => [
                'HTML', 'CSS', 'JavaScript', 'React', 'Vue.js', 'Angular', 'TailwindCSS',
            ],

            'Mobile Development' => [
                'Flutter', 'Dart', 'Kotlin', 'Swift', 'React Native',
            ],

            'Data Science' => [
                'Python', 'TensorFlow', 'Pandas', 'NumPy', 'Machine Learning',
            ],

            'DevOps' => [
                'Docker', 'Kubernetes', 'Jenkins', 'CI/CD', 'Linux', 'AWS',
            ],

            'UI/UX Design' => [
                'Figma', 'Adobe XD', 'Sketch', 'Wireframing', 'Prototyping',
            ],

            'Graphic Design' => [
                'Photoshop', 'Illustrator', 'InDesign', 'Branding', 'Logo Design',
            ],

            'Digital Marketing' => [
                'SEO', 'Google Ads', 'Facebook Ads', 'Analytics', 'Copywriting',
            ],

            'Content Writing' => [
                'Blog Writing', 'Copywriting', 'Technical Writing', 'SEO Writing',
            ],

            'Project Management' => [
                'Agile', 'Scrum', 'Jira', 'Leadership', 'Planning',
            ],

            'Business Analysis' => [
                'Requirement Gathering', 'Process Mapping', 'BPMN', 'UML',
            ],

            'Quality Assurance' => [
                'Manual Testing', 'Automation Testing', 'Selenium', 'JMeter',
            ],

            'Network Administration' => [
                'Cisco', 'Routers', 'Switching', 'Networking Protocols',
            ],

            'Cybersecurity' => [
                'Penetration Testing', 'Firewalls', 'Ethical Hacking', 'SIEM',
            ],

            'Database Administration' => [
                'SQL', 'MySQL', 'PostgreSQL', 'Oracle DB', 'NoSQL',
            ],

            'Sales' => [
                'Negotiation', 'Lead Generation', 'CRM', 'Communication',
            ],

            'Customer Support' => [
                'CRM', 'Customer Handling', 'Communication',
            ],

            'Human Resources' => [
                'Recruitment', 'Interviewing', 'HR Policies', 'Payroll',
            ],

            'Finance' => [
                'Financial Analysis', 'Forecasting', 'Budgeting',
            ],

            'Accounting' => [
                'Accounting Principles', 'QuickBooks', 'Bookkeeping',
            ],
        ];

        foreach ($data as $categoryName => $skills) {

            // Create Category
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );

            // Create Skills for Category
            foreach ($skills as $skillName) {
                Skill::firstOrCreate(
                    ['name' => $skillName, 'category_id' => $category->id],
                    ['slug' => Str::slug($skillName) . '-' . uniqid()]
                );
            }
        }
    }
}
