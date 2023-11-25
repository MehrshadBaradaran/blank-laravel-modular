<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Gallery\app\Jobs\DeleteUnoccupiedImagesJob;
use Modules\Gallery\app\Jobs\DeleteUnoccupiedVideosJob;
use Modules\User\app\Jobs\DeleteUnregisteredUsersJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(DeleteUnregisteredUsersJob::class)->daily();
        $schedule->job(DeleteUnoccupiedImagesJob::class)->daily();
        $schedule->job(DeleteUnoccupiedVideosJob::class)->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
