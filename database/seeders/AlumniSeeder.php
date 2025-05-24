<?php

namespace Database\Seeders;

use App\Models\Tb_Alumni;
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
        
            Tb_Alumni::create([
                'nim' => '4342401044',
                'id_user' => 1,
                'name' => 'bima',
                'nik' => '4342401044',
                'gender'=>'male',
                'date_of_birth' => '2005-09-02',
                'phone_number'=>'082173634512',
                'email'=>'sheila@gmail.com',
                'status'=>'worked',
                'graduation_year'=>'2028',
                'ipk'=>'4.00',
                'batch'=>'24',
                'address'=>'legenda',
                'id_study'=>'1',
            ]);
    
            
        }
        //
    }

