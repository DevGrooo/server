<?php

namespace App\Events;

use App\Models\TradingOrder;

class PerformTradingOrderEvent extends Event
{
    public $trading_order;
    public $locale;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TradingOrder $trading_order)
    {
        $this->trading_order = $trading_order;
        $this->locale = app('translator')->getLocale();
    }
}
