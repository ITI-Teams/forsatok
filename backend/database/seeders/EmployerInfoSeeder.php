<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployerInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('employer_infos')->insert([
            [
                'user_id' => 1,
                'company_name' => 'Tech Innovations Ltd',
                'industry' => 'Technology',

                'about' => 'Leading provider of tech solutions.',
                'website' => 'https://techinnovations.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'company_name' => 'FinancePro Inc',
                'industry' => 'Finance',

                'about' => 'Finance consulting and investment services.',
                'website' => 'https://financepro.com',
                               'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'company_name' => 'HealthCare Global',
                'industry' => 'Healthcare',
                
                'about' => 'A global leader in healthcare solutions.',
                'website' => 'https://healthcareglobal.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
