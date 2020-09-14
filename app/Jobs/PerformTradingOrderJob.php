<?php

namespace App\Jobs;

use App\Models\TradingOrder;
use App\Services\Transactions\TradingOrderTransaction;
use Exception;

class PerformTradingOrderJob extends Job
{
    protected $trading_order;    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TradingOrder $trading_order)
    {
        $this->trading_order = $trading_order;        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->trading_order->exec_type == TradingOrder::EXEC_TYPE_BUY) {
            (new TradingOrderTransaction())->performBuy([
                'trading_order_id, receive_match_amount, send_match_amount, tax, nav, total_nav, vsd_trading_id, vsd_time_received, updated_by'
            ], true);
        } elseif ($this->trading_order->exec_type == TradingOrder::EXEC_TYPE_SELL) {
            (new TradingOrderTransaction())->performSell([
                'trading_order_id, receive_match_amount, send_match_amount, tax, nav, total_nav, vsd_trading_id, vsd_time_received, updated_by'
            ], true);
        } elseif ($this->trading_order->exec_type == TradingOrder::EXEC_TYPE_EXCHANGE) {
            (new TradingOrderTransaction())->performExchange([
                
            ], true);
        } else {
            throw new Exception('Loại lệnh giao dịch quỹ không được hỗ trợ');
        }        
    }
}
