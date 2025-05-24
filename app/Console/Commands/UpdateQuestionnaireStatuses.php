<?php

namespace App\Console\Commands;

use App\Models\Tb_Periode;
use Illuminate\Console\Command;

class UpdateQuestionnaireStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questionnaire:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all questionnaire period statuses based on current date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating questionnaire statuses...');
        
        $count = 0;
        $periodes = Tb_Periode::all();
        
        foreach ($periodes as $periode) {
            $oldStatus = $periode->status;
            $newStatus = $periode->calculateStatus();
            
            if ($oldStatus !== $newStatus) {
                $periode->status = $newStatus;
                $periode->save();
                $count++;
                
                $this->info("Updated period #{$periode->id_periode}: {$oldStatus} -> {$newStatus}");
            }
        }
        
        $this->info("Completed! Updated {$count} period statuses.");
        
        return Command::SUCCESS;
    }
}
