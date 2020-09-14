<?php

namespace App\Console;

use App\Jobs\UpdateTradingSessionTimeUpJob;
use App\Jobs\UpdateTradingSessionEndJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new UpdateTradingSessionTimeUpJob(), 'schedules', config('queue.default'))->withoutOverlapping()->everyMinute();
        $schedule->job(new UpdateTradingSessionEndJob(), 'schedules', config('queue.default'))->withoutOverlapping()->daily();
    }
}
