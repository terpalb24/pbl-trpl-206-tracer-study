<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Faker = Faker::create('id_ID');

      for ($i = 0; $i < 10; $i++) {
        \DB::table('tb_company')->insert([
            'id_user'=>'3',
            'company_name'=>$Faker->company(),
            'password'=>$Faker->password,
            'company_address'=>$Faker->address(),
            'company_email'=>$Faker->companyEmail(),
            'company_phone_number'=>$Faker->numerify('####-####-####'),


    


        ]);

      }
        //
    }
}
