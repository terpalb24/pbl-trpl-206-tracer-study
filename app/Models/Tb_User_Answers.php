<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_User_Answers extends Model
{
    use HasFactory;

    protected $table = 'tb_user_answers';
    protected $primaryKey = 'id_user_answer';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_periode',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(Tb_User::class, 'id_user', 'id_user')->with('alumni')->with('company');
    }

    public function periode()
    {
        return $this->belongsTo(Tb_Periode::class, 'id_periode', 'id_periode');
    }

    public function items()
    {
        return $this->hasMany(Tb_User_Answer_Item::class, 'id_user_answer', 'id_user_answer');
    }
}
