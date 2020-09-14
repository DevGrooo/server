<?php

namespace App\Jobs;

use App\Models\FundDistributorProduct;
use App\Models\TradingSession;
use App\Models\TransactionCommission;
use App\Services\Transactions\FundDistributorProductBalanceTransaction;

class UpdateBalanceFundDistributorProductJob extends Job
{
    protected $trading_session;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TradingSession $trading_session)
    {
        $this->trading_session = $trading_session;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->trading_session->previous_trading_session_id > 0) {
            $day_holding = 0;
            $fund_distributor_products = FundDistributorProduct::all();
            $previous_trading_session = TradingSession::find($this->trading_session->previous_trading_session_id);
            if ($previous_trading_session) {
                $day_holding = floor(($this->trading_session->end_at - $previous_trading_session->end_at) / 86400);
            }            
            $transaction = new FundDistributorProductBalanceTransaction();
            foreach ($fund_distributor_products as $fund_distributor_product) {
                $result = $transaction->create([
                    'trading_session_id' => $this->trading_session->previous_trading_session_id, 
                    'fund_distributor_product_id' => $fund_distributor_product->id, 
                    'created_by' => 0,
                ], true);
                if ($day_holding > 0 && $result['balance'] > 0) {
                    $job = (new CreateTransactionCommissionJob(TransactionCommission::TYPE_MAINTANCE, [
                        'fund_distributor_product_id' => $fund_distributor_product->id,                    
                        'trading_session_id' => $this->trading_session->id,
                        'balance' => $result['balance'],
                        'day_holding' => $day_holding,
                        'created_by' => 0,
                    ]))->onConnection(config('queue.default'))->onQueue('commissions');
                    dispatch($job);
                }
            }
        }
    }
}
