<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The application's command schedule.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\UpdatePeriodeStatus::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run daily at midnight to update all questionnaire statuses
        $schedule->command('questionnaire:update-statuses')->daily();

        // Update periode status setiap jam
        $schedule->command('periode:update-status')
                 ->hourly()
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
