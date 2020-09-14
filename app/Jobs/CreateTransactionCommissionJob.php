<?php

namespace App\Jobs;

use App\Models\TransactionCommission;
use App\Services\Transactions\TransactionCommissionTransaction;
use Exception;

class CreateTransactionCommissionJob extends Job
{
    protected $type;
    protected $params;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, array $params)
    {
        $this->type = $type;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->type == TransactionCommission::TYPE_BUY) {
            (new TransactionCommissionTransaction())->createAndVerifyForOrderBuy($this->params, true);
        } elseif ($this->type == TransactionCommission::TYPE_MAINTANCE) {
            (new TransactionCommissionTransaction())->createAndVerifyForFundHolding($this->params, true);
        } else {
            throw new Exception('type not exists');
        }        
    }
}
