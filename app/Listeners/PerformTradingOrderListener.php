<?php

namespace App\Listeners;

use App\Events\PerformTradingOrderEvent;
use App\Jobs\CreateTransactionCommissionJob;
use App\Models\TradingOrder;
use App\Models\TransactionCommission;
use Illuminate\Support\Carbon;

class PerformTradingOrderListener
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
     * @param  \App\Events\PerformTradingOrderEvent  $event
     * @return void
     */
    public function handle(PerformTradingOrderEvent $event)
    {
        if ($event->trading_order->exec_type == TradingOrder::EXEC_TYPE_BUY) {
            // create transaction commission for trading order exec_type = buy
            $params = [
                'trading_order_id' => $event->trading_order->id, 
                'created_by' => auth()->user()->id,
            ];
            $job = (new CreateTransactionCommissionJob(TransactionCommission::TYPE_BUY, $params))
                ->onConnection(config('queue.default'))
                ->onQueue('commissions')
                ->delay(Carbon::now()->addMinutes(5));
            dispatch($job);
        }
        
    }
}
