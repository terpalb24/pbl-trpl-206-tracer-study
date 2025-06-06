<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Tb_Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Tb_Company::create([
            'id_user' => 3,
            'company_name' => 'CV Karya Abadi',
            'company_address' => 'Jl. Kenanga No 3',
            'company_email' => 'karyaabadi@gmail.com',
            'company_phone_number' => '08123456781',
        ]);
    }
}

