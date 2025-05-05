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
                'nim' => '4342401037',
                'id_user' => 4,
                'name' => 'hasna fadhilah',
                'nik' => '234567333',
                'gender'=>'female',
                'date_of_birth' => '2005-10-11',
                'phone_number'=>'082173634539',
                'email'=>'hasna@gmail.com',
                'status'=>'worked',
                'graduation_year'=>'2028',
                'ipk'=>'4.00',
                'batch'=>'24',
                'address'=>'batuaji',
            ]);
            
        }
        //
    }

