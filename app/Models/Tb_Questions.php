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
        'order',
        'before_text',
        'after_text',
        'depends_on',
        'depends_value',
        'status'
    ];

    protected $attributes = [
        'status' => 'visible' // Ubah default
    ];

    // Scope untuk mengambil hanya pertanyaan yang visible
    public function scopeVisible($query)
    {
        return $query->where('status', 'visible');
    }

    // Scope untuk mengambil hanya pertanyaan yang active
    // Update scope active untuk backward compatibility
    public function scopeActive($query)
    {
        return $query->where('status', 'visible');
    }

    // Scope untuk mengambil semua pertanyaan termasuk yang hidden (untuk admin)
    public function scopeWithHidden($query)
    {
        return $query; // Return semua
    }

    public function category()
    {
        return $this->belongsTo(Tb_Category::class, 'id_category', 'id_category');
    }

    public function options()
    {
        return $this->hasMany(Tb_Question_Options::class, 'id_question', 'id_question');
    }

    public function dependsOnQuestion()
    {
        return $this->belongsTo(Tb_Questions::class, 'depends_on', 'id_question');
    }

    public function dependentQuestions()
    {
        return $this->hasMany(Tb_Questions::class, 'depends_on', 'id_question');
    }
}
