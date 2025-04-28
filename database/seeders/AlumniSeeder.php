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
                'nim' => '4343401036',
                'id_user' => 2,
                'name' => 'Andri Putra Desyandra siregar ',
                'nik' => '234567811',
                'gender'=>'male',
                'date_of_birth' => '2006-09-11',
                'phone_number'=>'082173634512',
                'email'=>'andriputra110906@gmail.com',
                'status'=>'worked',
                'study_program'=>'TRPL',
                'graduation_year'=>'2028',
                'ipk'=>'4.00',
                'batch'=>'24',
                'address'=>'simpang raya',
            ]);
    
            
        }
        //
    }

