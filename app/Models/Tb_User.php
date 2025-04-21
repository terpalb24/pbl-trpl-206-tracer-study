<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tb_User  extends Authenticatable
{
    protected $table = 'tb_user';
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
    //
}
