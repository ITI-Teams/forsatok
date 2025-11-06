<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Software Development',
            'Web Development',
            'Mobile Development',
            'Data Science',
            'DevOps',
            'UI/UX Design',
            'Graphic Design',
            'Digital Marketing',
            'Content Writing',
            'Project Management',
            'Business Analysis',
            'Quality Assurance',
            'Network Administration',
            'Cybersecurity',
            'Database Administration',
            'Sales',
            'Customer Support',
            'Human Resources',
            'Finance',
            'Accounting',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
        }

    }
}
