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
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Update status before saving
        static::saving(function ($periode) {
            $periode->status = $periode->calculateStatus();
        });
    }

    /**
     * Calculate status based on dates.
     *
     * @return string
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
     *
     * @return void
     */
    public static function updateAllStatuses()
    {
        $periodes = self::all();
        
        foreach ($periodes as $periode) {
            $newStatus = $periode->calculateStatus();
            
            if ($periode->status !== $newStatus) {
                $periode->status = $newStatus;
                $periode->save();
            }
        }
    }

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
