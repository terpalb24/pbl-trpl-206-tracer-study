<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
class Tb_User  extends Authenticatable implements MustVerifyEmail
{
    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_First_login'=> 'boolean',
    ];
    //
    public function alumni()
    {
        return $this->hasOne(Tb_Alumni::class, 'id_user', 'id_user');
    }
    public function company()
    {
        return $this->hasOne(Tb_Company::class, 'id_user', 'id_user');
    }
    
}
