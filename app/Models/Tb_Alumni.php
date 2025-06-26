<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Tb_Alumni extends Authenticatable
{
    use HasFactory, Notifiable;

    // Menetapkan nama tabel yang digunakan model
    protected $table = 'tb_alumni';

    // Menetapkan primary key yang digunakan
    protected $primaryKey = 'nim';  // Pastikan ini sesuai dengan kolom primary key yang kamu pakai

    // Menonaktifkan auto-increment karena nim tidak auto increment
    public $incrementing = false;

    // Kolom-kolom yang bisa diisi massal
    protected $fillable = [
        'nim',
        'id_study',
        'id_user',
        'name',
        'nik',
        'date_of_birth',
        'gender',
        'phone_number',
        'email',
        'status',
        'study_program',
        'graduation_year',
        'ipk',
        'batch',
        'address',
        'created_at',
        'is_First_login',
        'updated_at'
    ];
    public function routeNotificationForMail()
    {
        return $this->email; // Mengembalikan alamat email alumni
    }
    // App\Models\Tb_Alumni.php

    public function studyProgram()
    {
        return $this->belongsTo(Tb_study_program::class, 'id_study', 'id_study');
    }

    public function jobHistories()
    {
        return $this->hasMany(Tb_JobHistory::class, 'nim', 'nim');
    }

    
    

    // ...
}
