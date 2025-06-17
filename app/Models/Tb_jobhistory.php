<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tb_jobhistory extends Model
{
    use HasFactory;

    protected $table = 'tb_jobhistory';
    protected $primaryKey = 'id_jobhistory';

    protected $fillable = [
        'nim', 'id_company', 'position', 'salary', 'duration', 'start_date', 'end_date', 'user_id'
    ];

    protected $casts = [
        'salary' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Scope untuk job history yang masih aktif
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('duration', 'Masih bekerja')
              ->orWhereNull('end_date');
        });
    }

    // Scope untuk job history yang sudah selesai
    public function scopeInactive($query)
    {
        return $query->where('duration', '!=', 'Masih bekerja')
                     ->whereNotNull('end_date');
    }

    public function alumni()
    {
        return $this->belongsTo(Tb_Alumni::class, 'nim', 'nim');
    }

    public function company()
    {
        return $this->belongsTo(Tb_Company::class, 'id_company', 'id_company');
    }

    // Method untuk mengecek apakah job masih aktif
    public function isActive()
    {
        return $this->duration === 'Masih bekerja' || is_null($this->end_date);
    }
}



