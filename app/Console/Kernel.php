<?php

namespace App\Console;

use App\Jobs\Cost;
use App\Http\Controllers\ServerController;
use App\Jobs\ReviewWebsiteJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            (new ServerController())->checkServer();
        })->everyMinute()->name('FrpServer')->withoutOverlapping()->onOneServer();

        $schedule->call(function () {
            (new Cost())->handle();
        })->hourly()->name('FrpServerCost')->withoutOverlapping()->onOneServer();

        // every three days
        $schedule->job(new ReviewWebsiteJob())->cron('0 0 */3 * *')->name('reviewWebsiteJob')->withoutOverlapping()->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
