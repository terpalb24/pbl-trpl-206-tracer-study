<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i=1; $i<=30; $i++){
            \DB::table('tb_user')->insert([
                'username'=> $faker->firstName,
                'password'=> $faker->password,
                'role'=> '2',
            ]);
        }
        //
    }
}
