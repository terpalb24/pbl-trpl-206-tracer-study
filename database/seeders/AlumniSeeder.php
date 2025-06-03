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
                'nim' => '4342401058',
                'id_user' => 1,
                'name' => 'dinny',
                'nik' => '4342401058',
                'gender'=>'female',
                'date_of_birth' => '2005-06-02',
                'phone_number'=>'082376741905',
                'email'=>'dinnymardin22@gmail.com',
                'status'=>'worked',
                'graduation_year'=>'2028',
                'ipk'=>'4.00',
                'batch'=>'24',
                'address'=>'batu aji',
                'id_study'=>'1',
            ]);
    
            
        }
        //
    }

