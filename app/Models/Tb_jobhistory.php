<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tb_jobhistory extends Model
{
    use HasFactory;

    protected $table = 'tb_jobhistory';

    protected $primaryKey = 'id_jobhistory';

    protected $fillable = [
        'nim', 'id_company', 'position', 'salary', 'duration'
    ];

    protected $casts = [
    'salary' => 'string',
    'start_date' => 'date',
    'end_date' => 'date',
];

 

    public function alumni()
    {
        return $this->belongsTo(Tb_Alumni::class, 'nim', 'nim');
    }

    public function company()
    {
        return $this->belongsTo(Tb_Company::class, 'id_company', 'id_company');
    }
}



