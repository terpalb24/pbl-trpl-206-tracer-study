<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Question_Options extends Model
{
    use HasFactory;

    protected $table = 'tb_questions_options';
    protected $primaryKey = 'id_questions_options';  // This matches the actual DB column
    public $timestamps = true;

    protected $fillable = [
        'id_question',
        'order',
        'option',
        'is_other_option',
        'other_before_text',
        'other_after_text'
    ];

    // Add default attributes
    protected $attributes = [
        'is_other_option' => false
    ];

    // Add accessor to provide id_option alias for id_questions_options
    public function getIdOptionAttribute()
    {
        return $this->attributes['id_questions_options'];
    }

    public function question()
    {
        return $this->belongsTo(Tb_Questions::class, 'id_question', 'id_question');
    }

    public function answerItems()
    {
        return $this->hasMany(Tb_User_Answer_Item::class, 'id_questions_options', 'id_questions_options');
    }
}
