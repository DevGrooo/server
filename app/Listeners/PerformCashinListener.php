<?php

namespace App\Listeners;

use App\Events\PerformCashinEvent;
use App\Jobs\SendMailCashinPerformJob;

class PerformCashinListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PerformCashinEvent  $event
     * @return void
     */
    public function handle(PerformCashinEvent $event)
    {
        $job = (new SendMailCashinPerformJob($event->cashin, $event->locale))->onConnection(config('queue.default'))->onQueue('notifications');
        dispatch($job);
    }
}
