<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_Periode extends Model
{
    use HasFactory;

    protected $table = 'tb_periode';
    protected $primaryKey = 'id_periode';
    public $timestamps = true;

    protected $fillable = [
        'id_user_answer',
        'start_date',
        'end_date',
        'status',
        'target_graduation_years',
        'all_alumni',
        'target_type',
        'years_ago_list',
        'target_description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_graduation_years' => 'array',
        'years_ago_list' => 'array'
    ];

    // Static variable untuk menyimpan old status sementara
    private static $tempOldStatuses = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Update status before saving and handle auto-complete
        static::saving(function ($periode) {
            $oldStatus = $periode->getOriginal('status');
            $newStatus = $periode->calculateStatus();
            
            $periode->status = $newStatus;
            
            // Store old status in static variable using model's primary key
            self::$tempOldStatuses[$periode->id_periode] = $oldStatus;
        });
        
        // Handle auto-complete after saving
        static::saved(function ($periode) {
            $oldStatus = self::$tempOldStatuses[$periode->id_periode] ?? null;
            $newStatus = $periode->status;
            
            // Auto-complete draft answers when periode becomes expired
            if ($newStatus === 'expired' && $oldStatus !== 'expired' && $oldStatus !== null) {
                $periode->autoCompleteDraftAnswers();
            }
            
            // Clean up temporary storage
            unset(self::$tempOldStatuses[$periode->id_periode]);
        });
    }

    /**
     * Calculate status based on dates.
     */
    public function calculateStatus()
    {
        $now = now();
        
        if ($now < $this->start_date) {
            return 'inactive';
        } elseif ($now >= $this->start_date && $now <= $this->end_date) {
            return 'active';
        } else {
            return 'expired';
        }
    }

    /**
     * Update status for all periods.
     */
    public static function updateAllStatuses()
    {
        $periodes = self::all();
        
        foreach ($periodes as $periode) {
            $oldStatus = $periode->status;
            $newStatus = $periode->calculateStatus();
            
            if ($oldStatus !== $newStatus) {
                $periode->status = $newStatus;
                $periode->save();
                
                // Auto-complete draft answers when periode becomes expired
                if ($newStatus === 'expired' && $oldStatus !== 'expired') {
                    $periode->autoCompleteDraftAnswers();
                }
            }
        }
    }

    /**
     * Auto-complete all draft answers when periode expires
     */
    public function autoCompleteDraftAnswers()
    {
        $draftAnswers = Tb_User_Answers::where('id_periode', $this->id_periode)
            ->where('status', 'draft')
            ->get();
        
        $completedCount = 0;
        
        foreach ($draftAnswers as $userAnswer) {
            // Update status to completed with current timestamp
            $userAnswer->update([
                'status' => 'completed',
                'updated_at' => now()
            ]);
            
            $completedCount++;
            
            \Log::info('Auto-completed draft answer due to periode expiry', [
                'user_answer_id' => $userAnswer->id_user_answer,
                'periode_id' => $this->id_periode,
                'user_id' => $userAnswer->id_user,
                'nim' => $userAnswer->nim ?? 'N/A'
            ]);
        }
        
        if ($completedCount > 0) {
            \Log::info("Auto-completed {$completedCount} draft answers for expired periode", [
                'periode_id' => $this->id_periode,
                'periode_name' => $this->periode_name ?? "Periode #{$this->id_periode}"
            ]);
        }
        
        return $completedCount;
    }

    /**
     * Check if alumni can access this questionnaire period
     */
    public function isAccessibleByAlumni($alumni)
    {
        // Jika questionnaire untuk semua alumni
        if ($this->all_alumni || $this->target_type === 'all') {
            return true;
        }

        $currentYear = now()->year;
        $alumniGraduationYear = (int) $alumni->graduation_year;

        // Jika target berdasarkan tahun lalu (years ago)
        if ($this->target_type === 'years_ago' && !empty($this->years_ago_list)) {
            $targetYears = collect($this->years_ago_list)->map(function($yearsAgo) use ($currentYear) {
                return $currentYear - $yearsAgo;
            })->toArray();
            
            return in_array($alumniGraduationYear, $targetYears);
        }

        // Jika target berdasarkan tahun spesifik
        if ($this->target_type === 'specific_years' && !empty($this->target_graduation_years)) {
            $targetYears = array_map('intval', $this->target_graduation_years);
            return in_array($alumniGraduationYear, $targetYears);
        }

        return false;
    }

    /**
     * Get eligible alumni for this period
     */
    public function getEligibleAlumni()
    {
        if ($this->all_alumni || $this->target_type === 'all') {
            return Tb_Alumni::all();
        }

        $currentYear = now()->year;

        // Jika target berdasarkan tahun lalu (years ago)
        if ($this->target_type === 'years_ago' && !empty($this->years_ago_list)) {
            $targetYears = collect($this->years_ago_list)->map(function($yearsAgo) use ($currentYear) {
                return (string)($currentYear - $yearsAgo);
            })->toArray();
            
            return Tb_Alumni::whereIn('graduation_year', $targetYears)->get();
        }

        // Jika target berdasarkan tahun spesifik
        if ($this->target_type === 'specific_years' && !empty($this->target_graduation_years)) {
            return Tb_Alumni::whereIn('graduation_year', $this->target_graduation_years)->get();
        }

        return collect();
    }

    /**
     * Get target description for display
     */
    public function getTargetDescription()
    {
        if ($this->all_alumni || $this->target_type === 'all') {
            return 'Semua Alumni';
        }

        $currentYear = now()->year;

        // Jika target berdasarkan tahun lalu (years ago)
        if ($this->target_type === 'years_ago' && !empty($this->years_ago_list)) {
            $descriptions = collect($this->years_ago_list)->map(function($yearsAgo) use ($currentYear) {
                $year = $currentYear - $yearsAgo;
                return "{$yearsAgo} tahun lalu ({$year})";
            })->toArray();
            
            $count = $this->getEligibleAlumni()->count();
            return "Alumni Lulusan: " . implode(', ', $descriptions) . " ({$count} alumni)";
        }

        // Jika target berdasarkan tahun spesifik
        if ($this->target_type === 'specific_years' && !empty($this->target_graduation_years)) {
            $years = collect($this->target_graduation_years)->sort()->reverse()->implode(', ');
            $count = $this->getEligibleAlumni()->count();
            return "Alumni Lulusan Tahun: {$years} ({$count} alumni)";
        }

        return 'Tidak ada target yang ditentukan';
    }

    /**
     * Get alumni statistics by graduation year
     */
    public static function getAlumniStatisticsByYear()
    {
        return Tb_Alumni::select('graduation_year')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('graduation_year')
            ->where('graduation_year', '!=', '')
            ->groupBy('graduation_year')
            ->orderBy('graduation_year', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->graduation_year => $item->count];
            });
    }

    /**
     * Get years ago options with alumni count
     */
    public static function getYearsAgoOptions($maxYears = 10)
    {
        $currentYear = now()->year;
        $options = [];
        
        for ($i = 1; $i <= $maxYears; $i++) {
            $year = $currentYear - $i;
            $count = Tb_Alumni::where('graduation_year', (string)$year)->count();
            if ($count > 0) { // Hanya tampilkan yang ada alumni-nya
                $options[$i] = [
                    'years_ago' => $i,
                    'year' => $year,
                    'count' => $count,
                    'label' => "{$i} tahun lalu ({$year})"
                ];
            }
        }
        
        return collect($options);
    }

    // Existing relationships
    public function userAnswer()
    {
        return $this->belongsTo(Tb_User_Answers::class, 'id_user_answer', 'id_user_answer');
    }

    public function categories()
    {
        return $this->hasMany(Tb_Category::class, 'id_periode', 'id_periode');
    }

    public function userAnswers()
    {
        return $this->belongsTo(Tb_User_Answers::class, 'id_user_answer', 'id_user_answer')
            ->with('user');
    }
}
