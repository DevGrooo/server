<?php

namespace App\Jobs;

use App\Models\Investor;
use App\Services\Transactions\InvestorTransaction;

class UpdateStatusSendMailInvestorJob extends Job
{
    protected $investor;    
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Investor $investor, String $locale)
    {
        $this->investor = $investor;
        $this->locale = $locale;
    }

    public function handle()
    {
        (new InvestorTransaction)->updateStatusSendMail([
            'investor_id' => $this->investor->id, 
            'updated_by' => 0,
        ], true);
    }

}
