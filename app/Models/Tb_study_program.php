<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tb_study_program extends Model
{
    use HasFactory;

    protected $table = 'tb_study_program';
    protected $primaryKey = 'id_study';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'study_program', 
        'nim',
    ];


}
