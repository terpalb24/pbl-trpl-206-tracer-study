<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tb_User_Answers extends Model
{
    use HasFactory;

    protected $table = 'tb_user_answers';
    protected $primaryKey = 'id_user_answer';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_periode',
        'status',
        'nim', // TAMBAHAN: Field nim untuk relasi dengan alumni
        'created_at', // TAMBAHAN: Field untuk tracking kapan diselesaikan
    ];

    protected $casts = [
        'created_at' => 'datetime', // TAMBAHAN: Cast created_at ke datetime
    ];

    public function user()
    {
        return $this->belongsTo(Tb_User::class, 'id_user', 'id_user')->with('alumni')->with('company');
    }

    public function periode()
    {
        return $this->belongsTo(Tb_Periode::class, 'id_periode', 'id_periode');
    }

    public function items()
    {
        return $this->hasMany(Tb_User_Answer_Item::class, 'id_user_answer', 'id_user_answer');
    }

    // EXISTING: Relationship dengan Alumni melalui nim
    public function alumni()
    {
        return $this->belongsTo(Tb_Alumni::class, 'nim', 'nim');
    }

    // TAMBAHAN: Scope untuk filter berdasarkan company
    public function scopeForCompany($query, $companyId)
    {
        return $query->whereHas('user.company', function($q) use ($companyId) {
            $q->where('id_company', $companyId);
        });
    }

    // TAMBAHAN: Scope untuk filter berdasarkan periode aktif
    public function scopeActivePeriod($query)
    {
        return $query->whereHas('periode', function($q) {
            $q->where('status', 'active')
              ->whereDate('start_date', '<=', now())
              ->whereDate('end_date', '>=', now());
        });
    }

    // TAMBAHAN: Scope untuk filter berdasarkan status
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // TAMBAHAN: Accessor untuk format tanggal yang user-friendly
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y, H:i') : null;
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d M Y, H:i') : null;
    }

    public function getFormattedSubmittedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y, H:i') : null;
    }

    // TAMBAHAN: Method untuk mendapatkan progress completion
    public function getCompletionPercentage()
    {
        $totalQuestions = 0;
        $answeredQuestions = 0;
        
        // Get categories for this periode and user type
        $userType = $this->user->role == 3 ? ['company', 'both'] : ['alumni', 'both'];
        
        $categories = Tb_Category::where('id_periode', $this->id_periode)
            ->whereIn('for_type', $userType)
            ->get();
        
        foreach ($categories as $category) {
            $categoryQuestions = Tb_Questions::where('id_category', $category->id_category)
                ->where('status', 'active')
                ->count();
            $totalQuestions += $categoryQuestions;

            $answeredInCategory = Tb_User_Answer_Item::where('id_user_answer', $this->id_user_answer)
                ->whereHas('question', function($query) use ($category) {
                    $query->where('id_category', $category->id_category);
                })
                ->count();
            $answeredQuestions += $answeredInCategory;
        }

        return $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
    }

    // TAMBAHAN: Method untuk check apakah sudah lengkap
    public function isComplete()
    {
        return $this->status === 'completed' && $this->created_at !== null;
    }

    // TAMBAHAN: Method untuk check apakah masih dalam tahap draft
    public function isDraft()
    {
        return $this->status === 'draft';
    }
}
