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
                'nim' => '4343401035',
                'id_user' => 2,
                'name' => 'Muhammad Hasan Firdaus ',
                'nik' => '234567811',
                'gender'=>'male',
                'date_of_birth' => '2004-09-11',
                'phone_number'=>'082173634512',
                'email'=>'hasanfirdaus@gmail.com',
                'status'=>'worked',
                'study_program'=>'TRPL',
                'graduation_year'=>'2028',
                'ipk'=>'4.00',
                'batch'=>'24',
                'address'=>'batara raya',
            ]);
    
            
        }
        //
    }

