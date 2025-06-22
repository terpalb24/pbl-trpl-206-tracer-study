<?php

namespace App\Jobs;

use App\Models\Tb_Periode;
use App\Models\Tb_Alumni;
use App\Models\Tb_Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\RemindFillQuestionnaireMail;

class SendQuestionnaireReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id_periode;

    public function __construct($id_periode)
    {
        $this->id_periode = $id_periode;
    }

    public function handle()
    {
        $periode = Tb_Periode::find($this->id_periode);
        if (!$periode) return;

        // Ambil alumni sesuai target periode
        $alumniQuery = Tb_Alumni::query();
        if ($periode->all_alumni) {
            // Semua alumni
        } elseif ($periode->target_type === 'specific_years' && is_array($periode->target_graduation_years)) {
            $alumniQuery->whereIn('graduation_year', $periode->target_graduation_years);
        } elseif ($periode->target_type === 'years_ago' && is_array($periode->years_ago_list)) {
            $currentYear = now()->year;
            $targetYears = collect($periode->years_ago_list)->map(fn($y) => (string)($currentYear - $y))->toArray();
            $alumniQuery->whereIn('graduation_year', $targetYears);
        }
        $alumniList = $alumniQuery->get();

        // Ambil semua perusahaan (jika ada kategori untuk perusahaan)
        $hasCompanyCategory = $periode->categories()->whereIn('for_type', ['company', 'both'])->exists();
        $companyList = $hasCompanyCategory ? Tb_Company::all() : collect();

        foreach ($alumniList as $alumni) {
            if ($alumni->email) {
                Mail::to($alumni->email)->queue(new RemindFillQuestionnaireMail($periode));
            }
        }
        foreach ($companyList as $company) {
            if ($company->company_email) {
                Mail::to($company->company_email)->queue(new RemindFillQuestionnaireMail($periode));
            }
        }
    }
}
