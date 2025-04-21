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
            'username' => 'admin',
            'password' => Hash::make('tracer'),
            'role' => 1,
        ]);

        Tb_User::create([
            'username' => 'alumni',
            'password' => Hash::make('tracer'),
            'role' => 2,
        ]);
        Tb_User::create([
            'username' => 'perusahaan',
            'password' => Hash::make('tracer'),
            'role' => 3,
        ]);
       
        //
    }
}
