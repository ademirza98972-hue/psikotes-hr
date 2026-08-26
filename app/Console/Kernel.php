<?php

namespace App\Console;

use App\Console\Commands\PurgeSoftDeletes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(PurgeSoftDeletes::class)
            ->dailyAt('03:00')
            ->withoutOverlapping();
    }
}
