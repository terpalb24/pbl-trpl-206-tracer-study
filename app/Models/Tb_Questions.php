<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Questions extends Model
{
    use HasFactory;

    protected $table = 'tb_questions';
    protected $primaryKey = 'id_question';
    public $timestamps = true;

    protected $fillable = [
        'id_category',
        'question',
        'type',
        'before_text',
        'after_text',
        'order',
        'depends_on',
        'depends_value'
    ];

    // Update the mutator methods to be more robust
    public function setBeforeTextAttribute($value)
    {
        $this->attributes['before_text'] = is_null($value) || $value === '' ? null : $value;
    }

    public function setAfterTextAttribute($value)
    {
        $this->attributes['after_text'] = is_null($value) || $value === '' ? null : $value;
    }

    public function category()
    {
        return $this->belongsTo(Tb_Category::class, 'id_category', 'id_category');
    }

    public function options()
    {
        return $this->hasMany(Tb_Question_Options::class, 'id_question', 'id_question');
    }
}
