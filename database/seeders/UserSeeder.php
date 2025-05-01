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
            'id_user' => 4,
            'username' => '4342401031',
            'password' => Hash::make('4342401031'),
            'role' => 2,
        ]);


   

       
        //
    }
}
