<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tb_Periode;

class UpdatePeriodeStatus extends Command
{
    protected $signature = 'periode:update-status';
    protected $description = 'Update status periode dan auto-complete draft answers untuk periode yang expired';

    public function handle()
    {
        $this->info('Updating periode statuses...');
        
        $periodes = Tb_Periode::all();
        $updatedCount = 0;
        $autoCompletedTotal = 0;
        
        foreach ($periodes as $periode) {
            $oldStatus = $periode->status;
            $newStatus = $periode->calculateStatus();
            
            if ($oldStatus !== $newStatus) {
                $periode->status = $newStatus;
                $periode->save();
                $updatedCount++;
                
                $this->info("Updated Periode #{$periode->id_periode}: {$oldStatus} -> {$newStatus}");
                
                // Auto-complete draft answers when periode becomes expired
                if ($newStatus === 'expired' && $oldStatus !== 'expired') {
                    $completedCount = $periode->autoCompleteDraftAnswers();
                    $autoCompletedTotal += $completedCount;
                    
                    if ($completedCount > 0) {
                        $this->info("  └─ Auto-completed {$completedCount} draft answers");
                    }
                }
            }
        }
        
        $this->info("Completed! Updated {$updatedCount} periode statuses.");
        
        if ($autoCompletedTotal > 0) {
            $this->info("Auto-completed {$autoCompletedTotal} draft answers total.");
        }
        
        return Command::SUCCESS;
    }
}