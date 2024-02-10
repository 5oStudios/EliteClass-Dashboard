<?php

namespace App\Console;

use App\Console\Commands\SendOtp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RenameVideo;
use App\Console\Commands\ReplaceFiles;
use App\Jobs\UpcomingLiveStreaming;
use App\Jobs\PendingInstalmentDueDateAlert;
use App\Jobs\EnrolledUserOnExpiredSession;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Torann\Currency\Console\Update::class,
        \Torann\Currency\Console\Cleanup::class,
        \Torann\Currency\Console\Manage::class,
        RenameVideo::class,
        Commands\DatabaseBackUp::class,
        ReplaceFiles::class,
        SendOtp::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // Log::info("run in UpcomingLiveStreaming");
        // $schedule->job(new UpcomingLiveStreaming)->everyMinute();

        $schedule->job(new PendingInstalmentDueDateAlert)->daily();
        $schedule->job(new EnrolledUserOnExpiredSession)->everyMinute();
        $schedule->command('otp:fetch')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
