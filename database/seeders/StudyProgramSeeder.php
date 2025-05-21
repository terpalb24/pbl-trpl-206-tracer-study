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
            'id_study' => '1',
            'study_program' => 'Diploma 3 Teknik Informatika',
           
        ]);
        Tb_study_program::create([
            'id_study' => '2',
            'study_program' => 'Diploma 3 Teknologi Geomatika',
           
        ]);
        Tb_study_program::create([
            'id_study' => '3',
            'study_program' => 'Sarjana Terapan Animasi',
           
        ]);
        Tb_study_program::create([
            'id_study' => '4',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa Multimedia',
           
        ]);
        Tb_study_program::create([
            'id_study' => '5',
            'study_program' => 'Sarjana Terapan Rekayasa Keamanan Siber',
           
        ]);
        Tb_study_program::create([
            'id_study' => '6',
            'study_program' => 'Sarjana Terapan Rekayasa Perangkat Lunak',
           
        ]);
        Tb_study_program::create([
            'id_study' => '7',
            'study_program' => 'Sarjana Terapan Teknologi Permainan',
           
        ]);
        Tb_study_program::create([
            'id_study' => '8',
            'study_program' => 'Magister Terapan (S2) Rekayasa / Teknik Komputer',
           
        ]);
        //mesin
        Tb_study_program::create([
            'id_study' => '9',
            'study_program' => 'Diploma 3 Teknik Mesin',
           
        ]);
        Tb_study_program::create([
            'id_study' => '10',
            'study_program' => 'Diploma 3 Teknik Perawatan Pesawat Udara',
           
        ]);
        Tb_study_program::create([
            'id_study' => '11',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa Konstruksi Perkapalan',
           
        ]);
        Tb_study_program::create([
            'id_study' => '12',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa Pengelasan dan Fabrikasi',
           
        ]);
        Tb_study_program::create([
            'id_study' => '13',
            'study_program' => 'Program Profesi Insinyur (PSPPI)',

        ]);
        Tb_study_program::create([
            'id_study' => '14',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa Metalurgi',

        ]);
        //elektro
        Tb_study_program::create([
            'id_study' => '15',
            'study_program' => 'Diploma 3 Teknik Elektronika Manufaktur',

        ]);
        Tb_study_program::create([
            'id_study' => '16',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa Elektronika',

        ]);
        Tb_study_program::create([
            'id_study' => '17',
            'study_program' => 'Diploma 3 Teknik Instrumentasi',

        ]);
        Tb_study_program::create([
            'id_study' => '18',
            'study_program' => 'Sarjana Terapan teknik mekatronika',

        ]);
        Tb_study_program::create([
            'id_study' => '19',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa Pembangkit Energi',

        ]);
        Tb_study_program::create([
            'id_study' => '20',
            'study_program' => 'Sarjana Terapan Teknologi Rekayasa robotika',

        ]);
        //manajemen bisnis

        Tb_study_program::create([
            'id_study' => '21',
            'study_program' => 'Diploma 3 Akuntansi',

        ]);

        Tb_study_program::create([
            'id_study' => '22',
            'study_program' => 'Sarjana Terapan Akuntansi Manajerial',

        ]);
        Tb_study_program::create([
            'id_study' => '23',
            'study_program' => 'Sarjana Terapan Administrasi Bisnis Terapan',

        ]);
        Tb_study_program::create([
            'id_study' => '24',
            'study_program' => 'Sarjana Terapan Logistik Perdagangan Internasional',

        ]);
        Tb_study_program::create([
            'id_study' => '25',
            'study_program' => 'Program Studi D2 Jalur Cepat Distribusi Barang',

        ]);

        //
    }
}
