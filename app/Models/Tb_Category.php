<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Category extends Model
{
    use HasFactory;

    protected $table = 'tb_category';
    protected $primaryKey = 'id_category';
    public $timestamps = true;

    protected $fillable = [
        'id_periode',
        'category_name',
        'order',
        'for_type' // 'alumni', 'company', or 'both'
    ];

    public function periode()
    {
        return $this->belongsTo(Tb_Periode::class, 'id_periode', 'id_periode');
    }

    public function questions()
    {
        return $this->hasMany(Tb_Questions::class, 'id_category', 'id_category');
    }
}
