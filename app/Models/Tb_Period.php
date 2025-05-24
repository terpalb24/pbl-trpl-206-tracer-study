<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Period extends Model
{
    use HasFactory;

    protected $table = 'tb_period';
    protected $primaryKey = 'period_id';
    public $timestamps = true;

    protected $fillable = [
        'user_answer_id',
        'start_date',
        'end_date',
        'status_period',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function userAnswer()
    {
        return $this->belongsTo(Tb_User_Answers::class, 'user_answer_id');
    }

    public function categories()
    {
        return $this->hasMany(Tb_Category::class, 'period_id');
    }

    public function userAnswers()
    {
        return $this->belongsTo(Tb_User_Answers::class, 'user_answer_id')
            ->with('user');
    }
}
