<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $ipk = $faker->randomFloat(2, 2.0, 4.0);
        for ($i=1; $i<=30; $i++){
            \DB::table('tb_alumni')->insert([
                'nim'=> $faker->numberBetween(100000, 999999),
                'id_user'=> '2',
                'name'=> $faker->name,
                'password'=>$faker->password,
                'nik' => $faker->numberBetween(1000000000, 2147483647), // Menghasilkan 11 digit
                'gender'=> $faker->randomElement(['male','female']),
                'date_of_birth'=>$faker->dateTimeBetween('-20 years' ,  '-2 years '),
                'phone_number'=> $faker->numerify('####-####-####'),
                'email'=> $faker->email,
                'status'=> $faker->randomElement(['working','not working']),
                'study_program'=> $faker->randomElement(['IF','Mesin','MB','Elektro']),
                'graduation_year'=> $faker->randomElement(['2022','2023','2024']),
                'ipk'=> $ipk,
                'batch'=>$faker->randomElement(['2022','2023','2024']),
                'address'=>$faker->address,

            
            ]);
        }
        //
    }
}
