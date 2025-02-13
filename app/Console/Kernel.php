<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule) {
        $schedule->command('vacation:refresh')->yearlyOn(1,1, '5:0');
        $schedule->command('holidays:create')->yearlyOn(1,1, '5:0');
        $schedule->command('leaves:notification')->dailyAt('08:00');
        $schedule->command('anniversary:notification')->dailyAt('09:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
