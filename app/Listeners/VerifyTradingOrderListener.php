<?php

namespace App\Listeners;

use App\Events\VerifyTradingOrderEvent;
use App\Jobs\SendMailVerifyBuyTradingOrderJob;
use App\Jobs\SendMailVerifySellTradingOrderJob;
use App\Models\TradingOrder;

class VerifyTradingOrderListener
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
     * @param  \App\Events\VerifyTradingOrderEvent  $event
     * @return void
     */
    public function handle(VerifyTradingOrderEvent $event)
    {
        if ($event->trading_order->exec_type == TradingOrder::EXEC_TYPE_BUY) {
            // make file pdf + send mail
            $job = new SendMailVerifyBuyTradingOrderJob($event->trading_order, $event->locale);
            $job->onConnection(config('queue.default'))->onQueue('notifications');
            dispatch($job);

        } elseif ($event->trading_order->exec_type == TradingOrder::EXEC_TYPE_SELL) {
            // make file pdf + send mail
            $job = new SendMailVerifySellTradingOrderJob($event->trading_order, $event->locale);
            $job->onConnection(config('queue.default'))->onQueue('notifications');
            dispatch($job);
        }
        
    }
}
