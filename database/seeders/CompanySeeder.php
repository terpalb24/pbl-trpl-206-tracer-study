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
        
      Tb_Company::create([
        'id_user' => 1,
        'company_name' => 'pt sukamaju',
        'company_address' => 'jl. raya no 1',
        'company_email' => 'sukamaju@gmail.com',
        'company_phone_number' => '08123456789',
        
    ]);


        

      }
        //
    }

