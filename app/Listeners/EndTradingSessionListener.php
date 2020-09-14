<?php

namespace App\Listeners;

use App\Events\EndTradingSessionEvent;
use App\Jobs\CreateTransactionCommissionJob;
use App\Jobs\UpdateBalanceFundDistributorProductJob;
use Illuminate\Support\Carbon;

class EndTradingSessionListener
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
     * @param  \App\Events\EndTradingSessionEvent  $event
     * @return void
     */
    public function handle(EndTradingSessionEvent $event)
    {
        $job = (new UpdateBalanceFundDistributorProductJob($event->trading_session))
            ->onConnection(config('queue.default'))
            ->onQueue('commissions')
            ->delay(Carbon::now()->addMinutes(2));
        dispatch($job);
    }
}
