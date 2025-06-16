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
        'for_type',
        'is_status_dependent',
        'required_alumni_status'
    ];

    protected $casts = [
        'required_alumni_status' => 'array',
        'is_status_dependent' => 'boolean'
    ];

    public function periode()
    {
        return $this->belongsTo(Tb_Periode::class, 'id_periode', 'id_periode');
    }

    public function questions()
    {
        return $this->hasMany(Tb_Questions::class, 'id_category', 'id_category');
    }

    /**
     * Check if this category is accessible by alumni based on their status
     */
    public function isAccessibleByAlumni($alumni)
    {
        // Jika kategori tidak bergantung pada status, semua alumni bisa akses
        if (!$this->is_status_dependent || empty($this->required_alumni_status)) {
            return true;
        }

        // Jika kategori bukan untuk alumni, skip dependency check
        if ($this->for_type === 'company') {
            return true;
        }

        // Check apakah status alumni sesuai dengan requirement
        return in_array($alumni->status, $this->required_alumni_status);
    }

    /**
     * Get available alumni status options
     */
    public static function getAlumniStatusOptions()
    {
        return [
            'bekerja' => 'Bekerja',
            'tidak bekerja' => 'Tidak Bekerja',
            'melanjutkan studi' => 'Melanjutkan Studi',
            'berwiraswasta' => 'Berwiraswasta',
            'sedang mencari kerja' => 'Sedang Mencari Kerja'
        ];
    }
}
