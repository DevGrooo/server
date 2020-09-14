<?php

namespace App\Listeners;

use App\Events\ActiveInvestorEvent;
use App\Jobs\SendMailActiveInvestorJob;
use App\Jobs\UpdateStatusSendMailInvestorJob;

class ActiveInvestorListener
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
     * @param  \App\Events\ActiveInvestorEvent  $event
     * @return void
     */
    public function handle(ActiveInvestorEvent $event)
    {
        $job = (new SendMailActiveInvestorJob($event->investor, $event->locale))->chain([
            new UpdateStatusSendMailInvestorJob($event->investor, $event->locale)
        ]);
        $job->allOnConnection(config('queue.default'))->allOnQueue('notifications');
        dispatch($job);
    }
}
