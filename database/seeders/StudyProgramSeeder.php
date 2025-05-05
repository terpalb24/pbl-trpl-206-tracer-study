<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\Tb_study_program;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tb_study_program::create([
            'nim' => '4342401037',
            'study_program' => 'Teknolgi rekayasa perangkat lunak',
        ]);

        //
    }
}
