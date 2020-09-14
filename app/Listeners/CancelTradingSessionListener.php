<?php

namespace App\Listeners;

use App\Events\EndTradingSessionEvent;
use App\Jobs\CreateTransactionCommissionJob;
use App\Jobs\UpdateBalanceFundDistributorProductJob;
use Illuminate\Support\Carbon;

class CancelTradingSessionListener
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
        
    }
}
