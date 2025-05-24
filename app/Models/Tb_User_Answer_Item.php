<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_User_Answer_Item extends Model
{
    use HasFactory;

    protected $table = 'tb_user_answer_item';
    protected $primaryKey = 'id_user_answer_item';
    public $timestamps = true;

    protected $fillable = [
        'id_user_answer',
        'id_question',
        'id_questions_options',  // Pastikan ini sama dengan kolom di database
        'answer',
        'other_answer',
        'other_value'
    ];

    public function userAnswer()
    {
        return $this->belongsTo(Tb_User_Answers::class, 'id_user_answer', 'id_user_answer');
    }

    public function question()
    {
        return $this->belongsTo(Tb_Questions::class, 'id_question', 'id_question');
    }

    public function option()
    {
        return $this->belongsTo(Tb_Question_Options::class, 'id_questions_options', 'id_questions_options');
    }

    public function questionOption()
    {
        return $this->belongsTo(Tb_Question_Options::class, 'id_questions_options', 'id_questions_options');
    }
}
