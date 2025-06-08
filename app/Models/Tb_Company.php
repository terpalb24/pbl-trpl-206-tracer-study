<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Tb_Company extends Authenticatable
{
    use HasFactory;
    //
    protected $table = 'tb_company';
    protected $primaryKey = 'id_company';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_company',
        'id_user',
        'company_name',
        'company_address',
        'company_email',
        'company_phone_number',
        'created_at',
        'updated_at'
    ];

    public function jobHistories()
    {
        return $this->hasMany(Tb_JobHistory::class, 'id_company', 'id_company');
    }

    public function alumni()
    {
        return $this->hasManyThrough(
            Tb_Alumni::class,
            Tb_JobHistory::class,
            'id_company', // Foreign key on JobHistory
            'nim',        // Foreign key on Alumni
            'id_company', // Local key on Company
            'nim'         // Local key on JobHistory
        );
    }
    
}
