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
        'description',  // NEW: Added description field
        'order',
        'for_type',
        'is_status_dependent',
        'required_alumni_status',
        'is_graduation_year_dependent',  // NEW
        'required_graduation_years'      // NEW
    ];

    protected $casts = [
        'required_alumni_status' => 'array',
        'required_graduation_years' => 'array', // NEW
        'is_status_dependent' => 'boolean',
        'is_graduation_year_dependent' => 'boolean' // NEW
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
     * Check if this category is accessible by alumni based on their status and graduation year
     */
    public function isAccessibleByAlumni($alumni)
    {
        // Jika kategori tidak bergantung pada status, semua alumni bisa akses
        if ($this->is_status_dependent && !empty($this->required_alumni_status)) {
            // Jika kategori bukan untuk alumni, skip dependency check
            if ($this->for_type !== 'company') {
                // Check apakah status alumni sesuai dengan requirement
                if (!in_array($alumni->status, $this->required_alumni_status)) {
                    return false;
                }
            }
        }

        // NEW: Check graduation year dependency
        if ($this->is_graduation_year_dependent && !empty($this->required_graduation_years)) {
            if ($this->for_type !== 'company') {
                // Check apakah tahun lulus alumni sesuai dengan requirement
                if (!in_array($alumni->graduation_year, $this->required_graduation_years)) {
                    return false;
                }
            }
        }

        return true;
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

    /**
     * NEW: Get available graduation years from periode
     */
    public function getAvailableGraduationYears()
    {
        if (!$this->periode) {
            return [];
        }

        $periode = $this->periode;
        
        // Jika semua alumni bisa akses
        if ($periode->all_alumni || $periode->target_type === 'all') {
            return Tb_Alumni::select('graduation_year')
                ->distinct()
                ->orderBy('graduation_year', 'desc')
                ->pluck('graduation_year')
                ->toArray();
        }

        $currentYear = now()->year;

        // Jika target berdasarkan tahun lalu (years ago)
        if ($periode->target_type === 'years_ago' && !empty($periode->years_ago_list)) {
            return collect($periode->years_ago_list)->map(function($yearsAgo) use ($currentYear) {
                return (string)($currentYear - $yearsAgo);
            })->toArray();
        }

        // Jika target berdasarkan tahun spesifik
        if ($periode->target_type === 'specific_years' && !empty($periode->target_graduation_years)) {
            return collect($periode->target_graduation_years)->map(function($year) {
                return (string)$year;
            })->toArray();
        }

        return [];
    }
}
