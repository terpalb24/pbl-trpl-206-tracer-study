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
            'id_user' => 1,
            'company_name' => 'PT Sukamaju',
            'company_address' => 'Jl. Raya No 1',
            'company_email' => 'sukamaju@gmail.com',
            'company_phone_number' => '08123456789',
        ]);
        \App\Models\Tb_Company::create([
            'id_user' => 2,
            'company_name' => 'PT Maju Mundur',
            'company_address' => 'Jl. Melati No 2',
            'company_email' => 'majumundur@gmail.com',
            'company_phone_number' => '08123456780',
        ]);
        \App\Models\Tb_Company::create([
            'id_user' => 3,
            'company_name' => 'CV Karya Abadi',
            'company_address' => 'Jl. Kenanga No 3',
            'company_email' => 'karyaabadi@gmail.com',
            'company_phone_number' => '08123456781',
        ]);
    }
}

