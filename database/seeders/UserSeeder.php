<?php

namespace Database\Seeders;

use App\Models\Tb_User;
use Illuminate\Support\Facades\Hash;
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
      

        Tb_User::create([
            'username' => '4342401035',
            'password' => Hash::make('4342401035'),
            'role' => 2,
        ]);
   

       
        //
    }
}
