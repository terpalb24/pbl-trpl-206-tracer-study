<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tb_Company extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tb_company';
    protected $primaryKey = 'id_company';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_company',
        'id_user',
        'company_name',
        'company_address',
        'company_email',
        'company_phone_number',
        'created_at',
        'updated_at',
        'Hrd_name'
    ];

    public function jobHistories()
    {
        return $this->hasMany(Tb_jobhistory::class, 'id_company', 'id_company');
    }

    // Relasi untuk semua alumni (termasuk yang sudah tidak bekerja)
    public function alumni()
    {
        return $this->hasManyThrough(
            Tb_Alumni::class,
            Tb_jobhistory::class,
            'id_company', // Foreign key on JobHistory
            'nim',        // Foreign key on Alumni
            'id_company', // Local key on Company
            'nim'         // Local key on JobHistory
        );
    }

    // Relasi baru untuk alumni yang masih aktif bekerja
    public function activeAlumni()
    {
        return $this->hasManyThrough(
            Tb_Alumni::class,
            Tb_jobhistory::class,
            'id_company', // Foreign key on JobHistory
            'nim',        // Foreign key on Alumni
            'id_company', // Local key on Company
            'nim'         // Local key on JobHistory
        )->whereHas('jobHistories', function ($query) {
            $query->where('id_company', $this->id_company)
                  ->where(function ($subQuery) {
                      $subQuery->where('duration', 'Masih bekerja')
                               ->orWhereNull('end_date');
                  });
        });
    }

    // Method untuk mengecek apakah alumni masih aktif bekerja
    public function isAlumniActive($nim)
    {
        return $this->jobHistories()
                   ->where('nim', $nim)
                   ->where(function ($query) {
                       $query->where('duration', 'Masih bekerja')
                             ->orWhereNull('end_date');
                   })
                   ->exists();
    }
    
    // Method untuk mendapatkan job history aktif alumni
    public function getActiveJobHistory($nim)
    {
        return $this->jobHistories()
            ->where('nim', $nim)
            ->where(function ($query) {
                $query->where('duration', 'Masih bekerja')
                      ->orWhereNull('end_date');
            })
            ->first();
    }

    public function routeNotificationForMail()
    {
        return $this->company_email;
    }
}
